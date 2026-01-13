<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
$term = $_GET['q'] ?? '';
$db = DB::connect();

// Búsqueda simple y directa - INCLUYE limitecredito para validación de crédito
$sql = "SELECT codcliente as id, 
               nomcliente as text, 
               tlfcliente as telefono,
               limitecredito
        FROM clientes 
        WHERE (nomcliente LIKE ? OR tlfcliente LIKE ?) LIMIT 10";

$stmt = $db->prepare($sql);
$stmt->execute(["%$term%", "%$term%"]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay resultados, devolver array vacío (no null)
echo json_encode($resultados ?: []);
