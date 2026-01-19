<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Caja Express 🦄</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4" x-data="caja()">

    <!-- Encabezado -->
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-8 tracking-tight">
        Gestión de Caja Express <span class="text-pink-500">🦄</span>
    </h1>

    <!-- VISTA: CAJA CERRADA -->
    <div x-show="estado === 'CERRADA'" x-cloak class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gray-800 p-6 text-center">
                <h2 class="text-white text-xl font-bold uppercase tracking-wide">📦 Caja Cerrada</h2>
                <p class="text-gray-400 text-sm mt-1">Inicie turno para comenzar a vender</p>
            </div>
            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-gray-600 font-bold mb-2 text-lg">Monto de Apertura (C$)</label>
                    <input type="number" x-model="montoApertura" 
                           class="w-full p-4 text-3xl font-bold text-center border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 outline-none transition" 
                           placeholder="0.00">
                </div>
                <button @click="abrirCaja()"
                        :disabled="!montoApertura || montoApertura < 0"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-5 rounded-xl text-xl shadow-lg transform active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    🚀 ABRIR TURNO
                </button>
            </div>
        </div>
    </div>

    <!-- VISTA: CAJA ABIERTA -->
    <div x-show="estado === 'ABIERTA'" x-cloak class="w-full max-w-4xl">
        
        <!-- Grid de KPIs - Desglose por Forma de Pago -->
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
            
            <!-- Fondo Inicial -->
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-sm text-center">
                <p class="text-blue-500 font-bold uppercase text-xs tracking-wider mb-1">Fondo Inicial</p>
                <p class="text-2xl font-extrabold text-blue-800" x-text="formatMoney(calculos.inicial)"></p>
            </div>

            <!-- Ventas EFECTIVO (badge destacado) -->
            <div class="bg-green-50 border-2 border-green-400 p-4 rounded-xl shadow-md text-center relative">
                <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                    💵 EFECTIVO
                </div>
                <p class="text-green-600 font-bold uppercase text-xs tracking-wider mb-1">Ventas Efectivo</p>
                <p class="text-3xl font-extrabold text-green-800" x-text="formatMoney(calculos.ventas_efectivo || 0)"></p>
            </div>

            <!-- Ventas CRÉDITO -->
            <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl shadow-sm text-center">
                <p class="text-orange-500 font-bold uppercase text-xs tracking-wider mb-1">Ventas Crédito</p>
                <p class="text-2xl font-extrabold text-orange-800" x-text="formatMoney(calculos.ventas_credito || 0)"></p>
                <p class="text-xs text-orange-400 mt-1">Por cobrar</p>
            </div>

            <!-- Ventas TARJETA -->
            <div class="bg-purple-50 border border-purple-200 p-4 rounded-xl shadow-sm text-center">
                <p class="text-purple-500 font-bold uppercase text-xs tracking-wider mb-1">Tarjeta/Transfer</p>
                <p class="text-2xl font-extrabold text-purple-800" x-text="formatMoney(calculos.ventas_tarjeta || 0)"></p>
                <p class="text-xs text-purple-400 mt-1">En banco</p>
            </div>

            <!-- Abonos Recibidos (NEW) -->
            <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-xl shadow-sm text-center relative">
                <div class="absolute top-1 right-1 bg-indigo-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                     RECUPERADO
                </div>
                <p class="text-indigo-500 font-bold uppercase text-xs tracking-wider mb-1">Abonos Recibidos</p>
                <p class="text-2xl font-extrabold text-indigo-800" x-text="formatMoney(calculos.abonos || 0)"></p>
                <p class="text-xs text-indigo-400 mt-1">Pagos de créditos</p>
            </div>
        </div>

        <!-- Total Esperado en Caja FÍSICA (tarjeta destacada) -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-2xl shadow-xl text-white text-center mb-4">
            <p class="text-sm font-bold uppercase tracking-wider mb-1 opacity-90">💰 Total Esperado en Caja FÍSICA</p>
            <p class="text-5xl font-extrabold" x-text="formatMoney(calculos.esperado || 0)"></p>
            <p class="text-xs mt-2 opacity-75">
                Fondo (<span x-text="formatMoney(calculos.inicial || 0)"></span>) 
                + Efectivo (<span x-text="formatMoney(calculos.ventas_efectivo || 0)"></span>)
                + Abonos (<span x-text="formatMoney(calculos.abonos || 0)"></span>)
                + Ingresos (<span x-text="formatMoney(calculos.ingresos || 0)"></span>)
                - Egresos (<span x-text="formatMoney(calculos.egresos || 0)"></span>)
            </p>
        </div>

        <!-- Resumen Total Ventas (todas las formas de pago) -->
        <div class="bg-gray-100 border border-gray-300 p-4 rounded-xl mb-6 flex justify-between items-center">
            <div>
                <p class="text-gray-600 text-sm font-bold">Total Ventas del Día (Todas las formas de pago)</p>
                <p class="text-xs text-gray-400 mt-1">Efectivo + Crédito + Tarjeta</p>
            </div>
            <p class="text-3xl font-extrabold text-gray-800" x-text="formatMoney(calculos.total_ventas || 0)"></p>
        </div>

        <!-- Botones de Movimientos -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <button @click="abrirModalMovimiento('INGRESO')" 
                    class="bg-green-100 hover:bg-green-200 text-green-700 border-2 border-green-200 font-bold py-4 rounded-xl flex items-center justify-center gap-2 text-lg shadow-sm transition transform active:scale-95">
                <span>➕</span> REGISTRAR ENTRADA
            </button>
            <button @click="abrirModalMovimiento('EGRESO')" 
                    class="bg-red-100 hover:bg-red-200 text-red-700 border-2 border-red-200 font-bold py-4 rounded-xl flex items-center justify-center gap-2 text-lg shadow-sm transition transform active:scale-95">
                <span>➖</span> REGISTRAR SALIDA / GASTO
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-left">
                <p class="text-gray-500 text-sm">Turno iniciado el:</p>
                <p class="font-bold text-gray-800" x-text="formatDate(info.fechaapertura)"></p>
            </div>
            
            <button @click="abrirModalCierre()" 
                    class="w-full md:w-auto bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform active:scale-95 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                REALIZAR CORTE / CERRAR
            </button>
        </div>

    </div>

    <!-- MODAL DE CIERRE -->
    <div x-show="modalCierre" x-cloak 
         class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm"
         x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 p-8">
            
            <h3 class="text-2xl font-extrabold text-gray-800 mb-2">Cierre de Caja 🔒</h3>
            <p class="text-gray-500 mb-6">Por favor, cuente el dinero físico en efectivo.</p>

            <div class="bg-gray-50 p-4 rounded-xl mb-4">
                <label class="block text-gray-600 font-bold mb-2 text-sm">¿Cuánto dinero tienes en mano?</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold text-xl">C$</span>
                    <input type="number" x-model="montoCierre" 
                           class="w-full pl-12 pr-4 py-3 text-2xl font-bold bg-white border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none text-right"
                           placeholder="0.00">
                </div>
            </div>

            <!-- DESGLOSE DETALLADO -->
            <div x-show="montoCierre" class="space-y-3 mb-4">
                
                <!-- Desglose de Caja - FÓRMULA COMPLETA -->
                <div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-xl">
                    <p class="text-xs font-bold text-blue-600 mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        FÓRMULA MAESTRA - EFECTIVO ESPERADO
                    </p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fondo Inicial:</span>
                            <span class="font-bold" x-text="formatMoney(calculos.inicial)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Ventas Efectivo:</span>
                            <span class="font-bold text-green-600" x-text="formatMoney(calculos.ventas_efectivo)"></span>
                        </div>
                        <!-- Abonos de créditos -->
                        <div x-show="calculos.abonos > 0" class="flex justify-between">
                            <span class="text-gray-600">+ Abonos (Pagos de créditos):</span>
                            <span class="font-bold text-green-600" x-text="formatMoney(calculos.abonos || 0)"></span>
                        </div>
                        <!-- Ingresos manuales -->
                        <div x-show="calculos.ingresos > 0" class="flex justify-between">
                            <span class="text-gray-600">+ Ingresos Manuales:</span>
                            <span class="font-bold text-green-600" x-text="formatMoney(calculos.ingresos)"></span>
                        </div>
                        <!-- Egresos manuales -->
                        <div x-show="calculos.egresos > 0" class="flex justify-between">
                            <span class="text-gray-600">- Egresos (Salidas/Gastos):</span>
                            <span class="font-bold text-red-600" x-text="formatMoney(calculos.egresos)"></span>
                        </div>
                        <div class="flex justify-between border-t-2 border-blue-300 pt-2 mt-1">
                            <span class="text-gray-700 font-extrabold">= EFECTIVO ESPERADO:</span>
                            <span class="font-extrabold text-blue-700 text-lg" x-text="formatMoney(calculos.esperado)"></span>
                        </div>
                        <div class="flex justify-between mt-2 pt-2 border-t border-blue-200">
                            <span class="text-gray-600">- Efectivo Contado:</span>
                            <span class="font-bold" x-text="formatMoney(montoCierre)"></span>
                        </div>
                        <div class="flex justify-between border-t-2 border-blue-300 pt-2" 
                             :class="diferencia >= 0 ? 'text-green-600' : 'text-red-600'">
                            <span class="font-bold" x-text="diferencia >= 0 ? '✅ SOBRA:' : '❌ FALTA:'"></span>
                            <span class="font-extrabold text-xl" x-text="formatMoney(Math.abs(diferencia))"></span>
                        </div>
                    </div>
                </div>

                <!-- Info Ventas NO en Caja Física -->
                <div class="bg-orange-50 border border-orange-200 p-3 rounded-lg">
                    <p class="text-xs font-bold text-orange-600 mb-1">ℹ️ NO incluido en caja física</p>
                    <div class="flex justify-between text-sm">
                        <span>Ventas Crédito (por cobrar):</span>
                        <span class="font-bold" x-text="formatMoney(calculos.ventas_credito)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Ventas Tarjeta (en banco):</span>
                        <span class="font-bold" x-text="formatMoney(calculos.ventas_tarjeta)"></span>
                    </div>
                </div>

                <!-- Comentarios Cajero -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Observaciones del cierre:</label>
                    <textarea x-model="comentariosCierre" 
                              class="w-full p-3 border rounded-lg text-sm"
                              rows="2"
                              placeholder="Opcional: explique diferencias, incidentes, etc..."></textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="modalCierre = false" class="flex-1 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="button" @click="confirmarCierre()" 
                        :disabled="!montoCierre || procesandoCierre"
                        class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg transform active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!procesandoCierre">CONFIRMAR</span>
                    <span x-show="procesandoCierre">PROCESANDO...</span>
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL DE MOVIMIENTOS -->
    <div x-show="modalMovimiento" x-cloak 
         class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm"
         x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden p-6 transform transition-all delay-100 scale-100">
            
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" :class="tipoMovimiento === 'INGRESO' ? 'text-green-600' : 'text-red-600'" x-text="tipoMovimiento === 'INGRESO' ? '➕ Registrar Entrada' : '➖ Registrar Salida'"></h3>
                <button @click="modalMovimiento = false" class="text-gray-400 font-bold">✕</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Monto</label>
                    <input type="number" x-model="montoMovimiento" class="w-full text-2xl font-bold p-3 border rounded-lg focus:ring-2 outline-none" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1">Descripción</label>
                    <input type="text" x-model="descMovimiento" class="w-full text-lg p-3 border rounded-lg outline-none" placeholder="Motivo...">
                </div>
                
                <button @click="guardarMovimiento()" 
                        class="w-full py-4 rounded-xl text-white font-bold text-lg shadow mt-2"
                        :class="tipoMovimiento === 'INGRESO' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'">
                    GUARDAR MOVIMIENTO
                </button>
            </div>
        </div>
    </div>


    <script>
        function caja() {
            return {
                estado: 'LOADING', // LOADING, CERRADA, ABIERTA
                info: {},
                calculos: { inicial: 0, ventas_efectivo: 0, ventas_credito: 0, ventas_tarjeta: 0, total_ventas: 0, ingresos: 0, egresos: 0, abonos: 0, esperado: 0 },
                montoApertura: '',
                montoCierre: '',
                modalCierre: false,
                comentariosCierre: '',
                procesandoCierre: false, // Flag para evitar doble clic
                
                // Movimientos
                modalMovimiento: false,
                tipoMovimiento: 'INGRESO',
                montoMovimiento: '',
                descMovimiento: '',

                init() {
                    this.fetchEstado();
                },

                fetchEstado() {
                    fetch('api/caja_control.php?accion=estado')
                        .then(res => res.json())
                        .then(data => {
                            this.estado = data.estado;
                            if (this.estado === 'ABIERTA') {
                                this.info = data.info;
                                this.calculos = data.calculos;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert("Error de conexión");
                        });
                },

                get diferencia() {
                    let real = parseFloat(this.montoCierre) || 0;
                    let esperado = parseFloat(this.calculos.esperado) || 0;
                    return real - esperado;
                },

                abrirCaja() {
                    if (!confirm("¿Seguro que desea abrir caja con C$ " + this.montoApertura + "?")) return;
                    
                    fetch('api/caja_control.php?accion=abrir', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ monto: this.montoApertura })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            this.montoApertura = '';
                            this.fetchEstado();
                        } else {
                            alert(data.error || "Error al abrir");
                        }
                    });
                },

                abrirModalMovimiento(tipo) {
                    this.tipoMovimiento = tipo;
                    this.montoMovimiento = '';
                    this.descMovimiento = '';
                    this.modalMovimiento = true;
                },

                guardarMovimiento() {
                     if(!this.montoMovimiento || this.montoMovimiento <= 0) return alert("Ingrese un monto válido");

                     fetch('api/caja_control.php?accion=movimiento', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ 
                            tipo: this.tipoMovimiento,
                            monto: this.montoMovimiento,
                            descripcion: this.descMovimiento
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            this.modalMovimiento = false;
                            this.fetchEstado(); // Recargar dashboard
                            alert("Movimiento registrado con éxito");
                        } else {
                            alert(data.error || "Error");
                        }
                    });
                },

                abrirModalCierre() {
                    // Recargar datos por si hubo ventas de último segundo
                    this.fetchEstado();
                    this.modalCierre = true;
                    this.montoCierre = '';
                },

                confirmarCierre() {
                    console.log('🔴 Click en CONFIRMAR detectado');
                    
                    // Evitar múltiples clicks
                    if (this.procesandoCierre) {
                        console.log('⏳ Ya hay un cierre en proceso, ignorando...');
                        return;
                    }
                    
                    console.log('Monto ingresado:', this.montoCierre);
                    
                    if (!this.montoCierre || this.montoCierre <= 0) {
                        console.log('❌ Validación falló: monto inválido');
                        return alert("Ingrese el monto contado");
                    }
                    
                    console.log('✅ Validación OK, mostrando confirm...');
                    if (!confirm("¿Confirmar cierre de caja? Esta acción no se puede deshacer.")) {
                        console.log('❌ Usuario canceló en confirm()');
                        return;
                    }

                    this.procesandoCierre = true; // Bloquear más clicks
                    console.log('🚀 Enviando petición a API...');
                    fetch('api/caja_control.php?accion=cerrar', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            efectivo: this.montoCierre,
                            comentarios: this.comentariosCierre || ''
                        })
                    })
                    .then(r => {
                        console.log('📥 Respuesta recibida:', r.status);
                        return r.json();
                    })
                    .then(data => {
                        console.log('📦 Data parseada:', data);
                        if(data.status === 'ok') {
                            console.log('✅ Cierre exitoso, abriendo PDF...');
                            // Abrir reporte PDF usando link temporal (más confiable que window.open)
                            const link = document.createElement('a');
                            link.href = 'imprimir_cierre.php?codarqueo=' + data.codarqueo;
                            link.target = '_blank';
                            link.rel = 'noopener noreferrer';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            
                            this.modalCierre = false;
                            this.montoCierre = '';
                            this.comentariosCierre = '';
                            this.procesandoCierre = false; // Resetear para permitir próximos cierres
                            this.fetchEstado();
                            alert("✅ Caja cerrada correctamente. Diferencia: C$ " + data.diferencia.toFixed(2));
                        } else {
                            console.log('❌ Error del servidor:', data.message);
                            this.procesandoCierre = false;
                            alert("❌ ERROR: " + (data.message || "Error al cerrar"));
                        }
                    })
                    .catch(err => {
                        console.error('💥 Error en fetch:', err);
                        this.procesandoCierre = false;
                        alert("❌ Error de conexión al cerrar caja");
                    });
                },

                formatMoney(amount) {
                    return 'C$ ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                },

                formatDate(dateStr) {
                    if(!dateStr) return '';
                    return new Date(dateStr).toLocaleString();
                }
            }
        }
    </script>
</body>
</html>
