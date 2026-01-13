<?php
require_once "includes/header.php"; 
?>

<style>
    /* 1. ARREGLO DE CAPAS DEL HEADER (Compatibilidad Universal) */
    /* Apuntamos a todas las posibles clases de headers de plantillas legacy */
    .topbar, .navbar, .header, .main-header {
        position: relative !important; 
        z-index: 9999 !important; /* Siempre encima del contenido */
    }
    
    /* Aseguramos que los menús desplegables se vean */
    .dropdown-menu {
        z-index: 10000 !important;
    }

    /* 2. CONTENEDOR UNICORNIO (Namespace 'uni-') */
    .uni-wrapper {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        color: #333;
        background-color: #f3f4f6;
        min-height: 100vh;
        /* Corrección para que no se pegue al header */
        margin-top: 0px; 
        position: relative;
        z-index: 1; /* Menor que el header */
    }

    /* 3. HERO SECTION (El fondo morado) */
    .uni-hero {
        background: linear-gradient(90deg, #6d28d9 0%, #4f46e5 100%);
        padding: 40px 30px 100px 30px; /* Padding bottom extra para el efecto flotante */
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        color: white;
        margin-bottom: -60px; /* Truco para que las tarjetas floten encima */
        position: relative;
        z-index: 2;
    }

    /* 4. GRID SYSTEM (Reemplazo de Tailwind Grid) */
    .uni-container {
        padding: 0 30px;
        position: relative;
        z-index: 3;
        max-width: 1200px;
        margin: 0 auto;
    }

    .uni-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px;
    }

    .uni-col-3 {
        width: 33.333%;
        padding: 0 15px;
        box-sizing: border-box;
    }

    .uni-col-8 {
        width: 66.666%;
        padding: 0 15px;
        box-sizing: border-box;
    }
    
    .uni-col-4 {
        width: 33.333%;
        padding: 0 15px;
        box-sizing: border-box;
    }

    /* Responsivo para móviles */
    @media (max-width: 768px) {
        .uni-col-3, .uni-col-8, .uni-col-4 { width: 100%; margin-bottom: 20px; }
        .uni-hero { padding: 30px 20px 80px 20px; }
    }

    /* 5. TARJETAS (CARDS) */
    .uni-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-left: 5px solid transparent; /* Para los bordes de color */
    }
    
    .uni-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    /* Colores de borde */
    .border-green { border-left-color: #10b981; }
    .border-blue { border-left-color: #3b82f6; }
    .border-red { border-left-color: #ef4444; }

    /* Textos y Utilidades */
    .uni-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: #9ca3af;
        margin-bottom: 5px;
    }
    
    .uni-value {
        font-size: 2rem;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
    }
    
    .uni-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .bg-green-light { background-color: #d1fae5; color: #059669; }
    .bg-blue-light { background-color: #dbeafe; color: #2563eb; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }

    .uni-link {
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .text-blue { color: #2563eb; }
    .text-red { color: #dc2626; }

    /* Botones de Acceso Rápido */
    .uni-btn-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        text-decoration: none;
        color: #4b5563;
        font-weight: 600;
        transition: all 0.2s;
        margin-bottom: 10px;
    }
    
    .uni-btn-action:hover {
        background-color: #e0e7ff; /* Indigo suave */
        color: #4338ca;
        border-color: #c7d2fe;
    }
    
    .uni-btn-icon {
        width: 35px;
        height: 35px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="uni-wrapper">
    
    <div class="uni-hero">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin:0; font-size: 2.2rem; font-weight: 800;">¡Hola, Equipo! 👋</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Tu sistema funciona al 100% sin errores.</p>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; font-weight: bold; backdrop-filter: blur(5px);">
                📅 <?php echo date('d M, Y'); ?>
            </div>
        </div>
    </div>

    <div class="uni-container" x-data="dashboard()" x-init="init()">
        
        <div class="uni-row" style="margin-bottom: 30px;">
            <div class="uni-col-3">
                <div class="uni-card border-green" onclick="location.href='vender'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <div class="uni-label">Ventas de Hoy</div>
                            <h2 class="uni-value" x-text="datos.ventas_formateadas">...</h2>
                        </div>
                        <div class="uni-icon-box bg-green-light">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <span style="background: #ecfdf5; color: #047857; padding: 4px 8px; border-radius: 5px; font-size: 0.8rem; font-weight: bold;">
                            <i class="fa fa-check"></i> <span x-text="datos.transacciones">0</span> Transacciones
                        </span>
                        <a href="ventasxfechas.php?desde=<?php echo date('Y-m-d'); ?>&hasta=<?php echo date('Y-m-d'); ?>" class="uni-link text-blue" style="margin-left: 10px; font-size: 0.8rem;">Ver Detalle &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="uni-col-3">
                <div class="uni-card border-blue" onclick="location.href='caja_express'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <div class="uni-label">Efectivo en Caja</div>
                            <h2 class="uni-value" x-text="datos.caja_formateada">...</h2>
                        </div>
                        <div class="uni-icon-box bg-blue-light">
                            <i class="fa fa-briefcase"></i>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <a href="caja_express.php" class="uni-link text-blue">Ver Detalle &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="uni-col-3">
                <div class="uni-card border-red" onclick="location.href='productos'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <div class="uni-label">Stock Crítico</div>
                            <h2 class="uni-value" x-text="datos.stock_bajo" style="color: #dc2626;">0</h2>
                        </div>
                        <div class="uni-icon-box bg-red-light">
                            <i class="fa fa-warning"></i>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <a href="catalogo_express.php" class="uni-link text-red">Reponer &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="uni-row">
            <div class="uni-col-8">
                <div class="uni-card">
                    <h3 style="margin-top: 0; color: #374151; font-weight: 700;">Tendencia Semanal 📈</h3>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="uni-col-4">
                <div class="uni-card">
                    <h3 style="margin-top: 0; color: #374151; font-weight: 700; margin-bottom: 20px;">Acceso Rápido ⚡</h3>
                    
                    <a href="vender.php" class="uni-btn-action">
                        <div style="display: flex; align-items: center;">
                            <div class="uni-btn-icon" style="color: #4f46e5;"><i class="fa fa-shopping-cart"></i></div>
                            <span>Ir al POS</span>
                        </div>
                        <i class="fa fa-chevron-right" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>

                    <a href="caja_express.php" class="uni-btn-action">
                        <div style="display: flex; align-items: center;">
                            <div class="uni-btn-icon" style="color: #4b5563;"><i class="fa fa-briefcase"></i></div>
                            <span>Caja Express</span>
                        </div>
                        <i class="fa fa-chevron-right" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>

                    <a href="producto_express.php" class="uni-btn-action">
                        <div style="display: flex; align-items: center;">
                            <div class="uni-btn-icon" style="color: #4b5563;"><i class="fa fa-plus-circle"></i></div>
                            <span>Nuevo Producto</span>
                        </div>
                        <i class="fa fa-chevron-right" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>

                    <a href="analisis.php" class="uni-btn-action">
                        <div style="display: flex; align-items: center;">
                            <div class="uni-btn-icon" style="color: #6d28d9;"><i class="fa fa-line-chart"></i></div>
                            <span>Analítica</span>
                        </div>
                        <i class="fa fa-chevron-right" style="font-size: 0.8rem; opacity: 0.5;"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once "includes/footer.php"; ?>

<script>
function dashboard() {
    return {
        datos: { ventas_formateadas: 'C$ 0.00', transacciones: 0, caja_formateada: 'C$ 0.00', stock_bajo: 0, grafico: [] },
        init() {
            fetch('api/dashboard_data.php')
                .then(r => r.json())
                .then(d => { this.datos = d; this.renderChart(d.grafico); })
                .catch(e => console.error(e));
        },
        renderChart(dataGrafico) {
            const ctx = document.getElementById('ventasChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dataGrafico.map(d => d.fecha),
                    datasets: [{
                        label: 'Ventas',
                        data: dataGrafico.map(d => d.total),
                        borderColor: '#4f46e5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5'
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: {display:false} }, scales: { y: {beginAtZero: true} } }
            });
        }
    }
}
</script>