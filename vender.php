<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden" x-data="pos()">
    
    <!-- OVERLAY BLOQUEO CAJA CERRADA -->
    <div x-show="!cajaAbierta" style="display: none;" 
         class="fixed inset-0 bg-gray-900 bg-opacity-95 z-[9999] flex flex-col items-center justify-center text-center p-4 backdrop-blur-sm">
        
        <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-lg w-full transform transition-all scale-100">
            <div class="mb-6 bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto animate-pulse">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">¡Caja Cerrada! 🔒</h2>
            <p class="text-gray-500 mb-8 text-lg">No puedes realizar ventas hasta que inicies un turno de caja.</p>
            
            <a href="caja_express.php" 
               class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition transform hover:-translate-y-1 text-xl">
                🚀 Ir a Abrir Caja
            </a>
        </div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        <!-- Left Column: Product Grid -->
        <div class="w-2/3 p-4 overflow-y-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 content-start">
            
            <div class="col-span-full mb-4 flex gap-3">
                <a href="panel.php" class="bg-gray-800 hover:bg-gray-900 text-white p-4 rounded-lg flex items-center justify-center transition shadow-md group" title="Volver al Panel">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </a>
                <input type="text" x-model="busqueda" x-ref="buscador" 
                       @keydown.enter.prevent="escanear()"
                       placeholder="Buscar productos... (Escanee Aquí)" 
                       class="flex-1 p-4 rounded-lg border-2 border-gray-300 text-xl focus:border-blue-500 outline-none shadow-sm">
            </div>

            <template x-for="prod in filtrados" :key="prod.idproducto || prod.id">
                <div @click="agregar(prod)" 
                     :class="(parseFloat(prod.existencia) <= 0) ? 'opacity-50 grayscale cursor-not-allowed bg-gray-50' : 'bg-white hover:shadow-lg cursor-pointer'"
                     class="relative p-4 rounded-xl shadow transition border border-gray-100 flex flex-col justify-between h-40 group">
                    
                    <!-- Badge Stock -->
                    <div class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-bold shadow-sm"
                         :class="(parseFloat(prod.existencia) > 0) ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600'"
                         x-text="(parseFloat(prod.existencia) > 0) ? 'Stock: ' + prod.existencia : 'AGOTADO'">
                    </div>

                    <div class="mt-4">
                        <p class="font-bold text-gray-800 leading-tight line-clamp-2" x-text="prod.producto || prod.nombre"></p>
                        <p class="text-xs text-gray-400 mt-1" x-text="'Code: ' + (prod.codproducto || prod.codigo)"></p>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold" 
                             x-text="(prod.producto || prod.nombre || 'X').substring(0,2)"></div>
                        
                        <div class="text-green-600 font-bold text-xl" x-text="'C$ ' + getPrecio(prod)"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Right Column: Ticket -->
        <div class="w-1/3 bg-white shadow-2xl flex flex-col border-l">
            <div class="p-6 bg-gray-50 border-b">
                <h2 class="font-bold text-xl text-gray-800">Ticket de Venta</h2>
                <p class="text-sm text-gray-500" x-text="new Date().toLocaleString()"></p>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <template x-for="(item, index) in carrito" :key="index">
                    <div class="flex justify-between items-center group bg-gray-50 p-2 rounded">
                        <div class="flex-1">
                            <p class="font-medium text-gray-800" x-text="item.producto || item.nombre"></p>
                            <p class="text-xs text-gray-400">
                                <span x-text="item.cantidad"></span> x <span x-text="getPrecio(item)"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Botones +/- -->
                            <div class="flex items-center gap-1 bg-white border rounded">
                                <button @click="decrementar(index)" class="px-2 py-1 text-gray-600 hover:bg-gray-100">−</button>
                                <span class="px-2 font-bold text-sm" x-text="item.cantidad"></span>
                                <button @click="incrementar(index)" class="px-2 py-1 text-gray-600 hover:bg-gray-100">+</button>
                            </div>
                            <span class="font-bold text-gray-700" x-text="'C$ ' + (item.cantidad * getPrecio(item)).toFixed(2)"></span>
                            <button @click="remover(index)" class="text-red-400 hover:text-red-600">🗑️</button>
                        </div>
                    </div>
                </template>
                
                <div x-show="carrito.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p>Carrito vacío</p>
                </div>
            </div>

            <div class="p-6 bg-gray-50 border-t">
                <div class="flex justify-between text-xl font-bold mb-6 text-gray-800">
                    <span>Total</span>
                    <span x-text="'C$ ' + total()"></span>
                </div>
                <button @click="abrirModalPago()" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg transform active:scale-95 transition flex items-center justify-center gap-2">
                    <span>Cobrar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE PAGO -->
    <div x-show="modalPago" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Procesar Pago</h3>
                <button @click="modalPago = false" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            
            <div class="p-6 space-y-6">
                
                <!-- Buscador Cliente -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="busquedaCliente" @input.debounce.300ms="buscarCliente()" 
                               placeholder="Buscar por nombre o teléfono..." 
                               class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <button class="bg-blue-100 text-blue-600 p-3 rounded-lg" title="Cliente General" @click="seleccionarCliente({id:0, text:'Cliente General'})">👤</button>
                    </div>
                    
                    <!-- Resultados Búsqueda -->
                    <div x-show="clientesEncontrados.length > 0" 
                         class="absolute z-50 left-0 w-full bg-white border border-gray-200 rounded-b-lg shadow-xl max-h-60 overflow-y-auto mt-1">
                        <template x-for="cli in clientesEncontrados" :key="cli.id">
                            <div @click="seleccionarCliente(cli)" 
                                 class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 flex justify-between items-center group transition-colors">
                                <div>
                                    <p class="font-bold text-gray-700 group-hover:text-blue-700" x-text="cli.text"></p>
                                    <p class="text-xs text-gray-400" x-text="cli.telefono || 'Sin teléfono'"></p>
                                </div>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded group-hover:bg-blue-100 group-hover:text-blue-600" x-text="cli.id"></span>
                            </div>
                        </template>
                    </div>
                    
                    <div x-show="clienteSeleccionado" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="bg-green-500 text-white rounded-full p-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                                <span class="text-green-800 font-medium" x-text="clienteSeleccionado.text"></span>
                            </div>
                            <button @click="clienteSeleccionado = null; busquedaCliente = ''; tipoPago = 'CONTADO'" class="text-red-400 hover:text-red-600 text-sm font-bold px-2">Cambiar</button>
                        </div>
                        <!-- Mostrar línea de crédito si existe -->
                        <div x-show="clienteSeleccionado && clienteSeleccionado.limitecredito > 0" 
                             class="mt-2 pt-2 border-t border-green-200 flex items-center gap-1 text-xs">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-green-700">Crédito disponible: <span class="font-bold" x-text="'C$ ' + (clienteSeleccionado.limitecredito || 0).toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Selects Pago -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Venta</label>
                        <select x-model="tipoPago" class="w-full p-3 border rounded-lg bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="CONTADO">Contado</option>
                            <option value="CREDITO" 
                                    :disabled="!clienteSeleccionado || (clienteSeleccionado && clienteSeleccionado.limitecredito <= 0)">
                                Crédito
                            </option>
                        </select>
                        <!-- Advertencias -->
                        <div x-show="!clienteSeleccionado && tipoPago === 'CREDITO'" 
                             class="text-orange-600 text-xs mt-1 font-bold">
                            ⚠️ Seleccione un cliente para venta a crédito
                        </div>
                        <div x-show="clienteSeleccionado && clienteSeleccionado.limitecredito <= 0" 
                             class="text-red-600 text-xs mt-1 font-bold">
                            ❌ Este cliente no tiene línea de crédito aprobada
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método Pago</label>
                        <select x-model="formaPago" class="w-full p-3 border rounded-lg bg-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="CHEQUE">Cheque</option>
                            <option value="SIN_UTILIZACION_SISTEMA_FINANCIERO">Otros</option>
                        </select>
                    </div>
                </div>

                <!-- SECCIÓN DE PAGO MODERNIZADA -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-2xl border-2 border-gray-200">
                    
                    <!-- Total a Pagar -->
                    <div class="flex justify-between items-center mb-4 pb-4 border-b-2 border-gray-300">
                        <span class="text-gray-600 font-medium text-lg">Total a Pagar</span>
                        <span class="font-black text-3xl text-gray-900" x-text="'C$ ' + total()"></span>
                    </div>

                    <!-- Campo de Pago con Botón Exacto -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Recibido del Cliente</label>
                        <div class="flex gap-2">
                            <input type="number" 
                                   x-model="pagoCon" 
                                   x-ref="inputPago"
                                   @keydown.enter="confirmarVenta()"
                                   class="flex-1 p-4 border-2 rounded-xl text-right font-black text-3xl outline-none transition-all"
                                   :class="validacionPago()"
                                   step="0.01"
                                   placeholder="0.00">
                            <button @click="pagoCon = total()" 
                                    class="px-6 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition shadow-lg hover:shadow-xl transform hover:scale-105">
                                💯 Exacto
                            </button>
                        </div>
                    </div>

                    <!-- Botones de Denominación Rápida -->
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">⚡ PAGO RÁPIDO</label>
                        <div class="grid grid-cols-5 gap-2">
                            <button @click="pagoCon = 50" 
                                    class="py-3 bg-green-100 hover:bg-green-200 text-green-800 font-bold rounded-lg transition shadow hover:shadow-md transform hover:scale-105">
                                C$ 50
                            </button>
                            <button @click="pagoCon = 100" 
                                    class="py-3 bg-green-100 hover:bg-green-200 text-green-800 font-bold rounded-lg transition shadow hover:shadow-md transform hover:scale-105">
                                C$ 100
                            </button>
                            <button @click="pagoCon = 200" 
                                    class="py-3 bg-green-100 hover:bg-green-200 text-green-800 font-bold rounded-lg transition shadow hover:shadow-md transform hover:scale-105">
                                C$ 200
                            </button>
                            <button @click="pagoCon = 500" 
                                    class="py-3 bg-green-100 hover:bg-green-200 text-green-800 font-bold rounded-lg transition shadow hover:shadow-md transform hover:scale-105">
                                C$ 500
                            </button>
                            <button @click="pagoCon = 1000" 
                                    class="py-3 bg-green-100 hover:bg-green-200 text-green-800 font-bold rounded-lg transition shadow hover:shadow-md transform hover:scale-105">
                                C$ 1000
                            </button>
                        </div>
                    </div>

                    <!-- CAMBIO DESTACADO -->
                    <div class="p-6 rounded-2xl text-center transform transition-all"
                         :class="cambioClase()">
                        <div class="text-sm font-bold uppercase tracking-wider mb-1" 
                             :class="parseFloat(cambio()) < 0 ? 'text-red-700' : 'text-green-700'">
                            <span x-show="parseFloat(cambio()) >= 0">💰 Cambio a Devolver</span>
                            <span x-show="parseFloat(cambio()) < 0">⚠️ Falta por Pagar</span>
                        </div>
                        <div class="font-black text-5xl tracking-tight" 
                             :class="parseFloat(cambio()) < 0 ? 'text-red-600' : 'text-green-600'"
                             x-text="'C$ ' + Math.abs(parseFloat(cambio())).toFixed(2)">
                        </div>
                    </div>

                </div>

            </div>

            <!-- Botones de Acción - MEJORADOS -->
            <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 border-t-4 border-green-500 sticky bottom-0 shadow-2xl">
                <div class="flex gap-4">
                    <!-- Botón Cancelar -->
                    <button @click="modalPago = false" 
                            class="flex-1 py-5 px-6 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-2xl font-bold text-lg transition shadow-lg transform hover:scale-105 active:scale-95">
                        ❌ Cancelar
                    </button>
                    
                    <!-- Botón Confirmar Venta - MUY VISIBLE -->
                    <button @click="confirmarVenta()" 
                            :disabled="parseFloat(pagoCon) < parseFloat(total())"
                            :class="parseFloat(pagoCon) < parseFloat(total()) ? 
                                    'opacity-50 cursor-not-allowed bg-gray-400' : 
                                    'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-2xl hover:shadow-green-500/50'"
                            class="flex-1 py-6 px-8 text-white rounded-2xl font-black text-2xl transition-all transform hover:scale-105 active:scale-95 border-2 border-green-400 focus:outline-none focus:ring-4 focus:ring-green-300">
                        <span x-show="parseFloat(pagoCon) >= parseFloat(total())" class="flex items-center justify-center gap-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            ✅ CONFIRMAR VENTA
                        </span>
                        <span x-show="parseFloat(pagoCon) < parseFloat(total())" class="flex items-center justify-center gap-2">
                            ⚠️ Pago Insuficiente
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EXITO POST-VENTA -->
    <div x-show="ventaExitosa" style="display: none;" 
         class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md text-center transform transition-all scale-100">
            
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-2">¡Venta Registrada!</h2>
            <p class="text-gray-500 mb-8">Ticket: <span class="font-mono font-bold text-gray-800" x-text="ultimoCodigo"></span></p>

            <div class="space-y-3">
                <button @click="imprimirTicket()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition shadow-lg">
                    <span>🖨️ Imprimir Ticket PDF</span>
                </button>

                <button @click="enviarWhatsapp()" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition shadow-lg">
                    <span>📱 Enviar por WhatsApp</span>
                </button>

                <button @click="nuevaVenta()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg border border-gray-300 transition mt-4">
                    🏠 Nueva Venta
                </button>
            </div>
        </div>
    </div>

    <script>
        function pos() {
            return {
                cajaAbierta: true, // Default true to avoid flash, validated in init()
                productos: [],
                carrito: [],
                busqueda: '',
                
                // Estado Modal Pago
                modalPago: false,
                ventaExitosa: false, // Nuevo estado
                ultimoCodigo: '',    // Nuevo estado
                clienteSeleccionado: null,
                busquedaCliente: '',
                clientesEncontrados: [],
                pagoCon: 0,
                tipoPago: 'CONTADO',
                formaPago: 'EFECTIVO',

                get filtrados() {
                    if (this.busqueda === '') return this.productos;
                    return this.productos.filter(prod => {
                        const nombre = (prod.producto || prod.nombre || '').toLowerCase();
                        const codigo = (prod.codproducto || prod.codigo || '').toLowerCase();
                        const term = this.busqueda.toLowerCase();
                        return nombre.includes(term) || codigo.includes(term);
                    });
                },

                init() {
                    // Verificar Estado Caja
                    fetch('api/caja_control.php?accion=estado')
                        .then(r => r.json())
                        .then(data => {
                            if (data.estado === 'CERRADA') {
                                this.cajaAbierta = false;
                            }
                        })
                        .catch(e => console.error("Error checking caja:", e));

                    fetch('api/productos.php')
                        .then(res => res.json())
                        .then(data => {
                            this.productos = data;
                        })
                        .catch(err => console.error("Error:", err));

                    // Auto-focus al cerrar modal pago
                    this.$watch('modalPago', value => { 
                        if(!value) this.$nextTick(() => this.$refs.buscador.focus());
                    });
                    
                    // Auto-focus al iniciar
                    this.$nextTick(() => this.$refs.buscador.focus());
                },

                playBeep() {
                    const audio = document.getElementById('beepSound');
                    if(audio) { audio.currentTime = 0; audio.play().catch(e => {}); }
                },

                escanear() {
                    if (!this.busqueda) return;

                    // 1. Buscar coincidencia EXACTA por código
                    const term = this.busqueda.trim().toUpperCase();
                    // Buscamos tanto en codproducto como en codigobarra (si existiera)
                    const encontrado = this.productos.find(p => 
                        (p.codproducto || '').toUpperCase() === term || 
                        (p.codigobarra || '').toUpperCase() === term ||
                        (p.codigo || '').toUpperCase() === term
                    );

                    if (encontrado) {
                        // ¡BINGO! Es un escaneo
                        this.agregar(encontrado);
                        this.playBeep();      // Feedback auditivo
                        this.busqueda = '';   // Limpiar para el siguiente
                    } else {
                        // No es un código exacto; dejamos el filtro visual.
                    }
                },

                getPrecio(prod) {
                    let valor = prod.precioxpublico || 0;
                    return parseFloat(valor).toFixed(2);
                },

                agregar(prod) {
                    let stockActual = parseFloat(prod.existencia) || 0;
                    
                    // Si el stock es 0 o menor, no permitir agregar nada
                    if (stockActual <= 0) return;

                    // Buscar si el producto ya está en el carrito
                    const idProducto = prod.idproducto || prod.id;
                    const itemExistente = this.carrito.find(i => (i.idproducto || i.id) == idProducto);

                    if (itemExistente) {
                        // Producto ya existe: verificar si hay stock suficiente
                        if (itemExistente.cantidad >= stockActual) {
                            alert("¡No hay suficiente stock! Quedan: " + stockActual);
                            return;
                        }
                        // Incrementar cantidad
                        itemExistente.cantidad++;
                    } else {
                        // Producto nuevo: agregarlo con cantidad = 1
                        this.carrito.push({
                            ...prod,
                            cantidad: 1
                        });
                    }
                },

                incrementar(index) {
                    const item = this.carrito[index];
                    const prod = this.productos.find(p => (p.idproducto || p.id) == (item.idproducto || item.id));
                    const stockActual = parseFloat(prod?.existencia) || 0;
                    
                    if (item.cantidad >= stockActual) {
                        alert("¡No hay más stock! Máximo: " + stockActual);
                        return;
                    }
                    item.cantidad++;
                },

                decrementar(index) {
                    const item = this.carrito[index];
                    if (item.cantidad > 1) {
                        item.cantidad--;
                    } else {
                        // Si cantidad es 1, eliminar del carrito
                        this.remover(index);
                    }
                },

                remover(index) {
                    this.carrito.splice(index, 1);
                },

                total() {
                    return this.carrito.reduce((acc, item) => {
                        return acc + (parseFloat(this.getPrecio(item)) * (item.cantidad || 1));
                    }, 0).toFixed(2);
                },

                // FUNCIONES PARA MODAL MODERNIZADO
                cambio() {
                    return (parseFloat(this.pagoCon || 0) - parseFloat(this.total())).toFixed(2);
                },

                validacionPago() {
                    const pago = parseFloat(this.pagoCon) || 0;
                    const tot = parseFloat(this.total());
                    if (pago === 0) return 'border-gray-300 focus:border-blue-500';
                    if (pago < tot) return 'border-red-500 bg-red-50 focus:border-red-600';
                    return 'border-green-500 bg-green-50 focus:border-green-600';
                },

                cambioClase() {
                    const camb = parseFloat(this.cambio());
                    if (camb < 0) return 'bg-red-100 border-2 border-red-300';
                    return 'bg-green-100 border-2 border-green-300';
                },

                // Lógica Modal
                abrirModalPago() {
                    if (this.carrito.length === 0) return alert("Carrito vacío");
                    this.modalPago = true;
                    this.pagoCon = this.total(); // Sugerir pago exacto
                    this.clienteSeleccionado = null;
                    this.busquedaCliente = '';
                    this.clientesEncontrados = [];
                    // Auto-focus en el input de pago
                    this.$nextTick(() => {
                        if(this.$refs.inputPago) this.$refs.inputPago.select();
                    });
                },

                buscarCliente() {
                    if (this.busquedaCliente.length < 2) {
                        this.clientesEncontrados = [];
                        return;
                    }
                    // API devuelve 'text', 'telefono', y 'limitecredito' para validación
                    fetch(`api/clientes.php?q=${this.busquedaCliente}`)
                        .then(r => r.json())
                        .then(data => {
                            // Asegurar que limitecredito esté presente como número
                            this.clientesEncontrados = data.map(cli => ({
                                ...cli,
                                limitecredito: parseFloat(cli.limitecredito || 0)
                            }));
                        })
                        .catch(e => console.error(e));
                },
                
                seleccionarCliente(cli) {
                    // Guardamos el objeto completo con el limitecredito
                    this.clienteSeleccionado = {
                        ...cli,
                        limitecredito: parseFloat(cli.limitecredito || 0)
                    };
                    this.busquedaCliente = '';            // Limpiamos el input
                    this.clientesEncontrados = [];        // Ocultamos la lista
                    
                    // Si seleccionó crédito pero no tiene línea, revertir a contado
                    if (this.tipoPago === 'CREDITO' && this.clienteSeleccionado.limitecredito <= 0) {
                        this.tipoPago = 'CONTADO';
                    }
                },

                confirmarVenta() {
                    // Si hay cliente seleccionado usamos su ID, si no, usamos '0' (Genérico)
                    const idClienteFinal = this.clienteSeleccionado ? this.clienteSeleccionado.id : '0'; 
                    
                    fetch('api/guardar_venta.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            cliente_id: idClienteFinal,
                            total: this.total(),
                            productos: this.carrito,
                            tipopago: this.tipoPago,
                            formapago: this.formaPago
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            this.ultimoCodigo = data.ticket;
                            this.carrito = [];
                            this.modalPago = false;
                            this.clienteSeleccionado = null;
                            this.pagoCon = 0;
                            
                            // Mostrar Modal Exito
                            this.ventaExitosa = true;
                        } else {
                            alert('❌ ERROR: ' + data.message);
                        }
                    });
                },

                imprimirTicket() {
                    window.open('imprimir_ticket.php?cod=' + this.ultimoCodigo, '_blank');
                },

                enviarWhatsapp() {
                    // Generar mensaje resumen
                    let texto = "Hola, adjunto resumen de compra: " + this.ultimoCodigo;
                    let url = "https://wa.me/?text=" + encodeURIComponent(texto);
                    window.open(url, '_blank');
                },

                nuevaVenta() {
                    this.ventaExitosa = false;
                    this.ultimoCodigo = '';
                }
            }
        }
    </script>
    <audio id="beepSound" src="https://cdn.freesound.org/previews/242/242501_4414128-lq.mp3"></audio>
</body>
</html>
