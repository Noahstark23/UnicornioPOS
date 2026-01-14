<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- UPDATING SUCURSAL FOR CARLOSPEREZ ---\n";

try {
    $db = DB::connect();

    // 1. Check if Sucursal 1 exists
    $stmt = $db->query("SELECT COUNT(*) FROM sucursales WHERE codsucursal = 1");
    if ($stmt->fetchColumn() > 0) {
        echo "✅ Sucursal 1 exists.\n";
        
        // 2. Update User
        $update = $db->prepare("UPDATE usuarios SET codsucursal = 1 WHERE usuario = 'CARLOSPEREZ'");
        $update->execute();
        
        echo "✅ Update executed. Rows affected: " . $update->rowCount() . "\n";
        
        // 3. Verify
        $stmt2 = $db->query("SELECT codsucursal FROM usuarios WHERE usuario = 'CARLOSPEREZ'");
        echo "   New codsucursal: " . $stmt2->fetchColumn() . "\n";

    } else {
        echo "❌ Sucursal 1 does NOT exist. Aborting update.\n";
        
        // List available sucursales
        echo "Available Sucursales:\n";
        $stmt = $db->query("SELECT codsucursal, razonsocial FROM sucursales");
        while ($row = $stmt->fetch()) {
            echo " - ID: {$row['codsucursal']} ({$row['razonsocial']})\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
