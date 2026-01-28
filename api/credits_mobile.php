<?php
define('API_PUBLIC', true); // Bypass default session check in common.php
require_once 'common.php';

// Manual session check for legacy system
if (empty($_SESSION['codigo'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado. Inicie sesión.']);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    $db = DB::connect();

    // Ensure session variables are set
    $codsucursal = $_SESSION['codsucursal'] ?? '';
    $id_usuario = $_SESSION['codigo'] ?? '';

    if (empty($codsucursal)) {
        throw new Exception("Error de sesión: No hay sucursal definida.");
    }

    if ($action === 'get_debtors') {
        // List clients with positive debt
        $sql = "SELECT
                    c.codcliente as idcliente,
                    c.nomcliente as nombre,
                    cc.montocredito as deuda_total,
                    c.tlfcliente as telefono,
                    NULL as foto -- Placeholder as requested
                FROM creditosxclientes cc
                INNER JOIN clientes c ON cc.codcliente = c.codcliente
                WHERE cc.codsucursal = :codsucursal
                AND cc.montocredito > 0.00
                ORDER BY c.nomcliente ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([':codsucursal' => $codsucursal]);
        $debtors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format response
        $response = [];
        foreach ($debtors as $debtor) {
             $debtor['deuda_total_raw'] = $debtor['deuda_total'];
             $response[] = $debtor;
        }

        echo json_encode($response);
        exit;

    } elseif ($action === 'pay') {
        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        $idcliente = $input['idcliente'] ?? '';
        $monto_abono = floatval($input['monto_abono'] ?? 0);
        $metodo_pago = $input['metodo_pago'] ?? 'EFECTIVO'; // Default to CASH

        if (empty($idcliente) || $monto_abono <= 0) {
            throw new Exception("Datos inválidos: Cliente o monto incorrecto.");
        }

        $db->beginTransaction();

        // 1. Get User's Open Box
        $stmt = $db->prepare("SELECT codcaja, ingresos FROM arqueocaja
                              INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja
                              WHERE cajas.codigo = :codigo AND statusarqueo = 1 LIMIT 1");
        $stmt->execute([':codigo' => $id_usuario]);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            throw new Exception("No tienes una caja abierta para realizar cobros.");
        }
        $codcaja = $caja['codcaja'];

        // 2. Get Total Debt (Verify)
        $stmt = $db->prepare("SELECT montocredito FROM creditosxclientes WHERE codcliente = :idcliente AND codsucursal = :codsucursal FOR UPDATE");
        $stmt->execute([':idcliente' => $idcliente, ':codsucursal' => $codsucursal]);
        $creditInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$creditInfo) {
             throw new Exception("Cliente no encontrado o sin deuda registrada.");
        }

        $current_debt = floatval($creditInfo['montocredito']);

        if ($monto_abono > ($current_debt + 0.01)) { // Tolerance for floating point
             throw new Exception("El abono excede la deuda actual (" . number_format($current_debt, 2) . ").");
        }

        // 3. FIFO Strategy: Get Pending Invoices
        $stmt = $db->prepare("SELECT codventa, totalpago, creditopagado
                              FROM ventas
                              WHERE codcliente = :idcliente
                              AND codsucursal = :codsucursal
                              AND tipopago = 'CREDITO'
                              AND statusventa = 'PENDIENTE'
                              ORDER BY idventa ASC"); // Oldest first
        $stmt->execute([':idcliente' => $idcliente, ':codsucursal' => $codsucursal]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $remaining_payment = $monto_abono;
        $total_paid = 0;

        foreach ($invoices as $inv) {
            if ($remaining_payment <= 0.00) break;

            $codventa = $inv['codventa'];
            $total_factura = floatval($inv['totalpago']);
            $pagado_previamente = floatval($inv['creditopagado']);
            $saldo_factura = $total_factura - $pagado_previamente;

            if ($saldo_factura <= 0.00) continue;

            $payment_for_this = min($remaining_payment, $saldo_factura);

            // Update Venta
            $nuevo_pagado = $pagado_previamente + $payment_for_this;
            $nuevo_status = ($nuevo_pagado >= $total_factura - 0.01) ? 'PAGADA' : 'PENDIENTE';

            $updateVenta = $db->prepare("UPDATE ventas SET
                                            creditopagado = :nuevo_pagado,
                                            statusventa = :nuevo_status,
                                            fechapagado = IF(:nuevo_status = 'PAGADA', CURDATE(), fechapagado)
                                         WHERE codventa = :codventa AND codsucursal = :codsucursal");
            $updateVenta->execute([
                ':nuevo_pagado' => $nuevo_pagado,
                ':nuevo_status' => $nuevo_status,
                ':codventa' => $codventa,
                ':codsucursal' => $codsucursal
            ]);

            // Insert Payment Record (Abono)
            $insertAbono = $db->prepare("INSERT INTO abonoscreditosventas
                                            (codcaja, codventa, codcliente, montoabono, fechaabono, codsucursal)
                                         VALUES
                                            (:codcaja, :codventa, :idcliente, :montoabono, NOW(), :codsucursal)");
            $insertAbono->execute([
                ':codcaja' => $codcaja,
                ':codventa' => $codventa,
                ':idcliente' => $idcliente,
                ':montoabono' => $payment_for_this,
                ':codsucursal' => $codsucursal
            ]);

            $remaining_payment -= $payment_for_this;
            $total_paid += $payment_for_this;
        }

        // 4. Update Global Debt
        if ($total_paid > 0) {
            $updateGlobal = $db->prepare("UPDATE creditosxclientes SET montocredito = montocredito - :paid WHERE codcliente = :idcliente AND codsucursal = :codsucursal");
            $updateGlobal->execute([
                ':paid' => $total_paid,
                ':idcliente' => $idcliente,
                ':codsucursal' => $codsucursal
            ]);

            // 5. Update Cash Box (Ingresos)
            // Legacy logic updates 'ingresos' field for payments
            $updateCaja = $db->prepare("UPDATE arqueocaja SET ingresos = ingresos + :paid WHERE codcaja = :codcaja AND statusarqueo = 1");
            $updateCaja->execute([
                ':paid' => $total_paid,
                ':codcaja' => $codcaja
            ]);
        }

        $db->commit();

        $new_balance = $current_debt - $total_paid;

        echo json_encode([
            'status' => 'success',
            'message' => 'Pago realizado correctamente.',
            'monto' => number_format($total_paid, 2),
            'nuevo_saldo' => number_format($new_balance, 2)
        ]);
        exit;

    } else {
        throw new Exception("Acción no válida.");
    }

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
?>
