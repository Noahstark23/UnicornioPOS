<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

date_default_timezone_set('America/Managua'); // IMPORTANTE

try {
    $db = DB::connect();
    $hoy = date('Y-m-d');

    // 1. VENTAS HOY (Usa LIKE para ignorar la hora y status flexible)
    $sqlVentas = "SELECT SUM(totalpago) as total, COUNT(*) as cantidad 
                  FROM ventas 
                  WHERE fechaventa LIKE ? AND tenant_id = 1";
    $stmt = $db->prepare($sqlVentas);
    $stmt->execute(["$hoy%"]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalVentas = $res['total'] ?? 0;

    // 2. CAJA (Inicial + Ventas - Egresos)
    $sqlCaja = "SELECT montoinicial, ingresos, egresos FROM arqueocaja 
                WHERE codcaja = 1 AND statusarqueo = 1 ORDER BY codarqueo DESC LIMIT 1";
    $caja = $db->query($sqlCaja)->fetch(PDO::FETCH_ASSOC);
    
    $enCaja = 0;
    if($caja) {
        $enCaja = $caja['montoinicial'] + $totalVentas + ($caja['ingresos']??0) - ($caja['egresos']??0);
    }

    // 3. GRÁFICO
    $grafico = [];
    for($i=6; $i>=0; $i--){
        $f = date('Y-m-d', strtotime("-$i days"));
        $stmtG = $db->prepare("SELECT SUM(totalpago) FROM ventas WHERE fechaventa LIKE ?");
        $stmtG->execute(["$f%"]);
        $grafico[] = ['fecha' => date('d/m', strtotime($f)), 'total' => $stmtG->fetchColumn() ?: 0];
    }

    // 4. STOCK BAJO
    $stock = $db->query("SELECT COUNT(*) FROM productos WHERE existencia <= 5")->fetchColumn();

    echo json_encode([
        'ventas_formateadas' => 'C$ ' . number_format($totalVentas, 2),
        'transacciones' => $res['cantidad'] ?? 0,
        'caja_formateada' => 'C$ ' . number_format($enCaja, 2),
        'stock_bajo' => $stock,
        'grafico' => $grafico
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
