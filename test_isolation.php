<?php
// ISOLATION TEST
// Mimics class.php environment exactly
header('Content-Type: text/plain');

echo "--- ISOLATION TEST START ---\n";

// 1. Include the ACTUAL connection class used by the app
require_once 'class/classconexion.php';
echo "1. Included class/classconexion.php\n";

try {
    // 2. Instantiate Db class (as Login extends Db)
    class TestLogin extends Db {
        public function test() {
            $this->SetNames(); // mimic SetNames() call
            return $this->dbh;
        }
    }

    $app = new TestLogin();
    $conn = $app->test();
    echo "2. Connection object obtained via inheritance.\n";

    // 3. Run the EXACT failure query (simplified)
    $user = 'SOPORTE';
    echo "3. Querying for user: [$user]\n";

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user]);
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "✅ SUCCESS: User match found!\n";
        echo "   ID: " . $row['codigo'] . "\n";
        echo "   User: " . $row['usuario'] . "\n";
        echo "   Pass: " . $row['password'] . "\n";
        echo "   Status: " . $row['status'] . "\n";
        echo "   Sucursal: " . $row['codsucursal'] . "\n";
    } else {
        echo "❌ FAILURE: User NOT found with this connection.\n";
        
        // Debug: List all users seen by this connection
        echo "   List of available users:\n";
        $all = $conn->query("SELECT usuario FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
        print_r($all);
    }

} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}
?>
