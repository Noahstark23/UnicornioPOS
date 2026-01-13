<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("SHOW COLUMNS FROM creditosxclientes");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
?>
