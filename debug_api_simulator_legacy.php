<?php
// debug_api_simulator_legacy.php
// Simulates a Legacy User (Set 'codigo', unset 'id_usuario')

session_start();
$_SESSION['codigo'] = 'LEGACY_ADMIN'; 
unset($_SESSION['id_usuario']);

// Mock Request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['q'] = 'pineda';

// Capture
ob_start();
// Adjust path if run from root
require 'api/clients_v2.php';
$output = ob_get_clean();

echo "=== LEGACY SESSION TEST (BALANCE CHECK) ===\n";
echo "Raw Output Length: " . strlen($output) . "\n";
$data = json_decode($output, true);

if (is_array($data)) {
    echo "SUCCESS. Found " . count($data) . " rows.\n";
    foreach($data as $row) {
        echo "Client ID: " . $row['id'] . " | Name: " . $row['nombre'] . " | Balance: " . $row['current_balance'] . "\n";
    }
} else {
    echo "FAILURE: Output was: $output\n";
}

// DEBUG: Direct SQL check
echo "\n--- DIRECT DB CHECKS ---\n";
require_once 'includes/db.php';
$db = DB::connect();
// Find Jose Pineda ID
$stmt = $db->query("SELECT idcliente, nomcliente, codcliente FROM clientes WHERE nomcliente LIKE '%pineda%'");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($clients as $c) {
    echo "Checking Client: " . $c['nomcliente'] . " (ID: ".$c['idcliente'].", COD: ".$c['codcliente'].")\n";
    
    // Check Sales
    $stmt2 = $db->query("SELECT COUNT(*) as cnt, SUM(totalpago) as total FROM ventas WHERE codcliente = '".$c['codcliente']."' AND tipopago='CREDITO'");
    $sales = $stmt2->fetch();
    echo "  Credit Sales: " . $sales['cnt'] . " -> Total: " . $sales['total'] . "\n";

    // Check Payments (Abonos)
    $qpay = "SELECT COUNT(*) as cnt, SUM(a.montoabono) as total 
             FROM abonoscreditosventas a
             JOIN ventas v ON a.codventa = v.codventa
             WHERE v.codcliente = '".$c['codcliente']."' AND v.tipopago='CREDITO'";
    $stmt3 = $db->query($qpay);
    $pay = $stmt3->fetch();
    echo "  Payments: " . $pay['cnt'] . " -> Total: " . $pay['total'] . "\n";
}
?>
