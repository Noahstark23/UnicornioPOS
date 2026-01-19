<?php
// Aseguramos que no salga basura antes del PDF
ob_start();

require('fpdf/fpdf.php');
require_once 'includes/db.php';
require_once 'includes/config.php';

$codVenta = $_GET['cod'] ?? '';
if (!$codVenta) die("Error: Ticket no especificado.");

try {
    $db = DB::connect();

    // 0. Obtener Configuración del Sistema (DINÁMICO)
    $sqlConfig = "SELECT cuit, nomsucursal, tlfsucursal, direcsucursal FROM configuracion WHERE id = 1 LIMIT 1";
    $stmtConfig = $db->prepare($sqlConfig);
    $stmtConfig->execute();
    $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);

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

    // Encabezado (DATOS DINÁMICOS)
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(72,5, utf8_decode(strtoupper($config['nomsucursal'] ?? 'POS UNICORNIO')), 0, 1, 'C');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(72,4, "RUC: " . ($config['cuit'] ?? 'N/A'), 0, 1, 'C');
    $pdf->Cell(72,4, utf8_decode($config['direcsucursal'] ?? ''), 0, 1, 'C');
    $pdf->Cell(72,4, "Tel: " . ($config['tlfsucursal'] ?? ''), 0, 1, 'C');
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
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(40, 6, "TOTAL A PAGAR:", 0, 0, 'R');
    $pdf->Cell(32, 6, "C$ " . number_format($venta['totalpago'], 2), 0, 1, 'R');

    // Pie Mejorado
    $pdf->Ln(5);
    $pdf->SetFont('Courier','B',9);
    $pdf->Cell(72, 4, utf8_decode("¡Gracias por su compra!"), 0, 1, 'C');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(72, 4, utf8_decode("Vuelva pronto"), 0, 1, 'C');
    $pdf->Ln(3);
    
    // ========== FOOTER VIRAL (INSTALADOR) ==========
    $pdf->SetFont('Courier','B',7);
    $pdf->SetTextColor(100, 100, 100); // Gris
    $pdf->Cell(72, 3, "Sistema POS Unicornio", 0, 1, 'C');
    $pdf->SetFont('Courier','',7);
    $pdf->Cell(72, 3, utf8_decode("Instalado por: " . INSTALLER_COMPANY), 0, 1, 'C');
    $pdf->Cell(72, 3, "Tel: " . INSTALLER_PHONE, 0, 1, 'C');
    if (defined('INSTALLER_WEBSITE')) {
        $pdf->Cell(72, 3, INSTALLER_WEBSITE, 0, 1, 'C');
    }
    
    // Limpiamos buffer y salimos
    ob_end_clean();
    $pdf->Output('I', 'Ticket.pdf');

} catch (Exception $e) {
    die("Error PDF: " . $e->getMessage());
}
?>
