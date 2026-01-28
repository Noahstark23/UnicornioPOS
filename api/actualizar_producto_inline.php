<?php
require_once 'common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['field']) || !isset($input['value'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$id = $input['id'];
$field = $input['field'];
$value = $input['value'];

// Map frontend fields to DB columns
$fieldMap = [
    'precio' => 'precioxpublico',
    'stock' => 'existencia'
];

if (!isset($fieldMap[$field])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid field']);
    exit;
}

$dbColumn = $fieldMap[$field];

try {
    $db = DB::connect();
    // Using tenant_id = 1 as per listar_productos.php pattern
    $sql = "UPDATE productos SET $dbColumn = ? WHERE codproducto = ? AND tenant_id = 1";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([$value, $id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Update failed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
