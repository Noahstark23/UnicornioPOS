<?php
// api/productos.php
require_once 'common.php';

// La conexión DB ya está disponible como DB::connect() gracias a common.php -> db.php
// Auth check ya se realizó en common.php

try {
    $db = DB::connect();
    // CONSULTA AMPLIA: Seleccionamos todo para evitar errores de nombres de columna por ahora
    $sql = "SELECT * FROM productos WHERE tenant_id = 1 LIMIT 1000";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($productos);

} catch (Exception $e) {
    echo json_encode(["error" => "Error SQL: " . $e->getMessage()]);
}
