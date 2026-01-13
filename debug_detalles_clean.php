<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

try {
    $db = DB::connect();
    $cod = '0001-081041537'; 

    $stmt = $db->prepare("SELECT * FROM detalleventas WHERE codventa = ?");
    $stmt->execute([$cod]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($detalles, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
