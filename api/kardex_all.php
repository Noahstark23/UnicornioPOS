<?php
require_once __DIR__ . '/../includes/db.php'; // Fix path relative to this file
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $db = DB::connect();
    $action = $_GET['action'] ?? 'list';

    if ($action === 'list') {
        // --- MODO LISTADO Y KPIS ---
        
        $q = $_GET['q'] ?? '';

        // 1. KPIS GLOBAL (Sobre todo el inventario activo)
        // Valorizado = Stock * Precio Compra
        $kpis = [
            'valor_total' => 0,
            'total_items' => 0,
            'items_bajo_stock' => 0
        ];

        // Total Valorizado y Conteo
        // Usamos preciocompra para costo.
        $stmt = $db->query("SELECT 
            COUNT(*) as total_items,
            SUM(existencia * preciocompra) as valor_total,
            SUM(CASE WHEN existencia <= stockminimo THEN 1 ELSE 0 END) as bajo_stock
            FROM productos");
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $kpis['valor_total'] = $row['valor_total'] ?? 0;
        $kpis['total_items'] = $row['total_items'] ?? 0;
        $kpis['items_bajo_stock'] = $row['bajo_stock'] ?? 0;


        // 2. LISTADO VALORIZADO
        $term = "%$q%";
        // NOTE: Column is 'precioxpublico', not 'precioventa'.
        $sql = "SELECT 
                codproducto,
                producto,
                codigobarra,
                existencia as stock,
                preciocompra as costo,
                precioxpublico as precio,  
                (existencia * preciocompra) as total_costo
                FROM productos 
                WHERE producto LIKE ? OR codproducto LIKE ? OR codigobarra LIKE ?
                ORDER BY existencia ASC 
                LIMIT 50";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$term, $term, $term]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'kpis' => $kpis,
            'data' => $productos
        ]);

    } elseif ($action === 'history') {
        // --- MODO HISTORIAL (KARDEX FISICO) ---
        $codproducto = $_GET['codproducto'] ?? '';
        
        if (!$codproducto) {
            echo json_encode([]);
            exit;
        }

        // Buscamos en tabla 'kardex' si existe
        try {
            // Intento consultar tabla dedicada con alias para el frontend
            $sql = "SELECT 
                    fechakardex,
                    movimiento,
                    documento as coddocumento,
                    (entradas + salidas) as cantidad,
                    stockactual as saldoactual
                    FROM kardex 
                    WHERE codproducto = ? 
                    ORDER BY fechakardex DESC 
                    LIMIT 50";
            $stmt = $db->prepare($sql);
            $stmt->execute([$codproducto]);
            $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['source' => 'kardex', 'data' => $movimientos]);

        } catch (Exception $e) {
            echo json_encode(['source' => 'error', 'data' => [], 'message' => 'No tabla kardex']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
