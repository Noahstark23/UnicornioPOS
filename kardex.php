<?php
// Configuración de Sesión y Permisos
require_once("class/class.php");
if (isset($_SESSION['acceso'])) {
    if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {
        // Acceso permitido
    } else {
        header("Location: panel");
        exit;
    }
} else {
    header("Location: logout");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex Valorizado | Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
        .glass-metric {
            background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.4));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body class="text-slate-800" x-data="kardexApp()">

    <!-- BACKGROUND -->
    <div class="fixed inset-0 z-0 bg-slate-50">
        <div class="absolute top-0 w-full h-96 bg-gradient-to-b from-slate-200 to-transparent opacity-50"></div>
    </div>

    <div class="relative z-10 min-h-screen p-6 max-w-[1600px] mx-auto flex flex-col">
        
        <!-- HEADER -->
        <header class="flex justify-between items-end mb-8">
            <div>
                <a href="panel" class="text-sm font-semibold text-slate-500 hover:text-indigo-600 flex items-center mb-1 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver al Panel
                </a>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Inventario Valorizado</h1>
                <p class="text-slate-500 mt-1">Auditoría de existencias y costos en tiempo real</p>
            </div>
            
            <div class="text-right hidden md:block">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fecha de Corte</span>
                <div class="text-xl font-bold text-slate-700" x-text="new Date().toLocaleDateString('es-ES', {weekday:'long', day:'numeric', month:'long'})"></div>
            </div>
        </header>

        <!-- KPIS HERO -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Main Metric: Valor Total -->
            <div class="glass-metric rounded-3xl p-8 shadow-xl col-span-1 md:col-span-2 relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
                
                <h3 class="text-base font-bold text-slate-500 uppercase tracking-wide mb-2">Valor Total del Inventario</h3>
                <div class="flex items-baseline gap-4">
                    <span class="text-6xl font-black text-slate-900 tracking-tight" x-text="formatCurrency(kpis.valor_total)">$ 0.00</span>
                    <span class="text-sm font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full border border-emerald-200">Activos Circulantes</span>
                </div>
                <div class="mt-6 flex gap-8">
                    <div>
                        <span class="block text-xs text-slate-500 font-bold uppercase">Total SKUs</span>
                        <span class="text-xl font-bold text-slate-800" x-text="kpis.total_items || 0">0</span>
                    </div>
                </div>
            </div>

            <!-- Warning Metric: Bajo Stock -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-slate-100 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 text-red-50 opacity-20 transform -rotate-12">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </div>
                <h3 class="text-base font-bold text-red-500 uppercase tracking-wide mb-2">Alertas de Stock</h3>
                <div class="text-5xl font-black text-slate-800" x-text="kpis.items_bajo_stock || 0">0</div>
                <p class="text-slate-500 text-sm mt-2">Productos por debajo del mínimo</p>
            </div>
        </div>

        <!-- SEARCH -->
        <div class="mb-4">
            <div class="relative max-w-xl">
                <input type="text" x-model="search" @input.debounce.300ms="fetchData()" 
                       class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white shadow-sm border border-slate-200 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all text-lg outline-none" 
                       placeholder="Buscar producto por nombre, código o SKU...">
                <svg class="w-6 h-6 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex-1 flex flex-col">
            <div class="overflow-auto flex-1">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 sticky top-0 z-10 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Costo Unit.</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Existencia</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Valor Total</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <template x-for="p in products" :key="p.codproducto">
                            <tr class="hover:bg-indigo-50/30 transition-colors cursor-default">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800 text-lg" x-text="p.producto"></span>
                                        <div class="flex gap-3 text-xs text-slate-400 font-mono mt-1">
                                            <span x-text="p.codproducto"></span>
                                            <span x-show="p.codigobarra" x-text="'| ' + p.codigobarra"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-slate-600 font-medium" x-text="formatCurrency(p.costo)"></div>
                                    <div class="text-xs text-slate-400">P. Venta: <span x-text="formatCurrency(p.precio)"></span></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-bold text-xl" 
                                          :class="p.stock <= 5 ? 'text-red-600' : 'text-slate-800'"
                                          x-text="p.stock"></span>
                                    <span class="text-xs text-slate-400 block">unidades</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-black text-slate-900 text-lg" x-text="formatCurrency(p.total_costo)"></div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="loadHistory(p)" class="p-2 text-indigo-500 hover:bg-indigo-100 rounded-lg transition-colors" title="Ver Movimientos">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="products.length === 0" x-cloak>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-lg">
                                No se encontraron productos.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL HISTORIAL -->
    <div class="fixed inset-0 z-50 overflow-y-auto" x-show="showHistory" style="display: none;" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="showHistory = false"></div>
            <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl p-0">
                
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex flex-col">
                        <span>Historial de Movimientos</span>
                        <span class="text-sm font-normal text-indigo-200" x-text="selectedProduct?.producto"></span>
                    </h3>
                    <button @click="showHistory = false" class="text-white hover:text-indigo-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Documento</th>
                                <th class="px-4 py-3 text-right">Cant.</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="m in history" :key="m.idkardex">
                                <tr>
                                    <td class="px-4 py-3" x-text="formatDate(m.fechakardex)"></td>
                                    <td class="px-4 py-3">
                                        <span x-text="m.movimiento" 
                                              class="px-2 py-0.5 rounded text-xs font-bold"
                                              :class="m.movimiento === 'ENTRADA' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"></span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600" x-text="m.coddocumento"></td>
                                    <td class="px-4 py-3 text-right font-bold" x-text="m.cantidad"></td>
                                    <td class="px-4 py-3 text-right text-slate-500" x-text="m.saldoactual"></td>
                                </tr>
                            </template>
                            <tr x-show="history.length === 0">
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    No hay movimientos registrados en el kardex físico.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kardexApp() {
            return {
                products: [],
                kpis: {},
                search: '',
                showHistory: false,
                history: [],
                selectedProduct: null,

                init() {
                    this.fetchData();
                },

                fetchData() {
                    fetch(`api/kardex_all.php?action=list&q=${this.search}`)
                        .then(r => r.json())
                        .then(d => {
                            if(d.status === 'success') {
                                this.kpis = d.kpis;
                                this.products = d.data;
                            }
                        });
                },

                loadHistory(product) {
                    this.selectedProduct = product;
                    this.history = [];
                    this.showHistory = true;
                    
                    fetch(`api/kardex_all.php?action=history&codproducto=${product.codproducto}`)
                        .then(r => r.json())
                        .then(d => {
                            if(d.source !== 'error') {
                                this.history = d.data;
                            }
                        });
                },

                formatCurrency(val) {
                    return new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(val || 0);
                },

                formatDate(dateStr) {
                    if(!dateStr) return '';
                    return new Date(dateStr).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                }
            }
        }
    </script>
</body>
</html>
