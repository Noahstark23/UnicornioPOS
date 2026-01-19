<?php
require_once 'includes/db.php';

try {
    $db = DB::connect();

    // 1. Create `clientes` table if not exists (inferred schema)
    echo "Checking 'clientes' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS clientes (
        idcliente INT AUTO_INCREMENT PRIMARY KEY,
        codcliente VARCHAR(50),
        documcliente VARCHAR(50),
        dnicliente VARCHAR(50),
        nomcliente VARCHAR(255),
        tlfcliente VARCHAR(50),
        id_provincia INT DEFAULT 0,
        id_departamento INT DEFAULT 0,
        direccliente TEXT,
        emailcliente VARCHAR(255),
        tipocliente VARCHAR(50),
        limitecredito DECIMAL(10,2) DEFAULT 0.00,
        fechaingreso DATE,
        tenant_id INT DEFAULT 0,
        current_balance DECIMAL(10,2) DEFAULT 0.00,
        INDEX idx_clientes_tenant (tenant_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $db->exec($sql);
    echo "Table 'clientes' checked/created.\n";

    // 2. Modify `clientes` table if it existed but without new columns
    // Add tenant_id
    $stmt = $db->query("SHOW COLUMNS FROM clientes LIKE 'tenant_id'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE clientes ADD COLUMN tenant_id INT DEFAULT 0";
        $db->exec($sql);
        echo "Column 'tenant_id' added to 'clientes'.\n";

        $db->exec("CREATE INDEX idx_clientes_tenant ON clientes(tenant_id)");
        echo "Index 'idx_clientes_tenant' created.\n";
    }

    // Add current_balance
    $stmt = $db->query("SHOW COLUMNS FROM clientes LIKE 'current_balance'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE clientes ADD COLUMN current_balance DECIMAL(10,2) DEFAULT 0.00";
        $db->exec($sql);
        echo "Column 'current_balance' added to 'clientes'.\n";
    }

    // 3. Create `client_ledger` table
    echo "Checking 'client_ledger' table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS client_ledger (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        tenant_id INT NOT NULL,
        type ENUM('DEBT', 'PAYMENT') NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        reference_sale_id VARCHAR(50) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ledger_client (client_id),
        INDEX idx_ledger_tenant (tenant_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table 'client_ledger' created or already exists.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
