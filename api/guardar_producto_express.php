<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $db = DB::connect();

    $db->beginTransaction(); // Transacción para seguridad

    // 1. Datos básicos
    $codigo = (!empty($input['codigo'])) ? $input['codigo'] : 'PROD-' . time();
    $nombre = $input['nombre'];
    $precio = $input['precio_venta'];
    $costo = $input['costo'] ?? 0;
    $stock = $input['stock'] ?? 0;
    $fecha = date('Y-m-d H:i:s');

    // 2. Insertar Producto
    $sql = "INSERT INTO productos (
        codproducto, producto, preciocompra, precioxpublico, existencia,
        codmarca, codfamilia, codpresentacion, codproveedor, 
        ivaproducto, tenant_id
    ) VALUES (
        ?, ?, ?, ?, ?, 
        1, 1, 1, 1, 
        0, 1
    )";

    $stmt = $db->prepare($sql);
    $stmt->execute([$codigo, $nombre, $costo, $precio, $stock]);

    // 3. KARDEX INICIAL (Si hay stock > 0)
    // 3. KARDEX INICIAL (Si hay stock > 0)
    if ($stock > 0) {
        $sqlKardex = "INSERT INTO kardex (
            codproceso, codresponsable, codproducto, movimiento, 
            entradas, salidas, devolucion, stockactual, 
            ivaproducto, descproducto, precio, documento, 
            fechakardex, codsucursal
        ) VALUES (
            ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?
        )";
        $stmtKardex = $db->prepare($sqlKardex);
        // codproceso='P01', codresponsable=1, codproducto, mov='ENTRADA', ent=stock, sal=0, dev=0, stockactual=stock, iva='NO', desc=0, precio=costo, doc='INICIAL', fecha, sucursal=1
        $stmtKardex->execute([
            'INVENTARIO', 1, $codigo, 'ENTRADA', 
            $stock, 0, 0, $stock, 
            'NO', 0, $costo, 'INVENTARIO-INICIAL', 
            $fecha, 1
        ]);
    }

    $db->commit();

    echo json_encode(['status' => 'ok', 'message' => 'Producto creado con historial', 'id' => $codigo]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
