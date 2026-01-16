<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
    if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {

// Obtener código de caja para el usuario actual
$tra = new Login();
$arqueo = $tra->ArqueoCajaPorUsuario();
$codcaja = isset($arqueo[0]['codcaja']) ? $arqueo[0]['codcaja'] : 'SIN ARQUEO';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Créditos | Unicornio POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800" x-data="creditsManager()">

    <!-- NAV -->
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <div class="bg-red-600 text-white p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900">💳 Gestión de Créditos <span class="text-red-600 text-sm font-normal bg-red-50 px-2 py-1 rounded-full border border-red-100">Modernizado</span></h1>
        </div>
        <div class="flex gap-3">
            <a href="panel.php" class="text-gray-500 hover:text-gray-900 font-medium transition">Volver al Panel</a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium transition">Salir</a>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto p-6">
        
        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Total Créditos -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Total Créditos</p>
                        <p class="text-3xl font-black text-gray-900" x-text="credits.length || 0"></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Monto Pendiente -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Por Cobrar</p>
                        <p class="text-3xl font-black text-red-600" x-text="formatMoney(totalDebt())"></p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Clientes con Deuda -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold mb-1">Clientes</p>
                        <p class="text-3xl font-black text-indigo-600" x-text="uniqueClients()"></p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-xl">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="search" @input.debounce.300ms="filterCredits()" 
                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none shadow-sm transition"
                       placeholder="Buscar por cliente, factura...">
            </div>
            <button @click="fetchCredits()" class="text-gray-500 hover:bg-gray-100 p-2 rounded-full transition" title="Recargar">
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
                            <th class="p-4 font-semibold">Factura #</th>
                            <th class="p-4 font-semibold text-right">Total</th>
                            <th class="p-4 font-semibold text-right">Abonado</th>
                            <th class="p-4 font-semibold text-right">Saldo</th>
                            <th class="p-4 font-semibold text-center">Fecha</th>
                            <th class="p-4 font-semibold text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="credit in filteredCredits" :key="credit.codventa">
                            <tr class="hover:bg-red-50/30 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900" x-text="credit.nomcliente"></div>
                                    <div class="text-xs text-gray-400" x-text="credit.dnicliente"></div>
                                </td>
                                <td class="p-4 font-mono font-bold text-gray-800" x-text="credit.codventa"></td>
                                <td class="p-4 text-right font-mono text-gray-600" x-text="formatMoney(credit.totalpago)"></td>
                                <td class="p-4 text-right font-mono text-green-600" x-text="formatMoney(credit.totalabono)"></td>
                                <td class="p-4 text-right">
                                    <span class="font-mono font-bold text-lg text-red-600" x-text="formatMoney(credit.totaldebe)"></span>
                                </td>
                                <td class="p-4 text-center text-sm text-gray-600" x-text="credit.fechaventa"></td>
                                <td class="p-4 text-center">
                                    <button @click="openPaymentModal(credit)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg transition transform hover:scale-105">
                                        💰 ABONAR
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <tr x-show="filteredCredits.length === 0 && !loading">
                            <td colspan="7" class="p-12 text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                No se encontraron créditos pendientes
                            </td>
                        </tr>
                        
                        <!-- Loading -->
                        <tr x-show="loading">
                            <td colspan="7" class="p-12 text-center text-red-500 animate-pulse font-medium">
                                Cargando créditos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL REALIZAR PAGO -->
    <div x-show="paymentModalOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden transform transition-all scale-100" @click.away="paymentModalOpen = false">
            
            <!-- HEADER -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl shrink-0">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">💰 Registrar Abono</h3>
                    <p class="text-sm text-gray-500">Factura: <span class="font-bold text-gray-800" x-text="selectedCredit?.codventa"></span></p>
                </div>
                <button @click="paymentModalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- BODY SCROLLEABLE -->
            <div class="p-6 overflow-y-auto flex-1">
                <div class="bg-red-50 p-4 rounded-xl mb-4 border border-red-100">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Cliente:</span>
                        <span class="font-bold text-gray-800" x-text="selectedCredit?.nomcliente"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Total Factura:</span>
                        <span class="font-bold text-gray-800" x-text="formatMoney(selectedCredit?.totalpago)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Abonado:</span>
                        <span class="font-bold text-green-600" x-text="formatMoney(selectedCredit?.totalabono)"></span>
                    </div>
                    <div class="border-t border-red-200 pt-2 flex justify-between">
                        <span class="font-bold text-gray-700">Saldo Pendiente:</span>
                        <span class="font-black text-xl text-red-700" x-text="formatMoney(selectedCredit?.totaldebe)"></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Monto a Abonar (C$)</label>
                    <input type="number" x-model.number="paymentAmount" x-ref="paymentInput"
                           class="w-full p-4 border-2 rounded-xl text-2xl font-bold text-gray-800 text-right outline-none transition-all"
                           :class="paymentValidation()"
                           placeholder="0.00" step="0.01">
                    <p class="text-xs text-red-500 mt-1" x-show="paymentAmount > selectedCredit?.totaldebe">
                        ⚠️ El monto no puede ser mayor al saldo pendiente
                    </p>
                </div>
            </div>

            <!-- FOOTER STICKY -->
            <div class="p-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl shrink-0">
                <div class="flex gap-3">
                    <button @click="paymentModalOpen = false" class="flex-1 py-3 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-xl font-semibold transition">
                        ❌ Cancelar
                    </button>
                    <button @click="processPayment()" 
                            :disabled="!paymentAmount || paymentAmount <= 0 || paymentAmount > selectedCredit?.totaldebe"
                            :class="(!paymentAmount || paymentAmount <= 0 || paymentAmount > selectedCredit?.totaldebe) ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'hover:bg-green-700 shadow-xl bg-green-600'"
                            class="flex-1 py-3 text-white rounded-xl font-bold text-lg transition-all transform hover:scale-105">
                        ✅ PROCESAR ABONO
                    </button>
                </div>
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
        function creditsManager() {
            return {
                credits: [],
                filteredCredits: [],
                search: '',
                loading: false,
                
                // Modal Pagos
                paymentModalOpen: false,
                selectedCredit: null,
                paymentAmount: '',
                
                toast: { show: false, message: '', type: 'success' },

                init() {
                    this.fetchCredits();
                },

                fetchCredits() {
                    this.loading = true;
                    fetch(`consultas.php?CargaCreditos=si&t=${new Date().getTime()}`)
                        .then(r => r.text())
                        .then(html => {
                            // Parse HTML response to extract credit data
                            // For now, we'll use a simpler approach with API
                            return fetch(`api/credits.php?t=${new Date().getTime()}`);
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.credits = data;
                            this.filterCredits();
                            this.loading = false;
                        })
                        .catch(e => {
                            console.error(e);
                            this.showToast('Error cargando créditos', 'error');
                            this.loading = false;
                        });
                },

                filterCredits() {
                    if (!this.search) {
                        this.filteredCredits = this.credits;
                        return;
                    }
                    const term = this.search.toLowerCase();
                    this.filteredCredits = this.credits.filter(c => 
                        (c.nomcliente || '').toLowerCase().includes(term) ||
                        (c.codventa || '').toLowerCase().includes(term) ||
                        (c.dnicliente || '').toLowerCase().includes(term)
                    );
                },

                totalDebt() {
                    return this.credits.reduce((sum, c) => sum + parseFloat(c.totaldebe || 0), 0);
                },

                uniqueClients() {
                    const unique = new Set(this.credits.map(c => c.dnicliente));
                    return unique.size;
                },

                openPaymentModal(credit) {
                    this.selectedCredit = credit;
                    this.paymentAmount = credit.totaldebe; // Sugerir pago total
                    this.paymentModalOpen = true;
                    this.$nextTick(() => {
                        if(this.$refs.paymentInput) {
                            this.$refs.paymentInput.select();
                        }
                    });
                },

                paymentValidation() {
                    const amt = parseFloat(this.paymentAmount) || 0;
                    const debt = parseFloat(this.selectedCredit?.totaldebe) || 0;
                    if (amt === 0) return 'border-gray-300 focus:border-blue-500';
                    if (amt > debt) return 'border-red-500 bg-red-50 focus:border-red-600';
                    return 'border-green-500 bg-green-50 focus:border-green-600';
                },

                processPayment() {
                    if(!this.selectedCredit || this.paymentAmount <= 0) return;

                    // Usar la misma API que clients_saas.php para actualizar correctamente la caja
                    fetch('api/clients_v2.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'add_payment',
                            codventa: this.selectedCredit.codventa,
                            amount: this.paymentAmount
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if(data.status === 'ok') {
                            this.showToast('✅ Abono registrado exitosamente');
                            this.paymentModalOpen = false;
                            this.fetchCredits(); // Recargar lista
                        } else {
                            this.showToast('❌ Error: ' + (data.error || 'Desconocido'), 'error');
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        this.showToast('❌ Error al procesar el abono', 'error');
                    });
                },

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
