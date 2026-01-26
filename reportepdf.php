<?php
include_once('fpdf/pdf_custom.php'); // Usar clase customizada con datos dinámicos
include_once ('fpdf/barcode.php');
require_once("class/class.php");
require_once('includes/db.php');
require_once('includes/config.php');

//ob_end_clean();
ob_start();

// ========== CARGAR CONFIGURACIÓN DINÁMICA DEL NEGOCIO ==========
// Esta configuración estará disponible globalmente para todas las funciones de PDF
try {
    $db = DB::connect();
    $sqlConfig = "SELECT cuit, nomsucursal, tlfsucursal, direcsucursal, correosucursal 
                  FROM configuracion WHERE id = 1 LIMIT 1";
    $stmtConfig = $db->prepare($sqlConfig);
    $stmtConfig->execute();
    $GLOBALS['config_negocio'] = $stmtConfig->fetch(PDO::FETCH_ASSOC);
    
    // Si no hay configuración, usar valores por defecto
    if (!$GLOBALS['config_negocio']) {
        $GLOBALS['config_negocio'] = [
            'cuit' => 'N/A',
            'nomsucursal' => 'MI NEGOCIO',
            'tlfsucursal' => 'N/A',
            'direcsucursal' => 'Dirección no configurada',
            'correosucursal' => 'contacto@negocio.com'
        ];
    }
    
    // ========== FOOTER VIRAL DEL INSTALADOR ==========
    // Datos del instalador desde config.php (definidos como constantes)
    $GLOBALS['installer_info'] = [
        'company' => INSTALLER_COMPANY,
        'phone' => INSTALLER_PHONE,
        'website' => defined('INSTALLER_WEBSITE') ? INSTALLER_WEBSITE : ''
    ];
    
} catch (Exception $e) {
    // En caso de error, continuar con valores por defecto
    $GLOBALS['config_negocio'] = [
        'cuit' => 'N/A',
        'nomsucursal' => 'MI NEGOCIO',
        'tlfsucursal' => 'N/A',
        'direcsucursal' => 'Dirección no configurada',
        'correosucursal' => 'contacto@negocio.com'
    ];
    $GLOBALS['installer_info'] = [
        'company' => 'Sistema Unicornio POS',
        'phone' => '',
        'website' => ''
    ];
}


$casos = array (

                  'PROVINCIAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarProvincias',

                                    'output' => array('Listado de Provincias.pdf', 'I')

                                  ),

                  'DEPARTAMENTOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarDepartamentos',

                                    'output' => array('Listado de Departamentos.pdf', 'I')

                                  ),

                  'DOCUMENTOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarDocumentos',

                                    'output' => array('Listado de Tipos de Documentos.pdf', 'I')

                                  ),

                  'TIPOMONEDA' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarTiposMonedas',

                                    'output' => array('Listado de Tipos de Moneda.pdf', 'I')

                                  ),

                'TIPOCAMBIO' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarTiposCambio',

                                    'output' => array('Listado de Tipos de Cambio.pdf', 'I')

                                  ),
                  
                  'MEDIOSPAGOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarMediosPagos',

                                    'output' => array('Listado de Medios de Pago.pdf', 'I')

                                  ),
                  
                  'IMPUESTOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarImpuestos',

                                    'output' => array('Listado de Impuestos.pdf', 'I')

                                  ),

                  'FAMILIAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarFamilias',

                                    'output' => array('Listado de Familias.pdf', 'I')

                                  ),

                  'SUBFAMILIAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarSubfamilias',

                                    'output' => array('Listado de Sub-Familias.pdf', 'I')

                                  ),

                  'MARCAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarMarcas',

                                    'output' => array('Listado de Marcas.pdf', 'I')

                                  ),

                  'MODELOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarModelos',

                                    'output' => array('Listado de Modelos.pdf', 'I')

                                  ),

                   'PRESENTACIONES' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarPresentaciones',

                                    'output' => array('Listado de Presentaciones.pdf', 'I')

                                  ),

                  'COLORES' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarColores',

                                    'output' => array('Listado de Colores.pdf', 'I')

                                  ),

                  'ORIGENES' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarOrigenes',

                                    'output' => array('Listado de Origenes.pdf', 'I')

                                  ),

                  'SUCURSALES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarSucursales',

                                    'output' => array('Listado de Sucursales.pdf', 'I')

                                  ),

                  'USUARIOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarUsuarios',

                                    'output' => array('Listado de Usuarios.pdf', 'I')

                                  ),

                  'LOGS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarLogs',

                                    'output' => array('Listado Logs de Acceso.pdf', 'I')

                                  ),

                  'CLIENTES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarClientes',

                                    'output' => array('Listado de Clientes.pdf', 'I')

                                  ),

                  'PROVEDORES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProveedores',

                                    'output' => array('Listado de Proveedores.pdf', 'I')

                                  ),

                 'FACTURAPEDIDO' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'FacturaPedido',

                                    'output' => array('Factura de Pedido.pdf', 'I')

                                  ),

                 'PEDIDOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'ListarPedidos',

                                    'output' => array('Listado de Pedidos.pdf', 'I')

                                  ),

                  'PEDIDOSXPROVEEDOR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarPedidosxProveedor',

                                    'output' => array('Listado de Pedidos x Proveedor.pdf', 'I')

                                  ),

                 'PRODUCTOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductos',

                                    'output' => array('Listado de Productos.pdf', 'I')

                                  ),

                 'STOCKOPTIMO' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosOptimo',

                                    'output' => array('Listado de Productos en Stock Optimo.pdf', 'I')

                                  ),

                 'STOCKMEDIO' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosMedio',

                                    'output' => array('Listado de Productos en Stock Medio.pdf', 'I')

                                  ),

                 'STOCKMINIMO' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosMinimo',

                                    'output' => array('Listado de Productos en Stock Minimo.pdf', 'I')

                                  ),

                 'CODIGOBARRAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarCodigoBarras',

                                    'output' => array('Listado de Código de Barras.pdf', 'I')

                                  ),

                  'PRODUCTOSXSUCURSALES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosxSucursal',

                                    'output' => array('Listado de Productos.pdf', 'I')

                                  ),

                  'PRODUCTOSVENDIDOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosVendidos',

                                    'output' => array('Listado de Productos Vendidos.pdf', 'I')

                                  ),

                  'PRODUCTOSXVENDEDOR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosxVendedor',

                                    'output' => array('Listado de Productos Vendidos por Vendedor.pdf', 'I')

                                  ),

                  'PRODUCTOSXMONEDA' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosxMoneda',

                                    'output' => array('Listado de Productos por Moneda.pdf', 'I')

                                  ),

                   'KARDEXPRODUCTOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarKardex',

                                    'output' => array('Listado de Kardex de Producto.pdf', 'I')

                                  ),

                  'KARDEXVALORIZADO' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarKardexValorizado',

                                    'output' => array('Listado de Kardex Valorizado.pdf', 'I')

                                  ),

                  'VALORIZADOXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarKardexValorizadoxFechas',

                                    'output' => array('Listado de Kardex Valorizado por Fechas.pdf', 'I')

                                  ),

                 'FACTURATRASPASO' => array(

                                    //'medidas' => array('P', 'mm', 'A4'),
                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'FacturaTraspaso',

                                    'output' => array('Factura de Traspasos.pdf', 'I')

                                  ),

                  'TRASPASOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarTraspasos',

                                    'output' => array('Listado de Traspasos.pdf', 'I')

                                  ),

                  'TRASPASOSXSUCURSAL' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarTraspasosxSucursal',

                                    'output' => array('Listado de Traspasos por Sucursal.pdf', 'I')

                                  ),

                  'TRASPASOSXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarTraspasosxFechas',

                                    'output' => array('Listado de Traspasos por Fechas.pdf', 'I')

                                  ),

                 'FACTURACOMPRA' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'FacturaCompra',

                                    'output' => array('Factura de Compra.pdf', 'I')

                                  ),

                 'COMPRAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCompras',

                                    'output' => array('Listado de Compras.pdf', 'I')

                                  ),

                 'CUENTASXPAGAR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCuentasxPagar',

                                    'output' => array('Listado de Cuentas por Pagar.pdf', 'I')

                                  ),

              'COMPRASXPROVEEDOR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarComprasxProveedor',

                                    'output' => array('Listado de Compras por Proveedor.pdf', 'I')

                                  ),

              'COMPRASXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarComprasxFechas',

                                    'output' => array('Listado de Compras por Fechas.pdf', 'I')

                                  ),

        
                  'TICKETCOMPRA' => array(

                                    'medidas' => array('P','mm','ticket'),

                                    'func' => 'TicketCreditoCompra',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Ticket de Abonos.pdf', 'I')

                                  ),

                  'CREDITOSXPROVEEDOR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditosxProveedor',

                                    'output' => array('Listado de Creditos por Proveedor.pdf', 'I')

                                  ),

                  'CREDITOSCOMPRASXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditosComprasxFechas',

                                    'output' => array('Listado de Creditos de Compras por Fechas.pdf', 'I')

                                  ),

                 'FACTURACOTIZACION' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'FacturaCotizacion',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Factura de Cotizacion.pdf', 'I')

                                  ),

                  'COTIZACIONES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCotizaciones',

                                    'output' => array('Listado de Cotizaciones.pdf', 'I')

                                  ),

                  'COTIZACIONESXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCotizacionesxFechas',

                                    'output' => array('Listado de Cotizaciones.pdf', 'I')

                                  ),

                  'PRODUCTOSCOTIZADOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarProductosCotizados',

                                    'output' => array('Listado de Productos Cotizados.pdf', 'I')

                                  ),

                  'COTIZACIONESXVENDEDOR' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCotizacionesxVendedor',

                                    'output' => array('Listado de Productos Cotizados por Vendedor.pdf', 'I')

                                  ),

                'CAJAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarCajas',

                                    'output' => array('Listado de Cajas.pdf', 'I')

                                  ),

               'ARQUEOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarArqueos',

                                    'output' => array('Listado de Arqueos de Cajas.pdf', 'I')

                                  ),
        
                  'TICKETCIERRE' => array(

                                    'medidas' => array('P','mm','cierre'),

                                    'func' => 'TicketCierre',

                                    //'setPrintFooter' => 'true',

                                    'output' => array('Ticket de Cierre.pdf', 'I')

                                  ),

                'MOVIMIENTOS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarMovimientos',

                                    'output' => array('Listado de Movimientos en Caja.pdf', 'I')

                                  ),

                   'ARQUEOSXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarArqueosxFechas',

                                    'output' => array('Listado de Arqueos por Fechas.pdf', 'I')

                                  ),

                  'MOVIMIENTOSXFECHAS' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'TablaListarMovimientosxFechas',

                                    'output' => array('Listado de Movimientos por Fechas.pdf', 'I')

                                  ),
        
                  'TICKET' => array(

                                    'medidas' => array('P','mm','ticket'),

                                    'func' => 'TicketVenta',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Ticket de Venta.pdf', 'I')

                                  ),

                  'FACTURA' => array(

                                    'medidas' => array('P', 'mm', 'A4'),

                                    'func' => 'FacturaVenta',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Factura de Ventas.pdf', 'I')

                                  ),

                  'GUIA' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'GuiaVenta',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Guia de Remision.pdf', 'I')

                                  ),

                  'VENTAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarVentas',

                                    'output' => array('Listado de Ventas.pdf', 'I')

                                  ),

                  'VENTASDIARIAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarVentasDiarias',

                                    'output' => array('Listado de Ventas del Dia.pdf', 'I')

                                  ),

                  'VENTASXCAJAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarVentasxCajas',

                                    'output' => array('Listado de Ventas por Cajas.pdf', 'I')

                                  ),

                  'VENTASXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarVentasxFechas',

                                    'output' => array('Listado de Ventas por Fechas.pdf', 'I')

                                  ),
        
                  'TICKETCREDITO' => array(

                                    'medidas' => array('P','mm','ticket'),

                                    'func' => 'TicketCredito',

                                    'setPrintFooter' => 'true',

                                    'output' => array('Ticket de Abonos.pdf', 'I')

                                  ),

                  'CREDITOS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditos',

                                    'output' => array('Listado de Creditos.pdf', 'I')

                                  ),

                  'CREDITOSXCLIENTES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditosxClientes',

                                    'output' => array('Listado de Creditos por Clientes.pdf', 'I')

                                  ),

                  'CREDITOSXFECHAS' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditosxFechas',

                                    'output' => array('Listado de Creditos por Fechas.pdf', 'I')

                                  ),

                  'CREDITOSXDETALLES' => array(

                                    'medidas' => array('L', 'mm', 'LEGAL'),

                                    'func' => 'TablaListarCreditosxDetalles',

                                    'output' => array('Listado de Creditos por Detalles.pdf', 'I')

                                  ),

                );

 
$tipo = decrypt($_GET['tipo']);

if ($tipo == 'TICKET' || $tipo == 'FACTURA' || $tipo == 'GUIA' || $tipo == 'TICKETCREDITO' || $tipo == 'FACTURACOTIZACION') {

  $caso_data = $casos[$tipo];
  // Usar PDF_Custom para facturas que tienen datos dinámicos
  $pdf = new PDF_Custom($caso_data['medidas'][0], $caso_data['medidas'][1], $caso_data['medidas'][2]);
  $pdf->AddPage();
  $pdf->SetAuthor("Sistema Unicornio POS");
  $pdf->SetCreator("FPDF Y PHP");
  $pdf->{$caso_data['func']}();
  $pdf->AutoPrint(false);
  $pdf->Output($caso_data['output'][0], $caso_data['output'][1]);
  ob_end_flush();

} else {

  $caso_data = $casos[$tipo];
  // Para otros reportes usar PDF estándar
  $pdf = new PDF($caso_data['medidas'][0], $caso_data['medidas'][1], $caso_data['medidas'][2]);
  $pdf->AddPage();
  $pdf->SetAuthor("Sistema Unicornio POS");
  $pdf->SetCreator("FPDF Y PHP");
  $pdf->{$caso_data['func']}();
  $pdf->Output($caso_data['output'][0], $caso_data['output'][1]);
  ob_end_flush();
}
?>