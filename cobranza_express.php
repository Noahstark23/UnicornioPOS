<?php
require_once("class/class.php");
if (isset($_SESSION['acceso'])) {
    if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {
        $tra = new Login();
        $ses = $tra->ExpiraSession();
    } else {
        header("Location: timeout");
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
    <title>Cobranza Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100" x-data="cobradorApp()" x-init="initData()">

    <!-- Header Sticky -->
    <header class="bg-indigo-900 text-white sticky top-0 z-50 shadow-lg">
        <div class="px-4 py-3 flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center">
                <i class="fa-solid fa-mobile-screen-button mr-2"></i> Modo Cobrador
            </h1>
            <a href="panel" class="text-sm bg-indigo-700 px-3 py-1 rounded hover:bg-indigo-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Salir
            </a>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 gap-0 border-t border-indigo-800">
            <div class="p-3 text-center border-r border-indigo-800 bg-indigo-800">
                <div class="text-xs text-indigo-300 uppercase tracking-wide">Deuda en Calle</div>
                <div class="text-lg font-bold text-red-300" x-text="formatCurrency(totalDebt)"></div>
            </div>
            <div class="p-3 text-center bg-green-900">
                <div class="text-xs text-green-300 uppercase tracking-wide">Cobrado Hoy</div>
                <div class="text-lg font-bold text-green-300" x-text="formatCurrency(collectedToday)"></div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-4 pb-24 max-w-md mx-auto">

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-10">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-500"></i>
            <p class="mt-2 text-gray-500">Cargando clientes...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && clients.length === 0" x-cloak class="text-center py-10">
            <i class="fa-solid fa-check-circle text-4xl text-green-500"></i>
            <p class="mt-2 text-gray-500">¡Todo al día! No hay deudas pendientes.</p>
        </div>

        <!-- Client List -->
        <div class="space-y-4" x-show="!loading" x-cloak>
            <template x-for="client in clients" :key="client.codcliente">
                <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 relative">
                    <div class="p-4 flex items-center">
                        <!-- Avatar / Initials -->
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-bold text-gray-600 uppercase mr-4">
                            <span x-text="getInitials(client.nomcliente)"></span>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow">
                            <h3 class="font-bold text-gray-800 truncate w-48" x-text="client.nomcliente"></h3>
                            <div class="flex items-center text-sm text-gray-500 mb-1">
                                <i class="fa-brands fa-whatsapp mr-1 text-green-500"></i>
                                <span x-text="client.tlfcliente || 'Sin teléfono'"></span>
                            </div>
                            <div class="text-red-600 font-bold text-lg" x-text="formatCurrency(client.montocredito)"></div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button @click="openModal(client)" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 text-lg transition active:bg-green-800 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave mr-2"></i> COBRAR
                    </button>
                </div>
            </template>
        </div>
    </main>

    <!-- Payment Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <!-- Backdrop -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Cobrar a <span x-text="activeClient?.nomcliente" class="font-bold text-indigo-600"></span>
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Saldo actual: <span x-text="formatCurrency(activeClient?.montocredito)" class="font-bold text-red-500"></span>
                            </p>
                        </div>

                        <!-- Amount Input -->
                        <div class="mt-4">
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-lg">$</span>
                                </div>
                                <input type="number" x-model="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-3xl border-gray-300 rounded-md py-4 font-bold text-center" placeholder="0.00">
                            </div>
                        </div>

                        <!-- Quick Buttons -->
                        <div class="mt-4 grid grid-cols-4 gap-2">
                            <button @click="addAmount(100)" class="bg-indigo-100 text-indigo-700 font-semibold py-2 px-1 rounded hover:bg-indigo-200">+100</button>
                            <button @click="addAmount(200)" class="bg-indigo-100 text-indigo-700 font-semibold py-2 px-1 rounded hover:bg-indigo-200">+200</button>
                            <button @click="addAmount(500)" class="bg-indigo-100 text-indigo-700 font-semibold py-2 px-1 rounded hover:bg-indigo-200">+500</button>
                            <button @click="amount = ''" class="bg-red-100 text-red-700 font-semibold py-2 px-1 rounded hover:bg-red-200"><i class="fa-solid fa-eraser"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="submitPayment()" :disabled="processing || !amount || amount <= 0" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-3 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!processing">GUARDAR PAGO</span>
                        <span x-show="processing"><i class="fa-solid fa-spinner fa-spin"></i> Procesando...</span>
                    </button>
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cobradorApp() {
            return {
                loading: true,
                processing: false,
                clients: [],
                totalDebt: 0,
                collectedToday: 0,
                showModal: false,
                activeClient: null,
                amount: '',

                initData() {
                    fetch('api/cobrador.php?action=get_init_data')
                        .then(res => res.json())
                        .then(data => {
                            this.totalDebt = parseFloat(data.total_debt);
                            this.collectedToday = parseFloat(data.collected_today);
                            this.clients = data.clients;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error('Error fetching data:', err);
                            this.loading = false;
                        });
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);
                },

                getInitials(name) {
                    if (!name) return '?';
                    return name.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase();
                },

                openModal(client) {
                    this.activeClient = client;
                    this.amount = '';
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.activeClient = null;
                },

                addAmount(val) {
                    let current = parseFloat(this.amount) || 0;
                    this.amount = current + val;
                },

                submitPayment() {
                    if (!this.activeClient || this.amount <= 0) return;

                    this.processing = true;

                    const payload = {
                        codcliente: this.activeClient.codcliente,
                        amount: this.amount
                    };

                    fetch('api/cobrador.php?action=save_payment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.processing = false;
                        if (data.success) {
                            // Update local stats
                            this.collectedToday += parseFloat(data.applied_amount);
                            this.totalDebt -= parseFloat(data.applied_amount);

                            // Update client list
                            const index = this.clients.findIndex(c => c.codcliente === this.activeClient.codcliente);
                            if (index !== -1) {
                                let newBal = parseFloat(data.new_balance);
                                if (newBal <= 0) {
                                    this.clients.splice(index, 1); // Remove if fully paid
                                } else {
                                    this.clients[index].montocredito = newBal;
                                }
                            }

                            this.closeModal();

                            // WhatsApp Integration
                            const phone = data.client_phone || '';
                            const cleanPhone = phone.replace(/\D/g, ''); // Remove non-digits

                            if (cleanPhone) {
                                const msg = `Hola ${data.client_name}, recibí tu abono de ${data.applied_amount}. Restan ${data.new_balance}.`;
                                const url = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
                                window.open(url, '_blank');
                            } else {
                                alert('Pago guardado. No se abrió WhatsApp porque el cliente no tiene teléfono registrado.');
                            }

                        } else {
                            alert('Error: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        this.processing = false;
                        console.error(err);
                        alert('Error de conexión');
                    });
                }
            }
        }
    </script>
</body>
</html>
