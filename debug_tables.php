<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);

// Check columns of cuentasporcobrar if it exists
if(in_array('cuentasporcobrar', $tables)) {
    echo "\nCOLUMNS OF cuentasporcobrar:\n";
    $cols = $db->query("SHOW COLUMNS FROM cuentasporcobrar")->fetchAll(PDO::FETCH_COLUMN);
    print_r($cols);
}
?>
