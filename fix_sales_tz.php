<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

$db = DB::connect();

    echo "--- DIAGNÓSTICO DETALLADO ---\n";
    echo "BD NOW(): " . $db->query("SELECT NOW()")->fetchColumn() . "\n";
    echo "BD TIMEZONE: " . $db->query("SELECT @@time_zone")->fetchColumn() . "\n";
    echo "BD SYSTEM TIMEZONE: " . $db->query("SELECT @@system_time_zone")->fetchColumn() . "\n";
    
    echo "\nÚLTIMAS 5 VENTAS:\n";
    $stmt = $db->query("SELECT idventa, fechaventa, totalpago FROM ventas ORDER BY idventa DESC LIMIT 5");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
       echo "ID: {$row['idventa']} | Fecha: {$row['fechaventa']} | $ {$row['totalpago']}\n";
    }

    echo "\nPrueba de comparación:\n";
    $test = $db->query("SELECT count(*) FROM ventas WHERE fechaventa > NOW()")->fetchColumn();
    echo "Ventas > NOW(): $test\n";
    
    // Si hay ventas 'futuras' (erróneas), aplicar fix forzado a los últimos IDs
    if ($test > 0) {
        // ... fix manual ...
        $db->exec("UPDATE ventas SET fechaventa = DATE_ADD(fechaventa, INTERVAL -8 HOUR) WHERE fechaventa > NOW()");
        echo "FIX APLICADO: Ventas > NOW actualizadas -8H.\n";
    }
?>
