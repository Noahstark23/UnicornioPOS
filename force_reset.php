<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- FORCE RESET DIAGNOSTIC ---\n";

try {
    $db = DB::connect();
    
    // 1. Check BEFORE
    echo "1. BEFORE UPDATE:\n";
    $stmt = $db->prepare("SELECT codigo, usuario, password FROM usuarios WHERE usuario = ?");
    $stmt->execute(['CARLOSPEREZ']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "User: {$r['usuario']} (ID: {$r['codigo']})\n";
        echo "Pass: {$r['password']}\n";
    }

    // 2. FORCE UPDATE
    echo "\n2. EXECUTING UPDATE...\n";
    $targetHash = '7c4a8d09ca3762af61e59520943dc26494f8941b'; // sha1(md5('123456'))
    
    $update = $db->prepare("UPDATE usuarios SET password = ? WHERE usuario = ?");
    $update->execute([$targetHash, 'CARLOSPEREZ']);
    echo "Rows affected: " . $update->rowCount() . "\n";

    // 3. Check AFTER
    echo "\n3. AFTER UPDATE:\n";
    $stmt->execute(['CARLOSPEREZ']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "User: {$r['usuario']} (ID: {$r['codigo']})\n";
        echo "Pass: {$r['password']}\n";
        
        if ($r['password'] === $targetHash) {
            echo "VERDICT: OK (Matches expected hash)\n";
        } else {
            echo "VERDICT: FAIL (Mismatch)\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
