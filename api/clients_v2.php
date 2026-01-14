<?php
// api/clients_v2.php
// Manages clients with support for SaaS (multi-tenant) and credit limit validation using client_ledger.

require_once __DIR__ . '/common.php';

// Ensure DB connection is available
if (!isset($db)) {
    // If common.php didn't provide $db, try to connect manually
    if (class_exists('DB') && method_exists('DB', 'connect')) {
        $db = DB::connect();
    } elseif (class_exists('Db')) {
        // Use the legacy Db class via an anonymous class to access the protected connection
        $db = (new class extends Db {
            public function getPDO() { return $this->dbh; }
        })->getPDO();
    } else {
        // Fallback: try to include classconexion.php directly
        $conexionPath = __DIR__ . '/../class/classconexion.php';
        if (file_exists($conexionPath)) {
            require_once $conexionPath;
            $db = (new class extends Db {
                public function getPDO() { return $this->dbh; }
            })->getPDO();
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed: Class not found.']);
            exit;
        }
    }
}

// Verify Tenant/Session
if (empty($_SESSION['codsucursal'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Missing Tenant ID.']);
    exit;
}
$tenant_id = $_SESSION['codsucursal'];

// Handle Requests
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            handleGet($db, $tenant_id);
            break;
        case 'POST':
            handlePost($db, $tenant_id, $input);
            break;
        case 'PUT':
            handlePut($db, $tenant_id, $input);
            break;
        case 'DELETE':
            handleDelete($db, $tenant_id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// --- Functions ---

function handleGet($db, $tenant_id) {
    $search = $_GET['search'] ?? '';

    // Select clients and calculate balance from client_ledger
    // Note: client_ledger logic: amount > 0 is debt, amount < 0 is payment (or vice versa depending on logic).
    // Usually ledger: Debit (positive) - Credit (negative) = Balance.
    // We assume sum(amount) is the balance.

    $sql = "SELECT c.codcliente, c.nomcliente, c.doccliente, c.tlfcliente, c.email, c.direccion, c.limitecredito,
            COALESCE(SUM(l.amount), 0) as current_balance
            FROM clientes c
            LEFT JOIN client_ledger l ON c.codcliente = l.client_id
            WHERE c.codsucursal = :tenant_id";

    $params = [':tenant_id' => $tenant_id];

    if (!empty($search)) {
        $sql .= " AND (c.nomcliente LIKE :search OR c.doccliente LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " GROUP BY c.codcliente ORDER BY c.nomcliente ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $data]);
}

function handlePost($db, $tenant_id, $input) {
    if (empty($input['nomcliente']) || empty($input['doccliente'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Name and Document ID are required.']);
        return;
    }

    $sql = "INSERT INTO clientes (codsucursal, nomcliente, doccliente, tlfcliente, email, direccion, limitecredito, fecharegistro)
            VALUES (:tenant_id, :nomcliente, :doccliente, :tlfcliente, :email, :direccion, :limitecredito, NOW())";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':tenant_id' => $tenant_id,
        ':nomcliente' => $input['nomcliente'],
        ':doccliente' => $input['doccliente'],
        ':tlfcliente' => $input['tlfcliente'] ?? '',
        ':email' => $input['email'] ?? '',
        ':direccion' => $input['direccion'] ?? '',
        ':limitecredito' => $input['limitecredito'] ?? 0.00
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Client created successfully.', 'id' => $db->lastInsertId()]);
}

function handlePut($db, $tenant_id, $input) {
    if (empty($input['codcliente'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Client ID is required for update.']);
        return;
    }

    // Verify ownership
    $check = $db->prepare("SELECT codcliente FROM clientes WHERE codcliente = ? AND codsucursal = ?");
    $check->execute([$input['codcliente'], $tenant_id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Client not found.']);
        return;
    }

    $sql = "UPDATE clientes SET
            nomcliente = :nomcliente,
            doccliente = :doccliente,
            tlfcliente = :tlfcliente,
            email = :email,
            direccion = :direccion,
            limitecredito = :limitecredito
            WHERE codcliente = :codcliente AND codsucursal = :tenant_id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nomcliente' => $input['nomcliente'],
        ':doccliente' => $input['doccliente'],
        ':tlfcliente' => $input['tlfcliente'] ?? '',
        ':email' => $input['email'] ?? '',
        ':direccion' => $input['direccion'] ?? '',
        ':limitecredito' => $input['limitecredito'] ?? 0.00,
        ':codcliente' => $input['codcliente'],
        ':tenant_id' => $tenant_id
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Client updated successfully.']);
}

function handleDelete($db, $tenant_id) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Client ID is required.']);
        return;
    }

    // Check for existing debts in ledger
    // Assuming we shouldn't delete clients with history
    // But for now, basic delete from clientes table.

    $check = $db->prepare("SELECT codcliente FROM clientes WHERE codcliente = ? AND codsucursal = ?");
    $check->execute([$id, $tenant_id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Client not found.']);
        return;
    }

    $sql = "DELETE FROM clientes WHERE codcliente = ? AND codsucursal = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id, $tenant_id]);

    echo json_encode(['status' => 'success', 'message' => 'Client deleted successfully.']);
}
?>
