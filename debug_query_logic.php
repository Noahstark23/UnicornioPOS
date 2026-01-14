<?php
require_once 'includes/db.php';
$db = DB::connect();

$tenant_id = 1;
$q = 'pineda';

echo "=== TESTING API QUERY LOGIC ===\n";

$sql = "SELECT 
            c.idcliente as id, 
            c.nomcliente as nombre, 
            c.codcliente,
            COALESCE(debt.total_debt, 0) as raw_debt,
            COALESCE(pay.total_paid, 0) as raw_pay,
            (COALESCE(debt.total_debt, 0) - COALESCE(pay.total_paid, 0)) as current_balance
        FROM clientes c
        
        LEFT JOIN (
            SELECT codcliente, SUM(totalpago) as total_debt
            FROM ventas
            WHERE tipopago = 'CREDITO'
            GROUP BY codcliente
        ) debt ON c.codcliente = debt.codcliente

        LEFT JOIN (
            SELECT v.codcliente, SUM(a.montoabono) as total_paid
            FROM abonoscreditosventas a
            JOIN ventas v ON a.codventa = v.codventa
            WHERE v.tipopago = 'CREDITO'
            GROUP BY v.codcliente
        ) pay ON c.codcliente = pay.codcliente

        WHERE c.codcliente = 'C1205'";

$params = [];

$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($results) . " rows.\n";
foreach($results as $r) {
    print_r($r);
}
?>
