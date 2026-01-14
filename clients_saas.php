<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes SaaS | Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800" x-data="clientsManager()">

    <!-- NAV -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 text-white p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900">Gestión de Clientes <span class="text-indigo-600 text-sm font-normal bg-indigo-50 px-2 py-1 rounded-full border border-indigo-100">v2.0 SaaS</span></h1>
        </div>
        <div class="flex gap-3">
            <a href="panel.php" class="text-gray-500 hover:text-gray-900 font-medium transition">Volver al Panel</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium transition">Salir</a>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto p-6">
        
        <!-- TOOLBAR -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="search" @input.debounce.300ms="fetchClients()" 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none shadow-sm transition"
                       placeholder="Buscar por nombre, teléfono...">
            </div>
            <button @click="fetchClients()" class="text-gray-500 hover:bg-gray-100 p-2 rounded-full transition" title="Recargar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>

        <!-- TABLE CARD -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                            <th class="p-4 font-semibold">Cliente</th>
                            <th class="p-4 font-semibold">Contacto</th>
                            <th class="p-4 font-semibold text-right">Límite Crédito</th>
                            <th class="p-4 font-semibold text-right">Saldo Actual</th>
                            <th class="p-4 font-semibold text-center">Estado</th>
                            <th class="p-4 font-semibold text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="cli in clients" :key="cli.id">
                            <tr class="hover:bg-indigo-50/30 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900" x-text="cli.nombre"></div>
                                    <div class="text-xs text-gray-400">ID: <span x-text="cli.id"></span></div>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span x-text="cli.telefono || 'Sin Tlf'"></span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1" x-text="cli.email"></div>
                                </td>
                                <td class="p-4 text-right font-mono text-gray-600" x-text="formatMoney(cli.limitecredito)"></td>
                                <td class="p-4 text-right">
                                    <span class="font-mono font-bold text-lg" 
                                          :class="parseFloat(cli.current_balance) > 0 ? 'text-red-600' : 'text-green-600'"
                                          x-text="formatMoney(cli.current_balance)">
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                                          :class="parseFloat(cli.current_balance) > 0 ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'">
                                        <template x-if="parseFloat(cli.current_balance) > 0">
                                            <span>DEUDA</span>
                                        </template>
                                        <template x-if="parseFloat(cli.current_balance) <= 0">
                                            <span>AL DÍA</span>
                                        </template>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openDebtsModal(cli)" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                            Estado Cuenta
                                        </button>
                                        <!-- Boton Deuda Manual removido por solicitud, pero el modal sigue existiendo si se quisiera reactivar 
                                        <button @click="openModal(cli)" class="hidden text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg text-sm font-medium transition">
                                            + Deuda
                                        </button> -->
                                    </div>
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <tr x-show="clients.length === 0 && !loading">
                            <td colspan="6" class="p-12 text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                No se encontraron clientes
                            </td>
                        </tr>
                        
                        <!-- Loading -->
                         <tr x-show="loading">
                            <td colspan="6" class="p-12 text-center text-indigo-500 animate-pulse font-medium">
                                Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL ESTADO DE CUENTA -->
    <div x-show="debtsModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-0 flex flex-col max-h-[90vh]" @click.away="debtsModalOpen = false">
            
            <!-- HEADER -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Estado de Cuenta</h3>
                    <p class="text-sm text-gray-500">Cliente: <span class="font-bold text-gray-800" x-text="selectedClient?.nombre"></span></p>
                </div>
                <button @click="debtsModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto flex-1">
                
                <div x-show="loadingDebts" class="p-10 text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto animate-spin text-indigo-500 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Cargando deudas...
                </div>

                <div x-show="!loadingDebts && debts.length === 0" class="p-10 text-center text-green-600 bg-green-50 rounded-xl border border-green-100">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-bold text-lg">¡Cliente Solvente!</p>
                    <p class="text-sm opacity-80">No tiene facturas pendientes de pago.</p>
                </div>

                <div x-show="!loadingDebts && debts.length > 0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Factura #</th>
                                <th class="p-3 text-right">Total</th>
                                <th class="p-3 text-right">Abonado</th>
                                <th class="p-3 text-right">Pendiente</th>
                                <th class="p-3 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="debt in debts" :key="debt.codventa">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3 text-sm text-gray-600" x-text="debt.fechaventa"></td>
                                    <td class="p-3 font-mono font-bold text-gray-800" x-text="debt.codventa"></td>
                                    <td class="p-3 text-right font-mono text-gray-600" x-text="formatMoney(debt.totalpago)"></td>
                                    <td class="p-3 text-right font-mono text-green-600" x-text="formatMoney(debt.abonado)"></td>
                                    <td class="p-3 text-right font-mono font-bold text-red-600" x-text="formatMoney(debt.saldo_pendiente)"></td>
                                    <td class="p-3 text-center">
                                        <button @click="openPaymentModal(debt)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs font-bold shadow-sm transition">
                                            ABONAR
                                        </button>
                                        <!-- Botones PDF -->
										<div class="mt-1 flex gap-1 justify-center">
											<a :href="'reportepdf.php?tipo=TICKETCREDITO&codventa='+btoa(debt.codventa)" target="_blank" class="text-gray-400 hover:text-gray-600" title="Imprimir Ticket"><i class="mdi mdi-ticket"></i> T</a>
											<a :href="'reportepdf.php?tipo=FACTURA&codventa='+btoa(debt.codventa)" target="_blank" class="text-gray-400 hover:text-gray-600" title="Imprimir Factura"><i class="mdi mdi-file-pdf"></i> F</a>
										</div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

            </div>
            
            <!-- FOOTER -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl flex justify-end">
                <button @click="debtsModalOpen = false" class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition shadow-sm">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- MODAL REALIZAR PAGO -->
    <div x-show="paymentModalOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 transform transition-all scale-100" @click.away="paymentModalOpen = false">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Registrar Abono</h3>
            <p class="text-sm text-gray-500 mb-4">Factura: <span class="font-bold text-gray-800" x-text="selectedDebt?.codventa"></span></p>
            
            <div class="bg-indigo-50 p-4 rounded-xl mb-4 border border-indigo-100">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Saldo Pendiente:</span>
                    <span class="font-bold text-indigo-700" x-text="formatMoney(selectedDebt?.saldo_pendiente)"></span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Monto a abonar (C$)</label>
                <input type="number" x-model.number="paymentAmount" class="w-full p-3 border rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-green-500 outline-none" placeholder="0.00">
                <p class="text-xs text-red-500 mt-1" x-show="paymentAmount > selectedDebt?.saldo_pendiente">El monto no puede ser mayor al saldo.</p>
            </div>

            <div class="flex gap-3">
                <button @click="paymentModalOpen = false" class="flex-1 py-3 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-semibold transition">Cancelar</button>
                <button @click="processPayment()" 
                        :disabled="!paymentAmount || paymentAmount <= 0 || paymentAmount > selectedDebt?.saldo_pendiente"
                        :class="(!paymentAmount || paymentAmount <= 0 || paymentAmount > selectedDebt?.saldo_pendiente) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700 shadow-lg'"
                        class="flex-1 py-3 text-white bg-green-600 rounded-xl font-bold transition">
                    Procesar
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
        function clientsManager() {
            return {
                clients: [],
                search: '',
                loading: false,
                
                // Modal Estado de Cuenta
                debtsModalOpen: false,
                loadingDebts: false,
                debts: [],
                
                // Modal Pagos
                paymentModalOpen: false,
                selectedDebt: null,
                paymentAmount: '',
                
                // Legacy (Opcional)
                modalOpen: false, 
                selectedClient: null,
                debtAmount: '',

                toast: { show: false, message: '', type: 'success' },

                init() {
                    this.fetchClients();
                },

                fetchClients() {
                    this.loading = true;
                    // Cache busting param t
                    fetch(`api/clients_v2.php?q=${this.search}&t=${new Date().getTime()}`)
                        .then(r => r.json())
                        .then(data => {
                            this.clients = data;
                            this.loading = false;
                        })
                        .catch(e => {
                            console.error(e);
                            this.showToast('Error cargando clientes', 'error');
                            this.loading = false;
                        });
                },

                openDebtsModal(client) {
                    this.selectedClient = client;
                    this.debtsModalOpen = true;
                    this.fetchDebts(client.id);
                },

                fetchDebts(clientId) {
                    this.loadingDebts = true;
                    this.debts = [];
                    fetch(`api/clients_v2.php?action=get_debts&id=${clientId}&t=${new Date().getTime()}`)
                        .then(r => r.json())
                        .then(data => {
                            if(data.error) throw new Error(data.error);
                            this.debts = data;
                            this.loadingDebts = false;
                        })
                        .catch(e => {
                            console.error(e);
                            this.showToast('Error al cargar deudas: ' + e.message, 'error');
                            this.loadingDebts = false;
                        });
                },

                openPaymentModal(debt) {
                    this.selectedDebt = debt;
                    this.paymentAmount = debt.saldo_pendiente; // Sugerir pago total
                    this.paymentModalOpen = true;
                },

                processPayment() {
                    if(!this.selectedDebt || this.paymentAmount <= 0) return;

                    fetch('api/clients_v2.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            action: 'add_payment',
                            codventa: this.selectedDebt.codventa,
                            amount: this.paymentAmount
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            this.showToast('✅ Pago registrado exitosamente');
                            
                            // 1. Cerrar Modal Pago
                            this.paymentModalOpen = false;
                            
                            // 2. Recargar Lista de Deudas
                            this.fetchDebts(this.selectedClient.id);
                            
                            // 3. Recargar Lista de Clientes (para actualizar saldo total)
                            this.fetchClients();
                        } else {
                             this.showToast('❌ Error: ' + (data.error || 'Desconocido'), 'error');
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        this.showToast('Error de conexión', 'error');
                    });
                },

                // Legacy Add Debt (Opcional)
                openModal(client) {
                    this.selectedClient = client;
                    this.debtAmount = '';
                    this.modalOpen = true;
                },
                addDebt() { /* ... (Legacy Logic Code skipped as hidden) ... */ },

                formatMoney(amount) {
                    return 'C$ ' + parseFloat(amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
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
