<?php
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $codventa = isset($_GET['codventa']) ? $_GET['codventa'] : '';
    
    if (empty($codventa)) {
        throw new Exception('Código de venta no proporcionado');
    }
    
    $pdo = DB::connect();
    
    // Obtener información de la venta
    $sqlVenta = "SELECT 
                    v.*,
                    c.nomcliente,
                    c.dnicliente,
                    c.tlfcliente,
                    c.direccliente
                 FROM ventas v
                 LEFT JOIN clientes c ON v.codcliente = c.codcliente
                 WHERE v.codventa = :codventa";
    
    $stmtVenta = $pdo->prepare($sqlVenta);
    $stmtVenta->execute([':codventa' => $codventa]);
    $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        throw new Exception('Venta no encontrada');
    }
    
    // Obtener detalles de productos
    $sqlDetalle = "SELECT 
                      d.codproducto,
                      d.cantventa,
                      d.precioventa,
                      p.producto,
                      p.precioxpublico
                   FROM detalleventas d
                   LEFT JOIN productos p ON d.codproducto = p.codproducto
                   WHERE d.codventa = :codventa";
    
    $stmtDetalle = $pdo->prepare($sqlDetalle);
    $stmtDetalle->execute([':codventa' => $codventa]);
    $items = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear items
    $itemsFormateados = [];
    $subtotalGeneral = 0;
    
    foreach ($items as $item) {
        $precio = floatval($item['precioventa']);
        // Fallback si el precio es 0
        if ($precio == 0) {
            $precio = floatval($item['precioxpublico']);
        }
        
        $cantidad = floatval($item['cantventa']);
        $subtotal = $cantidad * $precio;
        $subtotalGeneral += $subtotal;
        
        $itemsFormateados[] = [
            'codproducto' => $item['codproducto'],
            'producto' => $item['producto'] ?? 'Producto Desconocido',
            'cantidad' => $cantidad,
            'precio' => $precio,
            'subtotal' => $subtotal
        ];
    }
    
    // Obtener abonos si es crédito
    $abonos = [];
    $totalAbonado = 0;
    
    if ($venta['tipopago'] == 'CREDITO') {
        $sqlAbonos = "SELECT 
                         fechaabono,
                         montoabono,
                         codcaja
                      FROM abonoscreditosventas
                      WHERE codventa = :codventa
                      ORDER BY fechaabono DESC";
        
        $stmtAbonos = $pdo->prepare($sqlAbonos);
        $stmtAbonos->execute([':codventa' => $codventa]);
        $abonosRaw = $stmtAbonos->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($abonosRaw as $abono) {
            $monto = floatval($abono['montoabono']);
            $totalAbonado += $monto;
            
            $abonos[] = [
                'fecha' => $abono['fechaabono'],
                'monto' => $monto,
                'codcaja' => $abono['codcaja']
            ];
        }
    }
    
    // Calcular saldo pendiente
    $saldoPendiente = floatval($venta['totalpago']) - $totalAbonado;
    
    // Respuesta
    echo json_encode([
        'success' => true,
        'venta' => [
            'codventa' => $venta['codventa'],
            'codventa_encoded' => encrypt($venta['codventa']),
            'ticket_encoded' => encrypt("TICKET"),
            'factura_encoded' => encrypt("FACTURA"),
            'fechaventa' => $venta['fechaventa'],
            'totalpago' => floatval($venta['totalpago']),
            'formapago' => $venta['formapago'] ?? 'EFECTIVO',
            'tipopago' => $venta['tipopago'] ?? 'CONTADO',
            'statusventa' => $venta['statusventa'] ?? 'COMPLETADO',
            'montoefectivo' => floatval($venta['montoefectivo'] ?? 0),
            'pagacon' => floatval($venta['pagacon'] ?? 0),
            'vuelto' => floatval($venta['vuelto'] ?? 0)
        ],
        'cliente' => [
            'codcliente' => $venta['codcliente'] ?? '',
            'nomcliente' => $venta['nomcliente'] ?? 'Cliente General',
            'dnicliente' => $venta['dnicliente'] ?? 'N/A',
            'tlfcliente' => $venta['tlfcliente'] ?? 'N/A',
            'direccliente' => $venta['direccliente'] ?? 'N/A'
        ],
        'items' => $itemsFormateados,
        'subtotal_calculado' => $subtotalGeneral,
        'abonos' => $abonos,
        'total_abonado' => $totalAbonado,
        'saldo_pendiente' => $saldoPendiente
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
