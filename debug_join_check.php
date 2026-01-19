<?php
require_once 'includes/db.php';
$db = DB::connect();

$name = 'JOSE PINEDA';

echo "=== DIAGNOSIS FOR: $name ===\n";

// 1. Get Client Data
$stmt = $db->prepare("SELECT idcliente, codcliente, hex(codcliente) as hex_c FROM clientes WHERE nomcliente = ?");
$stmt->execute([$name]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Client not found\n");
}

echo "Client Table: codcliente = [" . $client['codcliente'] . "] (Hex: " . $client['hex_c'] . ")\n";
$cod = $client['codcliente'];

// 2. Check Ventas Data
$stmt2 = $db->prepare("SELECT codcliente, hex(codcliente) as hex_v, totalpago, tipopago FROM ventas WHERE codcliente = ?");
$stmt2->execute([$cod]);
$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "Ventas Table (Exact Match): found " . count($rows) . " rows\n";
foreach($rows as $r) {
    echo "  - Sale: " . $r['totalpago'] . " | Type: " . $r['tipopago'] . " | Hex: " . $r['hex_v'] . "\n";
}

// 3. Check Ventas Data (Trimmed Match)
// Only if exact match failed
if (count($rows) == 0) {
    echo "Trying TRIM match...\n";
    $stmt3 = $db->prepare("SELECT codcliente, hex(codcliente) as hex_v, totalpago FROM ventas WHERE TRIM(codcliente) = TRIM(?)");
    $stmt3->execute([$cod]);
    $rows3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    echo "Ventas Table (Trim Match): found " . count($rows3) . " rows\n";
    foreach($rows3 as $r) {
        echo "  - Sale: " . $r['totalpago'] . " | Hex: " . $r['hex_v'] . "\n";
    }
}
?>
