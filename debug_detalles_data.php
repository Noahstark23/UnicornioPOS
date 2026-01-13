<?php
require_once 'includes/db.php';
ini_set('display_errors', 1);

try {
    $db = DB::connect();
    $cod = '0001-081041537'; // From screenshot
    
    echo "Checking sale: $cod\n";
    
    // Check v.codventa
    $stmt = $db->prepare("SELECT * FROM ventas WHERE codventa = ?");
    $stmt->execute([$cod]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($venta);
    
    // Check detalles
    echo "\nDetalles:\n";
    $stmt = $db->prepare("SELECT * FROM detalleventas WHERE codventa = ?");
    $stmt->execute([$cod]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($detalles);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
