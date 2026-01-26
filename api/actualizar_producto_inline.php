<?php
/**
 * API: Actualizar Producto Inline
 * Endpoint para edición inline de productos desde la matriz inteligente
 */

session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

// Validación de sesión
if (!isset($_SESSION['acceso'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Sesión no iniciada']);
    exit;
}

// Solo administradores pueden editar
$rolesPermitidos = ['administradorG', 'administradorS', 'secretaria'];
if (!in_array($_SESSION['acceso'], $rolesPermitidos)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['field']) || !isset($input['value'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
        exit;
    }
    
    $id = intval($input['id']);
    $field = $input['field'];
    $value = $input['value'];
    
    // Mapeo de campos frontend -> backend
    $fieldMap = [
        'precio' => 'precioventa',
        'stock' => 'existencia'
    ];
    
    if (!isset($fieldMap[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Campo no permitido']);
        exit;
    }
    
    $dbField = $fieldMap[$field];
    
    // Validar valor
    if ($field === 'precio') {
        $value = floatval($value);
        if ($value < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Precio no puede ser negativo']);
            exit;
        }
    } elseif ($field === 'stock') {
        $value = intval($value);
    }
    
    // Actualizar en BD
    $db = DB::connect();
    $sql = "UPDATE productos SET $dbField = :value WHERE codproducto = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':value', $value);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'status' => 'success',
            'message' => 'Producto actualizado'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No se encontró el producto'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
