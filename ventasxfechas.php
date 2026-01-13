<?php
session_start();
if(!isset($_SESSION['acceso'])) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas 🦄</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen" x-data="historial()">

    <!-- Header / Nav -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="panel.php" class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Historial de Ventas <span class="text-pink-500">🦄</span></h1>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-lg border border-gray-200">
                <input type="date" x-model="desde" class="bg-white border text-sm rounded-md px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400 font-bold">→</span>
                <input type="date" x-model="hasta" class="bg-white border text-sm rounded-md px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button @click="fetchVentas()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-md font-bold text-sm transition shadow-sm">
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 py-8">

        <!-- Loading -->
        <div x-show="loading" class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-400 font-medium">Cargando ventas...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && ventas.length === 0" x-cloak class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">👻</span>
            </div>
            <h3 class="text-lg font-bold text-gray-700">No hay ventas registradas</h3>
            <p class="text-gray-400">Prueba cambiando el rango de fechas.</p>
        </div>

        <!-- Sales List -->
        <div class="grid gap-4">
            <template x-for="venta in ventas" :key="venta.codventa">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    
                    <!-- Header Venta -->
                    <div @click="venta.open = !venta.open" class="p-5 cursor-pointer flex flex-col md:flex-row justify-between md:items-center gap-4 hover:bg-gray-50 transition">
                        
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-50 text-blue-600 font-bold p-3 rounded-lg border border-blue-100 text-center min-w-[80px]">
                                <div class="text-xs uppercase tracking-wider text-blue-400">Total</div>
                                <div x-text="formatMoney(venta.total)"></div>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                    <span x-text="'#' + venta.codventa"></span>
                                    <span class="px-2 py-0.5 rounded-full text-xs" 
                                          :class="venta.estado === 'EMITIDA' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                          x-text="venta.estado"></span>
                                </h3>
                                <div class="text-sm text-gray-500 mt-1 flex gap-4">
                                    <span class="flex items-center gap-1">
                                        📅 <span x-text="formatDate(venta.fecha)"></span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        💳 <span x-text="venta.forma"></span>
                                    </span>
                                    <span class="flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold" 
                                          :class="venta.tipo === 'CREDITO' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'"
                                          x-text="venta.tipo">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-gray-400">
                            <svg class="w-6 h-6 transform transition" :class="venta.open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>

                    </div>

                    <!-- Detalles (Expandible) -->
                    <div x-show="venta.open" x-collapse class="bg-gray-50 border-t border-gray-100 p-4">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2">Producto</th>
                                    <th class="px-4 py-2 text-center">Cant.</th>
                                    <th class="px-4 py-2 text-right">Precio</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="item in venta.items">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-800" x-text="item.producto"></td>
                                        <td class="px-4 py-3 text-center" x-text="item.cant"></td>
                                        <td class="px-4 py-3 text-right text-gray-600" x-text="formatMoney(item.precio)"></td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-800" x-text="formatMoney(item.subtotal)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        
                        <div class="mt-4 flex justify-end">
                            <a :href="'imprimir_ticket.php?cod=' + venta.codventa" target="_blank" 
                               class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">
                                🖨️ Reimprimir Ticket
                            </a>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>

    <script>
        function historial() {
            return {
                ventas: [],
                desde: '',
                hasta: '',
                loading: false,

                init() {
                    // Obtener params URL
                    const params = new URLSearchParams(window.location.search);
                    this.desde = params.get('desde') || new Date().toISOString().split('T')[0];
                    this.hasta = params.get('hasta') || new Date().toISOString().split('T')[0];
                    this.fetchVentas();
                },

                fetchVentas() {
                    this.loading = true;
                    fetch(`api/historial_ventas.php?desde=${this.desde}&hasta=${this.hasta}`)
                        .then(r => r.json())
                        .then(data => {
                            this.ventas = data.map(v => ({...v, open: false})); // Agregar estado open
                            this.loading = false;
                        })
                        .catch(() => {
                             alert("Error al cargar ventas");
                             this.loading = false;
                        });
                },

                formatMoney(amount) {
                    return 'C$ ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                },

                formatDate(dateStr) {
                    return new Date(dateStr).toLocaleString();
                }
            }
        }
    </script>
</body>
</html>