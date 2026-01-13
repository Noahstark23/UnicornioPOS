<?php
require_once("class/class.php");
// Same session logic as always
if (isset($_SESSION['acceso'])) {
    if ($_SESSION["acceso"] == "administradorG" || $_SESSION["acceso"] == "administradorS" || $_SESSION["acceso"] == "secretaria") {
    } else { header("Location: error"); exit; }
} else { header("Location: error"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Historial Compras | POS</title>
   
    <!-- Menu CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: { colors: { 'uni-primary': '#1e88e5' } }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .uni-table th { @apply px-4 py-2 text-left bg-gray-100 text-gray-600 font-bold uppercase text-xs; }
        .uni-table td { @apply px-4 py-3 border-b border-gray-100; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="fix-header">
    <div id="main-wrapper">
        
        <?php include('menu.php'); ?>
   
        <div class="page-wrapper" style="display: block !important; background: #f3f4f6;">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb bg-white border-b px-6 py-3 flex justify-between items-center">
                <h5 class="font-medium text-lg text-gray-800"><i class="fa fa-list mr-2"></i> Historial de Compras</h5>
                <nav aria-label="breadcrumb">
                    <ol class="flex space-x-2 text-sm text-gray-600">
                        <li>Compras</li>
                        <li class="text-gray-400">/</li>
                        <li class="text-blue-600 font-semibold">Historial</li>
                    </ol>
                </nav>
            </div>

            <!-- Content -->
            <div class="container-fluid p-6">
                
                <!-- ALPINE APP in CONTAINER -->
                <div x-data="historyApp()" x-init="load()" class="bg-white rounded-lg shadow-md p-6">
                    
                    <div class="flex justify-between mb-4">
                        <div class="relative w-64">
                            <input type="text" x-model="search" placeholder="Buscar..." 
                                   class="w-full border border-gray-300 rounded pl-10 pr-4 py-2 focus:border-blue-500 focus:outline-none">
                            <i class="fa fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <a href="forcompra" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <i class="fa fa-plus"></i> Nueva Compra
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full uni-table">
                            <thead>
                                <tr>
                                    <th>N° Factura</th>
                                    <th>Proveedor</th>
                                    <th>Emisión</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="c in filtered" :key="c.codcompra">
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="font-bold text-gray-800" x-text="c.codcompra"></td>
                                        <td x-text="c.nomproveedor || 'S/N'"></td>
                                        <td x-text="c.fechaemision"></td>
                                        <td>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 font-bold" x-text="c.tipocompra"></span>
                                        </td>
                                        <td>
                                            <span class="px-2 py-1 text-xs rounded-full font-bold" 
                                                  :class="c.status == 'PAGADA' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                                  x-text="c.status"></span>
                                        </td>
                                        <td class="text-right font-mono font-bold" x-text="formatMoney(c.total)"></td>
                                        <td class="text-center">
                                            <button @click="verDetalle(c)" class="text-blue-500 hover:text-blue-700 font-bold"><i class="fa fa-eye"></i> Ver</button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="compras.length === 0">
                                    <td colspan="7" class="text-center py-8 text-gray-500">
                                        <i class="fa fa-spinner fa-spin"></i> Cargando compras...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            
            <footer class="footer text-center mt-auto py-4 bg-white border-t">
                 2025 unicornio
            </footer>
        </div>
    </div>

    <!-- Scripts Legacy -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/app.init.horizontal-fullwidth.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
        function historyApp() {
            return {
                compras: [],
                search: '',
                
                async load() {
                    try {
                        const res = await fetch('api/listar_compras.php');
                        this.compras = await res.json();
                    } catch(e) { console.error(e); }
                },

                get filtered() {
                    if(!this.search) return this.compras;
                    const q = this.search.toUpperCase();
                    return this.compras.filter(c => 
                        c.codcompra.includes(q) || 
                        (c.nomproveedor && c.nomproveedor.toUpperCase().includes(q))
                    );
                },

                formatMoney(val) {
                    return '$ ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
                },

                verDetalle(compra) {
                    Swal.fire({
                        title: 'Detalle de Compra',
                        html: `
                            <div style="text-align: left;">
                                <p><strong>Factura:</strong> ${compra.codcompra}</p>
                                <p><strong>Proveedor:</strong> ${compra.nomproveedor || 'N/A'}</p>
                                <p><strong>Fecha Emisión:</strong> ${compra.fechaemision}</p>
                                <p><strong>Fecha Recepción:</strong> ${compra.fecharecepcion}</p>
                                <p><strong>Tipo:</strong> ${compra.tipocompra}</p>
                                <p><strong>Estado:</strong> ${compra.status}</p>
                                <p><strong>Total:</strong> ${this.formatMoney(compra.total)}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar'
                    });
                }
            }
        }
    </script>
</body>
</html>