<?php
/**
 * API: Credits Mobile - Cobranza Express
 * Backend para la app móvil de cobradores (PR #8 - Jules)
 * Implementa lógica de amortización automática FIFO
 */

// ============================================================================
// 1. CONFIGURACIÓN Y SEGURIDAD
// ============================================================================
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

// Validar sesión activa
if (!isset($_SESSION['acceso']) || !isset($_SESSION['codigo'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Sesión no autorizada']);
    exit;
}

$db = DB::connect();
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {

    // ========================================================================
    // 2. ENDPOINT: Listar Deudores (GET action=get_debtors)
    // ========================================================================
    if ($method === 'GET' && $action === 'get_debtors') {
        
        // Query Agrupada por Cliente con Agregaciones
        $sql = "SELECT 
                    c.codcliente as idcliente,
                    c.nomcliente as nombre,
                    c.tlfcliente as telefono,
                    c.dnicliente,
                    
                    -- Suma total de saldos pendientes del cliente
                    SUM(v.totalpago - COALESCE(abonos.total_abonado, 0)) as deuda_total,
                    
                    -- Fecha del último abono realizado por este cliente
                    MAX(ultimo_abono.fecha_max) as fecha_ultimo_abono,
                    
                    -- Ruta de foto (si existe, si no placeholder)
                    CASE 
                        WHEN c.dnicliente IS NOT NULL AND c.dnicliente != '' 
                        THEN CONCAT('fotos/', c.dnicliente, '.jpg')
                        ELSE 'fotos/avatar.jpg'
                    END as foto_url
                    
                FROM ventas v
                INNER JOIN clientes c ON v.codcliente = c.codcliente
                
                -- Subquery: Total abonado por cada venta
                LEFT JOIN (
                    SELECT codventa, SUM(montoabono) as total_abonado
                    FROM abonoscreditosventas
                    GROUP BY codventa
                ) abonos ON v.codventa = abonos.codventa
                
                -- Subquery: Fecha del último abono del cliente
                LEFT JOIN (
                    SELECT av.codcliente, MAX(av.fechaabono) as fecha_max
                    FROM abonoscreditosventas av
                    GROUP BY av.codcliente
                ) ultimo_abono ON c.codcliente = ultimo_abono.codcliente
                
                WHERE v.tipopago = 'CREDITO' 
                  AND v.statusventa != 'PAGADA'
                
                GROUP BY c.codcliente, c.nomcliente, c.tlfcliente, c.dnicliente
                HAVING deuda_total > 0
                ORDER BY deuda_total DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $debtors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formatear datos y validar existencia de fotos
        foreach ($debtors as &$d) {
            $d['deuda_total'] = (float)$d['deuda_total'];
            
            // Verificar si la foto existe físicamente
            $fotoPath = "../" . $d['foto_url'];
            if (!file_exists($fotoPath)) {
                $d['foto_url'] = 'fotos/avatar.jpg'; // Placeholder genérico
            }
        }

        echo json_encode([
            'success' => true, 
            'data' => $debtors,
            'count' => count($debtors)
        ]);
        exit;
    }

    // ========================================================================
    // 3. ENDPOINT: Procesar Abono con Amortización Automática (POST action=pay)
    // ========================================================================
    if ($method === 'POST' && $action === 'pay') {
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $idCliente = $input['idcliente'] ?? null;
        $montoAbono = (float)($input['monto_abono'] ?? 0);
        $metodoPago = $input['metodo_pago'] ?? 'EFECTIVO';

        // Validaciones básicas
        if (!$idCliente || $montoAbono <= 0) {
            throw new Exception("Datos inválidos. Se requiere 'idcliente' y 'monto_abono' > 0");
        }

        // ====================================================================
        // PASO 1: Obtener Caja Abierta del Usuario
        // ====================================================================
        $sqlCaja = "SELECT arqueocaja.codcaja, arqueocaja.ingresos, arqueocaja.codarqueo
                    FROM arqueocaja 
                    INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja 
                    INNER JOIN usuarios ON cajas.codigo = usuarios.codigo 
                    WHERE usuarios.codigo = ? AND arqueocaja.statusarqueo = 1";
        
        $stmtCaja = $db->prepare($sqlCaja);
        $stmtCaja->execute([$_SESSION['codigo']]);
        $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

        // Fallback: Cualquier caja abierta (en caso de que el usuario no tenga asignada)
        if (!$caja) {
            $sqlCajaGlobal = "SELECT codcaja, ingresos, codarqueo FROM arqueocaja WHERE statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1";
            $caja = $db->query($sqlCajaGlobal)->fetch(PDO::FETCH_ASSOC);
        }

        if (!$caja) {
            throw new Exception("No hay caja abierta. Debe abrir una caja antes de registrar pagos.");
        }

        $codCaja = $caja['codcaja'];
        $ingresosActuales = (float)$caja['ingresos'];

        // ====================================================================
        // PASO 2: Obtener Créditos Pendientes del Cliente (FIFO - Más Viejo Primero)
        // ====================================================================
        $sqlCreditos = "SELECT 
                            v.codventa,
                            v.codsucursal,
                            v.fechaventa,
                            v.totalpago,
                            COALESCE(SUM(a.montoabono), 0) as ya_abonado,
                            (v.totalpago - COALESCE(SUM(a.montoabono), 0)) as saldo_pendiente
                        FROM ventas v
                        LEFT JOIN abonoscreditosventas a ON v.codventa = a.codventa
                        WHERE v.codcliente = :codcliente
                          AND v.tipopago = 'CREDITO'
                          AND v.statusventa != 'PAGADA'
                        GROUP BY v.codventa, v.codsucursal, v.fechaventa, v.totalpago
                        HAVING saldo_pendiente > 0.01
                        ORDER BY v.fechaventa ASC, v.codventa ASC"; // FIFO: Más viejo primero

        $stmtCreditos = $db->prepare($sqlCreditos);
        $stmtCreditos->execute([':codcliente' => $idCliente]);
        $creditos = $stmtCreditos->fetchAll(PDO::FETCH_ASSOC);

        if (empty($creditos)) {
            throw new Exception("El cliente no tiene créditos pendientes.");
        }

        // ====================================================================
        // PASO 3: ALGORITMO DE AMORTIZACIÓN AUTOMÁTICA (FIFO)
        // ====================================================================
        $db->beginTransaction();

        $montoRestante = $montoAbono;
        $creditosAfectados = [];
        $fechaAbono = date("Y-m-d H:i:s");

        foreach ($creditos as $credito) {
            if ($montoRestante <= 0) break; // Ya se distribuyó todo el abono

            $codVenta = $credito['codventa'];
            $saldoPendiente = (float)$credito['saldo_pendiente'];
            $yaAbonado = (float)$credito['ya_abonado'];
            $totalVenta = (float)$credito['totalpago'];
            $codSucursal = $credito['codsucursal'];

            // Calcular cuánto podemos aplicar a este crédito
            $montoAAplicar = min($montoRestante, $saldoPendiente);

            // A. Insertar Registro de Abono
            $sqlInsertAbono = "INSERT INTO abonoscreditosventas 
                               (codcaja, codventa, codcliente, montoabono, fechaabono, codsucursal) 
                               VALUES (?, ?, ?, ?, ?, ?)";
            $stmtInsert = $db->prepare($sqlInsertAbono);
            $stmtInsert->execute([
                $codCaja, 
                $codVenta, 
                $idCliente, 
                $montoAAplicar, 
                $fechaAbono, 
                $codSucursal
            ]);

            // B. Actualizar Venta
            $nuevoAbonado = $yaAbonado + $montoAAplicar;
            $nuevoSaldo = $totalVenta - $nuevoAbonado;
            $nuevoEstado = ($nuevoSaldo < 0.01) ? 'PAGADA' : 'PENDIENTE';

            $sqlUpdateVenta = "UPDATE ventas SET creditopagado = ?, statusventa = ?";
            $paramsVenta = [$nuevoAbonado, $nuevoEstado];

            if ($nuevoEstado === 'PAGADA') {
                $sqlUpdateVenta .= ", fechapagado = ?";
                $paramsVenta[] = date("Y-m-d");
            }

            $sqlUpdateVenta .= " WHERE codventa = ?";
            $paramsVenta[] = $codVenta;

            $stmtUpdateVenta = $db->prepare($sqlUpdateVenta);
            $stmtUpdateVenta->execute($paramsVenta);

            // Registrar crédito afectado para el resumen
            $creditosAfectados[] = [
                'codventa' => $codVenta,
                'monto_aplicado' => $montoAAplicar,
                'nuevo_estado' => $nuevoEstado
            ];

            // Restar del monto restante
            $montoRestante -= $montoAAplicar;
        }

        // ====================================================================
        // PASO 4: Actualizar Saldo Global del Cliente (Tabla creditosxclientes - Legacy)
        // ====================================================================
        // Obtener codsucursal del primer crédito (asumimos una sucursal por simplicidad)
        $codSucursalCliente = $creditos[0]['codsucursal'];

        $stmtCreditoCliente = $db->prepare("SELECT montocredito FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?");
        $stmtCreditoCliente->execute([$idCliente, $codSucursalCliente]);
        $rowCreditoCliente = $stmtCreditoCliente->fetch(PDO::FETCH_ASSOC);

        if ($rowCreditoCliente) {
            $nuevoMontoCredito = max(0, (float)$rowCreditoCliente['montocredito'] - $montoAbono);
            $stmtUpdateCredito = $db->prepare("UPDATE creditosxclientes SET montocredito = ? WHERE codcliente = ? AND codsucursal = ?");
            $stmtUpdateCredito->execute([$nuevoMontoCredito, $idCliente, $codSucursalCliente]);
        }

        // ====================================================================
        // PASO 5: Registrar Movimiento en Caja (Ingreso de Dinero)
        // ====================================================================
        $nuevosIngresos = $ingresosActuales + $montoAbono;
        $stmtUpdateCaja = $db->prepare("UPDATE arqueocaja SET ingresos = ? WHERE codcaja = ?");
        $stmtUpdateCaja->execute([$nuevosIngresos, $codCaja]);

        // COMMIT de la transacción
        $db->commit();

        // ====================================================================
        // PASO 6: Calcular Saldo Restante del Cliente (para UI optimista)
        // ====================================================================
        $sqlSaldoRestante = "SELECT 
                                SUM(v.totalpago - COALESCE(abonos.total_abonado, 0)) as saldo_total
                             FROM ventas v
                             LEFT JOIN (
                                 SELECT codventa, SUM(montoabono) as total_abonado
                                 FROM abonoscreditosventas
                                 GROUP BY codventa
                             ) abonos ON v.codventa = abonos.codventa
                             WHERE v.codcliente = ? 
                               AND v.tipopago = 'CREDITO'
                               AND v.statusventa != 'PAGADA'
                             HAVING saldo_total > 0";
        
        $stmtSaldo = $db->prepare($sqlSaldoRestante);
        $stmtSaldo->execute([$idCliente]);
        $rowSaldo = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
        $nuevoSaldoCliente = (float)($rowSaldo['saldo_total'] ?? 0);

        // ====================================================================
        // RESPUESTA DE ÉXITO
        // ====================================================================
        echo json_encode([
            'success' => true,
            'message' => 'Abono procesado correctamente',
            'monto_total_aplicado' => $montoAbono,
            'new_balance' => $nuevoSaldoCliente, // ✅ Saldo restante del cliente
            'creditos_afectados' => $creditosAfectados,
            'metodo_pago' => $metodoPago,
            'caja_utilizada' => $codCaja
        ]);
        exit;
    }

    // ========================================================================
    // RUTA NO VÁLIDA
    // ========================================================================
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Acción no válida o método incorrecto',
        'action_received' => $action,
        'method_received' => $method
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>
