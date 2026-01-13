<?php
require_once 'includes/db.php';
$db = DB::connect();
function show_cols($table, $db) {
    echo "TABLE: $table\n";
    $stmt = $db->query("SHOW COLUMNS FROM $table");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    echo "\n";
}
show_cols('compras', $db);
show_cols('detallecompras', $db);
show_cols('kardex', $db);
?>
