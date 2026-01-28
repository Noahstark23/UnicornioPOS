<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobranza Express 💸</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Toastify for alerts -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4" x-data="cobranza()">

    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                Cobranza Express <span class="text-green-500">💸</span>
            </h1>
            <button @click="fetchDebtors()" class="text-blue-500 hover:text-blue-700 font-bold text-sm bg-white p-2 rounded-lg shadow">
                🔄 Recargar
            </button>
        </header>

        <!-- Search -->
        <div class="mb-6 relative">
            <input type="text" x-model="search" placeholder="Buscar cliente..."
                   class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 outline-none transition text-lg shadow-sm">
            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
        </div>

        <!-- Debtors List -->
        <div class="space-y-4">
            <template x-if="loading">
                <div class="text-center py-8 text-gray-500 animate-pulse">Cargando deudores...</div>
            </template>

            <template x-for="cliente in filteredClients" :key="cliente.idcliente">
                <div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Avatar/Placeholder -->
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-xl font-bold text-gray-400 shrink-0 overflow-hidden">
                            <template x-if="cliente.foto">
                                <img :src="cliente.foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!cliente.foto">
                                <span x-text="getInitials(cliente.nombre)"></span>
                            </template>
                        </div>

                        <div>
                            <h3 class="font-bold text-gray-800 text-lg leading-tight" x-text="cliente.nombre"></h3>
                            <p class="text-gray-500 text-xs mt-1" x-text="cliente.telefono || 'Sin teléfono'"></p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-bold uppercase mb-1">Deuda Total</p>
                        <p class="text-xl font-black text-red-500 mb-2" x-text="formatMoney(cliente.deuda_total_raw)"></p>
                        <button @click="openPayModal(cliente)"
                                class="bg-green-100 hover:bg-green-200 text-green-700 font-bold py-1 px-4 rounded-lg text-sm transition">
                            Cobrar 💰
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="!loading && filteredClients.length === 0" x-cloak class="text-center py-10">
                <p class="text-gray-400 text-lg">No se encontraron deudores 🎉</p>
            </div>
        </div>
    </div>

    <!-- Modal Pago -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm"
         x-transition.opacity>

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all scale-100">
            <!-- Header Modal -->
            <div class="bg-green-500 p-6 text-white text-center relative">
                <button @click="modalOpen = false" class="absolute top-4 right-4 text-white hover:text-green-100 font-bold text-xl">✕</button>
                <p class="opacity-80 text-sm font-bold uppercase tracking-wider">Abonar a cuenta de</p>
                <h2 class="text-2xl font-black mt-1 truncate px-2" x-text="selectedClient.nombre"></h2>
                <p class="mt-2 text-green-100 text-sm">Deuda Actual: <span class="font-bold text-white" x-text="formatMoney(selectedClient.deuda_total_raw)"></span></p>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <label class="block text-gray-600 font-bold mb-2 text-sm uppercase">Monto a Abonar (C$)</label>
                    <input type="number" x-model="amount" x-ref="amountInput"
                           class="w-full p-4 text-3xl font-bold text-center border-2 border-gray-300 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 outline-none transition text-gray-800"
                           placeholder="0.00">
                </div>

                <button @click="processPayment()"
                        :disabled="!isValidAmount || processing"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl text-lg shadow-lg transform active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span x-show="!processing">CONFIRMAR PAGO ✅</span>
                    <span x-show="processing">PROCESANDO... ⏳</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function cobranza() {
            return {
                clients: [],
                search: '',
                loading: false,
                modalOpen: false,
                selectedClient: {},
                amount: '',
                processing: false,

                init() {
                    this.fetchDebtors();
                },

                fetchDebtors() {
                    this.loading = true;
                    fetch('api/credits_mobile.php?action=get_debtors')
                        .then(r => r.json())
                        .then(data => {
                            if(data.status === 'error') {
                                alert(data.message);
                                if(data.message.includes('Inicie sesión')) window.location.href = 'index.php';
                            } else {
                                this.clients = data;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Toastify({text: "Error de conexión", backgroundColor: "#ef4444"}).showToast();
                        })
                        .finally(() => this.loading = false);
                },

                get filteredClients() {
                    if (this.search === '') return this.clients;
                    const lowerSearch = this.search.toLowerCase();
                    return this.clients.filter(c => c.nombre.toLowerCase().includes(lowerSearch));
                },

                openPayModal(client) {
                    this.selectedClient = client;
                    this.amount = '';
                    this.modalOpen = true;
                    setTimeout(() => this.$refs.amountInput.focus(), 100);
                },

                get isValidAmount() {
                    const val = parseFloat(this.amount);
                    return !isNaN(val) && val > 0 && val <= parseFloat(this.selectedClient.deuda_total_raw);
                },

                processPayment() {
                    if (!this.isValidAmount) return;

                    this.processing = true;
                    const payload = {
                        idcliente: this.selectedClient.idcliente,
                        monto_abono: this.amount,
                        metodo_pago: 'EFECTIVO'
                    };

                    fetch('api/credits_mobile.php?action=pay', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // 1. Close Modal
                            this.modalOpen = false;

                            // 2. Alert Success
                            Toastify({
                                text: "✅ Pago Registrado: C$ " + data.monto,
                                duration: 3000,
                                gravity: "top",
                                position: "center",
                                backgroundColor: "#22c55e",
                            }).showToast();

                            // 3. WhatsApp Redirect
                            if (this.selectedClient.telefono) {
                                const msg = `Hola ${this.selectedClient.nombre}, recibimos tu abono de C$ ${data.monto}. Saldo restante: C$ ${data.nuevo_saldo}.`;
                                const url = `https://wa.me/505${this.selectedClient.telefono}?text=${encodeURIComponent(msg)}`;
                                window.open(url, '_blank');
                            }

                            // 4. Reload List
                            this.fetchDebtors();

                        } else {
                            alert(data.message || "Error al procesar el pago");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Error de red");
                    })
                    .finally(() => this.processing = false);
                },

                getInitials(name) {
                    if (!name) return '?';
                    return name.substring(0, 2).toUpperCase();
                },

                formatMoney(amount) {
                    if (amount === undefined || amount === null) return 'C$ 0.00';
                    return 'C$ ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                }
            }
        }
    </script>
</body>
</html>
