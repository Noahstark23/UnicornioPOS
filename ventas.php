<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
    if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas | Unicornio POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800" x-data="salesManager()">

    <!-- NAV -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <div class="bg-blue-600 text-white p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900">📊 Historial de Ventas <span class="text-blue-600 text-sm font-normal bg-blue-50 px-2 py-1 rounded-full border border-blue-100">Modernizado</span></h1>
        </div>
        <div class="flex gap-3">
            <a href="panel.php" class="text-gray-500 hover:text-gray-900 font-medium transition">Volver al Panel</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium transition">Salir</a>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto p-6">
        
        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Ventas Hoy -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Ventas Hoy</p>
                        <p class="text-3xl font-black text-gray-900" x-text="stats.ventas_hoy || 0"></p>
                        <p class="text-sm text-green-600 font-bold mt-1" x-text="formatMoney(stats.monto_hoy || 0)"></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Total Mes -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Total Mes</p>
                        <p class="text-3xl font-black text-blue-600" x-text="formatMoney(stats.total_mes || 0)"></p>
                        <p class="text-sm text-gray-600 mt-1" x-text="(stats.ventas_mes || 0) + ' ventas'"></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Promedio Ticket -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Promedio Ticket</p>
                        <p class="text-3xl font-black text-purple-600" x-text="formatMoney(stats.promedio_ticket || 0)"></p>
                        <p class="text-sm text-gray-600 mt-1">Por venta</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Métodos de Pago -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div>
                    <p class="text-sm text-gray-500 uppercase font-semibold mb-2">Formas de Pago (Mes)</p>
                    <template x-for="(monto, forma) in stats.formas_pago" :key="forma">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600" x-text="forma"></span>
                            <span class="font-bold text-gray-800" x-text="formatMoney(monto)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- FILTROS Y BÚSQUEDA -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Fecha Desde -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Desde</label>
                    <input type="date" x-model="filters.desde" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Fecha Hasta -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hasta</label>
                    <input type="date" x-model="filters.hasta" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo</label>
                    <select x-model="filters.tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="TODOS">Todos</option>
                        <option value="CONTADO">Contado</option>
                        <option value="CREDITO">Crédito</option>
                    </select>
                </div>

                <!-- Forma -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Forma de Pago</label>
                    <select x-model="filters.forma" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="TODOS">Todos</option>
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                    </select>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Estado</label>
                    <select x-model="filters.estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="TODOS">Todos</option>
                        <option value="COMPLETADO">Completado</option>
                        <option value="ANULADO">Anulado</option>
                    </select>
                </div>

                <!-- Búsqueda -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Buscar</label>
                    <input type="text" x-model="filters.search" placeholder="Código, cliente..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>

            <div class="flex gap-3 mt-4">
                <button @click="fetchSales()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Buscar
                </button>
                <button @click="resetFilters()" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold transition">
                    Limpiar Filtros
                </button>
                
                <!-- Botones de Exportación -->
                <div class="ml-auto flex gap-2">
                    <a href="reportepdf?tipo=<?php echo encrypt("VENTAS") ?>" target="_blank" 
                       class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>
                        PDF
                    </a>
                    <a href="reporteexcel?documento=<?php echo encrypt("EXCEL") ?>&tipo=<?php echo encrypt("VENTAS") ?>" 
                       class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>
                        Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                            <th class="p-4 font-semibold">Código</th>
                            <th class="p-4 font-semibold">Fecha</th>
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold text-center">Items</th>
                            <th class="p-4 font-semibold text-right">Total</th>
                            <th class="p-4 font-semibold text-center">Tipo</th>
                            <th class="p-4 font-semibold text-center">Forma</th>
                            <th class="p-4 font-semibold text-center">Estado</th>
                            <th class="p-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="venta in sales" :key="venta.codventa">
                            <tr class="hover:bg-blue-50/30 transition group">
                                <td class="p-4 font-mono font-bold text-gray-800" x-text="venta.codventa"></td>
                                <td class="p-4 text-sm text-gray-600" x-text="formatDateTime(venta.fechaventa)"></td>
                                <td class="p-4">
                                    <div class="font-semibold text-gray-900" x-text="venta.nomcliente"></div>
                                    <div class="text-xs text-gray-400" x-text="venta.dnicliente"></div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-bold" x-text="venta.total_items"></span>
                                </td>
                                <td class="p-4 text-right font-mono font-bold text-gray-900" x-text="formatMoney(venta.totalpago)"></td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold"
                                          :class="venta.tipopago == 'CONTADO' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                                          x-text="venta.tipopago"></span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700" x-text="venta.formapago"></span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold"
                                          :class="venta.statusventa == 'COMPLETADO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                          x-text="venta.statusventa"></span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openDetailsModal(venta.codventa)" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs font-bold transition">
                                            Ver Detalles
                                        </button>
                                        <a :href="'reportepdf.php?tipo=' + venta.ticket_encoded + '&codventa=' + venta.codventa_encoded" target="_blank" 
                                           class="text-gray-400 hover:text-gray-600 transition" title="Ticket">
                                            📄
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <tr x-show="sales.length === 0 && !loading">
                            <td colspan="9" class="p-12 text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                No se encontraron ventas
                            </td>
                        </tr>
                        
                        <!-- Loading -->
                        <tr x-show="loading">
                            <td colspan="9" class="p-12 text-center text-blue-500 animate-pulse font-medium">
                                Cargando ventas...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL DETALLES DE VENTA -->
    <div x-show="detailsModalOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all scale-100" @click.away="detailsModalOpen = false">
            
            <!-- HEADER -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-2xl shrink-0">
                <div>
                    <h3 class="text-xl font-bold">Detalle de Venta</h3>
                    <p class="text-sm opacity-90">Código: <span class="font-mono font-bold" x-text="selectedSale?.codventa"></span></p>
                </div>
                <button @click="detailsModalOpen = false" class="text-white hover:bg-white/20 rounded-full p-2 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- BODY SCROLLEABLE -->
            <div class="p-6 overflow-y-auto flex-1">
                <template x-if="loadingDetails">
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto animate-spin text-blue-600 mb-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-600">Cargando detalles...</p>
                    </div>
                </template>

                <template x-if="!loadingDetails && selectedSale">
                    <div>
                        <!-- Info Venta -->
                        <div class="bg-blue-50 p-4 rounded-xl mb-4 border border-blue-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Fecha</p>
                                    <p class="font-bold text-gray-900" x-text="formatDateTime(selectedSale.venta?.fechaventa)"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Estado</p>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold inline-block"
                                          :class="selectedSale.venta?.statusventa == 'COMPLETADO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                          x-text="selectedSale.venta?.statusventa"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Tipo</p>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold inline-block"
                                          :class="selectedSale.venta?.tipopago == 'CONTADO' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                                          x-text="selectedSale.venta?.tipopago"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Forma de Pago</p>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold inline-block bg-blue-100 text-blue-700" x-text="selectedSale.venta?.formapago"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Cliente -->
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-700 mb-2">Cliente</h4>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="font-bold text-gray-900" x-text="selectedSale.cliente?.nomcliente"></p>
                                <p class="text-sm text-gray-600">DNI: <span x-text="selectedSale.cliente?.dnicliente"></span></p>
                                <p class="text-sm text-gray-600" x-show="selectedSale.cliente?.tlfcliente">Tel: <span x-text="selectedSale.cliente?.tlfcliente"></span></p>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-700 mb-2">Productos</h4>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="p-2 text-left font-semibold text-gray-600">Producto</th>
                                            <th class="p-2 text-center font-semibold text-gray-600">Cant.</th>
                                            <th class="p-2 text-right font-semibold text-gray-600">Precio</th>
                                            <th class="p-2 text-right font-semibold text-gray-600">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="item in selectedSale.items" :key="item.codproducto">
                                            <tr>
                                                <td class="p-2 font-medium text-gray-900" x-text="item.producto"></td>
                                                <td class="p-2 text-center text-gray-600" x-text="item.cantidad"></td>
                                                <td class="p-2 text-right font-mono text-gray-600" x-text="formatMoney(item.precio)"></td>
                                                <td class="p-2 text-right font-mono font-bold text-gray-900" x-text="formatMoney(item.subtotal)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Totales -->
                        <div class="bg-gray-50 p-4 rounded-xl border-2 border-gray-200">
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-700">Total:</span>
                                <span class="font-black text-2xl text-blue-600" x-text="formatMoney(selectedSale.venta?.totalpago)"></span>
                            </div>
                            <template x-if="selectedSale.venta?.pagacon > 0">
                                <div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>Pagó con:</span>
                                        <span x-text="formatMoney(selectedSale.venta?.pagacon)"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-green-600 font-bold">
                                        <span>Vuelto:</span>
                                        <span x-text="formatMoney(selectedSale.venta?.vuelto)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Abonos (si es crédito) -->
                        <template x-if="selectedSale.abonos && selectedSale.abonos.length > 0">
                            <div class="mt-4">
                                <h4 class="font-bold text-gray-700 mb-2">Abonos Realizados</h4>
                                <div class="space-y-2">
                                    <template x-for="abono in selectedSale.abonos" :key="abono.fecha">
                                        <div class="bg-green-50 p-3 rounded-lg flex justify-between items-center border border-green-100">
                                            <div>
                                                <p class="text-sm text-gray-600" x-text="formatDateTime(abono.fecha)"></p>
                                                <p class="text-xs text-gray-500">Caja: <span x-text="abono.codcaja"></span></p>
                                            </div>
                                            <p class="font-bold text-green-600" x-text="formatMoney(abono.monto)"></p>
                                        </div>
                                    </template>
                                    <div class="bg-orange-50 p-3 rounded-lg flex justify-between border-2 border-orange-200">
                                        <span class="font-bold text-gray-700">Saldo Pendiente:</span>
                                        <span class="font-black text-xl text-orange-600" x-text="formatMoney(selectedSale.saldo_pendiente)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- FOOTER -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl shrink-0 flex gap-2">
                <a :href="selectedSale ? 'reportepdf.php?tipo=' + selectedSale.venta.ticket_encoded + '&codventa=' + selectedSale.venta.codventa_encoded : '#'" target="_blank"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-center transition">
                    Imprimir Ticket
                </a>
                <a :href="selectedSale ? 'reportepdf.php?tipo=' + selectedSale.venta.factura_encoded + '&codventa=' + selectedSale.venta.codventa_encoded : '#'" target="_blank"
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold text-center transition">
                    Imprimir Factura
                </a>
                <button @click="detailsModalOpen = false" 
                        class="px-6 bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 rounded-xl font-bold transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div x-show="toast.show" x-cloak 
         class="fixed bottom-6 right-6 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all"
         :class="toast.type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
         x-transition:enter="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="translate-y-10 opacity-0">
        <span class="font-bold" x-text="toast.message"></span>
    </div>

    <script>
        function salesManager() {
            return {
                sales: [],
                stats: {},
                filters: {
                    desde: '<?php echo date('Y-m-01'); ?>',  // Primer día del mes
                    hasta: '<?php echo date('Y-m-d'); ?>',    // Hoy
                    tipo: 'TODOS',
                    forma: 'TODOS',
                    estado: 'TODOS',
                    search: ''
                },
                loading: false,
                
                // Modal Detalles
                detailsModalOpen: false,
                loadingDetails: false,
                selectedSale: null,
                
                toast: { show: false, message: '', type: 'success' },

                init() {
                    this.fetchSales();
                },

                async fetchSales() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            desde: this.filters.desde,
                            hasta: this.filters.hasta,
                            tipo: this.filters.tipo,
                            forma: this.filters.forma,
                            estado: this.filters.estado,
                            search: this.filters.search,
                            t: new Date().getTime()
                        });

                        const response = await fetch(`api/sales.php?${params}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            this.sales = data.ventas;
                            this.stats = data.stats;
                        } else {
                            this.showToast('Error al cargar ventas', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToast('Error de conexión', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async openDetailsModal(codventa) {
                    this.detailsModalOpen = true;
                    this.loadingDetails = true;
                    this.selectedSale = null;

                    try {
                        const response = await fetch(`api/sale_details.php?codventa=${codventa}&t=${new Date().getTime()}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            this.selectedSale = data;
                        } else {
                            this.showToast('Error al cargar detalles', 'error');
                            this.detailsModalOpen = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToast('Error de conexión', 'error');
                        this.detailsModalOpen = false;
                    } finally {
                        this.loadingDetails = false;
                    }
                },

                resetFilters() {
                    this.filters = {
                        desde: '<?php echo date('Y-m-01'); ?>',
                        hasta: '<?php echo date('Y-m-d'); ?>',
                        tipo: 'TODOS',
                        forma: 'TODOS',
                        estado: 'TODOS',
                        search: ''
                    };
                    this.fetchSales();
                },

                formatMoney(amount) {
                    return 'C$ ' + parseFloat(amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                },

                formatDateTime(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleString('es-NI', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                showToast(msg, type = 'success') {
                    this.toast.message = msg;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => this.toast.show = false, 3000);
                }
            }
        }
    </script>
</body>
</html>
<?php } else { ?>   
    <script type='text/javascript' language='javascript'>
    alert('NO TIENES PERMISO PARA ACCEDER A ESTA PAGINA.\\nCONSULTA CON EL ADMINISTRADOR PARA QUE TE DE ACCESO')  
    document.location.href='panel'   
    </script> 
<?php } } else { ?>
    <script type='text/javascript' language='javascript'>
    alert('NO TIENES PERMISO PARA ACCEDER AL SISTEMA.\\nDEBERA DE INICIAR SESION')  
    document.location.href='logout'  
    </script> 
<?php } ?>