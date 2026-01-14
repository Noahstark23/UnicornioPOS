<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cierre de Caja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-white p-8">
    
<?php
require_once 'includes/db.php';

// Obtener codarqueo
$codarqueo = intval($_GET['codarqueo'] ?? 0);

if ($codarqueo === 0) {
    die("<h1>Error: No se especificó un código de arqueo válido.</h1>");
}

try {
    $db = DB::connect();
    
    // 1. Obtener datos del cierre
    $sqlCierre = "SELECT * FROM arqueocaja WHERE codarqueo = ?";
    $stmt = $db->prepare($sqlCierre);
    $stmt->execute([$codarqueo]);
    $cierre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cierre) {
        die("<h1>Error: No se encontró el cierre especificado.</h1>");
    }
    
    $inicial = floatval($cierre['montoinicial']);
    $efectivoContado = floatval($cierre['dineroefectivo']);
    $diferencia = floatval($cierre['diferencia']);
    $comentarios = $cierre['comentarios'];
    $fechaInicio = $cierre['fechaapertura'];
    $fechaCierre = $cierre['fechacierre'];
    
    // 2. Calcular componentes de la fórmula maestra
    
    // Ventas en efectivo
    $sqlEfectivo = "SELECT SUM(totalpago) FROM ventas 
                    WHERE fechaventa >= ? AND fechaventa <= ?
                    AND statusventa = 'EMITIDA' 
                    AND tipopago = 'CONTADO' 
                    AND formapago = 'EFECTIVO'";
    $stmt = $db->prepare($sqlEfectivo);
    $stmt->execute([$fechaInicio, $fechaCierre]);
    $ventasEfectivo = floatval($stmt->fetchColumn()) ?: 0;
    
    // Ventas a crédito (informativo, no suman al esperado)
    $sqlCredito = "SELECT SUM(totalpago) FROM ventas 
                   WHERE fechaventa >= ? AND fechaventa <= ?
                   AND statusventa = 'EMITIDA' 
                   AND tipopago = 'CREDITO'";
    $stmt = $db->prepare($sqlCredito);
    $stmt->execute([$fechaInicio, $fechaCierre]);
    $ventasCredito = floatval($stmt->fetchColumn()) ?: 0;
    
    // Ventas con tarjeta (informativo, no suman al esperado)
    $sqlTarjeta = "SELECT SUM(totalpago) FROM ventas 
                   WHERE fechaventa >= ? AND fechaventa <= ?
                   AND statusventa = 'EMITIDA' 
                   AND tipopago = 'CONTADO'
                   AND formapago IN ('TARJETA', 'TRANSFERENCIA')";
    $stmt = $db->prepare($sqlTarjeta);
    $stmt->execute([$fechaInicio, $fechaCierre]);
    $ventasTarjeta = floatval($stmt->fetchColumn()) ?: 0;
    
    // Ingresos manuales
    $sqlIngresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                    WHERE codarqueo = ? AND tipomovimiento = 'INGRESO'";
    $stmt = $db->prepare($sqlIngresos);
    $stmt->execute([$codarqueo]);
    $ingresos = floatval($stmt->fetchColumn()) ?: 0;
    
    // Egresos manuales
    $sqlEgresos = "SELECT IFNULL(SUM(monto), 0) FROM movimientos_caja 
                   WHERE codarqueo = ? AND tipomovimiento = 'EGRESO'";
    $stmt = $db->prepare($sqlEgresos);
    $stmt->execute([$codarqueo]);
    $egresos = floatval($stmt->fetchColumn()) ?: 0;
    
    // Abonos de créditos (Recuperado)
    $codCajaFisica = $cierre['codcaja'];
    
    // 1. Total Abonos
    $sqlAbonos = "SELECT IFNULL(SUM(montoabono), 0) FROM abonoscreditosventas 
                  WHERE codcaja = ? AND fechaabono >= ? AND fechaabono <= ?";
    $stmt = $db->prepare($sqlAbonos);
    $stmt->execute([$codCajaFisica, $fechaInicio, $fechaCierre ?: date('Y-m-d H:i:s')]); // Si fechaCierre es null (abierta), usar NOW
    $abonos = floatval($stmt->fetchColumn()) ?: 0;

    // 2. Detalle de Abonos (para reporte detallado)
    // CORRECCION: codfactura no existe, usamos codventa como referencia
    // Agregamos statusventa y totalpago para determinar si fue cancelación o abono
    $sqlDetalleAbonos = "SELECT a.fechaabono, c.nomcliente, a.montoabono, v.codventa, v.statusventa, v.totalpago 
                         FROM abonoscreditosventas a 
                         INNER JOIN clientes c ON a.codcliente = c.codcliente 
                         INNER JOIN ventas v ON a.codventa = v.codventa 
                         WHERE a.codcaja = ? AND a.fechaabono >= ? AND a.fechaabono <= ?
                         ORDER BY a.fechaabono DESC";
    $stmt = $db->prepare($sqlDetalleAbonos);
    $stmt->execute([$codCajaFisica, $fechaInicio, $fechaCierre ?: date('Y-m-d H:i:s')]);
    $listaAbonos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // FÓRMULA MAESTRA RECALCULADA
    // Recalculamos todo para corregir inconsistencias históricas en la BD (donde no se sumaban abonos)
    $esperado = $inicial + $ventasEfectivo + $ingresos + $abonos - $egresos;
    
    // CORRECCIÓN VISUAL: Recalcular diferencia basada en el esperado CORREGIDO
    // Esto evita que el reporte diga "Esperado: 588, Contado: 588, Diferencia: 88" (error legacy)
    $diferencia = $efectivoContado - $esperado;
    
    // Obtener historial del cierre (si existe)
    $historial = null;
    try {
        $sqlHistorial = "SELECT * FROM historial_cierres WHERE codarqueo = ?";
        $stmt = $db->prepare($sqlHistorial);
        $stmt->execute([$codarqueo]);
        $historial = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Tabla no existe o error
    }
    
} catch (Exception $e) {
    die("<h1>Error de Base de Datos: " . htmlspecialchars($e->getMessage()) . "</h1>");
}

// Función helper para formatear dinero
function formatMoney($amount) {
    return 'C$ ' . number_format(floatval($amount), 2, '.', ',');
}
?>

    <!-- HEADER -->
    <div class="text-center border-b-2 border-gray-800 pb-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-1">🦄 UNICORNIO POS</h1>
        <h2 class="text-xl font-bold text-gray-700">REPORTE DE CIERRE DE CAJA</h2>
        <p class="text-sm text-gray-600 mt-2">Arqueo #<?php echo $codarqueo; ?></p>
    </div>

    <!-- INFORMACIÓN GENERAL -->
    <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Fecha Apertura</p>
            <p class="font-bold text-gray-800"><?php echo date('d/m/Y H:i:s', strtotime($fechaInicio)); ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Fecha Cierre</p>
            <p class="font-bold text-gray-800"><?php echo date('d/m/Y H:i:s', strtotime($fechaCierre)); ?></p>
        </div>
        <?php if ($historial): ?>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Cajero</p>
            <p class="font-bold text-gray-800"><?php echo htmlspecialchars($historial['nombre_cajero']); ?></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Tipo Diferencia</p>
            <p class="font-bold <?php echo $diferencia > 0 ? 'text-green-600' : ($diferencia < 0 ? 'text-red-600' : 'text-blue-600'); ?>">
                <?php echo htmlspecialchars($historial['tipo_diferencia']); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <!-- FÓRMULA MAESTRA DE CIERRE -->
    <div class="border-2 border-blue-600 rounded-lg p-5 mb-6 bg-blue-50">
        <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center gap-2">
            📊 FÓRMULA MAESTRA - CÁLCULO DE EFECTIVO ESPERADO
        </h3>
        
        <table class="w-full text-sm">
            <tr class="border-b border-blue-200">
                <td class="py-2 text-gray-700">Fondo Inicial</td>
                <td class="text-right font-bold"><?php echo formatMoney($inicial); ?></td>
            </tr>
            <tr class="border-b border-blue-200">
                <td class="py-2 text-gray-700">+ Ventas en Efectivo</td>
                <td class="text-right font-bold text-green-600"><?php echo formatMoney($ventasEfectivo); ?></td>
            </tr>
            <?php if ($abonos > 0): ?>
            <tr class="border-b border-blue-200">
                <td class="py-2 text-gray-700">+ Abonos a Créditos (Efectivo)</td>
                <td class="text-right font-bold text-green-600"><?php echo formatMoney($abonos); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($ingresos > 0): ?>
            <tr class="border-b border-blue-200">
                <td class="py-2 text-gray-700">+ Ingresos Manuales</td>
                <td class="text-right font-bold text-green-600"><?php echo formatMoney($ingresos); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($egresos > 0): ?>
            <tr class="border-b border-blue-200">
                <td class="py-2 text-gray-700">- Egresos (Gastos/Salidas)</td>
                <td class="text-right font-bold text-red-600"><?php echo formatMoney($egresos); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="border-t-2 border-blue-600 bg-blue-100">
                <td class="py-3 font-extrabold text-blue-900">= EFECTIVO ESPERADO</td>
                <td class="text-right font-extrabold text-blue-900 text-lg"><?php echo formatMoney($esperado); ?></td>
            </tr>
        </table>
    </div>

    <!-- DETALLE DE ABONOS (Nuevo) -->
    <?php if (count($listaAbonos) > 0): ?>
    <div class="border border-indigo-300 bg-indigo-50 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-bold text-indigo-800 mb-3">🧾 DETALLE DE ABONOS RECIBIDOS (CRÉDITOS QUE PAGARON)</h3>
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="border-b border-indigo-200 text-indigo-600">
                    <th class="pb-2">Hora</th>
                    <th class="pb-2">Cliente</th>
                    <th class="pb-2">Ref. / Estado</th>
                    <th class="pb-2 text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaAbonos as $abono): ?>
                <?php 
                    // Determinar etiqueta
                    $esCancelacion = ($abono['statusventa'] === 'PAGADA');
                    $etiqueta = $esCancelacion ? "CANCELACIÓN (FINAL)" : "ABONO PARCIAL";
                    $estilo = $esCancelacion ? "text-green-600 font-bold" : "text-orange-500";
                ?>
                <tr class="border-b border-indigo-100">
                    <td class="py-1 text-gray-600"><?php echo date('H:i', strtotime($abono['fechaabono'])); ?></td>
                    <td class="py-1 font-bold text-gray-800"><?php echo htmlspecialchars($abono['nomcliente']); ?></td>
                    <td class="py-1 text-xs">
                        Ref: #<?php echo $abono['codventa']; ?> <br>
                        <span class="<?php echo $estilo; ?>"><?php echo $etiqueta; ?></span>
                    </td>
                    <td class="py-1 text-right font-bold text-indigo-700"><?php echo formatMoney($abono['montoabono']); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="border-t border-indigo-300">
                    <td colspan="3" class="pt-2 text-right font-bold text-indigo-900">TOTAL RECUPERADO:</td>
                    <td class="pt-2 text-right font-bold text-indigo-900 text-sm"><?php echo formatMoney($abonos); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- RESULTADO DEL CONTEO -->
    <div class="border-2 <?php echo $diferencia >= 0 ? 'border-green-600 bg-green-50' : 'border-red-600 bg-red-50'; ?> rounded-lg p-5 mb-6">
        <h3 class="text-lg font-bold <?php echo $diferencia >= 0 ? 'text-green-800' : 'text-red-800'; ?> mb-4">
            💰 RESULTADO DEL CONTEO FÍSICO
        </h3>
        
        <table class="w-full text-sm">
            <tr class="border-b <?php echo $diferencia >= 0 ? 'border-green-200' : 'border-red-200'; ?>">
                <td class="py-2 text-gray-700">Efectivo Contado</td>
                <td class="text-right font-bold"><?php echo formatMoney($efectivoContado); ?></td>
            </tr>
            <tr class="border-b <?php echo $diferencia >= 0 ? 'border-green-200' : 'border-red-200'; ?>">
                <td class="py-2 text-gray-700">Efectivo Esperado</td>
                <td class="text-right font-bold"><?php echo formatMoney($esperado); ?></td>
            </tr>
            <tr class="border-t-2 <?php echo $diferencia >= 0 ? 'border-green-600 bg-green-100' : 'border-red-600 bg-red-100'; ?>">
                <td class="py-3 font-extrabold <?php echo $diferencia >= 0 ? 'text-green-900' : 'text-red-900'; ?>">
                    <?php echo $diferencia >= 0 ? '✅ SOBRA' : '❌ FALTA'; ?>
                </td>
                <td class="text-right font-extrabold <?php echo $diferencia >= 0 ? 'text-green-900' : 'text-red-900'; ?> text-xl">
                    <?php echo formatMoney(abs($diferencia)); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- VENTAS NO INCLUIDAS EN CAJA FÍSICA -->
    <div class="border border-orange-300 bg-orange-50 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-bold text-orange-700 mb-3">ℹ️ VENTAS NO INCLUIDAS EN CAJA FÍSICA</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600">Ventas a Crédito (por cobrar)</p>
                <p class="font-bold text-gray-800"><?php echo formatMoney($ventasCredito); ?></p>
            </div>
            <div>
                <p class="text-gray-600">Ventas con Tarjeta (en banco)</p>
                <p class="font-bold text-gray-800"><?php echo formatMoney($ventasTarjeta); ?></p>
            </div>
        </div>
        <div class="mt-3 pt-3 border-t border-orange-200">
            <p class="text-gray-600 text-sm">Total Ventas del Turno (todas las formas)</p>
            <p class="font-bold text-gray-800 text-lg"><?php echo formatMoney($ventasEfectivo + $ventasCredito + $ventasTarjeta); ?></p>
        </div>
    </div>

    <!-- COMENTARIOS -->
    <?php if (!empty($comentarios)): ?>
    <div class="border border-gray-300 bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="text-sm font-bold text-gray-700 mb-2">📝 OBSERVACIONES DEL CAJERO</h3>
        <p class="text-gray-800 text-sm whitespace-pre-wrap"><?php echo htmlspecialchars($comentarios); ?></p>
    </div>
    <?php endif; ?>

    <!-- FIRMAS -->
    <div class="grid grid-cols-2 gap-8 mt-12 pt-8 border-t-2 border-gray-400">
        <div class="text-center">
            <div class="border-t border-gray-800 pt-2 mt-16">
                <p class="font-bold text-gray-700">Cajero Responsable</p>
                <?php if ($historial): ?>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($historial['nombre_cajero']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center">
            <div class="border-t border-gray-800 pt-2 mt-16">
                <p class="font-bold text-gray-700">Supervisor / Gerente</p>
                <p class="text-sm text-gray-600">Firma y Sello</p>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="text-center text-xs text-gray-500 mt-8 pt-4 border-t border-gray-300">
        <p>Reporte generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p class="mt-1">🦄 Unicornio POS - Sistema de Punto de Venta</p>
    </div>

    <!-- BOTÓN IMPRIMIR -->
    <div class="no-print mt-6 text-center">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
            🖨️ Imprimir Reporte
        </button>
        <button onclick="window.close()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-8 rounded-lg shadow ml-4">
            ✕ Cerrar
        </button>
    </div>

    <script>
        // Auto-abrir diálogo de impresión al cargar (opcional)
        // window.onload = () => window.print();
    </script>

</body>
</html>
