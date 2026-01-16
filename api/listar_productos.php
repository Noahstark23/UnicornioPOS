<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $db = DB::connect();
    $q = $_GET['q'] ?? '';
    
    $sql = "SELECT * FROM productos 
            WHERE tenant_id = 1 
            AND (producto LIKE ? OR codproducto LIKE ?)
            LIMIT 50";

    $stmt = $db->prepare($sql);
    $term = "%$q%";
    $stmt->execute([$term, $term]);
    
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultados as &$prod) {
        // Normalizamos el ID
        $prod['id'] = $prod['codproducto'] ?? $prod['id'] ?? null;
        
        // Normalizamos el Nombre
        $prod['nombre'] = $prod['producto'] ?? $prod['nombre_producto'] ?? 'Sin Nombre';
        
        // Normalizamos el Precio
        $prod['precio'] = $prod['precioventa'] 
                       ?? $prod['preciopublico'] 
                       ?? $prod['precioxpublico'] 
                       ?? $prod['precio_publico'] 
                       ?? $prod['pvp'] 
                       ?? 0;
                       
        // Normalizamos el Stock
        $prod['stock'] = $prod['existencia'] ?? $prod['stock'] ?? $prod['cantidad'] ?? 0;

        // Foto URL
        $prod['foto_url'] = '';
        if (isset($prod['codproducto'])) {
             // Path relative to web root. Frontend is in root, api is in api/.
             // Files are in fotos/productos/
             $imagePath = "../fotos/productos/" . $prod['codproducto'] . ".jpg";
             if (file_exists($imagePath)) {
                 $prod['foto_url'] = "fotos/productos/" . $prod['codproducto'] . ".jpg";
             }
        }
    }
    
    echo json_encode($resultados ?: []);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
