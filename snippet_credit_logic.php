<?php
require_once '../includes/db.php';

// ... (existing includes)

// START OF CREDIT LOGIC
if ($input['tipopago'] === 'CREDITO') {
    $montoCredito = $total;
    
    // 1. Actualizar Arqueo de Caja (Registro de Salida a Crédito)
    $stmtArqueo = $db->prepare("UPDATE arqueocaja SET creditos = creditos + ? WHERE codcaja = ? AND statusarqueo = 1");
    $stmtArqueo->execute([$montoCredito, $codCaja]);

    // 2. Actualizar Saldo del Cliente (creditosxclientes)
    // Verificar si existe registro
    $stmtCheck = $db->prepare("SELECT codcredito FROM creditosxclientes WHERE codcliente = ? AND codsucursal = ?");
    $stmtCheck->execute([$input['cliente_id'], $codSucursal]);
    $existeCredito = $stmtCheck->fetchColumn();

    if ($existeCredito) {
        $stmtUpd = $db->prepare("UPDATE creditosxclientes SET montocredito = montocredito + ? WHERE codcliente = ? AND codsucursal = ?");
        $stmtUpd->execute([$montoCredito, $input['cliente_id'], $codSucursal]);
    } else {
        $stmtIns = $db->prepare("INSERT INTO creditosxclientes (codcliente, montocredito, codsucursal) VALUES (?, ?, ?)");
        $stmtIns->execute([$input['cliente_id'], $montoCredito, $codSucursal]);
    }
} elseif ($input['tipopago'] === 'CONTADO') {
     // Si es contado, suma a INGRESOS en Arqueo (si es efectivo, tarjeta, etc lo manejas global o específico)
     // Por simplicidad sumamos a ingresos generales
     // $stmtIngreso = $db->prepare("UPDATE arqueocaja SET ingresos = ingresos + ? WHERE codcaja = ? AND statusarqueo = 1");
     // $stmtIngreso->execute([$total, $codCaja]);
     // (Omitido por ahora para no alterar lógica existente de caja sin petición explicita, 
     // pero credditos es CRITICO porque si no no aparece la deuda)
}
// END OF CREDIT LOGIC
?>
