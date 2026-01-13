<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients SaaS - 10x Boost</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <!-- Session Check (PHP) -->
    <?php
    session_start();
    if(!isset($_SESSION['codsucursal'])) {
        header("Location: index.php");
        exit;
    }
    ?>

    <div x-data="clientsModule()" x-init="fetchClients()" class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow z-10">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center">
                    <span class="text-4xl mr-2">🦄</span>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                        Clients SaaS <span class="text-sm px-2 py-1 rounded bg-indigo-100 text-indigo-800 ml-2 font-mono">10x Boost</span>
                    </h1>
                </div>
                <a href="panel" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    &larr; Back to Legacy Dashboard
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">

                <!-- Toolbar -->
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" x-model="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-4 pr-12 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="Search clients...">
                    </div>

                    <button @click="fetchClients()" class="bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 border border-gray-300 rounded shadow-sm inline-flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>

                <!-- Table -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name / Contact</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="client in filteredClients" :key="client.codcliente">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-lg">
                                                            <span x-text="client.nomcliente.charAt(0)"></span>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900" x-text="client.nomcliente"></div>
                                                            <div class="text-sm text-gray-500" x-text="client.emailcliente || 'No email'"></div>
                                                            <div class="text-xs text-gray-400" x-text="client.tlfcliente"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900 font-mono" x-text="client.dnicliente"></div>
                                                    <div class="text-xs text-gray-500" x-text="client.documcliente_name || 'ID'"></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm"
                                                          :class="parseFloat(client.current_balance) > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                                                        <span x-text="parseFloat(client.current_balance) > 0 ? 'Debt: ' + formatMoney(client.current_balance) : 'Clean'"></span>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button @click="openDebtModal(client)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded transition-colors">
                                                        Add Debt
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="clients.length === 0 && !loading">
                                            <tr>
                                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                                    <p class="text-lg">No clients found.</p>
                                                    <p class="text-xs mt-1">Make sure you are logged in and have clients in your database.</p>
                                                </td>
                                            </tr>
                                        </template>
                                         <template x-if="loading">
                                            <tr>
                                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Loading data...
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal -->
        <div x-show="showModal" class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Simulate Credit Charge
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Client: <span class="font-bold text-gray-700" x-text="selectedClient?.nomcliente"></span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Current Debt: <span x-text="formatMoney(selectedClient?.current_balance || 0)"></span>
                                        <br>
                                        Limit: <span x-text="formatMoney(selectedClient?.limitecredito || 0)"></span>
                                    </p>

                                    <div class="mt-4">
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" x-model="debtAmount" id="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2 border" placeholder="0.00" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="submitDebt()"
                                :disabled="processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!processing">Add Debt</span>
                            <span x-show="processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function clientsModule() {
            return {
                clients: [],
                search: '',
                showModal: false,
                selectedClient: null,
                debtAmount: '',
                loading: false,
                processing: false,

                get filteredClients() {
                    if (this.search === '') return this.clients;
                    return this.clients.filter(client => {
                        return client.nomcliente.toLowerCase().includes(this.search.toLowerCase()) ||
                               client.dnicliente.includes(this.search);
                    });
                },

                async fetchClients() {
                    this.loading = true;
                    try {
                        const response = await fetch('api/clients_v2.php');
                        if (!response.ok) {
                            if (response.status === 401) {
                                alert('Session expired or unauthorized. Please log in.');
                                return;
                            }
                            throw new Error('Network response was not ok');
                        }
                        this.clients = await response.json();
                    } catch (error) {
                        console.error('Error fetching clients:', error);
                        // Don't alert on first load if empty, just log
                    } finally {
                        this.loading = false;
                    }
                },

                openDebtModal(client) {
                    this.selectedClient = client;
                    this.debtAmount = '';
                    this.showModal = true;
                },

                async submitDebt() {
                    if (!this.debtAmount || this.debtAmount <= 0) {
                        alert('Please enter a valid amount.');
                        return;
                    }

                    this.processing = true;

                    try {
                        const response = await fetch('api/clients_v2.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                action: 'add_debt',
                                codcliente: this.selectedClient.codcliente,
                                amount: parseFloat(this.debtAmount)
                            })
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.error || 'Unknown error');
                        }

                        // Success
                        // alert(result.message); // removed alert for smoother UX, maybe show toast
                        this.showModal = false;
                        this.fetchClients(); // Refresh data

                    } catch (error) {
                        console.error('Error adding debt:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        this.processing = false;
                    }
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
                }
            }
        }
    </script>
</body>
</html>
