<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $db = DB::connect();
    $q = $_GET['q'] ?? '';
    
    // USAMOS SELECT * PARA EVITAR ERRORES DE COLUMNA
    // Así traemos todo lo que tenga la tabla, se llame como se llame.
    $sql = "SELECT * FROM productos 
            WHERE (producto LIKE ? OR codproducto LIKE ?)
            LIMIT 50";

    $stmt = $db->prepare($sql);
    $term = "%$q%";
    $stmt->execute([$term, $term]);
    
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // DEBUG DE EMERGENCIA:
    // Si la tabla usa nombres raros, esto normaliza los datos para el frontend
    foreach ($resultados as &$prod) {
        // Normalizamos el ID
        $prod['id'] = $prod['codproducto'] ?? $prod['id'] ?? null;
        
        // Normalizamos el Nombre
        $prod['nombre'] = $prod['producto'] ?? $prod['nombre_producto'] ?? 'Sin Nombre';
        
        // Normalizamos el Precio (BUSCAMOS EL CULPABLE)
        // Probamos todas las variantes comunes en sistemas legacy
        $prod['precio'] = $prod['precioventa'] 
                       ?? $prod['preciopublico'] 
                       ?? $prod['precioxpublico'] 
                       ?? $prod['precio_publico'] 
                       ?? $prod['pvp'] 
                       ?? 0;
                       
        // Normalizamos el Stock
        $prod['stock'] = $prod['existencia'] ?? $prod['stock'] ?? $prod['cantidad'] ?? 0;
        
        // Image handling (keeping consistent with previous steps, though user code didn't explicitly include it, it's safer to keep or re-add if needed. 
        // User said "REEMPLAZO TOTAL" with the provided code. The provided code doesn't check filesystem manually but `SELECT *` might include 'foto' if it exists. 
        // However, the previous context established 'foto' column doesn't exist. 
        // But the user's provided code for this step DOES NOT include the filesystem check I wrote earlier. 
        // EXCEPT: The user's prompt code block *does not* have the image check logic I wrote.
        // It relies on SELECT * returning 'foto' OR the frontend handling missing 'foto'.
        // Frontend handles missing 'foto' with Initials.
        // I will stick strictly to the User's provided code as requested ("CÓDIGO A PEGAR (Reemplazo total)").
        // Wait, if I drop the filesystem check, images won't show up even if they exist on disk.
        // But the user asked for this specific code. I should follow instructions. 
        // Actually, looking at the code I'm pasting, it doesn't have the filesystem check.
        // I'll stick to the user's provided code. If images break, we can fix that next.
    }
    
    echo json_encode($resultados ?: []);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
