<?php
session_start();
// Security check
if(!isset($_SESSION['acceso'])) {
    header("Location: index.php");
    exit;
}
require_once("class/class.php"); // Include class definition just in case, though menu.php might handle it.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Unicornio</title>

    <!-- LEGACY CSS (Required for Sidebar) -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- MODERN STACK -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        /* Fix conflict between Bootstrap (legacy) and Tailwind */
        .page-wrapper {
            background: #f9fafb !important; /* bg-gray-50 */
            padding-bottom: 0px !important;
        }
    </style>
</head>

<body class="fix-header bg-gray-50" x-data="dashboard()">

    <!-- WRAPPER FOR SIDEBAR -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-boxed-layout="full" data-header-position="fixed" data-sidebar-position="fixed" class="mini-sidebar">
        
        <!-- INCLUDE SYSTEM MENU -->
        <?php include('menu.php'); ?>

        <!-- PAGE CONTENT -->
        <div class="page-wrapper">
            
            <!-- HERO HEADER (Inside page wrapper) -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 pb-32 pt-12 px-6 shadow-xl relative overflow-hidden">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-10 rounded-full -ml-24 -mb-24 blur-2xl"></div>

                <div class="max-w-7xl mx-auto flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-indigo-100 text-sm font-semibold uppercase tracking-wider mb-1">Bienvenido de nuevo</p>
                        <h1 class="text-4xl font-black text-white leading-tight">
                            Hola, <?php echo explode(' ', $_SESSION['nombres'] ?? 'Usuario')[0]; ?> 👋
                        </h1>
                        <p class="text-indigo-100 mt-2 text-lg opacity-90">Aquí está el resumen de tu negocio hoy.</p>
                    </div>
                </div>
            </div>

            <!-- DASHBOARD CONTENT -->
            <main class="max-w-7xl mx-auto px-6 -mt-20 relative z-20 pb-12">
                
                <!-- METRICS CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <!-- Ventas -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition transform duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Ventas de Hoy</p>
                                <h2 class="text-3xl font-black text-green-600 mt-1" x-text="formatMoney(metrics.ventasHoy)">...</h2>
                            </div>
                            <div class="p-2 bg-green-50 rounded-lg text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 mt-auto">Calculado al cierre actual</div>
                    </div>

                    <!-- Transacciones -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition transform duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Transacciones</p>
                                <h2 class="text-3xl font-black text-gray-800 mt-1" x-text="metrics.transaccionesHoy">0</h2>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 mt-auto">Tickets emitidos hoy</div>
                    </div>

                    <!-- Alertas -->
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col justify-between h-32 hover:-translate-y-1 transition transform duration-300 border-l-4" 
                        :class="metrics.alertasStock > 0 ? 'border-red-500' : 'border-gray-200'">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-wide">Alertas Stock</p>
                                <h2 class="text-3xl font-black mt-1" 
                                    :class="metrics.alertasStock > 0 ? 'text-red-600 animate-pulse' : 'text-gray-800'"
                                    x-text="metrics.alertasStock">0</h2>
                            </div>
                            <div class="p-2 bg-red-50 rounded-lg text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 mt-auto">
                            <span x-show="metrics.alertasStock > 0">Necesitan reposición inmediata</span>
                            <span x-show="metrics.alertasStock == 0">Todo en orden ✅</span>
                        </div>
                    </div>

                </div>

                <!-- MAIN SECTION -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- GRAPH -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 relative">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">Rendimiento Semanal</h3>
                        <div class="h-64 w-full">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- QUICK ACTIONS -->
                    <div class="space-y-4">
                        
                        <h3 class="text-lg font-bold text-gray-800 px-1">Accesos Rápidos</h3>

                        <a href="vender.php" class="block w-full group">
                            <div class="bg-white hover:bg-indigo-50 border border-gray-100 p-4 rounded-xl shadow-sm hover:shadow-md transition flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Nueva Venta</h4>
                                    <p class="text-xs text-gray-500">Ir al punto de venta</p>
                                </div>
                                <div class="ml-auto text-gray-300 group-hover:text-indigo-600">➝</div>
                            </div>
                        </a>

                        <a href="producto_express.php" class="block w-full group">
                            <div class="bg-white hover:bg-purple-50 border border-gray-100 p-4 rounded-xl shadow-sm hover:shadow-md transition flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Nuevo Producto</h4>
                                    <p class="text-xs text-gray-500">Añadir inventario</p>
                                </div>
                                <div class="ml-auto text-gray-300 group-hover:text-purple-600">➝</div>
                            </div>
                        </a>

                        <a href="caja_express.php" class="block w-full group">
                            <div class="bg-white hover:bg-green-50 border border-gray-100 p-4 rounded-xl shadow-sm hover:shadow-md transition flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center group-hover:scale-110 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Caja / Turnos</h4>
                                    <p class="text-xs text-gray-500">Gestión de efectivo</p>
                                </div>
                                <div class="ml-auto text-gray-300 group-hover:text-green-600">➝</div>
                            </div>
                        </a>

                        <a href="catalogo_express.php" class="block w-full group">
                            <div class="bg-white hover:bg-orange-50 border border-gray-100 p-4 rounded-xl shadow-sm hover:shadow-md transition flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center group-hover:scale-110 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Catálogo Visual</h4>
                                    <p class="text-xs text-gray-500">Ver productos</p>
                                </div>
                                <div class="ml-auto text-gray-300 group-hover:text-orange-600">➝</div>
                            </div>
                        </a>

                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- LEGACY SCRIPTS -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/sidebar-nav.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- DASHBOARD LOGIC -->
    <script>
        function dashboard() {
            return {
                metrics: {
                    ventasHoy: 0,
                    transaccionesHoy: 0,
                    alertasStock: 0,
                    chartLabels: [],
                    chartData: []
                },

                init() {
                    fetch('api/dashboard_data.php')
                        .then(r => r.json())
                        .then(data => {
                            this.metrics = data;
                            this.initChart();
                        })
                        .catch(e => console.error(e));
                },

                initChart() {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.metrics.chartLabels,
                            datasets: [{
                                label: 'Ventas (C$)',
                                data: this.metrics.chartData,
                                borderColor: '#4F46E5', // Indigo 600
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 3,
                                tension: 0.4, // Curva suave
                                fill: true,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#4F46E5',
                                pointRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { borderDash: [2, 4], color: '#f3f4f6' }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                },

                formatMoney(amount) {
                    return 'C$ ' + Number(amount).toFixed(2);
                }
            }
        }
    </script>
</body>
</html>
