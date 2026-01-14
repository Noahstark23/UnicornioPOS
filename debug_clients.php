<?php
require 'includes/db.php';
$db = DB::connect();

$colsToCheck = ['current_balance', 'limitecredito', 'email', 'tenant_id'];
foreach($colsToCheck as $col) {
    $exists = $db->query("SHOW COLUMNS FROM clientes LIKE '$col'")->fetch();
    echo $col . ": " . ($exists ? "YES" : "NO") . "\n";
}

$stmt = $db->query("SELECT count(*) FROM clientes WHERE nomcliente LIKE '%jose%' AND tenant_id = 1");
echo "Matches for 'jose': " . $stmt->fetchColumn() . "\n";
