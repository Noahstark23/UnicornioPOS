<?php
require_once("class/classconexion.php");
require_once("class/funciones_basicas.php"); // For decrypt/limpiar if needed

header('Content-Type: application/json');

// Check params
if (!isset($_GET['codsucursal']) || !isset($_GET['desde']) || !isset($_GET['hasta'])) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$codsucursal = isset($_GET['codsucursal']) ? $_GET['codsucursal'] : '';
// Try to decrypt if it looks encrypted (long string), otherwise use as is
// (The app uses encrypted IDs in forms)
$decrypted_sucursal = decrypt($codsucursal);
// Fallback if decryption returns empty but input wasn't empty (or if using plain ID)
$final_sucursal = $decrypted_sucursal ? $decrypted_sucursal : $codsucursal;

$desde = date("Y-m-d", strtotime($_GET['desde']));
$hasta = date("Y-m-d", strtotime($_GET['hasta']));

class DashboardAPI extends Db {
    public function __construct() {
        parent::__construct();
    }

    public function getData($sucursal, $from, $to) {
        $this->SetNames();
        $response = [
            "summary" => [
                "total_revenue" => 0,
                "total_sold" => 0,
                "top_product" => "N/A"
            ],
            "chart" => [
                "labels" => [],
                "revenue" => [],
                "sales" => []
            ]
        ];

        // 1. Chart Data (Time Series) & Summary Totals
        // Group by Date
        $sql = "SELECT 
                    DATE_FORMAT(ventas.fechaventa, '%Y-%m-%d') as fecha,
                    SUM(detalleventas.cantventa) as cantidad,
                    SUM((detalleventas.precioventa * detalleventas.cantventa) - ((detalleventas.precioventa * detalleventas.cantventa) * (detalleventas.descproducto/100))) as total_money
                FROM ventas 
                INNER JOIN detalleventas ON ventas.codventa=detalleventas.codventa
                INNER JOIN productos ON detalleventas.codproducto=productos.codproducto
                WHERE ventas.codsucursal = ? 
                AND productos.codsucursal = ?
                AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? 
                AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? 
                GROUP BY DATE_FORMAT(ventas.fechaventa, '%Y-%m-%d')
                ORDER BY fecha ASC";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute([$sucursal, $sucursal, $from, $to]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response['chart']['labels'][] = $row['fecha'];
            $response['chart']['sales'][] = (int)$row['cantidad'];
            $response['chart']['revenue'][] = (float)$row['total_money'];

            $response['summary']['total_revenue'] += (float)$row['total_money'];
            $response['summary']['total_sold'] += (int)$row['cantidad'];
        }

        // 2. Top 10 Products
        $sqlTop = "SELECT 
                    productos.producto,
                    SUM(detalleventas.cantventa) as cantidad
                   FROM ventas 
                   INNER JOIN detalleventas ON ventas.codventa=detalleventas.codventa
                   INNER JOIN productos ON detalleventas.codproducto=productos.codproducto
                   WHERE ventas.codsucursal = ? 
                   AND productos.codsucursal = ?
                   AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') >= ? 
                   AND DATE_FORMAT(ventas.fechaventa,'%Y-%m-%d') <= ? 
                   GROUP BY productos.codproducto
                   ORDER BY cantidad DESC
                   LIMIT 10";
        
        $stmtTop = $this->dbh->prepare($sqlTop);
        $stmtTop->execute([$sucursal, $sucursal, $from, $to]);
        
        $response['summary']['top_products_list'] = [];
        while ($row = $stmtTop->fetch(PDO::FETCH_ASSOC)) {
           $response['summary']['top_products_list'][] = $row;
        }

        // Keep backward compatibility for 'top_product' simple string if needed, or just take first
        $response['summary']['top_product'] = isset($response['summary']['top_products_list'][0]) 
            ? $response['summary']['top_products_list'][0]['producto'] 
            : "N/A";

        return $response;
    }
}

try {
    $api = new DashboardAPI();
    $data = $api->getData($final_sucursal, $desde, $hasta);
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
