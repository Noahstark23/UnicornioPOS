<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

try {
    $db = DB::connect();
    
    echo "--- COLUMNS IN KARDEX ---\n";
    $stmt = $db->query("SHOW COLUMNS FROM kardex");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
