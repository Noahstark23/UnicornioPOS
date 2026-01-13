<?php
require_once 'includes/db.php';

try {
    $db = DB::connect();
    // Check if column exists first
    $stmt = $db->query("SHOW COLUMNS FROM ventas LIKE 'tenant_id'");
    if ($stmt->rowCount() == 0) {
        // Add column, default 0 for existing rows (legacy/bad data)
        $sql = "ALTER TABLE ventas ADD COLUMN tenant_id INT DEFAULT 0";
        $db->exec($sql);
        echo "Column 'tenant_id' added successfully (Default 0).";
        
        // Add index for performance
        $db->exec("CREATE INDEX idx_tenant ON ventas(tenant_id)");
    } else {
        echo "Column 'tenant_id' already exists.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
