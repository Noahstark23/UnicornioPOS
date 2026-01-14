<?php
// api/clients_v2.php
// Protocolo: Bridge (Usa common.php)
require_once 'common.php';

// Validar Session Strict (Redundante con common, pero explicito)
if (empty($_SESSION['id_usuario']) && empty($_SESSION['codigo'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = DB::connect();
$method = $_SERVER['REQUEST_METHOD'];

// TENANT ID Fixed for now
$tenant_id = 1; 

try {
    if ($method === 'GET') {
        // LISTAR CLIENTES
        $q = $_GET['q'] ?? '';
        $sql = "SELECT idcliente as id, nomcliente as nombre, tlfcliente as telefono, 
                       email, limitecredito, current_balance 
                FROM clientes 
                WHERE tenant_id = :tid";
        
        $params = [':tid' => $tenant_id];

        if (!empty($q)) {
            $sql .= " AND (nomcliente LIKE :q OR tlfcliente LIKE :q)";
            $params[':q'] = "%$q%";
        }

        $sql .= " ORDER BY nomcliente ASC LIMIT 50";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($method === 'POST') {
        // AGREGAR DEUDA (Simulacion Cargo)
        $input = json_decode(file_get_contents('php://input'), true);
        
        $idCliente = $input['id'] ?? null;
        $monto = $input['amount'] ?? 0;

        if (!$idCliente || $monto <= 0) throw new Exception("Datos inválidos");

        // 1. Validar Cliente
        $stmtChk = $db->prepare("SELECT idcliente, current_balance FROM clientes WHERE idcliente = ? AND tenant_id = ?");
        $stmtChk->execute([$idCliente, $tenant_id]);
        $cliente = $stmtChk->fetch();

        if (!$cliente) throw new Exception("Cliente no encontrado");

        // 2. Actualizar Saldo (Directo a tabla clientes por ahora, idealmente deberia ser transaccion en ledger)
        $nuevoSaldo = $cliente['current_balance'] + $monto;
        $upd = $db->prepare("UPDATE clientes SET current_balance = ? WHERE idcliente = ?");
        $upd->execute([$nuevoSaldo, $idCliente]);

        echo json_encode(['status' => 'ok', 'new_balance' => $nuevoSaldo]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
