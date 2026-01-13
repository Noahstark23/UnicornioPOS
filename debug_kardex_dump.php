<?php
require_once 'includes/db.php';
$db = DB::connect();
$stmt = $db->query("DESCRIBE kardex");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
