<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación Rápida | Unicornio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .gradient-text { background: linear-gradient(to right, #6366f1, #a855f7, #ec4899); -webkit-background-clip: text; color: transparent; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden" x-data="productWizard()">

    <!-- LEFT COLUMN: WIZARD -->
    <div class="w-1/2 p-10 flex flex-col justify-center overflow-y-auto bg-white shadow-xl z-10 relative">
        <a href="panel.php" class="absolute top-6 left-6 text-gray-400 hover:text-gray-800 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>

        <div class="max-w-md mx-auto w-full">
            <h1 class="text-4xl font-extrabold mb-2 text-gray-900">Nuevo Producto</h1>
            <p class="text-gray-500 mb-8">Completa la ficha para desbloquear el producto.</p>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between text-sm font-bold mb-1">
                    <span class="text-gray-600">Integridad del Producto</span>
                    <span :class="progress === 100 ? 'text-green-600' : 'text-blue-600'" x-text="progress + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                    <div class="h-full transition-all duration-700 ease-out flex items-center justify-end pr-1"
                         :class="progress === 100 ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-blue-400 to-indigo-600'"
                         :style="`width: ${progress}%`">
                         <template x-if="progress === 100">
                             <span class="text-[10px] text-white">🎉</span>
                         </template>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="space-y-6">
                
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Nombre del Producto</label>
                    <input type="text" x-model="form.nombre" 
                           class="w-full text-2xl font-bold bg-transparent border-b-2 border-gray-300 focus:border-indigo-600 outline-none py-2 placeholder-gray-300 transition-colors"
                           placeholder="Ej. Coca Cola 3L">
                    <p class="text-xs text-gray-400 mt-1" x-show="form.nombre.length > 0">¡Excelente nombre!</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Precio -->
                    <div>
                        <label class="block text-sm font-bold text-green-700 uppercase tracking-wider mb-2">Precio Venta</label>
                        <div class="relative">
                            <span class="absolute left-0 top-2 text-green-600 font-bold text-xl">C$</span>
                            <input type="number" x-model="form.precio_venta" 
                                   class="w-full pl-8 text-xl font-bold bg-green-50 rounded-lg border-2 border-green-200 focus:border-green-500 outline-none py-2 text-green-800 placeholder-green-200 transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Costo -->
                    <div>
                        <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Costo (Opcional)</label>
                        <div class="relative">
                            <span class="absolute left-0 top-2 text-gray-400 font-bold text-xl">C$</span>
                            <input type="number" x-model="form.costo" 
                                   class="w-full pl-8 text-xl font-medium bg-gray-50 rounded-lg border-2 border-gray-200 focus:border-gray-400 outline-none py-2 text-gray-600 transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Stock & Code Switch -->
                <div class="grid grid-cols-2 gap-6 items-center">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Stock Inicial</label>
                        <div class="flex items-center">
                            <button @click="form.stock = Math.max(0, parseInt(form.stock || 0) - 1)" class="bg-gray-200 w-10 h-10 rounded-l-lg hover:bg-gray-300 font-bold">-</button>
                            <input type="number" x-model="form.stock" class="w-full text-center border-y-2 border-gray-200 h-10 font-bold text-gray-800 outline-none" placeholder="0">
                            <button @click="form.stock = parseInt(form.stock || 0) + 1" class="bg-gray-200 w-10 h-10 rounded-r-lg hover:bg-gray-300 font-bold">+</button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center cursor-pointer space-x-3">
                            <div class="relative">
                                <input type="checkbox" x-model="hasCodigo" class="sr-only">
                                <div class="w-12 h-6 bg-gray-300 rounded-full shadow-inner transition duration-300" :class="{ 'bg-indigo-500': hasCodigo }"></div>
                                <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -left-1 -top-1 transition transform duration-300" :class="{ 'translate-x-full': hasCodigo }"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-600">Tiene Código de Barras</span>
                        </label>
                    </div>
                </div>

                <!-- Input Codigo (Condicional) -->
                <div x-show="hasCodigo" x-transition class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                     <label class="block text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">Escanea el Código Aqui 👇</label>
                     <input type="text" x-model="form.codigo" 
                            class="w-full bg-white border border-indigo-200 rounded p-2 font-mono text-indigo-900 focus:ring-2 focus:ring-indigo-400 outline-none"
                            placeholder="Escanea o escribe..."
                            @keydown.enter.prevent=""> 
                </div>

                <!-- Botón Acción -->
                <button @click="guardarProducto()" 
                        :disabled="progress < 40"
                        class="w-full py-4 rounded-xl font-extrabold text-white text-lg shadow-lg transform transition-all hover:-translate-y-1 active:scale-95 flex justify-center items-center gap-2"
                        :class="progress === 100 ? 'bg-gradient-to-r from-yellow-400 to-yellow-600 hover:shadow-yellow-300/50' : (progress < 40 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700')"
                >
                    <span x-show="loading" class="animate-spin text-white">⭕</span>
                    <span x-show="!loading" x-text="progress === 100 ? '✨ CREAR PRODUCTO MAESTRO ✨' : 'Guardar Producto'"></span>
                </button>

            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: PREVIEW GAMIFICADA -->
    <div class="w-1/2 bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col items-center justify-center p-12 relative overflow-hidden">
        
        <!-- Decoration Background -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#6366f1 1px, transparent 1px); background-size: 30px 30px;"></div>

        <h2 class="text-gray-400 font-bold mb-8 uppercase tracking-[0.2em] text-sm z-10">Vista Previa (POS Card)</h2>

        <!-- The Card -->
        <div class="bg-white w-80 rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-500 hover:scale-105 hover:rotate-1 z-10 border border-white/50">
            
            <!-- Image Placeholder -->
            <div class="h-48 bg-gray-100 flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer" title="Próximamente Subida de Imagen">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-10 transition-opacity"></div>
                
                <!-- Dynamic Initials Avatar -->
                <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-lg transform transition-transform group-hover:scale-110"
                     :style="`background-color: ${getColor(form.nombre)}`">
                    <span x-text="(form.nombre || '?').substring(0,2).toUpperCase()"></span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Stock Badge -->
                <div class="flex justify-between items-start mb-2">
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full" 
                          x-text="(form.stock > 0) ? 'En Stock: ' + form.stock : 'Agotado'"></span>
                </div>

                <!-- Title -->
                <h3 class="text-xl font-bold text-gray-900 leading-tight mb-2 h-14 overflow-hidden" 
                    x-text="form.nombre || 'Nombre del Producto'"></h3>
                
                <!-- Price -->
                <div class="flex items-end justify-between mt-4">
                    <div class="text-xs text-gray-400">
                        Code: <span class="font-mono" x-text="form.codigo || 'AUTO-GEN'"></span>
                    </div>
                    <div class="text-2xl font-extrabold text-green-600" x-text="'C$ ' + Number(form.precio_venta).toFixed(2)"></div>
                </div>
            </div>
            
            <!-- Bottom Bar Decoration -->
            <div class="h-2 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        </div>

        <p class="mt-8 text-gray-400 text-sm max-w-xs text-center z-10">
            Así es como verán tus cajeros este producto. <br>¡Asegúrate que se vea genial! 🚀
        </p>

    </div>

    <!-- MODAL SUCCESS -->
    <div x-show="successModal" x-cloak 
         class="fixed inset-0 bg-black bg-opacity-80 z-50 flex items-center justify-center backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-blue-500"></div>
            
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <span class="text-4xl">🏆</span>
            </div>
            
            <h2 class="text-3xl font-black text-gray-800 mb-2">¡Producto Creado!</h2>
            <p class="text-gray-500 mb-8">Has desbloqueado un nuevo item en tu inventario. ¿Qué quieres hacer ahora?</p>

            <div class="space-y-3">
                <button @click="resetForm()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-lg">
                    ✨ Crear Otro Producto
                </button>
                <a href="vender.php" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition">
                    💰 Ir a Vender
                </a>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
     <div x-show="toast.visible" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 translate-y-0"
          x-transition:leave-end="opacity-0 translate-y-2"
          class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-black text-white px-6 py-3 rounded-full shadow-2xl z-50 flex items-center gap-3">
         <span x-text="toast.icon"></span>
         <span x-text="toast.message" class="font-medium"></span>
    </div>

    <script>
        function productWizard() {
            return {
                form: {
                    nombre: '',
                    precio_venta: '',
                    costo: '',
                    stock: '',
                    codigo: ''
                },
                hasCodigo: false,
                loading: false,
                successModal: false,
                toast: { visible: false, message: '', icon: '' },

                get progress() {
                    let p = 0;
                    if (this.form.nombre.length > 3) p += 25;
                    if (this.form.precio_venta > 0) p += 25;
                    if (this.form.stock !== '') p += 25;
                    // El último 25% es flexible
                    if (this.hasCodigo && this.form.codigo.length > 2) p += 25;
                    if (!this.hasCodigo) p += 25; // Si decide no usar código, le damos el punto por confiar en el auto-gen
                    
                    if (p === 100 && !this.celebrated) {
                        this.celebrar();
                        this.celebrated = true; // Solo celebrar una vez al llegar a 100
                    }
                    if (p < 100) this.celebrated = false; // Reset si borra algo

                    return p;
                },

                celebrated: false,

                celebrar() {
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                },

                getColor(str) {
                    if (!str) return '#ccc';
                    let hash = 0;
                    for (let i = 0; i < str.length; i++) {
                        hash = str.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    const c = (hash & 0x00FFFFFF).toString(16).toUpperCase();
                    return '#' + "00000".substring(0, 6 - c.length) + c;
                },

                guardarProducto() {
                    if (this.progress < 40) return this.showToast('⚠️ Faltan datos clave', 'Complete más información');
                    
                    this.loading = true;
                    // Ajuste de codigo vacio
                    if(!this.hasCodigo) this.form.codigo = '';

                    fetch('api/guardar_producto_express.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(this.form)
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.loading = false;
                        if(data.status === 'ok') {
                            this.successModal = true;
                            this.celebrar();
                        } else {
                            this.showToast('❌ Error', data.message);
                        }
                    })
                    .catch(e => {
                        this.loading = false;
                        this.showToast('❌ Error Conexión', e.message);
                    });
                },

                resetForm() {
                    this.successModal = false;
                    this.form = { nombre: '', precio_venta: '', costo: '', stock: '', codigo: '' };
                    this.hasCodigo = false;
                    this.progress = 0; // Trigger recalculation visually
                },

                showToast(title, msg) {
                    this.toast.message = title + ": " + msg;
                    this.toast.icon = '🔔';
                    this.toast.visible = true;
                    setTimeout(() => this.toast.visible = false, 3000);
                }
            }
        }
    </script>
</body>
</html>
