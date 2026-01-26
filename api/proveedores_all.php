<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $db = DB::connect();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // 1. KPIS GLOBAL
        $kpis = [
            'total_proveedores' => 0,
            'compras_total_mes' => 0,
            'deuda_pendiente' => 0
        ];

        // Total Proveedores
        $stmt = $db->query("SELECT COUNT(*) as total FROM proveedores");
        $kpis['total_proveedores'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Compras del Mes (Estado = PAGADA o similar? Asumimos todas las emitidas este mes)
        $mes_actual = date('Y-m');
        $stmt = $db->prepare("SELECT SUM(totalpagoc) as total FROM compras WHERE fechaemision LIKE ? AND statuscompra != 'ANULADA'");
        $stmt->execute(["$mes_actual%"]);
        $kpis['compras_total_mes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Deuda Pendiente (Compras a CREDITO no pagadas? Asumimos status != PAGADA y tipo = CREDITO)
        $stmt = $db->prepare("SELECT SUM(totalpagoc) as total FROM compras WHERE tipocompra = 'CREDITO' AND statuscompra = 'PENDIENTE'");
        $stmt->execute();
        $kpis['deuda_pendiente'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // 2. LISTADO DE PROVEEDORES CON METRICAS INDIVIDUALES
        // Obtenemos proveedores y sumamos sus compras históricas
        /*
            Nota: codproveedor en proveedores es varchar (P1, P2...). 
            En compras debería ser igual.
        */
        $q = $_GET['q'] ?? '';
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM compras c WHERE c.codproveedor = p.codproveedor) as total_ordenes,
                (SELECT SUM(totalpagoc) FROM compras c WHERE c.codproveedor = p.codproveedor AND c.statuscompra != 'ANULADA') as total_gastado,
                (SELECT MAX(fechaemision) FROM compras c WHERE c.codproveedor = p.codproveedor) as ultima_compra
                FROM proveedores p 
                WHERE p.nomproveedor LIKE ? OR p.cuitproveedor LIKE ?
                ORDER BY p.idproveedor DESC
                LIMIT 100";
        
        $stmt = $db->prepare($sql);
        $term = "%$q%";
        $stmt->execute([$term, $term]);
        $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'kpis' => $kpis,
            'data' => $proveedores
        ]);

    } elseif ($method === 'POST') {
        // CREACION RAPIDA DE PROVEEDOR
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Generar Codigo P...
        $stmt = $db->query("SELECT codproveedor FROM proveedores ORDER BY idproveedor DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $cod = "P1";
        } else {
            // Extraer numero, asumiendo P{numero}
            $last_cod = $row['codproveedor'];
            $num = (int)substr($last_cod, 1);
            $cod = "P" . ($num + 1);
        }

        $sql = "INSERT INTO proveedores (
            codproveedor, documproveedor, cuitproveedor, nomproveedor, tlfproveedor, 
            id_provincia, id_departamento, direcproveedor, emailproveedor, 
            vendedor, tlfvendedor, fechaingreso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $res = $stmt->execute([
            $cod,
            $data['documproveedor'] ?? 0,
            $data['cuitproveedor'] ?? '',
            strtoupper($data['nomproveedor'] ?? ''),
            $data['tlfproveedor'] ?? '',
            $data['id_provincia'] ?? 0,
            $data['id_departamento'] ?? 0,
            strtoupper($data['direcproveedor'] ?? ''),
            $data['emailproveedor'] ?? '',
            strtoupper($data['vendedor'] ?? ''),
            $data['tlfvendedor'] ?? '',
            date('Y-m-d')
        ]);

        if ($res) {
            echo json_encode(['status' => 'success', 'message' => 'Proveedor creado', 'codproveedor' => $cod]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al insertar en BD']);
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
