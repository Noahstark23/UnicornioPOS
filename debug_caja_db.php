<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

$db = DB::connect();

echo "=== ÚLTIMOS 5 ARQUEOS ===\n";
$sql = "SELECT codarqueo, fechaapertura, fechacierre, statusarqueo, montoinicial, dineroefectivo FROM arqueocaja ORDER BY codarqueo DESC LIMIT 5";
$stmt = $db->query($sql);
$arqueos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($arqueos as $a) {
    echo "ID: {$a['codarqueo']} | Apertura: {$a['fechaapertura']} | Cierre: " . ($a['fechacierre'] ?? 'NULL') . " | Status: {$a['statusarqueo']} | Inicial: {$a['montoinicial']}\n";
}

echo "\n=== ÚLTIMAS 10 VENTAS ===\n";
$sql = "SELECT codventa, fechaventa, totalpago, tipopago, statusventa FROM ventas ORDER BY idventa DESC LIMIT 10";
$stmt = $db->query($sql);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($ventas as $v) {
    echo "Venta: {$v['codventa']} | Fecha: {$v['fechaventa']} | Pago: {$v['tipopago']} | Total: {$v['totalpago']} | Status: {$v['statusventa']}\n";
}

echo "\n=== DEBUG LOGIC ===\n";
// Simular lógica de caja_control
$cajaAbierta = $db->query("SELECT * FROM arqueocaja WHERE statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($cajaAbierta) {
    echo "Caja Abierta ID: {$cajaAbierta['codarqueo']}\n";
    $fechaInicio = $cajaAbierta['fechaapertura'];
    echo "Fecha Apertura (Base): $fechaInicio\n";
    
    $sqlUltimoCierre = "SELECT fechacierre FROM arqueocaja 
                        WHERE statusarqueo = 0 
                        AND fechacierre IS NOT NULL
                        AND fechacierre < ?
                        ORDER BY fechacierre DESC 
                        LIMIT 1";
    $stmt = $db->prepare($sqlUltimoCierre);
    $stmt->execute([$fechaInicio]);
    $ultimoCierre = $stmt->fetchColumn();
    
    echo "Último Cierre Encontrado: " . ($ultimoCierre ? $ultimoCierre : 'NINGUNO') . "\n";
    
    if ($ultimoCierre) {
        $fechaInicioData = $ultimoCierre; // La variable real usada
        echo "Fecha Inicio Ajustada (Usando Cierre): $fechaInicioData\n";
    } else {
        echo "Fecha Inicio (Original): $fechaInicio\n";
        $fechaInicioData = $fechaInicio;
    }

    // Consulta de prueba
    $sqlCount = "SELECT COUNT(*), SUM(totalpago) FROM ventas WHERE fechaventa > ? AND statusventa = 'EMITIDA' AND tipopago = 'CONTADO' AND formapago = 'EFECTIVO'";
    $stmt = $db->prepare($sqlCount);
    $stmt->execute([$fechaInicioData]);
    $res = $stmt->fetch(PDO::FETCH_NUM);
    echo "Ventas contadas con esta fecha: Cant={$res[0]}, Total={$res[1]}\n";
} else {
    echo "No hay caja abierta actualmente.\n";
}
?>
