<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

try {
    $db = DB::connect();
    
    $desde = $_GET['desde'] ?? date('Y-m-d');
    $hasta = $_GET['hasta'] ?? date('Y-m-d');
    
    // Validar fechas
    $desde = date('Y-m-d', strtotime($desde));
    $hasta = date('Y-m-d', strtotime($hasta));

    $sql = "SELECT 
                v.idventa, v.codventa, v.fechaventa, v.totalpago, v.formapago, v.tipopago, v.statusventa,
                d.cantventa, d.precioventa,
                p.producto, p.codproducto, p.precioxpublico
            FROM ventas v
            LEFT JOIN detalleventas d ON v.codventa = d.codventa
            LEFT JOIN productos p ON d.codproducto = p.codproducto
            WHERE v.tenant_id = 1
              AND DATE(v.fechaventa) >= ? 
              AND DATE(v.fechaventa) <= ?
            ORDER BY v.idventa DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$desde, $hasta]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ventas = [];
    foreach ($rows as $r) {
        $id = $r['codventa'];
        if (!isset($ventas[$id])) {
            $ventas[$id] = [
                'codventa' => $r['codventa'],
                'fecha' => $r['fechaventa'],
                'total' => floatval($r['totalpago']),
                'forma' => $r['formapago'],
                'tipo' => $r['tipopago'],  // CONTADO o CREDITO
                'estado' => $r['statusventa'],
                'items' => []
            ];
        }
        
        if ($r['producto']) {
            $precio = floatval($r['precioventa']);
            // Fallback para datos malos de hoy
            if ($precio == 0) $precio = floatval($r['precioxpublico']);
            
            $ventas[$id]['items'][] = [
                'producto' => $r['producto'],
                'cant' => floatval($r['cantventa']),
                'precio' => $precio,
                'subtotal' => floatval($r['cantventa']) * $precio
            ];
        }
    }

    echo json_encode(array_values($ventas));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
