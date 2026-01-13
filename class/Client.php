<?php
require_once __DIR__ . '/../includes/db.php';

class Client {
    private $db;

    public function __construct() {
        $this->db = DB::connect();
    }

    /**
     * List clients filtered by tenant_id
     */
    public function listClients($tenant_id) {
        $sql = "SELECT * FROM clientes WHERE tenant_id = ? ORDER BY nomcliente ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tenant_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get a single client by ID and tenant_id
     */
    public function getClient($id, $tenant_id) {
        $sql = "SELECT * FROM clientes WHERE idcliente = ? AND tenant_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $tenant_id]);
        return $stmt->fetch();
    }

    /**
     * Create a new client
     */
    public function createClient($data) {
        // Prepare data with defaults
        $data = array_merge([
            'codcliente' => '',
            'documcliente' => '',
            'dnicliente' => '',
            'nomcliente' => '',
            'tlfcliente' => '',
            'id_provincia' => 0,
            'id_departamento' => 0,
            'direccliente' => '',
            'emailcliente' => '',
            'tipocliente' => '',
            'limitecredito' => 0.00,
            'tenant_id' => 0,
            'fechaingreso' => date('Y-m-d')
        ], $data);

        // Force current_balance to 0.00 for new clients to ensure integrity with ledger
        $data['current_balance'] = 0.00;

        $sql = "INSERT INTO clientes (
            codcliente, documcliente, dnicliente, nomcliente, tlfcliente,
            id_provincia, id_departamento, direccliente, emailcliente, tipocliente,
            limitecredito, fechaingreso, tenant_id, current_balance
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['codcliente'], $data['documcliente'], $data['dnicliente'], $data['nomcliente'], $data['tlfcliente'],
            $data['id_provincia'], $data['id_departamento'], $data['direccliente'], $data['emailcliente'], $data['tipocliente'],
            $data['limitecredito'], $data['fechaingreso'], $data['tenant_id'], $data['current_balance']
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Add debt to a client
     * Checks credit limit and updates balance and ledger
     */
    public function addDebt($client_id, $amount, $tenant_id, $reference_sale_id = null) {
        $client = $this->getClient($client_id, $tenant_id);
        if (!$client) {
            throw new Exception("Client not found.");
        }

        $new_balance = $client['current_balance'] + $amount;

        // Check credit limit if set (assuming limit > 0 means limit exists)
        if ($client['limitecredito'] > 0 && $new_balance > $client['limitecredito']) {
            throw new Exception("Credit Limit Exceeded. Limit: " . $client['limitecredito'] . ", Current Balance: " . $client['current_balance'] . ", Attempted: " . $amount);
        }

        try {
            $this->db->beginTransaction();

            // Update client balance
            $sql = "UPDATE clientes SET current_balance = ? WHERE idcliente = ? AND tenant_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$new_balance, $client_id, $tenant_id]);

            // Add ledger entry
            $sql = "INSERT INTO client_ledger (client_id, tenant_id, type, amount, reference_sale_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$client_id, $tenant_id, 'DEBT', $amount, $reference_sale_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get ledger history for a client
     */
    public function getLedger($client_id, $tenant_id) {
         $sql = "SELECT * FROM client_ledger WHERE client_id = ? AND tenant_id = ? ORDER BY created_at DESC";
         $stmt = $this->db->prepare($sql);
         $stmt->execute([$client_id, $tenant_id]);
         return $stmt->fetchAll();
    }
}
?>
