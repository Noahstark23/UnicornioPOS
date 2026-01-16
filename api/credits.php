<?php
// api/credits.php
require_once 'common.php';

$db = DB::connect();

try {
    // Obtener créditos pendientes
    $sql = "SELECT 
                v.codventa,
                v.fechaventa,
                v.totalpago,
                c.dnicliente,
                c.nomcliente,
                c.codcliente,
                COALESCE(SUM(av.montoabono), 0) as totalabono,
                (v.totalpago - COALESCE(SUM(av.montoabono), 0)) as totaldebe
            FROM ventas v
            INNER JOIN clientes c ON v.codcliente = c.codcliente
            LEFT JOIN abonoscreditosventas av ON v.codventa = av.codventa
            WHERE v.statusventa = 'CREDITO' OR v.tipopago = 'CREDITO'
            GROUP BY v.codventa, v.fechaventa, v.totalpago, c.dnicliente, c.nomcliente, c.codcliente
            HAVING totaldebe > 0.01
            ORDER BY v.fechaventa DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result ?: []);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
