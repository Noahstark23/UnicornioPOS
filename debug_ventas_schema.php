<?php
require_once 'includes/db.php';
ini_set('display_errors', 1);

function show_columns($table) {
    try {
        $db = DB::connect();
        $stmt = $db->query("SHOW COLUMNS FROM $table");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\nTABLE: $table\n";
        echo "----------------\n";
        foreach ($cols as $col) {
            echo trim($col['Field']) . "\n";
        }
        echo "----------------\n";
    } catch (Exception $e) {
        echo "Error showing $table: " . $e->getMessage() . "\n";
    }
}

show_columns('ventas');
?>
