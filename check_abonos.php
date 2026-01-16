<?php
require_once 'includes/db.php';
try {
    $db = DB::connect();
    $res = $db->query("SELECT 1 FROM abonoscreditosventas LIMIT 1");
    echo "Tabla abonoscreditosventas existe.";
} catch (Exception $e) {
    echo "Tabla NO existe: " . $e->getMessage();
}
?>
