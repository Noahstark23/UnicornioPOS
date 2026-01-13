<?php
header('Content-Type: application/json');
session_start();

// Disable error display for production API
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../includes/db.php';

// Strict session check
if (!isset($_SESSION['codsucursal'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$tenant_id = $_SESSION['codsucursal'];
$db = DB::connect();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Fetch clients filtered by tenant_id
        $stmt = $db->prepare("SELECT * FROM clientes WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($clients);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['action'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing action']);
        exit;
    }

    if ($input['action'] === 'add_debt') {
        $codcliente = $input['codcliente'] ?? null;
        $amount = $input['amount'] ?? 0;

        if (!$codcliente || $amount <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        try {
            // Validate client belongs to tenant and get current status
            $check = $db->prepare("SELECT current_balance, limitecredito FROM clientes WHERE codcliente = ? AND tenant_id = ?");
            $check->execute([$codcliente, $tenant_id]);
            $client = $check->fetch(PDO::FETCH_ASSOC);

            if (!$client) {
                http_response_code(404);
                echo json_encode(['error' => 'Client not found']);
                exit;
            }

            // Financial Logic: Check credit limit
            if ($client['current_balance'] + $amount > $client['limitecredito']) {
                 http_response_code(400);
                 echo json_encode(['error' => 'Credit limit exceeded']);
                 exit;
            }

            // Update balance
            $update = $db->prepare("UPDATE clientes SET current_balance = current_balance + ? WHERE codcliente = ? AND tenant_id = ?");
            $update->execute([$amount, $codcliente, $tenant_id]);

            // Fetch updated balance for response
            $check->execute([$codcliente, $tenant_id]);
            $updatedClient = $check->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => 'Debt added successfully',
                'new_balance' => $updatedClient['current_balance']
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
