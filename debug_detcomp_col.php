<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("DESCRIBE detallecompras");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode(", ", $cols);
?>
