<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

$imp = new Login();
$imp = $imp->ImpuestosPorId();
$impuesto = $imp[0]['nomimpuesto'];
$valor = $imp[0]['valorimpuesto'];

$con = new Login();
$con = $con->ConfiguracionPorId();
$simbolo = "<strong>".$con[0]['simbolo']."</strong>";

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save") {
    $reg = $tra->RegistrarCompras();
    exit;
}
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="update") {
    $reg = $tra->ActualizarCompras();
    exit;
}    
?>
<!DOCTYPE html>
<html dir="ltr" lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Gestión de Compras</title>

    <!-- Menu CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="assets/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sweetalert.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false, 
            },
            theme: {
                extend: {
                    colors: {
                        'uni-primary': '#1e88e5',
                        'uni-dark': '#2c3b41'
                    }
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Essential Overrides */
        [x-cloak] { display: none !important; }
        .form-control { display: inline-block; width: 100%; } /* Bootstrap Compat */
    </style>

</head>

<body onLoad="muestraReloj()" class="fix-header">
    
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-boxed-layout="full" data-header-position="fixed" data-sidebar-position="fixed" class="mini-sidebar">       
                    
        <?php include('menu.php'); ?>
   
        <div class="page-wrapper">
            
            <div class="page-breadcrumb border-bottom">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
                        <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-shopping-cart"></i> Gestión de Compras</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Compras</li>
                                <li class="breadcrumb-item active" aria-current="page">Nueva Compra</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
           
            <div class="page-content container-fluid" style="background: #f4f6f9;">
                
                <!-- ALPINE APP -->
                <div x-data="purchaseApp()" x-init="initData()" x-cloak class="pb-10">
                    
                    <!-- HEADER -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
                            <i class="fa fa-file-text-o mr-3 text-uni-primary"></i>
                            Datos de la Factura
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Sucursal -->
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Sucursal</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 text-gray-500 font-medium" value="<?php echo $_SESSION['razonsocial'] ?? 'PRINCIPAL'; ?>" readonly>
                            </div>
                            
                            <!-- Proveedor -->
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Proveedor <span class="text-red-500">*</span></label>
                                <select x-model="header.codproveedor" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition bg-white">
                                    <option value="">-- Seleccione --</option>
                                    <template x-for="p in proveedores" :key="p.codproveedor">
                                        <option :value="p.codproveedor" x-text="p.nomproveedor"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Nro Compra -->
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">N° Factura <span class="text-red-500">*</span></label>
                                <input type="text" x-model="header.codcompra" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 outline-none transition" placeholder="Ej: F001-00432">
                            </div>

                            <!-- Fechas -->
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Emisión</label>
                                <input type="date" x-model="header.fechaemision" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 outline-none transition">
                            </div>

                             
                        </div>
                        
                        <!-- Second Row -->
                         <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Recepción</label>
                                <input type="date" x-model="header.fecharecepcion" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 outline-none transition">
                            </div>

                             <!-- Condicion -->
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Condición</label>
                                <select x-model="header.tipocompra" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-blue-500 outline-none transition bg-white">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CRÉDITO</option>
                                </select>
                            </div>

                            <!-- Payment Method or Date -->
                            <div x-show="header.tipocompra == 'CONTADO'">
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Método Pago</label>
                                <select x-model="header.formacompra" class="w-full border border-gray-300 rounded px-3 py-2 bg-white">
                                    <option value="EFECTIVO">EFECTIVO</option>
                                    <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                    <option value="CHEQUE">CHEQUE</option>
                                    <option value="TARJETA">TARJETA</option>
                                </select>
                            </div>

                            <div x-show="header.tipocompra == 'CREDITO'">
                                <label class="block text-gray-600 text-sm font-bold mb-2 uppercase">Vencimiento</label>
                                <input type="date" x-model="header.fechavencecredito" class="w-full border border-gray-300 rounded px-3 py-2">
                            </div>
                         </div>
                    </div>

                    <!-- SEARCH & ADD -->
                    <div class="bg-blue-50 rounded-lg shadow-sm p-6 mb-6 border border-blue-100">
                        <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <i class="fa fa-search mr-3"></i> Buscar Productos
                        </h4>

                        <div class="relative">
                            <input type="text" x-model="searchQuery" @input="filterProducts()" @keydown.escape="searchResults=[]"
                                   class="w-full border border-blue-300 rounded-lg px-4 py-3 text-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none transition" 
                                   placeholder="Escriba nombre o código del producto..." autocomplete="off">
                            
                            <div x-show="searchResults.length > 0" class="absolute z-50 w-full bg-white border border-gray-200 shadow-xl rounded-b-lg max-h-64 overflow-y-auto mt-1 left-0" 
                                 @click.away="searchResults = []">
                                <template x-for="prod in searchResults" :key="prod.codproducto">
                                    <div @click="selectProduct(prod)" 
                                         class="p-3 hover:bg-blue-50 cursor-pointer border-b flex justify-between items-center transition-colors">
                                        <div>
                                            <div class="font-bold text-gray-800" x-text="prod.producto"></div>
                                            <div class="text-xs text-gray-500">Cod: <span x-text="prod.codproducto"></span></div>
                                        </div>
                                        <span class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full" x-text="'Stock: ' + prod.existencia"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- PRODUCT EDITOR (MODAL-LIKE INLINE) -->
                    <div x-show="currentProduct.codproducto" class="bg-white rounded-lg shadow-lg border-t-4 border-blue-500 p-6 mb-6 animation-fade-in">
                        <div class="flex justify-between items-start mb-4 border-b pb-2">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800" x-text="currentProduct.producto"></h3>
                                <p class="text-sm text-gray-500">Código: <span x-text="currentProduct.codproducto"></span></p>
                            </div>
                            <button @click="clearCurrent()" class="text-gray-400 hover:text-red-500 transition"><i class="fa fa-times text-xl"></i></button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cantidad</label>
                                <input type="number" x-model.number="currentDetails.cantidad" class="w-full border border-gray-300 rounded px-2 py-2 text-center font-bold text-lg focus:border-blue-500 outline-none" min="1">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Costo Unit.</label>
                                <input type="number" step="0.01" x-model.number="currentDetails.costo" class="w-full border border-gray-300 rounded px-2 py-2 text-right focus:border-blue-500 outline-none">
                            </div>
                             <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">IVA</label>
                                <select x-model="currentDetails.iva" class="w-full border border-gray-300 rounded px-2 py-2 bg-white">
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dsc %</label>
                                <input type="number" step="0.01" x-model.number="currentDetails.descuento_porc" class="w-full border border-gray-300 rounded px-2 py-2 text-center">
                            </div>
                            <div class="md:col-span-2 bg-gray-50 p-2 rounded text-right border border-gray-200">
                                <label class="block text-xs text-gray-500 font-bold uppercase">Subtotal Línea</label>
                                <div class="text-2xl font-bold text-blue-600" x-text="formatMoney(lineTotal)"></div>
                            </div>
                        </div>
                        
                        <!-- Toggle Details -->
                         <div class="mt-4 pt-4 border-t border-dashed">
                             <button @click="showAdvanced = !showAdvanced" class="text-sm text-blue-600 hover:underline flex items-center font-medium">
                                <i class="fa mr-1" :class="showAdvanced ? 'fa-caret-down' : 'fa-caret-right'"></i> 
                                Editar Precios de Venta / Lotes
                            </button>
                            
                            <div x-show="showAdvanced" class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded border">
                                <div>
                                    <label class="block text-xs text-gray-600 uppercase">P. Público</label>
                                    <input type="number" step="0.01" x-model.number="currentDetails.p_publico" class="w-full border border-gray-300 rounded px-2 py-1 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 uppercase">P. Mayorista</label>
                                    <input type="number" step="0.01" x-model.number="currentDetails.p_mayor" class="w-full border border-gray-300 rounded px-2 py-1 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 uppercase">P. Menor</label>
                                    <input type="number" step="0.01" x-model.number="currentDetails.p_menor" class="w-full border border-gray-300 rounded px-2 py-1 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 uppercase">Vencimiento</label>
                                    <input type="date" x-model="currentDetails.expiry" class="w-full border border-gray-300 rounded px-2 py-1 text-sm bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 uppercase">Lote</label>
                                    <input type="text" x-model="currentDetails.lote" class="w-full border border-gray-300 rounded px-2 py-1 text-sm bg-white">
                                </div>
                            </div>
                         </div>

                        <div class="mt-4 flex justify-end">
                            <button @click="addToCart()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded shadow-md transition transform hover:scale-105 flex items-center">
                                <i class="fa fa-plus-circle mr-2"></i> AGREGAR
                            </button>
                        </div>
                    </div>

                    <!-- CART TABLE -->
                    <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200 overflow-hidden">
                         <div class="bg-gray-100 px-6 py-3 border-b flex justify-between items-center">
                            <h4 class="font-bold text-gray-700 m-0">Detalle de Productos</h4>
                            <span class="text-sm bg-blue-100 text-blue-800 py-1 px-3 rounded-full font-bold" x-text="cart.length + ' Items'"></span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Stock A.</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Cant</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Costo</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">IVA</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr class="hover:bg-blue-50 transition">
                                            <td class="px-6 py-3">
                                                <div class="text-sm font-bold text-gray-900" x-text="item.producto"></div>
                                                <div class="text-xs text-gray-500" x-text="item.codproducto"></div>
                                            </td>
                                            <td class="px-6 py-3 text-center text-gray-900 font-bold text-blue-600" x-text="item.existencia"></td>
                                            <td class="px-6 py-3 text-center text-gray-900">
                                                <input type="number" x-model.number="item.cantidad" @change="updateLine(index)" min="1" class="w-16 border rounded text-center focus:ring-2 focus:ring-blue-500 options-input">
                                            </td>
                                            <td class="px-6 py-3 text-right text-gray-900 font-mono" x-text="formatMoney(item.costo)"></td>
                                            <td class="px-6 py-3 text-center">
                                                <span class="px-2 py-1 text-xs rounded-full font-bold" :class="item.iva==='SI'?'bg-green-100 text-green-800':'bg-gray-200 text-gray-600'" x-text="item.iva"></span>
                                            </td>
                                            <td class="px-6 py-3 text-right font-bold text-gray-900" x-text="formatMoney(item.total)"></td>
                                            <td class="px-6 py-3 text-center">
                                                <button @click="removeFromCart(index)" class="text-red-400 hover:text-red-600 transition p-1"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </template>
                                     <tr x-show="cart.length === 0">
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fa fa-shopping-basket text-4xl mb-2 text-gray-300"></i>
                                                <span>No hay productos agregados</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 border-t">
                                    <tr>
                                        <td colspan="5" class="px-6 py-2 text-right text-xs font-bold text-gray-500 uppercase">Subtotal</td>
                                        <td class="px-6 py-2 text-right font-bold text-gray-700" x-text="formatMoney(totals.subtotal)"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-6 py-2 text-right text-xs font-bold text-gray-500 uppercase">IVA</td>
                                        <td class="px-6 py-2 text-right font-bold text-gray-700" x-text="formatMoney(totals.iva)"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-6 py-2 text-right text-xs font-bold text-gray-500 uppercase">
                                            Descuento Global <input type="number" x-model.number="header.descuento_global" class="w-16 border rounded text-center ml-2">%
                                        </td>
                                        <td class="px-6 py-2 text-right font-bold text-red-600" x-text="'- ' + formatMoney(totals.discount)"></td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-blue-600 text-white">
                                        <td colspan="5" class="px-6 py-3 text-right text-sm font-bold uppercase">Total General</td>
                                        <td class="px-6 py-3 text-right text-xl font-bold" x-text="formatMoney(totals.total)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button onclick="window.location.reload()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded shadow transition">
                            CANCELAR
                        </button>
                        <button @click="savePurchase()" 
                                :disabled="cart.length === 0 || !header.codproveedor || !header.codcompra"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded shadow-lg transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                            <i class="fa fa-save mr-2"></i> PROCESAR COMPRA
                        </button>
                    </div>

                </div>
            </div>
            
            <footer class="footer text-center">
                <i class="fa fa-copyright"></i> <span class="current-year"></span>.
            </footer>
        </div>
    </div>


    <!-- Javascipt Original -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/app.init.horizontal-fullwidth.js"></script>
    <script src="assets/js/app-style-switcher.js"></script>
    <script src="assets/js/perfect-scrollbar.js"></script>
    <script src="assets/js/sparkline.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- ALPINE LOGIC -->
    <script>
        function purchaseApp() {
            return {
                header: {
                    codproveedor: '',
                    codcompra: '',
                    fechaemision: new Date().toISOString().split('T')[0],
                    fecharecepcion: new Date().toISOString().split('T')[0],
                    tipocompra: 'CONTADO',
                    formacompra: 'EFECTIVO',
                    fechavencecredito: '',
                    descuento_global: 0,
                    tasa_iva: <?php echo $valor ? $valor/100 : 0.16; ?> 
                },
                proveedores: [],
                allProducts: [],
                searchResults: [],
                searchQuery: '',
                currentProduct: {},
                showAdvanced: false,
                currentDetails: {
                    cantidad: 1,
                    costo: 0,
                    iva: 'NO',
                    descuento_porc: 0,
                    p_publico: 0,
                    p_mayor: 0,
                    p_menor: 0,
                    lote: '',
                    expiry: ''
                },
                cart: [],

                async initData() {
                    try {
                        let pResp = await fetch('api/proveedores.php');
                        this.proveedores = await pResp.json();
                        
                        let prodResp = await fetch('api/productos.php');
                        this.allProducts = await prodResp.json();
                    } catch(e) { console.error('Init error', e); }
                },

                filterProducts() {
                    if (this.searchQuery.length < 2) { 
                        this.searchResults = []; 
                        return; 
                    }
                    const q = this.searchQuery.toUpperCase();
                    this.searchResults = this.allProducts.filter(p => 
                        (p.codproducto && p.codproducto.toUpperCase().includes(q)) || 
                        (p.producto && p.producto.toUpperCase().includes(q))
                    ).slice(0, 10);
                },

                selectProduct(prod) {
                    this.currentProduct = prod;
                    this.currentDetails = {
                        cantidad: 1,
                        costo: parseFloat(prod.preciocompra) || 0,
                        iva: prod.ivaproducto || 'NO', 
                        descuento_porc: 0,
                        p_publico: parseFloat(prod.precioxpublico) || 0,
                        p_mayor: parseFloat(prod.precioxmayor) || 0,
                        p_menor: parseFloat(prod.precioxmenor) || 0, 
                        lote: '',
                        expiry: ''
                    };
                    this.searchQuery = '';
                    this.searchResults = [];
                },

                clearCurrent() {
                    this.currentProduct = {};
                    this.showAdvanced = false;
                },

                get lineTotal() {
                    let base = this.currentDetails.cantidad * this.currentDetails.costo;
                    let disc = base * (this.currentDetails.descuento_porc / 100);
                    return base - disc;
                },

                addToCart() {
                    if (!this.currentProduct.codproducto) return;
                    
                    this.cart.push({
                        codproducto: this.currentProduct.codproducto,
                        producto: this.currentProduct.producto,
                        existencia: this.currentProduct.existencia,
                        cantidad: this.currentDetails.cantidad,
                        costo: this.currentDetails.costo,
                        iva: this.currentDetails.iva,
                        descuento_monto: (this.currentDetails.cantidad * this.currentDetails.costo) * (this.currentDetails.descuento_porc / 100),
                        descuento_porc: this.currentDetails.descuento_porc,
                        total: this.lineTotal,
                        p_publico: this.currentDetails.p_publico,
                        p_mayor: this.currentDetails.p_mayor,
                        p_menor: this.currentDetails.p_menor,
                        lote: this.currentDetails.lote,
                        expiry: this.currentDetails.expiry
                    });
                    this.clearCurrent();
                    const toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 1500
                    });
                    toast.fire({ icon: 'success', title: 'Agregado' });
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateLine(index) {
                    let item = this.cart[index];
                    if(item.cantidad < 1) item.cantidad = 1;
                    
                    let base = item.cantidad * item.costo;
                    let disc = base * (item.descuento_porc / 100);
                    item.total = base - disc;
                    item.descuento_monto = disc;
                },

                get totals() {
                    let subtotal = 0;
                    let iva = 0;
                    // Calculate Subtotals from lines (already discounted by item)
                    this.cart.forEach(item => {
                        let base = item.total; 
                        subtotal += base;
                        if(item.iva === 'SI') {
                            iva += base * this.header.tasa_iva;
                        }
                    });
                    
                    // Apply Global Discount
                    let discountAmount = subtotal * (this.header.descuento_global / 100);
                    let finalSubtotal = subtotal - discountAmount;
                    
                    // Re-calculate IVA on discounted base? Usually yes.
                    // But simplified: Just subtract discount from total.
                    // Let's stick to API expcecting 'totaldescuentoc'
                    
                    return { 
                        subtotal: subtotal, 
                        iva: iva, 
                        discount: discountAmount,
                        total: finalSubtotal + iva 
                    };
                },

                formatMoney(val) {
                    return '$ ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                async savePurchase() {
                    const t = this.totals;
                    const payload = {
                        header: {
                            ...this.header,
                            total: t.total,
                            monto_iva: t.iva,
                            subtotal_gravado: t.subtotal, 
                            subtotal_exento: 0, // TODO: separate items by tax status
                            tasa_descuento: this.header.descuento_global,
                            monto_descuento: t.discount
                        },
                        items: this.cart
                    };

                    try {
                        Swal.fire({ title: 'Guardando...', didOpen: () => Swal.showLoading() });
                        
                        const res = await fetch('api/guardar_compra.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        
                        if(data.status === 'ok') {
                            Swal.fire('Éxito', 'Compra registrada correctamente', 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.error || 'No se pudo guardar', 'error');
                        }
                    } catch(e) {
                        Swal.fire('Error', 'Fallo de red', 'error');
                    }
                }
            }
        }
    </script>
</body>
</html>
<?php } } else { header("Location: logout"); } ?>