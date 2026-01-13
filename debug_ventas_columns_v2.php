<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("SHOW COLUMNS FROM ventas");
foreach($stmt->fetchAll(PDO::FETCH_COLUMN) as $col) {
    echo $col . "\n";
}
?>
