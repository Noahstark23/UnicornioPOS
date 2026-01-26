<?php
// Configuración de Sesión y Permisos
require_once("class/class.php");
if (isset($_SESSION['acceso'])) {
    if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {
        // Acceso permitido
    } else {
        header("Location: panel");
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
    <title>Gestión de Proveedores | Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        [x-cloak] { display: none !important; }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="text-gray-800" x-data="suppliersApp()">

    <!-- BACKGROUND DECORATION -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col p-6 max-w-[1600px] mx-auto">
        
        <!-- HEADER & ACTIONS -->
        <header class="flex justify-between items-center mb-8">
            <div>
                <a href="panel" class="text-sm font-semibold text-gray-500 hover:text-indigo-600 flex items-center mb-1 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Volver al Panel
                </a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Proveedores & Compras</h1>
                <p class="text-gray-500">Gestión integral de la cadena de suministro</p>
            </div>
            <div class="flex gap-3">
                <button @click="openModal()" class="bg-black hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-lg font-medium transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Nuevo Proveedor
                </button>
            </div>
        </header>

        <!-- KPIS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Card 1: Total Proveedores -->
            <div class="glass-card rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Total Proveedores</h3>
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800" x-text="kpis.total_proveedores || 0">0</div>
                <div class="text-xs text-green-500 font-medium mt-1 flex items-center">
                    <span class="bg-green-100 px-1.5 py-0.5 rounded mr-1">Activos</span> Registrados en sistema
                </div>
            </div>

            <!-- Card 2: Deuda Pendiente -->
            <div class="glass-card rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Deuda Pendiente</h3>
                    <div class="p-2 bg-red-50 text-red-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800" x-text="formatCurrency(kpis.deuda_pendiente)">$ 0.00</div>
                <div class="text-xs text-red-500 font-medium mt-1">
                     cuentas por pagar (Crédito)
                </div>
            </div>

            <!-- Card 3: Compras del Mes -->
            <div class="glass-card rounded-2xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Compras (Mes Actual)</h3>
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-800" x-text="formatCurrency(kpis.compras_total_mes)">$ 0.00</div>
                <div class="text-xs text-gray-400 mt-1">
                    Volumen de compra procesado
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT (TABLE) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex-1 overflow-hidden flex flex-col">
            
            <!-- Toolbar -->
            <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between items-center bg-gray-50/50">
                <div class="relative w-full md:w-96">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="search" @input.debounce.500ms="fetchData()" placeholder="Buscar por Nombre o RUC..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                </div>
                <div class="flex gap-2 text-sm text-gray-500">
                    <span x-text="suppliers.length">0</span> registros encontrados
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-auto flex-1 h-0"> <!-- h-0 flex-1 forces scroll within this container -->
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 border-b border-gray-100">PROVEEDOR</th>
                            <th class="px-6 py-4 border-b border-gray-100">CONTACTO / CONTACTO</th>
                            <th class="px-6 py-4 border-b border-gray-100 text-right">HISTÓRICO COMPRAS</th>
                            <th class="px-6 py-4 border-b border-gray-100 text-center">ÚLTIMA COMPRA</th>
                            <th class="px-6 py-4 border-b border-gray-100 text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="s in suppliers" :key="s.codproveedor">
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold shadow-inner">
                                            <span x-text="getInitials(s.nomproveedor)"></span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900" x-text="s.nomproveedor"></div>
                                            <div class="text-xs text-gray-400" x-text="s.cuitproveedor || 'Sin ID Fiscal'"></div>
                                            <div class="text-xs text-gray-400" x-text="s.codproveedor"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">
                                        <i class="opacity-50 mr-1">📞</i> <span x-text="s.tlfproveedor || 'N/A'"></span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1" x-show="s.vendedor">
                                        <i class="opacity-50 mr-1">👤</i> <span x-text="s.vendedor"></span> (<span x-text="s.tlfvendedor"></span>)
                                    </div>
                                    <div class="text-xs text-indigo-500 hover:underline mt-1 cursor-pointer" x-show="s.emailproveedor" x-text="s.emailproveedor"></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-gray-900" x-text="formatCurrency(s.total_gastado)"></div>
                                    <div class="text-xs text-gray-400"><span x-text="s.total_ordenes || 0"></span> órdenes</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span x-text="s.ultima_compra ? formatDate(s.ultima_compra) : 'Nunca'" 
                                          class="px-2 py-1 rounded text-xs font-medium"
                                          :class="isRecent(s.ultima_compra) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="p-2 hover:bg-indigo-50 text-indigo-600 rounded-lg transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button class="p-2 hover:bg-green-50 text-green-600 rounded-lg transition-colors" title="Nuevo Pedido">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="suppliers.length === 0" x-cloak>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                No se encontraron proveedores que coincidan con la búsqueda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL NUEVO PROVEEDOR -->
    <div class="fixed inset-0 z-50 overflow-y-auto" x-show="showModal" style="display: none;" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="closeModal()"></div>

            <div class="inline-block w-full max-w-2xl px-8 py-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl" 
                 x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                <h3 class="text-2xl font-black text-gray-900 mb-6">Nuevo Proveedor</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre / Razón Social</label>
                        <input x-model="form.nomproveedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none" placeholder="Ej. Distribuidora XYZ">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Fiscal / CUIT / NIT</label>
                        <input x-model="form.cuitproveedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none" placeholder="000-000000-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Principal</label>
                        <input x-model="form.tlfproveedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Corporativo</label>
                        <input x-model="form.emailproveedor" type="email" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa</label>
                        <input x-model="form.direcproveedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none">
                    </div>
                    
                    <div class="col-span-1 md:col-span-2 border-t pt-4">
                        <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Contacto de Venta Directa</h4>
                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Vendedor</label>
                                <input x-model="form.vendedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Vendedor</label>
                                <input x-model="form.tlfvendedor" type="text" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-2 focus:ring-black outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button @click="closeModal()" class="px-6 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 font-medium">Cancelar</button>
                    <button @click="saveSupplier()" class="px-6 py-2.5 rounded-lg bg-black text-white hover:bg-gray-800 font-bold shadow-lg flex items-center gap-2">
                        <span x-show="!loading">Guardar Registro</span>
                        <span x-show="loading">Guardando...</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS -->
    <div x-show="notification.show" 
         x-transition 
         class="fixed bottom-5 right-5 z-50 bg-gray-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3" style="display: none;">
        <span x-text="notification.message"></span>
    </div>

    <script>
        function suppliersApp() {
            return {
                suppliers: [],
                search: '',
                kpis: {},
                showModal: false,
                loading: false,
                form: {
                    nomproveedor: '',
                    cuitproveedor: '',
                    tlfproveedor: '',
                    emailproveedor: '',
                    direcproveedor: '',
                    vendedor: '',
                    tlfvendedor: ''
                },
                notification: { show: false, message: '' },

                init() {
                    this.fetchData();
                },

                fetchData() {
                    fetch(`api/proveedores_all.php?q=${this.search}`)
                        .then(r => r.json())
                        .then(data => {
                            if(data.status === 'success' || !data.error) {
                                this.kpis = data.kpis || {};
                                this.suppliers = data.data || [];
                            }
                        })
                        .catch(e => console.error(e));
                },

                openModal() {
                    this.form = { nomproveedor: '', cuitproveedor: '', tlfproveedor: '', emailproveedor: '', direcproveedor: '', vendedor: '', tlfvendedor: '' };
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                saveSupplier() {
                    if(!this.form.nomproveedor) return alert('El nombre es obligatorio');
                    
                    this.loading = true;
                    fetch('api/proveedores_all.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(this.form)
                    })
                    .then(r => r.json())
                    .then(res => {
                        this.loading = false;
                        if(res.status === 'success') {
                            this.closeModal();
                            this.fetchData();
                            this.notify('Proveedor creado correctamente');
                        } else {
                            alert('Error: ' + res.message);
                        }
                    })
                    .catch(e => {
                        this.loading = false;
                        console.error(e);
                        alert('Error de conexión');
                    });
                },

                notify(msg) {
                    this.notification.message = msg;
                    this.notification.show = true;
                    setTimeout(() => this.notification.show = false, 3000);
                },

                formatCurrency(val) {
                    return new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(val || 0);
                },

                formatDate(dateStr) {
                    if(!dateStr) return '';
                    return new Date(dateStr).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' });
                },

                isRecent(dateStr) {
                    if(!dateStr) return false;
                    const d = new Date(dateStr);
                    const now = new Date();
                    const diffTime = Math.abs(now - d);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                    return diffDays < 30; // Menos de 30 días
                },

                getInitials(name) {
                    return name ? name.substring(0, 2).toUpperCase() : '??';
                }
            }
        }
    </script>
</body>
</html>