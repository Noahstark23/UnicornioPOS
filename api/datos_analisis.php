<?php
require_once '../includes/db.php';
// Headers para asegurar que SIEMPRE se interprete como JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Evitar que warnings de PHP rompan el JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = [];

try {
    $db = DB::connect();
    $tenant_id = 1;
    $accion = $_GET['accion'] ?? '';

    // Manejo seguro de fechas
    $inicio = $_GET['inicio'] ?? date('Y-m-01');
    $fin    = $_GET['fin'] ?? date('Y-m-d');
    $inicioSql = $inicio . ' 00:00:00';
    $finSql    = $fin . ' 23:59:59';

    if ($accion === 'kpis_generales') {
        // Inventario
        $sqlInv = "SELECT SUM(existencia * preciocompra) FROM productos WHERE tenant_id = ? AND existencia > 0";
        $stmt = $db->prepare($sqlInv); $stmt->execute([$tenant_id]);
        $inv = $stmt->fetchColumn();

        // Ventas
        $sqlVentas = "SELECT SUM(totalpago) FROM ventas WHERE tenant_id = ? AND statusventa='EMITIDA' AND fechaventa BETWEEN ? AND ?";
        $stmt = $db->prepare($sqlVentas); $stmt->execute([$tenant_id, $inicioSql, $finSql]);
        $ventas = $stmt->fetchColumn();

        // Ticket Promedio
        $sqlAvg = "SELECT AVG(totalpago) FROM ventas WHERE tenant_id = ? AND statusventa='EMITIDA' AND fechaventa BETWEEN ? AND ?";
        $stmt = $db->prepare($sqlAvg); $stmt->execute([$tenant_id, $inicioSql, $finSql]);
        $avg = $stmt->fetchColumn();

        $response = [
            'inventario_costo' => number_format((float)($inv ?: 0), 2),
            'ventas_periodo'   => number_format((float)($ventas ?: 0), 2),
            'ticket_promedio'  => number_format((float)($avg ?: 0), 2)
        ];
    }
    elseif ($accion === 'ventas_tendencia') {
        $sql = "SELECT DATE_FORMAT(fechaventa, '%d/%m') as fecha, SUM(totalpago) as total 
                FROM ventas 
                WHERE tenant_id = ? AND statusventa='EMITIDA' AND fechaventa BETWEEN ? AND ? 
                GROUP BY DATE(fechaventa) ORDER BY fechaventa ASC";
        $stmt = $db->prepare($sql); $stmt->execute([$tenant_id, $inicioSql, $finSql]);
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    elseif ($accion === 'top_rentables') {
        $sql = "SELECT p.producto, SUM((d.precioventa - p.preciocompra) * d.cantventa) as ganancia
                FROM detalleventas d
                INNER JOIN productos p ON d.codproducto = p.codproducto AND d.tenant_id = p.tenant_id
                INNER JOIN ventas v ON d.codventa = v.codventa
                WHERE d.tenant_id = ? AND v.statusventa = 'EMITIDA' AND v.fechaventa BETWEEN ? AND ?
                GROUP BY d.codproducto ORDER BY ganancia DESC LIMIT 5";
        $stmt = $db->prepare($sql); $stmt->execute([$tenant_id, $inicioSql, $finSql]);
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    elseif ($accion === 'metodos_pago') {
        $sql = "SELECT formapago, COUNT(*) as cantidad FROM ventas 
                WHERE tenant_id = ? AND statusventa='EMITIDA' AND fechaventa BETWEEN ? AND ?
                GROUP BY formapago";
        $stmt = $db->prepare($sql); $stmt->execute([$tenant_id, $inicioSql, $finSql]);
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($response);

} catch (Exception $e) {
    // Retornar error controlado en JSON
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
