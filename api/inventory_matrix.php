<?php
/**
 * API: Inventory Matrix (Matriz Inteligente de Productos)
 * Endpoint dedicado para gestión masiva de inventario sin afectar módulos express
 * 
 * GET:  Devuelve todos los productos con campos clave para la matriz
 * POST: Permite editar precio_venta o existencia al vuelo (inline editing)
 */

session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

// ========== VALIDACIÓN DE PERMISOS ==========
if (!isset($_SESSION['acceso'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Sesión no iniciada. Acceso denegado.']);
    exit;
}

$rolesPermitidos = ['administradorG', 'administradorS', 'secretaria'];
if (!in_array($_SESSION['acceso'], $rolesPermitidos)) {
    http_response_code(403);
    echo json_encode(['error' => 'Permiso denegado. Solo administradores pueden gestionar inventario.']);
    exit;
}

// ========== MANEJO DE ACCIONES ==========
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    $db = DB::connect();
    
    if ($metodo === 'GET') {
        // ========== ENDPOINT GET: Listar Productos ==========
        
        // Parámetros opcionales de filtrado
        $busqueda = $_GET['q'] ?? '';
        $limite = isset($_GET['limit']) ? (int)$_GET['limit'] : 1000;
        
        $sql = "SELECT 
                    codproducto as id,
                    codigo,
                    producto as nombre,
                    precioventa as precio_venta,
                    existencia,
                    foto,
                    codfamilia,
                    codsucursal,
                    tenant_id
                FROM productos 
                WHERE tenant_id = 1";
        
        if ($busqueda !== '') {
            $sql .= " AND (codigo LIKE :busqueda OR producto LIKE :busqueda)";
        }
        
        $sql .= " ORDER BY producto ASC LIMIT :limite";
        
        $stmt = $db->prepare($sql);
        
        if ($busqueda !== '') {
            $stmt->bindValue(':busqueda', "%$busqueda%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos para la matriz
        $productosFormateados = array_map(function($p) {
            return [
                'id' => $p['id'],
                'codigo' => $p['codigo'] ?? 'N/A',
                'nombre' => $p['nombre'],
                'precio_venta' => floatval($p['precio_venta'] ?? 0),
                'existencia' => intval($p['existencia'] ?? 0),
                'foto' => $p['foto'] ?: 'fotos/productos/default.png'
            ];
        }, $productos);
        
        echo json_encode([
            'success' => true,
            'total' => count($productosFormateados),
            'data' => $productosFormateados
        ]);
        
    } elseif ($metodo === 'POST') {
        // ========== ENDPOINT POST: Actualizar Campo Específico ==========
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id']) || !isset($input['campo']) || !isset($input['valor'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan parámetros: id, campo, valor']);
            exit;
        }
        
        $id = intval($input['id']);
        $campo = $input['campo'];
        $valor = $input['valor'];
        
        // Validar campos permitidos (whitelist de seguridad)
        $camposPermitidos = ['precio_venta' => 'precioventa', 'existencia' => 'existencia'];
        
        if (!isset($camposPermitidos[$campo])) {
            http_response_code(400);
            echo json_encode(['error' => "Campo no permitido. Solo se puede editar: " . implode(', ', array_keys($camposPermitidos))]);
            exit;
        }
        
        $campoReal = $camposPermitidos[$campo];
        
        // Validar tipo de dato según el campo
        if ($campo === 'precio_venta') {
            $valor = floatval($valor);
            if ($valor < 0) {
                http_response_code(400);
                echo json_encode(['error' => 'El precio no puede ser negativo']);
                exit;
            }
        } elseif ($campo === 'existencia') {
            $valor = intval($valor);
        }
        
        // Actualizar BD
        $sql = "UPDATE productos SET $campoReal = :valor WHERE codproducto = :id AND tenant_id = 1";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Producto actualizado correctamente',
                'id' => $id,
                'campo' => $campo,
                'nuevo_valor' => $valor
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró el producto o no hubo cambios'
            ]);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido. Solo GET y POST']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error inesperado: ' . $e->getMessage()]);
}
?>
