<?php
// Aseguramos que no salga basura antes del PDF
ob_start();

require('fpdf/fpdf.php');
require_once 'includes/db.php';

$codVenta = $_GET['cod'] ?? '';
if (!$codVenta) die("Error: Ticket no especificado.");

try {
    $db = DB::connect();

    // 1. Obtener Cabecera (Venta + Cliente)
    $sqlHead = "SELECT v.*, c.nomcliente 
                FROM ventas v 
                LEFT JOIN clientes c ON v.codcliente = c.codcliente
                WHERE v.codventa = ?";
    $stmt = $db->prepare($sqlHead);
    $stmt->execute([$codVenta]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) die("Error: Venta no encontrada.");

    // 2. Obtener Detalles (CON JOIN CORRECTO)
    // Unimos detalleventas con productos para sacar el nombre
    $sqlDet = "SELECT d.cantventa, d.precioventa, (d.cantventa * d.precioventa) as importe, p.producto as nombre_prod
               FROM detalleventas d 
               LEFT JOIN productos p ON d.codproducto = p.codproducto
               WHERE d.codventa = ?";
    $stmtDet = $db->prepare($sqlDet);
    $stmtDet->execute([$codVenta]);
    $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

    // --- GENERAR PDF ---
    $pdf = new FPDF('P','mm',array(80, 200)); // 80mm ancho (Ticket)
    $pdf->AddPage();
    $pdf->SetMargins(4, 4, 4);
    $pdf->SetAutoPageBreak(true, 5);

    // Encabezado
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(72,5,"POS UNICORNIO",0,1,'C');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(72,4,"RUC: J0310000000000",0,1,'C');
    $pdf->Cell(72,4,"----------------------------------------",0,1,'C');

    // Info Venta
    $pdf->Cell(72,4,"Ticket: " . $venta['codventa'],0,1,'L');
    $pdf->Cell(72,4,"Fecha:  " . date('d/m/Y H:i', strtotime($venta['fechaventa'])),0,1,'L');
    $pdf->Cell(72,4,"Cliente: " . utf8_decode(substr($venta['nomcliente'] ?? 'Publico General', 0, 20)),0,1,'L');
    $pdf->Cell(72,4,"Tipo:    " . $venta['tipopago'],0,1,'L');
    $pdf->Cell(72,4,"Metodo:  " . $venta['formapago'],0,1,'L');
    $pdf->Ln(2);

    // Tabla Productos
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(8, 4, "Can", 0, 0, 'C');
    $pdf->Cell(42, 4, "Producto", 0, 0, 'L');
    $pdf->Cell(22, 4, "Total", 0, 1, 'R');
    $pdf->Cell(72, 1, "----------------------------------------", 0, 1, 'C');

    $pdf->SetFont('Courier','',8);
    
    foreach ($detalles as $row) {
        $cant = $row['cantventa'];
        $nombre = utf8_decode(substr($row['nombre_prod'] ?? 'Item sin nombre', 0, 22));
        $total = number_format($row['importe'], 2);

        $pdf->Cell(8, 4, $cant, 0, 0, 'C');
        $pdf->Cell(42, 4, $nombre, 0, 0, 'L');
        $pdf->Cell(22, 4, $total, 0, 1, 'R');
    }

    $pdf->Ln(2);
    $pdf->Cell(72, 1, "----------------------------------------", 0, 1, 'C');

    // Totales
    $pdf->Cell(40, 6, "TOTAL A PAGAR:", 0, 0, 'R');
    $pdf->Cell(32, 6, "C$ " . number_format($venta['totalpago'], 2), 0, 1, 'R');

    // Sección de Pago (si hay datos)
    if (!empty($venta['pagorecibido']) && $venta['pagorecibido'] > 0) {
        $pdf->Ln(2);
        $pdf->Cell(72, 1, "----------------------------------------", 0, 1, 'C');
        $pdf->SetFont('Courier','B',9);
        $pdf->Cell(72, 4, "DETALLES DE PAGO", 0, 1, 'C');
        $pdf->Cell(72, 1, "----------------------------------------", 0, 1, 'C');
        
        $pdf->SetFont('Courier','',8);
        $pdf->Cell(40, 4, "Recibido:", 0, 0, 'R');
        $pdf->Cell(32, 4, "C$ " . number_format($venta['pagorecibido'], 2), 0, 1, 'R');
        
        if (!empty($venta['cambio']) && $venta['cambio'] > 0) {
            $pdf->SetFont('Courier','B',9);
            $pdf->Cell(40, 5, "Cambio:", 0, 0, 'R');
            $pdf->Cell(32, 5, "C$ " . number_format($venta['cambio'], 2), 0, 1, 'R');
        } else {
            $pdf->SetFont('Courier','',8);
            $pdf->Cell(72, 4, utf8_decode("✓ Pago Exacto"), 0, 1, 'C');
        }
    }

    // Pie mejorado
    $pdf->Ln(5);
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(72, 4, utf8_decode("¡Gracias por su compra!"), 0, 1, 'C');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(72, 4, utf8_decode("Vuelva pronto"), 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->Cell(72, 3, "www.gruponortex.com", 0, 1, 'C');
    
    // Limpiamos buffer y salimos
    ob_end_clean();
    $pdf->Output('I', 'Ticket.pdf');

} catch (Exception $e) {
    die("Error PDF: " . $e->getMessage());
}
?>
