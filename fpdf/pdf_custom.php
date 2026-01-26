<?php
/**
 * PDF_Custom - Extensión de la clase PDF con datos dinámicos
 * 
 * Esta clase sobrescribe las funciones de fpdf/pdf.php que tienen datos hardcoded,
 * reemplazándolos con configuración dinámica de la tabla 'configuracion' y
 * footer viral del instalador.
 * 
 * Funciones sobrescritas:
 * - FacturaVenta(): Factura formal A4 para ventas
 * - TicketVenta(): Ticket térmico (si es necesario)
 */

require_once 'pdf.php';

class PDF_Custom extends PDF {
    
    /**
     * Footer personalizado con marca del instalador
     * Se ejecuta automáticamente en cada página
     */
    function Footer() {
        // Variables globales con datos del instalador
        $installer = $GLOBALS['installer_info'] ?? [];
        
        // Posicionarnos a 15mm del final
        $this->SetY(-15);
        
        // Línea separadora
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        
        $this->Ln(2);
        
        // Footer viral del instalador
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 4, utf8_decode('Sistema POS Unicornio'), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 7);
        if (!empty($installer['company'])) {
            $this->Cell(0, 3, utf8_decode('Instalado por: ' . $installer['company']), 0, 1, 'C');
        }
        if (!empty($installer['phone'])) {
            $this->Cell(0, 3, 'Tel: ' . $installer['phone'], 0, 1, 'C');
        }
        if (!empty($installer['website'])) {
            $this->Cell(0, 3, $installer['website'], 0, 1, 'C');
        }
        
        // Resetear color
        $this->SetTextColor(0, 0, 0);
    }
    
    /**
     * FacturaVenta() - Sobrescritura con datos dinámicos
     * Genera factura A4 formal con datos del negocio desde BD
     */
    function FacturaVenta() {
        // Obtener configuración global
        $config = $GLOBALS['config_negocio'] ?? [];
        
        // Obtener código de venta desde GET
        $codventa = isset($_GET['codventa']) ? decrypt($_GET['codventa']) : '';
        
        if (empty($codventa)) {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'ERROR: No se especifico venta', 0, 1, 'C');
            return;
        }
        
        try {
            // Conectar a BD
            $tra = new Login();
            
            // Obtener datos de la venta
            $venta = $tra->VentaPorId($codventa);
            
            if (empty($venta)) {
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, 'ERROR: Venta no encontrada', 0, 1, 'C');
                return;
            }
            
            // ========== ENCABEZADO DINÁMICO ==========
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 8, utf8_decode(strtoupper($config['nomsucursal'] ?? 'MI NEGOCIO')), 0, 1, 'C');
            
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'RUC: ' . ($config['cuit'] ?? 'N/A'), 0, 1, 'C');
            $this->Cell(0, 5, utf8_decode($config['direcsucursal'] ?? ''), 0, 1, 'C');
            $this->Cell(0, 5, 'Tel: ' . ($config['tlfsucursal'] ?? 'N/A'), 0, 1, 'C');
            $this->Cell(0, 5, 'Email: ' . ($config['correosucursal'] ?? ''), 0, 1, 'C');
            
            $this->Ln(5);
            
            // ========== TÍTULO FACTURA ==========
            $this->SetFont('Arial', 'B', 14);
            $this->SetFillColor(220, 220, 220);
            $this->Cell(0, 8, 'FACTURA DE VENTA', 0, 1, 'C', true);
            
            $this->Ln(3);
            
            // ========== INFORMACIÓN DE LA VENTA ==========
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 6, 'Factura No:', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(60, 6, $venta[0]['codventa'], 0, 0);
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(30, 6, 'Fecha:', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, date('d/m/Y H:i', strtotime($venta[0]['fechaventa'])), 0, 1);
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 6, 'Cliente:', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, utf8_decode($venta[0]['nomcliente'] ?? 'Cliente General'), 0, 1);
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 6, 'Tipo de Pago:', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(60, 6, $venta[0]['tipopago'] ?? 'CONTADO', 0, 0);
            
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(30, 6, 'Forma:', 0, 0);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, $venta[0]['formapago'] ?? 'EFECTIVO', 0, 1);
            
            $this->Ln(5);
            
            // ========== TABLA DE PRODUCTOS ==========
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(230, 230, 230);
            
            $this->Cell(15, 7, 'Cant.', 1, 0, 'C', true);
            $this->Cell(90, 7, utf8_decode('Descripción'), 1, 0, 'C', true);
            $this->Cell(30, 7, 'Precio Unit.', 1, 0, 'C', true);
            $this->Cell(30, 7, 'Subtotal', 1, 1, 'C', true);
            
            // Obtener detalles de la venta
            $detalles = $tra->DetallesVentaPorId($codventa);
            
            $this->SetFont('Arial', '', 9);
            $subtotal = 0;
            
            foreach ($detalles as $item) {
                $cantidad = $item['cantventa'];
                $descripcion = utf8_decode(substr($item['producto'] ?? 'Producto', 0, 50));
                $precio = floatval($item['precioventa'] ?? 0);
                $importe = $cantidad * $precio;
                $subtotal += $importe;
                
                $this->Cell(15, 6, $cantidad, 1, 0, 'C');
                $this->Cell(90, 6, $descripcion, 1, 0, 'L');
                $this->Cell(30, 6, 'C$ ' . number_format($precio, 2), 1, 0, 'R');
                $this->Cell(30, 6, 'C$ ' . number_format($importe, 2), 1, 1, 'R');
            }
            
            $this->Ln(3);
            
            // ========== TOTALES ==========
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(135, 7, 'TOTAL A PAGAR:', 0, 0, 'R');
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(30, 7, 'C$ ' . number_format($venta[0]['totalpago'], 2), 1, 1, 'R');
            
            $this->Ln(10);
            
            // ========== MENSAJE DE AGRADECIMIENTO ==========
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 5, utf8_decode('¡Gracias por su preferencia!'), 0, 1, 'C');
            
        } catch (Exception $e) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'ERROR: ' . $e->getMessage(), 0, 1, 'C');
        }
    }
}
?>
