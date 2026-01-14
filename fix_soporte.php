<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- UPDATING SOPORTE USER ---\n";

try {
    $db = DB::connect();
    
    // Update SOPORTE to use codsucursal = 1
    $sql = "UPDATE usuarios SET codsucursal = 1 WHERE usuario = 'SOPORTE'";
    $db->exec($sql);
    
    echo "✅ SOPORTE Updated to codsucursal = 1\n";
    
    // Verify
    $stmt = $db->query("SELECT usuario, codsucursal FROM usuarios WHERE usuario = 'SOPORTE'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Verify: " . print_r($row, true) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
