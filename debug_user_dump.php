<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- USER DUMP ANALYSIS ---\n";

try {
    $db = DB::connect();
    
    $users = ['SOPORTE', 'CARLOSPEREZ'];
    
    foreach ($users as $u) {
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$u]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo "USER: {$row['usuario']} (ID: {$row['codigo']})\n";
            echo " - Password: {$row['password']}\n";
            echo " - Status: {$row['status']}\n";
            echo " - CodSucursal: {$row['codsucursal']}\n";
            echo " - Nivel: {$row['nivel']}\n";
            echo " - Email: {$row['email']}\n";
            
            // Check Sucursal Existence
            $stmtSuc = $db->prepare("SELECT count(*) FROM sucursales WHERE codsucursal = ?");
            $stmtSuc->execute([$row['codsucursal']]);
            $exists = $stmtSuc->fetchColumn();
            echo " - Sucursal Valid? " . ($exists ? "YES" : "NO (FK Issue?)") . "\n";
            
            echo "\n";
        } else {
            echo "USER: $u - NOT FOUND\n\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
