<?php
// api/listar_compras.php
ini_set('display_errors', 1);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../includes/db.php';

try {
    $db = DB::connect();
    
    $sql = "SELECT 
            c.codcompra,
            c.fechaemision,
            c.fecharecepcion,
            c.totalpagoc as total,
            c.statuscompra as status,
            c.tipocompra,
            p.nomproveedor
            FROM compras c
            LEFT JOIN proveedores p ON c.codproveedor = p.codproveedor
            ORDER BY c.fechaemision DESC
            LIMIT 200";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
