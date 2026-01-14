<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

echo "--- MASTER KEY RESET SEQUENCE ---\n";
$targetHash = '7c4a8d09ca3762af61e59520943dc26494f8941b';

try {
    $db = DB::connect();

    // 1. EXECUTE UPDATE
    echo "1. Executing SQL Update...\n";
    $sql = "UPDATE usuarios 
            SET password = '$targetHash' 
            WHERE usuario = 'CARLOSPEREZ'";
    $db->exec($sql);
    echo "   Update command sent.\n";

    // 2. VERIFY
    echo "2. Verifying Database State...\n";
    $stmt = $db->query("SELECT password FROM usuarios WHERE usuario = 'CARLOSPEREZ'");
    $currentHash = $stmt->fetchColumn();

    echo "   Current Hash in DB: [$currentHash]\n";
    
    if ($currentHash === $targetHash) {
        echo "   ✅ VERIFICATION SUCCESS: Hash matches expected value.\n";
    } else {
        echo "   ❌ VERIFICATION FAILED: Hash mismatch.\n";
    }

} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
}
?>
