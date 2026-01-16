<?php
require_once '../includes/db.php';
require_once '../class/funciones_basicas.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = DB::connect();
    
    // Obtener parámetros de filtro
    $desde = isset($_GET['desde']) ? $_GET['desde'] : date('Y-m-01'); // Primer día del mes
    $hasta = isset($_GET['hasta']) ? $_GET['hasta'] : date('Y-m-d');   // Hoy
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'TODOS';            // CONTADO, CREDITO, TODOS
    $forma = isset($_GET['forma']) ? $_GET['forma'] : 'TODOS';         // EFECTIVO, TARJETA, TRANSFERENCIA, TODOS
    $estado = isset($_GET['estado']) ? $_GET['estado'] : 'TODOS';     // COMPLETADO, ANULADO, TODOS
    $search = isset($_GET['search']) ? $_GET['search'] : '';           // Búsqueda general
    
    // Validar y formatear fechas
    $desde = date('Y-m-d', strtotime($desde));
    $hasta = date('Y-m-d', strtotime($hasta));
    
    // ==================== ESTADÍSTICAS ====================
    
    // Ventas de hoy
    $sqlHoy = "SELECT COUNT(*) as total, COALESCE(SUM(totalpago), 0) as monto 
               FROM ventas 
               WHERE DATE(fechaventa) = CURDATE() 
               AND statusventa != 'ANULADO'";
    $stmtHoy = $pdo->query($sqlHoy);
    $hoy = $stmtHoy->fetch(PDO::FETCH_ASSOC);
    
    // Total del mes actual
    $sqlMes = "SELECT COUNT(*) as total, COALESCE(SUM(totalpago), 0) as monto 
               FROM ventas 
               WHERE YEAR(fechaventa) = YEAR(CURDATE()) 
               AND MONTH(fechaventa) = MONTH(CURDATE())
               AND statusventa != 'ANULADO'";
    $stmtMes = $pdo->query($sqlMes);
    $mes = $stmtMes->fetch(PDO::FETCH_ASSOC);
    
    // Promedio de ticket
    $promedio = $mes['total'] > 0 ? $mes['monto'] / $mes['total'] : 0;
    
    // Distribución por forma de pago (mes actual)
    $sqlFormas = "SELECT formapago, COALESCE(SUM(totalpago), 0) as monto 
                  FROM ventas 
                  WHERE YEAR(fechaventa) = YEAR(CURDATE()) 
                  AND MONTH(fechaventa) = MONTH(CURDATE())
                  AND statusventa != 'ANULADO'
                  GROUP BY formapago";
    $stmtFormas = $pdo->query($sqlFormas);
    $formasPago = [];
    while ($row = $stmtFormas->fetch(PDO::FETCH_ASSOC)) {
        $formasPago[$row['formapago']] = floatval($row['monto']);
    }
    
    $stats = [
        'ventas_hoy' => intval($hoy['total']),
        'monto_hoy' => floatval($hoy['monto']),
        'ventas_mes' => intval($mes['total']),
        'total_mes' => floatval($mes['monto']),
        'promedio_ticket' => floatval($promedio),
        'formas_pago' => $formasPago
    ];
    
    // ==================== CONSULTA PRINCIPAL ====================
    
    // Construir SQL con filtros
    $sql = "SELECT 
                v.codventa,
                v.fechaventa,
                v.totalpago,
                v.formapago,
                v.tipopago,
                v.statusventa,
                c.nomcliente,
                c.dnicliente,
                c.codcliente,
                COUNT(DISTINCT d.codproducto) as total_items
            FROM ventas v
            LEFT JOIN clientes c ON v.codcliente = c.codcliente
            LEFT JOIN detalleventas d ON v.codventa = d.codventa
            WHERE DATE(v.fechaventa) >= :desde 
            AND DATE(v.fechaventa) <= :hasta";
    
    $params = [
        ':desde' => $desde,
        ':hasta' => $hasta
    ];
    
    // Filtro por tipo de pago
    if ($tipo != 'TODOS') {
        $sql .= " AND v.tipopago = :tipo";
        $params[':tipo'] = $tipo;
    }
    
    // Filtro por forma de pago
    if ($forma != 'TODOS') {
        $sql .= " AND v.formapago = :forma";
        $params[':forma'] = $forma;
    }
    
    // Filtro por estado
    if ($estado != 'TODOS') {
        $sql .= " AND v.statusventa = :estado";
        $params[':estado'] = $estado;
    }
    
    // Búsqueda general
    if (!empty($search)) {
        $sql .= " AND (v.codventa LIKE :search 
                  OR c.nomcliente LIKE :search 
                  OR c.dnicliente LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " GROUP BY v.codventa, v.fechaventa, v.totalpago, v.formapago, v.tipopago, v.statusventa, c.nomcliente, c.dnicliente, c.codcliente
              ORDER BY v.fechaventa DESC, v.codventa DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear resultados
    $ventasFormateadas = [];
    foreach ($ventas as $venta) {
        $ventasFormateadas[] = [
            'codventa' => $venta['codventa'],
            'codventa_encoded' => encrypt($venta['codventa']),
            'ticket_encoded' => encrypt("TICKET"),
            'factura_encoded' => encrypt("FACTURA"),
            'fechaventa' => $venta['fechaventa'],
            'totalpago' => floatval($venta['totalpago']),
            'formapago' => $venta['formapago'] ?? 'EFECTIVO',
            'tipopago' => $venta['tipopago'] ?? 'CONTADO',
            'statusventa' => $venta['statusventa'] ?? 'COMPLETADO',
            'nomcliente' => $venta['nomcliente'] ?? 'Cliente General',
            'dnicliente' => $venta['dnicliente'] ?? 'N/A',
            'codcliente' => $venta['codcliente'] ?? '',
            'total_items' => intval($venta['total_items'])
        ];
    }
    
    // Respuesta final
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'ventas' => $ventasFormateadas,
        'filtros_aplicados' => [
            'desde' => $desde,
            'hasta' => $hasta,
            'tipo' => $tipo,
            'forma' => $forma,
            'estado' => $estado,
            'search' => $search
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
