<?php
// api/guardar_compra.php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once '../includes/db.php';

try {
    $db = DB::connect();
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception("Datos inválidos");
    }

    $db->beginTransaction();

    $h = $input['header'];
    $items = $input['items'];

    // 1. Insert Header
    $status = ($h['tipocompra'] === 'CONTADO') ? 'PAGADA' : 'PENDIENTE';

    $stmt = $db->prepare("INSERT INTO compras (
        codcompra, codproveedor, subtotalivasic, subtotalivanoc, ivac, totalivac, descuentoc, totaldescuentoc, 
        totalpagoc, tipocompra, formacompra, fechavencecredito, fechapagado, statuscompra, fechaemision, fecharecepcion, 
        observaciones, codigo, codsucursal
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?
    )");

    $stmt->execute([
        $h['codcompra'], 
        $h['codproveedor'], 
        $h['subtotal_gravado'], 
        $h['subtotal_exento'], 
        $h['tasa_iva'], 
        $h['monto_iva'], 
        $h['tasa_descuento'], 
        $h['monto_descuento'],
        $h['total'], 
        $h['tipocompra'], 
        $h['formacompra'], 
        $h['fechavencecredito'] ?: '0000-00-00', 
        '0000-00-00', 
        $status, 
        $h['fechaemision'], 
        $h['fecharecepcion'],
        $h['observaciones'] ?? 'NINGUNA', 
        0, 
        1 
    ]);

    // Prepare statements for loop
    
    // DETAIL
    $stmtItem = $db->prepare("INSERT INTO detallecompras (
        codcompra, codproducto, producto, preciocomprac, precioxmenorc, precioxmayorc, precioxpublicoc, cantcompra, 
        ivaproductoc, descfactura, totaldescuentoc, valortotal, codsucursal
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?
    )");

    // UPDATE PRODUCT
    // Using simple update. Ideally 'existencia = existencia + ?'
    $stmtUpdateProd = $db->prepare("UPDATE productos SET 
        existencia = existencia + ?, 
        preciocompra = ?, 
        precioxmenor = ?, 
        precioxmayor = ?, 
        precioxpublico = ? 
        WHERE codproducto = ? AND tenant_id = 1");

    // CHECK STOCK (for Kardex)
    $stmtGetStock = $db->prepare("SELECT existencia FROM productos WHERE codproducto = ? AND tenant_id = 1");

    // KARDEX
    // (null, codproceso, codresponsable, codproducto, movimiento, entradas, salidas, devolucion, stockactual, ivaproducto, descproducto, precio, documento, fechakardex, codsucursal)
    $stmtKardex = $db->prepare("INSERT INTO kardex (
        codproceso, codresponsable, codproducto, movimiento, entradas, salidas, devolucion, 
        stockactual, ivaproducto, precio, documento, fechakardex, codsucursal
    ) VALUES (
        ?, ?, ?, 'ENTRADAS', ?, 0, 0, 
        ?, ?, ?, ?, NOW(), ?
    )");

    foreach ($items as $item) {
        $codProd = $item['codproducto'];
        $qty = $item['cantidad'];
        
        // 1. Get Current Stock (to calc Balance)
        $stmtGetStock->execute([$codProd]);
        $currentStock = $stmtGetStock->fetchColumn() ?: 0;
        $newStock = $currentStock + $qty;

        // 2. Insert Detail
        $stmtItem->execute([
            $h['codcompra'], 
            $codProd, 
            $item['producto'], 
            $item['costo'], 
            $item['p_menor'], 
            $item['p_mayor'], 
            $item['p_publico'], 
            $qty,
            $item['iva'] ?? 'NO', 
            $h['tasa_descuento'] ?? 0, 
            0, // desc total line calc if needed
            $item['total'], 
            1
        ]);

        // 3. Update Product
        $stmtUpdateProd->execute([
            $qty, 
            $item['costo'], 
            $item['p_menor'], 
            $item['p_mayor'], 
            $item['p_publico'],
            $codProd
        ]);

        // 4. Insert Kardex
        $stmtKardex->execute([
            $h['codcompra'], 
            $h['codproveedor'], 
            $codProd, 
            $qty, // Entradas
            $newStock, // Stock Actual
            $item['iva'] ?? 'NO', 
            $item['costo'], 
            'COMPRA: ' . $h['codcompra'], 
            1
        ]);
    }

    $db->commit();
    echo json_encode(['status' => 'ok']);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
?>
