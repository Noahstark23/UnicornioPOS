<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
// Evitar mostrar errores en el JSON para no romper el frontend
ini_set('display_errors', 0); 
error_reporting(0);

// Iniciar sesión para acceder a datos del usuario
session_start();

// CRITICAL: Check for action parameter FIRST to route correctly
$accion = $_GET['accion'] ?? 'estado';

try {
    $db = DB::connect();
    
    // ========== ENDPOINT: CERRAR CAJA ==========
    if ($accion === 'cerrar') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $efectivoContado = floatval($input['efectivo'] ?? 0);
        $comentarios = $input['comentarios'] ?? '';
        $billetaje = $input['billetaje'] ?? null;
        
        // Buscar caja abierta
        $caja = $db->query("SELECT * FROM arqueocaja WHERE statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1")->fetch();
        
        if (!$caja) throw new Exception("No hay caja abierta");
        
        // 🔒 INICIAR TRANSACCIÓN
        $db->beginTransaction();
        
        try {
            $inicial = floatval($caja['montoinicial']);
            $fechaInicio = $caja['fechaapertura'];
            
            // 1. Ventas en EFECTIVO
            $sqlEfectivo = "SELECT SUM(totalpago) FROM ventas 
                            WHERE fechaventa >= ? 
                            AND statusventa = 'EMITIDA' 
                            AND tipopago = 'CONTADO' 
                            AND formapago = 'EFECTIVO'";
            $stmt = $db->prepare($sqlEfectivo);
            $stmt->execute([$fechaInicio]);
            $ventasEfectivo = floatval($stmt->fetchColumn()) ?: 0;
            
            // 2. Movimientos MANUALES
            $sqlIngresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                            WHERE codarqueo = ? AND tipomovimiento = 'INGRESO'";
            $stmt = $db->prepare($sqlIngresos);
            $stmt->execute([$caja['codarqueo']]);
            $ingresosManuales = floatval($stmt->fetchColumn()) ?: 0;
            
            $sqlEgresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                           WHERE codarqueo = ? AND tipomovimiento = 'EGRESO'";
            $stmt = $db->prepare($sqlEgresos);
            $stmt->execute([$caja['codarqueo']]);
            $egresosManuales = floatval($stmt->fetchColumn()) ?: 0;
            
            // ⭐ FÓRMULA MAESTRA DE CIERRE
            // NOTA: abonos removidos temporalmente hasta agregar column formapago a tabla
            $esperado = $inicial 
                      + $ventasEfectivo 
                      + $ingresosManuales 
                      - $egresosManuales;
            
            $diferencia = $efectivoContado - $esperado;
            
            // OPERACIÓN 1: Actualizar arqueocaja
            $sqlUpdate = "UPDATE arqueocaja SET 
                          dineroefectivo = ?,
                          diferencia = ?,
                          comentarios = ?,
                          fechacierre = NOW(),
                          statusarqueo = 0
                          WHERE codarqueo = ?";
            $db->prepare($sqlUpdate)->execute([$efectivoContado, $diferencia, $comentarios, $caja['codarqueo']]);
            
            // OPERACIÓN 2: Guardar historial (verificar si existe tabla primero)
            try {
                // Obtener nombre del cajero de la sesión
                $nombreCajero = $_SESSION['nombre_usuario'] ?? $_SESSION['usuario'] ?? $_SESSION['nombre'] ?? 'Cajero';
                $idCajero = $_SESSION['id_usuario'] ?? $_SESSION['acceso'] ?? 0;
                $tipoDif = ($diferencia > 0) ? 'SOBRA' : (($diferencia < 0) ? 'FALTA' : 'EXACTO');
                
                $sqlHistorial = "INSERT INTO historial_cierres 
                                 (tenant_id, codarqueo, codresponsable, nombre_cajero, fecha_cierre, diferencia, tipo_diferencia, comentarios, billetaje)
                                 VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
                $db->prepare($sqlHistorial)->execute([
                    1,
                    $caja['codarqueo'],
                    $idCajero,
                    $nombreCajero,
                    $diferencia,
                    $tipoDif,
                    $comentarios,
                    $billetaje ? json_encode($billetaje) : null
                ]);
            } catch (Exception $e) {
                // Si la tabla no existe, continuamos sin error
                // TODO: Notificar al log pero no fallar el cierre
            }
            
            // ✅ TODO BIEN: Confirmar transacción
            $db->commit();
            
            echo json_encode([
                'status' => 'ok',
                'codarqueo' => $caja['codarqueo'],
                'diferencia' => $diferencia
            ]);
            
        } catch (Exception $e) {
            // ❌ ERROR: Deshacer TODO
            $db->rollBack();
            throw $e;
        }
        
        exit;
    }
    
    // ========== ENDPOINT: ABRIR CAJA ==========
    if ($accion === 'abrir') {
        $input = json_decode(file_get_contents('php://input'), true);
        $monto = floatval($input['monto'] ?? 0);
        
        if ($monto < 0) throw new Exception("Monto inválido");
        
        // Verificar si ya hay una caja abierta
        $cajaAbierta = $db->query("SELECT codarqueo FROM arqueocaja WHERE statusarqueo = 1")->fetch();
        if ($cajaAbierta) throw new Exception("Ya existe una caja abierta");
        
        $sql = "INSERT INTO arqueocaja (montoinicial, fechaapertura, statusarqueo) VALUES (?, NOW(), 1)";
        $db->prepare($sql)->execute([$monto]);
        
        echo json_encode(['status' => 'ok', 'codarqueo' => $db->lastInsertId()]);
        exit;
    }
    
    // ========== ENDPOINT: MOVIMIENTO ==========
    if ($accion === 'movimiento') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $tipo = $input['tipo'] ?? '';
        $monto = floatval($input['monto'] ?? 0);
        $descripcion = $input['descripcion'] ?? '';
        
        // Buscar caja abierta
        $caja = $db->query("SELECT codarqueo FROM arqueocaja WHERE statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1")->fetch();
        
        if (!$caja) throw new Exception("No hay caja abierta");
        if ($monto <= 0) throw new Exception("Monto inválido");
        if (!in_array($tipo, ['INGRESO', 'EGRESO'])) throw new Exception("Tipo inválido");
        
        $sql = "INSERT INTO movimientos_caja (codarqueo, tipomovimiento, monto, descripcion, fechamovimiento) 
                VALUES (?, ?, ?, ?, NOW())";
        $db->prepare($sql)->execute([$caja['codarqueo'], $tipo, $monto, $descripcion]);
        
        echo json_encode(['status' => 'ok']);
        exit;
    }
    
    // ========== ENDPOINT: ESTADO (DEFAULT) ==========
    // 1. Buscar ÚLTIMA caja abierta
    $sql = "SELECT codarqueo, montoinicial, fechaapertura, statusarqueo 
            FROM arqueocaja 
            WHERE statusarqueo = 1 
            ORDER BY codarqueo DESC LIMIT 1";
    $caja = $db->query($sql)->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        // Si no hay caja abierta, simplemente retornamos estado CERRADA
        echo json_encode(['estado' => 'CERRADA']);
        exit;
    }

    // 2. Calcular Ventas del Turno POR TIPO Y FORMA DE PAGO
    $fechaInicio = $caja['fechaapertura'];
    
    // Ventas al CONTADO en EFECTIVO (dinero físico en caja)
    $sqlEfectivo = "SELECT SUM(totalpago) 
                    FROM ventas 
                    WHERE fechaventa >= ? 
                    AND statusventa = 'EMITIDA' 
                    AND tipopago = 'CONTADO'
                    AND formapago = 'EFECTIVO'";
    $stmt = $db->prepare($sqlEfectivo);
    $stmt->execute([$fechaInicio]);
    $ventasEfectivo = floatval($stmt->fetchColumn()) ?: 0.00;
    
    // Ventas al CRÉDITO (por cobrar, NO en caja física)
    $sqlCredito = "SELECT SUM(totalpago) 
                   FROM ventas 
                   WHERE fechaventa >= ? 
                   AND statusventa = 'EMITIDA' 
                   AND tipopago = 'CREDITO'";
    $stmt = $db->prepare($sqlCredito);
    $stmt->execute([$fechaInicio]);
    $ventasCredito = floatval($stmt->fetchColumn()) ?: 0.00;
    
    // Ventas al CONTADO con TARJETA/TRANSFERENCIA (en banco, NO en caja física)
    $sqlTarjeta = "SELECT SUM(totalpago) 
                   FROM ventas 
                   WHERE fechaventa >= ? 
                   AND statusventa = 'EMITIDA' 
                   AND tipopago = 'CONTADO'
                   AND formapago IN ('TARJETA', 'TRANSFERENCIA')";
    $stmt = $db->prepare($sqlTarjeta);
    $stmt->execute([$fechaInicio]);
    $ventasTarjeta = floatval($stmt->fetchColumn()) ?: 0.00;

    // 3. Movimientos MANUALES
    $sqlIngresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                    WHERE codarqueo = ? AND tipomovimiento = 'INGRESO'";
    $stmt = $db->prepare($sqlIngresos);
    $stmt->execute([$caja['codarqueo']]);
    $ingresosManuales = floatval($stmt->fetchColumn()) ?: 0;
    
    $sqlEgresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                   WHERE codarqueo = ? AND tipomovimiento = 'EGRESO'";
    $stmt = $db->prepare($sqlEgresos);
    $stmt->execute([$caja['codarqueo']]);
    $egresosManuales = floatval($stmt->fetchColumn()) ?: 0;

    $inicial = floatval($caja['montoinicial']);
    $totalVentas = $ventasEfectivo + $ventasCredito + $ventasTarjeta;
    
    // ⭐ FÓRMULA MAESTRA: Total esperado incluye TODOS los movimientos de efectivo
    // NOTA: abonos removidos temporalmente hasta agregar column formapago a tabla abonoscreditosventas
    $totalEsperado = $inicial + $ventasEfectivo + $ingresosManuales - $egresosManuales;

    // 5. Respuesta JSON con DESGLOSE COMPLETO
    echo json_encode([
        'estado' => 'ABIERTA',
        'info' => [
            'codarqueo' => $caja['codarqueo'],
            'fechaapertura' => $fechaInicio
        ],
        'calculos' => [
            'inicial' => $inicial,
            'ventas_efectivo' => $ventasEfectivo,
            'ventas_credito' => $ventasCredito,
            'ventas_tarjeta' => $ventasTarjeta,
            'total_ventas' => $totalVentas,
            'ingresos' => $ingresosManuales,
            'egresos' => $egresosManuales,
            'abonos' => 0, // TODO: Agregar column formapago a abonoscreditosventas
            'esperado' => $totalEsperado
        ]
    ]);

} catch (Exception $e) {
    // En caso de error de BD, devolvemos un JSON de error controlado
    echo json_encode(['estado' => 'ERROR', 'mensaje' => $e->getMessage()]);
}
?>
