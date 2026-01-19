<?php
// api/clients_v2.php
// Protocolo: Bridge (Usa common.php)
require_once 'common.php';

// La validación de sesión ya es manejada por common.php (verificar_acceso_api)

$db = DB::connect();
$method = $_SERVER['REQUEST_METHOD'];

// TENANT ID Fixed for now
$tenant_id = 1; 

try {
// -----------------------------------------------------------------------------
// GET METHOD
// -----------------------------------------------------------------------------
    if ($method === 'GET') {
        
        $action = $_GET['action'] ?? 'list';

        if ($action === 'get_debts') {
            // LISTAR DEUDAS PENDIENTES DE UN CLIENTE
            $id = $_GET['id'] ?? null;
            if (!$id) throw new Exception("ID de cliente requerido");

            // Primero obtenemos el codcliente real porque el ID es autoincremental
            $stmtC = $db->prepare("SELECT codcliente FROM clientes WHERE idcliente = ?");
            $stmtC->execute([$id]);
            $clientData = $stmtC->fetch(PDO::FETCH_ASSOC);
            if (!$clientData) throw new Exception("Cliente no encontrado");
            
            $codCliente = $clientData['codcliente'];

            // Query para obtener ventas a crédito pendientes (Saldo > 0)
            // Calculado dinamico: TotalVenta - TotalAbonado
            $sql = "SELECT 
                        v.codventa, 
                        v.fechaventa, 
                        v.totalpago,
                        COALESCE(SUM(a.montoabono),0) as abonado,
                        (v.totalpago - COALESCE(SUM(a.montoabono),0)) as saldo_pendiente
                    FROM ventas v
                    LEFT JOIN abonoscreditosventas a ON v.codventa = a.codventa
                    WHERE v.tipopago = 'CREDITO' 
                    AND v.codcliente = :cod
                    GROUP BY v.codventa
                    HAVING saldo_pendiente > 0.01
                    ORDER BY v.fechaventa ASC";

            $stmt = $db->prepare($sql);
            $stmt->execute([':cod' => $codCliente]);
            $debts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cast floats
            foreach($debts as &$d) {
                $d['totalpago'] = (float)$d['totalpago'];
                $d['abonado'] = (float)$d['abonado'];
                $d['saldo_pendiente'] = (float)$d['saldo_pendiente'];
            }

            echo json_encode($debts);
            exit;
        }

        // DEFAULT: LISTAR CLIENTES
        $q = $_GET['q'] ?? '';
        // QUERY COMPLEX: Calcular deuda real igual que sistema legacy
        // Deuda = Suma(Ventas Credito) - Suma(Abonos)
        $sql = "SELECT 
                    c.idcliente as id, 
                    c.codcliente as codigo,
                    c.nomcliente as nombre, 
                    c.tlfcliente as telefono, 
                    c.email, 
                    c.limitecredito,
                    
                    (COALESCE(debt.total_debt, 0) - COALESCE(pay.total_paid, 0)) as current_balance

                FROM clientes c
                
                -- 1. Subquery Deuda Total (Ventas Credito)
                LEFT JOIN (
                    SELECT codcliente, SUM(totalpago) as total_debt
                    FROM ventas
                    WHERE tipopago = 'CREDITO'
                    GROUP BY codcliente
                ) debt ON c.codcliente = debt.codcliente

                -- 2. Subquery Pagos Totales (Abonos)
                LEFT JOIN (
                    SELECT v.codcliente, SUM(a.montoabono) as total_paid
                    FROM abonoscreditosventas a
                    JOIN ventas v ON a.codventa = v.codventa
                    WHERE v.tipopago = 'CREDITO'
                    GROUP BY v.codcliente
                ) pay ON c.codcliente = pay.codcliente

                WHERE c.tenant_id = :tid";
        
        $params = [':tid' => $tenant_id];

        if (!empty($q)) {
            $sql .= " AND (c.nomcliente LIKE :q1 OR c.tlfcliente LIKE :q2)";
            $params[':q1'] = "%$q%";
            $params[':q2'] = "%$q%";
        }

        $sql .= " ORDER BY c.nomcliente ASC LIMIT 50";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fuerza conversion a float para el JSON
        foreach($results as &$row) {
            $row['current_balance'] = (float)$row['current_balance'];
        }

        echo json_encode($results);

    } elseif ($method === 'POST') {
        // -----------------------------------------------------------------------------
        // POST METHOD - PROCESAR PAGO
        // -----------------------------------------------------------------------------
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? 'add_debt'; // default legacy simulacion

        if ($action === 'add_payment') {
            // PROCESAR PAGO REAL
            /*
                Requerimientos:
                1. Verificar Caja Abierta del Usuario (session[codigo])
                2. Insertar en abonoscreditosventas
                3. Actualizar ventas (creditopagado)
                4. Actualizar arqueocaja (ingresos)
            */

            // Validar sesion legacy
            if (!isset($_SESSION['codigo'])) throw new Exception("Sesión inválida o expirada");

            $codVenta = $input['codventa'] ?? null;
            $montoAbono = (float)($input['amount'] ?? 0);
            
            if (!$codVenta || $montoAbono <= 0) throw new Exception("Datos de pago inválidos");

            // 1. Verificar Caja Abierta
            // INTENTO 1: Caja por Usuario (Legacy estricto)
            $sqlCaja = "SELECT arqueocaja.codcaja, arqueocaja.ingresos 
                        FROM arqueocaja 
                        INNER JOIN cajas ON arqueocaja.codcaja = cajas.codcaja 
                        INNER JOIN usuarios ON cajas.codigo = usuarios.codigo 
                        WHERE usuarios.codigo = ? AND arqueocaja.statusarqueo = 1";
            $stmtCaja = $db->prepare($sqlCaja);
            $stmtCaja->execute([$_SESSION['codigo'] ?? 0]); // Use 0 if not set to fail gracefully to fallback
            $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

            // INTENTO 2: Fallback a ÚLTIMA CAJA ABIERTA (Como caja_express.php / caja_control.php)
            // Esto soluciona el error si el usuario de sesión no coincide con el dueño de la caja pero es un sistema mono-usuario efectivo
            if (!$caja) {
                $sqlCajaGlobal = "SELECT codcaja, ingresos FROM arqueocaja WHERE statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1";
                $caja = $db->query($sqlCajaGlobal)->fetch(PDO::FETCH_ASSOC);
            }

            if (!$caja) throw new Exception("No hay ninguna caja abierta en el sistema.");
            
            $codCaja = $caja['codcaja'];
            $ingresosActuales = (float)$caja['ingresos'];

            // 2. Obtener datos de la venta y cliente
            $stmtVenta = $db->prepare("SELECT codcliente, codsucursal, totalpago, (SELECT SUM(montoabono) FROM abonoscreditosventas WHERE codventa = ventas.codventa) as abonado FROM ventas WHERE codventa = ?");
            $stmtVenta->execute([$codVenta]);
            $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);
            
            if (!$venta) throw new Exception("Venta no encontrada");

            $codCliente = $venta['codcliente'];
            $codSucursal = $venta['codsucursal'];
            $totalVenta = (float)$venta['totalpago'];
            $yaAbonado = (float)$venta['abonado'];
            $pendiente = $totalVenta - $yaAbonado;

            if ($montoAbono > $pendiente) {
                // Permitir margen de error de decimales? No, estricto por ahora.
                // Ajustar si es minúsculo error de redondeo
                if (abs($montoAbono - $pendiente) < 0.01) {
                    $montoAbono = $pendiente;
                } else {
                    throw new Exception("El abono (C$ $montoAbono) excede el saldo pendiente (C$ $pendiente)");
                }
            }

            // INICIAR TRANSACCION
            $db->beginTransaction();

            try {
                // A. Insertar Abono
                $fechaAbono = date("Y-m-d H:i:s");
                $sqlInst = "INSERT INTO abonoscreditosventas (codcaja, codventa, codcliente, montoabono, fechaabono, codsucursal) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInst = $db->prepare($sqlInst);
                $stmtInst->execute([$codCaja, $codVenta, $codCliente, $montoAbono, $fechaAbono, $codSucursal]);

                // B. Actualizar Venta (Credito Pagado acumulado)
                $nuevoAbonado = $yaAbonado + $montoAbono;
                $statusVenta = ($nuevoAbonado >= $totalVenta - 0.01) ? "PAGADA" : "PENDIENTE"; // SIMPLE LOGIC
                // NOTA: Legacy usa "PAGADA" y actualiza fechapagado si se completa
                
                $sqlUpdVenta = "UPDATE ventas SET creditopagado = ?, statusventa = ? WHERE codventa = ?";
                $paramsVenta = [$nuevoAbonado, $statusVenta, $codVenta];
                
                if ($statusVenta === "PAGADA") {
                    $sqlUpdVenta = "UPDATE ventas SET creditopagado = ?, statusventa = ?, fechapagado = ? WHERE codventa = ?";
                    $paramsVenta = [$nuevoAbonado, $statusVenta, date("Y-m-d"), $codVenta];
                }
                
                $stmtUpdV = $db->prepare($sqlUpdVenta);
                $stmtUpdV->execute($paramsVenta);

                // C. Actualizar Caja (Ingresos)
                $nuevosIngresos = $ingresosActuales + $montoAbono;
                $stmtUpdCaja = $db->prepare("UPDATE arqueocaja SET ingresos = ? WHERE codcaja = ?");
                $stmtUpdCaja->execute([$nuevosIngresos, $codCaja]);

                // D. Actualizar montocredito en creditosxclientes (Legacy Compatibility)
                // Primero obtener montocredito actual
                $stmtCred = $db->prepare("SELECT montocredito FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?");
                $stmtCred->execute([$codCliente, $codSucursal]);
                $credRows = $stmtCred->fetch(PDO::FETCH_ASSOC);
                
                if ($credRows) {
                    $newMontoCredito = max(0, (float)$credRows['montocredito'] - $montoAbono);
                    $stmtUpdCred = $db->prepare("UPDATE creditosxclientes SET montocredito = ? WHERE codcliente = ? AND codsucursal = ?");
                    $stmtUpdCred->execute([$newMontoCredito, $codCliente, $codSucursal]);
                }

                $db->commit();
                echo json_encode(['status' => 'ok', 'msg' => 'Pago realizado correctamente', 'new_balance' => $pendiente - $montoAbono]);
                
            } catch (Exception $ex) {
                $db->rollBack();
                throw $ex;
            }

        } else {
            // FALLBACK OLD SIMULATION Logic (just in case)
            echo json_encode(['error' => 'Action not supported in generic POST']);
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
