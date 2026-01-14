<?php
require 'includes/db.php';

try {
    $db = DB::connect();
    echo "Applying Migration: Adding email column...\n";
    
    // Check if column exists first to avoid error
    $cols = $db->query("SHOW COLUMNS FROM clientes LIKE 'email'")->fetchAll();
    
    if (count($cols) > 0) {
        echo "[SKIP] Column 'email' already exists.\n";
    } else {
        $db->exec("ALTER TABLE clientes ADD COLUMN email VARCHAR(100) NULL");
        echo "[create] Column 'email' added successfully.\n";
    }
    
    // Verify API output logic
    echo "\n--- API Simulation (Search 'jose') ---\n";
    $stmt = $db->query("SELECT idcliente, nomcliente, tlfcliente, email, limitecredito, current_balance FROM clientes WHERE nomcliente LIKE '%jose%' AND tenant_id = 1");
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($res) . " records.\n";
    if(count($res) > 0) {
        print_r($res[0]);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
