<?php
// Define Public Access to bypass standard common.php check (which looks for id_usuario)
// We will manually check for legacy session 'codigo'
define('API_PUBLIC', true);

require_once '../api/common.php';

// Manual Security Check for Legacy Session
if (empty($_SESSION['codigo']) || empty($_SESSION['codsucursal'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - Legacy Session Required']);
    exit;
}

$action = $_GET['action'] ?? '';
// DB is already connected in common.php usually, but we can ensure it
$db = DB::connect();
$codSucursal = $_SESSION['codsucursal'];
$userId = $_SESSION['codigo'];

if ($action === 'get_init_data') {
    try {
        // 1. Total Debt
        $stmtDebt = $db->prepare("SELECT SUM(montocredito) as total FROM creditosxclientes WHERE codsucursal = ?");
        $stmtDebt->execute([$codSucursal]);
        $totalDebt = $stmtDebt->fetchColumn() ?: 0;

        // 2. Collected Today
        $stmtCollected = $db->prepare("SELECT SUM(montoabono) as total FROM abonoscreditosventas WHERE codsucursal = ? AND DATE(fechaabono) = CURDATE()");
        $stmtCollected->execute([$codSucursal]);
        $collectedToday = $stmtCollected->fetchColumn() ?: 0;

        // 3. Client List with Debt
        $stmtClients = $db->prepare("
            SELECT
                c.codcliente,
                c.nomcliente,
                c.tlfcliente,
                c.dnicliente,
                cc.montocredito
            FROM clientes c
            INNER JOIN creditosxclientes cc ON c.codcliente = cc.codcliente
            WHERE cc.codsucursal = ? AND cc.montocredito > 0.01
            ORDER BY cc.montocredito DESC
        ");
        $stmtClients->execute([$codSucursal]);
        $clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'total_debt' => number_format($totalDebt, 2, '.', ''),
            'collected_today' => number_format($collectedToday, 2, '.', ''),
            'clients' => $clients
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

} elseif ($action === 'save_payment') {
    // Read JSON Input
    $input = json_decode(file_get_contents('php://input'), true);
    $codCliente = $input['codcliente'] ?? '';
    $amount = floatval($input['amount'] ?? 0);

    if (empty($codCliente) || $amount <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    try {
        $db->beginTransaction();

        // 1. Get Open Caja for User
        $stmtCaja = $db->prepare("
            SELECT a.codcaja, a.ingresos, a.abonos
            FROM arqueocaja a
            INNER JOIN cajas c ON a.codcaja = c.codcaja
            WHERE c.codigo = ? AND a.statusarqueo = 1
            LIMIT 1
        ");
        $stmtCaja->execute([$userId]);
        $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            throw new Exception("No hay caja abierta para este usuario. Por favor abra una caja primero.");
        }
        $codCaja = $caja['codcaja'];

        // 2. Fetch Pending Invoices (FIFO)
        $stmtInvoices = $db->prepare("
            SELECT codventa, totalpago, creditopagado
            FROM ventas
            WHERE codcliente = ? AND codsucursal = ?
              AND statusventa != 'PAGADA'
              AND tipopago = 'CREDITO'
            ORDER BY fechaventa ASC
        ");
        $stmtInvoices->execute([$codCliente, $codSucursal]);
        $invoices = $stmtInvoices->fetchAll(PDO::FETCH_ASSOC);

        $remainingPayment = $amount;
        $totalApplied = 0;

        foreach ($invoices as $inv) {
            if ($remainingPayment <= 0.001) break;

            $codVenta = $inv['codventa'];
            $totalPago = floatval($inv['totalpago']);
            $creditoPagado = floatval($inv['creditopagado']);
            $debtOnInvoice = $totalPago - $creditoPagado;

            if ($debtOnInvoice <= 0.01) continue; // Skip fully paid

            $amountToApply = min($remainingPayment, $debtOnInvoice);

            // A. Insert Abono Record
            $stmtAbono = $db->prepare("
                INSERT INTO abonoscreditosventas (codcaja, codventa, codcliente, montoabono, fechaabono, codsucursal)
                VALUES (?, ?, ?, ?, NOW(), ?)
            ");
            $stmtAbono->execute([$codCaja, $codVenta, $codCliente, $amountToApply, $codSucursal]);

            // B. Update Venta
            $newCreditoPagado = $creditoPagado + $amountToApply;
            $newStatus = ($newCreditoPagado >= $totalPago - 0.01) ? 'PAGADA' : 'PENDIENTE';
            $fechaPagado = ($newStatus === 'PAGADA') ? date('Y-m-d') : '0000-00-00';

            $stmtUpdateVenta = $db->prepare("
                UPDATE ventas
                SET creditopagado = ?, statusventa = ?, fechapagado = ?
                WHERE codventa = ? AND codsucursal = ?
            ");
            $stmtUpdateVenta->execute([$newCreditoPagado, $newStatus, $fechaPagado, $codVenta, $codSucursal]);

            // C. Update Arqueo (Sum to Ingresos? No, Sum to Abonos? or Ingresos?)
            // Checking RegistrarPago legacy:
            // "UPDATE arqueocaja SET ingresos = ingresos + ? ..." (Wait, RegistrarPago logic for Credito Payment?)
            // Looking at RegistrarPago in class.php:
            // "UPDATE arqueocaja set ingresos = ? ..." ($txtTotal = $_POST["montoabono"]+$ingreso).
            // So it updates 'ingresos'.
            $stmtUpdateArqueo = $db->prepare("
                UPDATE arqueocaja
                SET ingresos = ingresos + ?
                WHERE codcaja = ? AND statusarqueo = 1
            ");
            $stmtUpdateArqueo->execute([$amountToApply, $codCaja]);

            $remainingPayment -= $amountToApply;
            $totalApplied += $amountToApply;
        }

        // 3. Update Global Ledger (creditosxclientes)
        if ($totalApplied > 0) {
            $stmtUpdateLedger = $db->prepare("
                UPDATE creditosxclientes
                SET montocredito = montocredito - ?
                WHERE codcliente = ? AND codsucursal = ?
            ");
            $stmtUpdateLedger->execute([$totalApplied, $codCliente, $codSucursal]);
        }

        $db->commit();

        // Fetch new balance
        $stmtBal = $db->prepare("SELECT montocredito FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?");
        $stmtBal->execute([$codCliente, $codSucursal]);
        $newBalance = $stmtBal->fetchColumn() ?: 0;

        // Fetch client phone
        $stmtPhone = $db->prepare("SELECT nomcliente, tlfcliente FROM clientes WHERE codcliente = ?");
        $stmtPhone->execute([$codCliente]);
        $clientData = $stmtPhone->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'new_balance' => number_format($newBalance, 2, '.', ''),
            'applied_amount' => number_format($totalApplied, 2, '.', ''),
            'client_name' => $clientData['nomcliente'],
            'client_phone' => $clientData['tlfcliente']
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}
