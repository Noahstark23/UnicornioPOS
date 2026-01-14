<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- CREATING USER 'SOPORTE' ---\n";

try {
    $db = DB::connect();

    // 1. DELETE IF EXISTS (Clean Slate)
    $db->exec("DELETE FROM usuarios WHERE usuario = 'SOPORTE'");

    // 2. INSERT SOPORTE
    $sql = "INSERT INTO usuarios (usuario, password, nivel, status, email, codsucursal) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'SOPORTE',
        '7c4a8d09ca3762af61e59520943dc26494f8941b', // 123456
        'ADMINISTRADOR(A) GENERAL',
        1,
        'soporte@unicornio.com',
        0
    ]);
    
    echo "✅ User 'SOPORTE' created successfully.\n";
    echo "   Pass: 123456 (Hash: 7c4a...)\n";
    echo "   Nivel: ADMINISTRADOR(A) GENERAL\n";
    echo "   Sucursal: 0\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
