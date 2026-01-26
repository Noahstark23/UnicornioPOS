<?php
require_once("class/class.php");
if(!isset($_SESSION['acceso'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Unicornio</title>

    <!-- LEGACY CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- MODERN STACK -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Table Scrollbar */
        .table-container::-webkit-scrollbar {
            height: 8px; width: 8px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800" x-data="productsGrid()">

    <div class="min-h-screen flex flex-col">
        
        <!-- HERO HEADER -->
        <div class="bg-gradient-to-r from-indigo-700 to-purple-700 pb-24 pt-12 px-8 shadow-xl relative overflow-hidden">
            
            <!-- Botón Volver -->
            <a href="panel" class="absolute top-6 left-6 z-20 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg backdrop-blur-sm transition flex items-center gap-2 border border-white/20 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Panel
            </a>

            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32 blur-3xl"></div>

                <div class="max-w-7xl mx-auto flex justify-between items-center relative z-10">
                    <div>
                        <h1 class="text-3xl font-black text-white leading-tight">Inventario de Productos</h1>
                        <p class="text-indigo-100 mt-1 opacity-90">Gestiona precios y stock en tiempo real.</p>
                    </div>

                    <?php if($_SESSION['acceso'] != 'cajero') { ?>
                    <a href="producto_express.php" class="bg-white text-indigo-600 hover:bg-indigo-50 font-bold py-3 px-6 rounded-full shadow-lg transition transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        NUEVO PRODUCTO
                    </a>
                    <?php } ?>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <main class="max-w-7xl mx-auto px-6 -mt-16 relative z-20 pb-12">

                <!-- DATA GRID -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

                    <!-- Toolbar -->
                    <div class="p-4 border-b border-gray-100 flex gap-4 bg-gray-50">
                        <div class="relative flex-1">
                            <input type="text" x-model="search" @input.debounce.500ms="fetchProducts()" placeholder="Buscar por código o nombre..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <button @click="fetchProducts()" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto table-container">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="p-4 font-semibold w-24">Código</th>
                                    <th class="p-4 font-semibold">Producto</th>
                                    <th class="p-4 font-semibold w-32 text-right">Precio</th>
                                    <th class="p-4 font-semibold w-24 text-center">Stock</th>
                                    <th class="p-4 font-semibold w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="product in products" :key="product.id">
                                    <tr class="hover:bg-gray-50 transition group">
                                        <!-- Código -->
                                        <td class="p-4 text-sm text-gray-500 font-mono" x-text="product.codproducto"></td>

                                        <!-- Nombre + Foto -->
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-200">
                                                    <template x-if="product.foto_url">
                                                        <img :src="product.foto_url" class="h-full w-full object-cover">
                                                    </template>
                                                    <template x-if="!product.foto_url">
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400 font-bold text-xs">
                                                            <span x-text="getInitials(product.nombre)"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="font-semibold text-gray-800 text-sm truncate max-w-xs" x-text="product.nombre"></div>
                                            </div>
                                        </td>

                                        <!-- Precio (Editable o Solo Lectura) -->
                                        <td class="p-2 text-right">
                                            <?php if($_SESSION['acceso'] == 'cajero') { ?>
                                                <span class="font-mono text-gray-700 font-bold">C$ <span x-text="product.precio"></span></span>
                                            <?php } else { ?>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">C$</span>
                                                    <input type="number"
                                                        x-model="product.precio"
                                                        @focus="$el.select()"
                                                        @keyup.enter="$el.blur()"
                                                        @blur="updateProduct(product.id, 'precio', $event.target.value)"
                                                        class="w-full text-right bg-transparent border-0 rounded-md py-2 px-3 pl-8 font-mono text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition"
                                                    >
                                                </div>
                                            <?php } ?>
                                        </td>

                                        <!-- Stock (Editable o Solo Lectura) -->
                                        <td class="p-2 text-center">
                                            <?php if($_SESSION['acceso'] == 'cajero') { ?>
                                                <span class="font-mono text-gray-700 font-bold" :class="product.stock <= 5 ? 'text-red-600' : ''" x-text="product.stock"></span>
                                            <?php } else { ?>
                                                <input type="number"
                                                    x-model="product.stock"
                                                    @focus="$el.select()"
                                                    @keyup.enter="$el.blur()"
                                                    @blur="updateProduct(product.id, 'stock', $event.target.value)"
                                                    class="w-full text-center bg-transparent border-0 rounded-md py-2 px-2 font-mono text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition"
                                                    :class="product.stock <= 5 ? 'text-red-600 font-bold' : ''"
                                                >
                                            <?php } ?>
                                        </td>

                                        <!-- Actions/Status -->
                                        <td class="p-4 text-center">
                                            <div x-show="product.status === 'saving'" class="text-indigo-500 animate-spin">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>
                                            <div x-show="product.status === 'saved'" x-transition.duration.1000ms class="text-green-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="products.length === 0">
                                    <td colspan="5" class="p-8 text-center text-gray-400">
                                        No se encontraron productos.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div x-data="{ show: false, message: '' }"
         @notify.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
         class="fixed bottom-5 right-5 z-50"
         x-cloak>
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="bg-gray-900 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span x-text="message"></span>
        </div>
    </div>

    <!-- LEGACY SCRIPTS -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/sidebar-nav.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- LOGIC -->
    <script>
        function productsGrid() {
            return {
                products: [],
                search: '',

                init() {
                    this.fetchProducts();
                },

                fetchProducts() {
                    fetch(`api/listar_productos.php?q=${this.search}`)
                        .then(r => r.json())
                        .then(data => {
                            this.products = data.map(p => ({
                                ...p,
                                status: 'idle' // idle, saving, saved, error
                            }));
                        })
                        .catch(e => console.error(e));
                },

                updateProduct(id, field, value) {
                    const product = this.products.find(p => p.id === id);
                    if (!product) return;

                    // Optimistic UI? Maybe wait for response to be safe but show loading
                    product.status = 'saving';

                    fetch('api/actualizar_producto_inline.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, field, value })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success || res.status === 'success') {
                            product.status = 'saved';
                            this.$dispatch('notify', 'Producto actualizado correctamente');
                            setTimeout(() => product.status = 'idle', 2000);
                        } else {
                            product.status = 'error';
                            // Revert? Hard to revert without keeping old value.
                            // For now just alert.
                            alert('Error al guardar: ' + (res.error || 'Desconocido'));
                        }
                    })
                    .catch(e => {
                        console.error(e);
                        product.status = 'error';
                    });
                },

                getInitials(name) {
                    return name ? name.substring(0, 2).toUpperCase() : '??';
                }
            }
        }
    </script>
</body>
</html>
