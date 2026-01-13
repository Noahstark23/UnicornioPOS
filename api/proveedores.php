<?php
// api/proveedores.php
ini_set('display_errors', 1);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../includes/db.php';

try {
    $db = DB::connect();
    $sql = "SELECT codproveedor, nomproveedor, cuitproveedor AS rucproveedor FROM proveedores ORDER BY nomproveedor ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
