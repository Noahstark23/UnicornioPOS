function InitDashboard() {
    console.log("Elite Dashboard Initialized");

    // Hook into the existing Search Button
    // The existing button calls BuscaProductosVendidos() onclick. 
    // We will augment that function or just listen for clicks.
    // Since BuscaProductosVendidos is global, we can wrap it? 
    // Or just attach a listener to the button which we will give an ID if it doesn't have one, or use the class.

    // Better strategy: Expose a function that productosvendidos.php calls alongside the original one.
}

// Auto-init when document is ready
$(document).ready(function () {
    // Initial load (will work if fields have values, e.g. for non-admins or pre-filled dates)
    UpdateDashboard();

    // specific listeners for "Real Time" feel
    $("#codsucursal").on("change", function () { UpdateDashboard(); });
    // For date inputs, we listen to change and blur to catch calendar updates
    $("#desde, #hasta").on("change blur", function () {
        // Small delay to ensure value is committed by datepicker
        setTimeout(UpdateDashboard, 200);
    });
});

let salesChart = null;

function UpdateDashboard() {
    const codsucursal = $("#codsucursal").val();
    const desde = $("#desde").val();
    const hasta = $("#hasta").val();

    if (!codsucursal || !desde || !hasta) return;

    // Show loading state for cards
    $(".dash-value").text("...");

    $.ajax({
        url: "api_dashboard.php",
        type: "GET",
        data: {
            codsucursal: codsucursal,
            desde: desde,
            hasta: hasta
        },
        dataType: "json",
        success: function (data) {
            if (data.error) {
                console.error("API Error:", data.error);
                return;
            }

            // Animate Numbers
            animateValue("card_revenue", 0, data.summary.total_revenue, 1000, true);
            animateValue("card_sales", 0, data.summary.total_sold, 1000, false);

            // Top 10 List
            const listBody = $("#top_products_list_body");
            listBody.empty();

            if (data.summary.top_products_list && data.summary.top_products_list.length > 0) {
                // Set Leader Label
                $("#top_product_label").text("Líder: " + data.summary.top_products_list[0].producto.substring(0, 20) + "...");

                // Populate List
                data.summary.top_products_list.forEach((item, index) => {
                    const row = `<tr>
                        <td style="width: 20px;">${index + 1}.</td>
                        <td>${item.producto}</td>
                        <td class="text-right"><strong>${item.cantidad}</strong></td>
                    </tr>`;
                    listBody.append(row);
                });
            } else {
                $("#top_product_label").text("Sin datos");
                listBody.html("<tr><td colspan='3'>No hay ventas en este periodo</td></tr>");
            }

            // Render Chart
            renderChart(data.chart);
        },
        error: function (err) {
            console.error("Dashboard Fetch Error", err);
        }
    });
}

function animateValue(id, start, end, duration, isCurrency) {
    if (start === end) return;
    const range = end - start;
    let current = start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    const obj = document.getElementById(id);

    // For large numbers, just set it directly to avoid UI lag, or use a simpler tween
    if (range > 100 && stepTime < 1) {
        obj.innerHTML = isCurrency ? formatCurrency(end) : end;
        return;
    }

    const timer = setInterval(function () {
        current += increment;
        obj.innerHTML = isCurrency ? formatCurrency(current) : current;
        if (current == end) {
            clearInterval(timer);
        }
    }, stepTime);

    // Fallback
    obj.innerHTML = isCurrency ? formatCurrency(end) : end;
}

function formatCurrency(num) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(num);
}

function renderChart(chartData) {
    // Chart.js 1.0.2 Syntax
    // new Chart(ctx).Line(data, options);

    // Ensure element exists
    var cvs = document.getElementById('salesChart');
    if (!cvs) return;

    // Resize for responsiveness manually or rely on CSS/ChartJS 1.0.2 quirks
    // 1.0.2 isn't fully responsive by default in the same way modern ones are.
    // We make sure the canvas parent handles it, but we might need to reset canvas

    var ctx = cvs.getContext('2d');

    // Destroy previous instance if we could (ChartJS 1.x doesn't have destroy() easily accessible on the instance itself in the same way)
    // Best way in 1.x is to clear canvas or replace node.
    // Let's replace the node to start fresh.
    var container = cvs.parentNode;
    var newCanvas = document.createElement('canvas');
    newCanvas.id = 'salesChart';
    // Copy attributes if needed, or Set width/height
    newCanvas.style.width = "100%";
    newCanvas.style.height = "300px";

    // Remove old, add new
    container.removeChild(cvs);
    container.appendChild(newCanvas);

    // Get new context
    var ctxNew = newCanvas.getContext("2d");

    // Prepare Data for 1.x
    var data = {
        labels: chartData.labels,
        datasets: [
            {
                label: "Ventas",
                fillColor: "rgba(46, 204, 113, 0.2)", // Greenish
                strokeColor: "rgba(46, 204, 113, 1)",
                pointColor: "rgba(46, 204, 113, 1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(46, 204, 113, 1)",
                data: chartData.revenue // Showing Revenue as main metric? Or Sales?
                // User asked for "Tendencia de Ventas" (Sales Trend)
                // We have chartData.revenue and chartData.sales
                // Let's show Revenue for now as it looks better, or maybe Units?
                // Let's sticking to REVENUE as it maps to $.
            }
        ]
    };

    var options = {
        responsive: true,
        maintainAspectRatio: false,
        tooltipTemplate: "<%= value %>", // Basic tooltip
        scaleLabel: "<%=value%>"
    };

    if (salesChart) {
        salesChart = null; // Clear reference
    }

    salesChart = new Chart(ctxNew).Line(data, options);
}
