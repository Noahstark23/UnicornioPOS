<?php
session_start();
if (empty($_SESSION['codsucursal'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100" x-data="clientsManager()">

    <!-- Navbar -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <span class="font-bold text-xl text-gray-800">SaaS Clientes</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="panel.php" class="text-gray-500 hover:text-gray-700">Volver al Panel</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        <!-- Toolbar -->
        <div class="mb-4 flex justify-between items-center">
            <div class="flex-1 max-w-lg">
                <input type="text" x-model="search" @input.debounce.300ms="fetchClients()"
                       placeholder="Buscar clientes..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 border">
            </div>
            <button @click="openModal()"
                    class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Nuevo Cliente
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="client in clients" :key="client.codcliente">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="client.nomcliente"></div>
                                <div class="text-sm text-gray-500" x-text="client.email"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="client.doccliente"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="client.tlfcliente"></div>
                                <div class="text-sm text-gray-500" x-text="client.direccion"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      :class="parseFloat(client.current_balance) > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                                      x-text="formatCurrency(client.current_balance)">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="editClient(client)" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</button>
                                <button @click="deleteClient(client.codcliente)" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="clients.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron clientes.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="isModalOpen" x-cloak class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="isEditing ? 'Editar Cliente' : 'Nuevo Cliente'"></h3>
                    <div class="mt-4 grid grid-cols-1 gap-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <input type="text" x-model="form.nomcliente" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Documento ID</label>
                            <input type="text" x-model="form.doccliente" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" x-model="form.tlfcliente" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" x-model="form.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" x-model="form.direccion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Límite de Crédito</label>
                            <input type="number" step="0.01" x-model="form.limitecredito" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 border p-2">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="saveClient()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar
                    </button>
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function clientsManager() {
            return {
                clients: [],
                search: '',
                isModalOpen: false,
                isEditing: false,
                form: {
                    codcliente: null,
                    nomcliente: '',
                    doccliente: '',
                    tlfcliente: '',
                    email: '',
                    direccion: '',
                    limitecredito: 0
                },
                init() {
                    this.fetchClients();
                },
                async fetchClients() {
                    try {
                        const response = await fetch(`api/clients_v2.php?search=${this.search}`);
                        const result = await response.json();
                        if (result.status === 'success') {
                            this.clients = result.data;
                        } else {
                            console.error('Error fetching clients:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('es-VE', { style: 'currency', currency: 'VES' }).format(value);
                },
                openModal() {
                    this.resetForm();
                    this.isModalOpen = true;
                    this.isEditing = false;
                },
                closeModal() {
                    this.isModalOpen = false;
                },
                resetForm() {
                    this.form = {
                        codcliente: null,
                        nomcliente: '',
                        doccliente: '',
                        tlfcliente: '',
                        email: '',
                        direccion: '',
                        limitecredito: 0
                    };
                },
                editClient(client) {
                    this.form = { ...client };
                    this.isEditing = true;
                    this.isModalOpen = true;
                },
                async saveClient() {
                    const method = this.isEditing ? 'PUT' : 'POST';
                    try {
                        const response = await fetch('api/clients_v2.php', {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });
                        const result = await response.json();
                        if (result.status === 'success') {
                            this.closeModal();
                            this.fetchClients();
                            alert(result.message);
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Ocurrió un error al guardar.');
                    }
                },
                async deleteClient(id) {
                    if (!confirm('¿Está seguro de eliminar este cliente?')) return;

                    try {
                        const response = await fetch(`api/clients_v2.php?id=${id}`, {
                            method: 'DELETE'
                        });
                        const result = await response.json();
                        if (result.status === 'success') {
                            this.fetchClients();
                            alert(result.message);
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Ocurrió un error al eliminar.');
                    }
                }
            }
        }
    </script>
</body>
</html>
