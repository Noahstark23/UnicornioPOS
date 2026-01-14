<?php
require_once 'includes/db.php';

header('Content-Type: text/plain');

$userInput = 'CARLOSPEREZ';
$passInput = '123456';

echo "--- DEBUG LOGIN ANALYSIS ---\n";
$generatedHash = sha1(md5($passInput));
echo "1. HASH VERIFICATION\n";
echo "Input: '$passInput'\n";
echo "Hash : '$generatedHash'\n\n";

try {
    $db = DB::connect();

    echo "2. DATABASE INSPECTION\n";
    $sql = "SELECT codigo, usuario, password, status FROM usuarios WHERE usuario = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute(['CARLOSPEREZ']); 
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        foreach ($users as $u) {
            echo "Found User: '" . $u['usuario'] . "' (ID: " . $u['codigo'] . ")\n";
            echo "Stored Hash: '" . $u['password'] . "'\n";
            echo "Status: " . $u['status'] . "\n";
            
            if ($u['password'] === $generatedHash) {
                echo "RESULT: PASSWORD MATCHES OK\n";
            } else {
                echo "RESULT: PASSWORD MISMATCH\n";
                echo "Expected: $generatedHash\n";
                echo "Actual  : " . $u['password'] . "\n";
            }

            if ($u['status'] == 1) {
                echo "RESULT: STATUS ACTIVE OK\n";
            } else {
                echo "RESULT: STATUS INACTIVE (Current: " . $u['status'] . ")\n";
            }
        }
    } else {
        echo "RESULT: USER 'CARLOSPEREZ' NOT FOUND IN DB (Exact Match)\n";
        
        // Try LIKE
        $stmt = $db->query("SELECT usuario FROM usuarios WHERE usuario LIKE '%CARLOS%'");
        echo "Similar users found: " . print_r($stmt->fetchAll(PDO::FETCH_COLUMN), true) . "\n";
    }

    echo "\n3. LOGIN QUERY SIMULATION\n";
    $sqlLogin = "SELECT * FROM usuarios WHERE usuario = ? AND password = ? AND status = 1";
    $stmtLogin = $db->prepare($sqlLogin);
    $stmtLogin->execute([$userInput, $generatedHash]);
    
    if ($stmtLogin->rowCount() > 0) {
        echo "FINAL JUDGMENT: QUERY SUCCESS (Login should work)\n";
    } else {
        echo "FINAL JUDGMENT: QUERY FAILED (Login rejected)\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
