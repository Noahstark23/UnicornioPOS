<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("DESCRIBE kardex");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode(", ", $cols);
?>
