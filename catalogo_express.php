<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Visual | Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c7c7c7; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="catalog()">

    <!-- HEADER -->
    <header class="bg-white shadow-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Logo / Title -->
            <div class="flex items-center gap-3 min-w-fit">
                <a href="panel.php" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight hidden sm:block">📦 Mi Catálogo</h1>
            </div>

            <!-- Search (Center) -->
            <div class="flex-1 max-w-2xl relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       x-model="search"
                       @input.debounce.300ms="buscar()"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-full leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm shadow-sm"
                       placeholder="Buscar por nombre, código o barra...">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center" x-show="loadingSearch">
                     <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>

            <!-- Action Button -->
            <div class="min-w-fit">
                <a href="producto_express.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <span>🚀</span> <span class="hidden sm:inline">Nuevo Producto</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN GRID -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Empty State -->
        <div x-show="productos.length === 0 && !loading" x-cloak class="text-center py-20">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-6">
                <span class="text-4xl">🔍</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No encontramos productos</h3>
            <p class="text-gray-500">Intenta con otro término o crea uno nuevo.</p>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <template x-for="prod in productos" :key="prod.codproducto || prod.id">
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden flex flex-col group relative">
                    
                    <div class="h-40 w-full bg-gray-100 flex items-center justify-center overflow-hidden relative">
                        <template x-if="prod.foto">
                             <img :src="prod.foto" class="object-cover h-full w-full group-hover:scale-110 transition-transform duration-500">
                        </template>
                        <template x-if="!prod.foto">
                            <div class="text-4xl font-bold text-gray-300 select-none" 
                                 x-text="(prod.producto || prod.nombre || '??').substring(0,2).toUpperCase()"></div>
                        </template>
                        
                        <span class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded" 
                              x-text="prod.codproducto || prod.id"></span>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1 truncate" 
                            x-text="prod.producto || prod.nombre || 'Sin Nombre'"></h3>
                        
                        <p class="text-xs text-gray-400 mb-3" 
                           x-text="prod.codigobarra || prod.codigo || 'Sin código'"></p>

                        <div class="text-2xl font-black text-indigo-600 mb-4">
                            C$ <span x-text="Number(prod.precioventa || prod.precio || 0).toFixed(2)"></span>
                        </div>

                        <div class="mt-auto" x-data="{ stock: Number(prod.existencia || prod.stock || 0) }">
                            <div class="flex justify-between text-xs mb-1 font-bold">
                                <span :class="stock > 5 ? 'text-gray-500' : 'text-red-500'">Stock Disponible</span>
                                <span x-text="stock" :class="stock > 5 ? 'text-green-600' : 'text-red-600 animate-pulse'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all duration-1000"
                                     :class="stock > 10 ? 'bg-green-500' : (stock > 0 ? 'bg-yellow-400' : 'bg-red-500')"
                                     :style="`width: ${Math.min(stock, 100)}%`">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button @click="openEdit(prod)" class="w-full bg-gray-50 hover:bg-blue-50 text-gray-600 hover:text-blue-600 font-bold py-3 border-t transition-colors flex items-center justify-center gap-2">
                        ✏️ Editar Rápido
                    </button>
                </div>
            </template>
        </div>
    </main>

    <!-- EDIT MODAL -->
    <div x-show="modalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50 backdrop-blur-sm">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 relative transform transition-all"
             @click.away="modalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>

            <h2 class="text-xl font-bold text-gray-800 mb-1">Edición Rápida</h2>
            <p class="text-sm text-gray-500 mb-6" x-text="editItem.nombre"></p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Precio Venta</label>
                    <input type="number" x-model="editItem.precio" class="w-full text-lg font-bold border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2 border">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock Actual</label>
                    <div class="flex items-center">
                        <button @click="editItem.stock--" class="px-4 py-2 bg-gray-100 rounded-l-lg hover:bg-gray-200">-</button>
                        <input type="number" x-model="editItem.stock" class="w-full text-center text-lg font-bold border-y border-gray-300 h-[42px] focus:outline-none">
                        <button @click="editItem.stock++" class="px-4 py-2 bg-gray-100 rounded-r-lg hover:bg-gray-200">+</button>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button @click="guardarCambios()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg">
                    💾 Guardar Cambios
                </button>
            </div>

        </div>
    </div>


    <script>
        function catalog() {
            return {
                search: '',
                productos: [],
                loading: true,
                loadingSearch: false,
                modalOpen: false,
                editItem: {},

                init() {
                    this.buscar();
                },

                buscar() {
                    this.loadingSearch = true;
                    fetch(`api/listar_productos.php?q=${this.search}`)
                        .then(r => r.json())
                        .then(data => {
                            this.productos = data;
                            this.loading = false;
                            this.loadingSearch = false;
                        })
                        .catch(e => {
                            console.error(e);
                            this.loadingSearch = false;
                        });
                },

                openEdit(item) {
                    // Clone item to avoid direct mutation
                    this.editItem = JSON.parse(JSON.stringify(item)); 
                    this.modalOpen = true;
                },

                guardarCambios() {
                    alert("🚧 Función de Guardar Pendiente de Implementación Backend 🚧\n\nDatos a enviar: " + JSON.stringify(this.editItem));
                    this.modalOpen = false;
                },

                formatMoney(amount) {
                    return 'C$ ' + Number(amount).toFixed(2);
                },

                getColor(str) {
                    if (!str) return '#ccc';
                    let hash = 0;
                    for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
                    const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
                    return '#' + "00000".substring(0, 6 - c.length) + c;
                },

                getStockColorText(stock) {
                    stock = parseInt(stock);
                    if (stock <= 5) return 'text-red-600 animate-pulse';
                    if (stock <= 10) return 'text-yellow-600';
                    return 'text-green-600';
                },

                getStockBarClass(stock) {
                    stock = parseInt(stock);
                    if (stock <= 5) return 'bg-red-500';
                    if (stock <= 10) return 'bg-yellow-500';
                    return 'bg-green-500';
                }
            }
        }
    </script>
</body>
</html>
