<?php require_once "includes/header.php"; ?>

<style>
    .navbar, .topbar, .main-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding-top: 70px !important; background: #f3f4f6; min-height: 100vh; }
    .uni-bar { position: fixed; top: 0; left: 0; right: 0; height: 60px; background: linear-gradient(to right, #6d28d9, #4f46e5); z-index: 1000; display: flex; align-items: center; padding: 0 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); color: white; }
    .card-chart { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); height: 100%; position: relative; }
    
    /* Estilos para el filtro */
    .filter-bar { background: white; padding: 15px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
    .form-input { border: 1px solid #ddd; padding: 8px 12px; border-radius: 8px; outline: none; color: #555; }
    .btn-filter { background: #4f46e5; color: white; padding: 8px 20px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer; transition: 0.3s; }
    .btn-filter:hover { background: #4338ca; }
</style>

<div class="uni-bar">
    <a href="panel.php" style="color:white; font-size:20px; margin-right:15px;"><i class="fa fa-arrow-left"></i></a>
    <span style="font-size:20px; font-weight:800;">📊 Analítica Avanzada</span>
</div>

<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-wrapper">
    <div class="px-8 mt-4 pb-8" x-data="analisis()" x-init="init()">
        
        <div class="filter-bar">
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-500">Desde:</label>
                <input type="date" x-model="filtros.inicio" class="form-input">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-bold text-gray-500">Hasta:</label>
                <input type="date" x-model="filtros.fin" class="form-input">
            </div>
            <button @click="aplicarFiltros()" class="btn-filter" :disabled="cargando">
                <template x-if="!cargando">
                    <span><i class="fa fa-filter"></i> Actualizar Datos</span>
                </template>
                <template x-if="cargando">
                    <span><i class="fa fa-spinner fa-spin"></i> Cargando...</span>
                </template>
            </button>
            <span class="text-xs text-gray-400 ml-auto" x-text="'Mostrando datos del ' + filtros.inicio + ' al ' + filtros.fin"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-indigo-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Valor Inventario (Actual)</p>
                <h2 class="text-3xl font-black text-gray-800" x-text="'C$ ' + kpis.inventario_costo">...</h2>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Ventas (Selección)</p>
                <h2 class="text-3xl font-black text-gray-800" x-text="'C$ ' + kpis.ventas_periodo">...</h2>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Ticket Promedio</p>
                <h2 class="text-3xl font-black text-gray-800" x-text="'C$ ' + kpis.ticket_promedio">...</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card-chart">
                <h3 class="font-bold text-gray-700 mb-4">📈 Tendencia de Ventas (Selección)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="chartTendencia"></canvas>
                </div>
            </div>
            <div class="card-chart">
                <h3 class="font-bold text-gray-700 mb-4">🏆 Top Productos Más Rentables</h3>
                <div class="relative h-64 w-full">
                    <canvas id="chartRentable"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card-chart md:col-span-1">
                <h3 class="font-bold text-gray-700 mb-4">💳 Métodos de Pago</h3>
                <div class="relative h-48 w-full">
                    <canvas id="chartPago"></canvas>
                </div>
            </div>
            <div class="md:col-span-2 bg-purple-50 rounded-xl p-6 border border-purple-100 flex items-center gap-4">
                <div class="bg-white p-4 rounded-full shadow-sm text-purple-600">
                    <i class="fa fa-lightbulb-o fa-2x"></i>
                </div>
                <div>
                    <h4 class="font-bold text-purple-900">Consejo de Negocio:</h4>
                    <p class="text-sm text-purple-700 mt-1">
                        Si tu "Ticket Promedio" baja, intenta ofrecer productos complementarios en caja. 
                        Analiza los días con picos bajos en la gráfica de tendencia para lanzar promociones específicas.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>

<script>
function analisis() {
    return {
        // Fechas por defecto: Primer día del mes hasta hoy
        filtros: {
            inicio: new Date().toISOString().slice(0, 8) + '01',
            fin: new Date().toISOString().slice(0, 10)
        },
        kpis: { inventario_costo: '0.00', ventas_periodo: '0.00', ticket_promedio: '0.00' },
        charts: {}, // Almacena instancias de Chart.js para poder destruirlas
        cargando: false,

        init() {
            this.aplicarFiltros();
        },

        async aplicarFiltros() {
            try {
                this.cargando = true;
                const params = `?inicio=${this.filtros.inicio}&fin=${this.filtros.fin}`;
                
                // 1. Cargar KPIs
                const resKpi = await fetch('api/datos_analisis.php' + params + '&accion=kpis_generales');
                if (!resKpi.ok) throw new Error('Error al cargar datos');
                this.kpis = await resKpi.json();

                // 2. Renderizar Gráficos
                await Promise.all([
                    this.renderChartTendencia(params),
                    this.renderChartRentable(params),
                    this.renderChartPago(params)
                ]);
                
                // 3. Mostrar confirmación
                this.mostrarExito();
            } catch (error) {
                alert('Error al actualizar datos. Por favor intenta de nuevo.');
                console.error(error);
            } finally {
                this.cargando = false;
            }
        },

        mostrarExito() {
            // Crear notificación temporal
            const notif = document.createElement('div');
            notif.innerHTML = '<i class="fa fa-check-circle"></i> Datos actualizados';
            notif.style.cssText = 'position:fixed;top:80px;right:20px;background:#10b981;color:white;padding:15px 25px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.2);z-index:9999;font-weight:bold;';
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 2000);
        },

        async renderChartTendencia(params) {
            const res = await fetch('api/datos_analisis.php' + params + '&accion=ventas_tendencia');
            const data = await res.json();
            
            this.destroyChart('chartTendencia');
            
            const ctx = document.getElementById('chartTendencia');
            this.charts['chartTendencia'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.fecha),
                    datasets: [{
                        label: 'Ventas',
                        data: data.map(d => d.total),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3, tension: 0.4, fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        },

        async renderChartRentable(params) {
            const res = await fetch('api/datos_analisis.php' + params + '&accion=top_rentables');
            const data = await res.json();

            this.destroyChart('chartRentable');

            const ctx = document.getElementById('chartRentable');
            this.charts['chartRentable'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.producto.substring(0, 15) + '...'),
                    datasets: [{
                        label: 'Ganancia (C$)',
                        data: data.map(d => d.ganancia),
                        backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderRadius: 5
                    }]
                },
                options: { 
                    indexAxis: 'y', 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        },

        async renderChartPago(params) {
            const res = await fetch('api/datos_analisis.php' + params + '&accion=metodos_pago');
            const data = await res.json();

            this.destroyChart('chartPago');

            const ctx = document.getElementById('chartPago');
            this.charts['chartPago'] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.formapago),
                    datasets: [{
                        data: data.map(d => d.cantidad),
                        backgroundColor: ['#4f46e5', '#ec4899', '#f59e0b', '#10b981']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        },

        destroyChart(id) {
            if (this.charts[id]) {
                this.charts[id].destroy();
            }
        }
    }
}
</script>
