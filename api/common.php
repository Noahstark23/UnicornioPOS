<?php
// api/common.php - Bootstrap para todas las APIs
// Mantiene seguridad, configuración y headers centralizados

// 1. Configuración de Errores (Production Safe)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. Headers Standard
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); // Ajustar según necesidad en producción
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// 3. Iniciar Sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Verificación de Seguridad (Middleware)
function verificar_acceso_api() {
    // Si no hay usuario en sesión, denegar acceso
    // Se permiten excepciones si definimos una constante API_PUBLIC antes de incluir este archivo
    if (!defined('API_PUBLIC') && empty($_SESSION['id_usuario'])) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Acceso no autorizado. Inicie sesión.'
        ]);
        exit;
    }
}

// Ejecutar verificación por defecto
verificar_acceso_api();

// 4.1 Inicializar Contexto Multi-Tenant
require_once __DIR__ . '/../includes/tenant_middleware.php';
// Si hay sesión, el middleware tomará el ID de ahí. 
// Si es API key (futuro), aquí se setearía.
TenantContext::getTenantId(); // Inicializa singleton

// 5. Incluir Base de Datos
// Ajustar ruta relativa automáticamente
$paths = [
    __DIR__ . '/../includes/db.php',
    __DIR__ . '/includes/db.php'
];

$dbLoaded = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $dbLoaded = true;
        break;
    }
}

if (!$dbLoaded) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error de configuración: DB no encontrada']);
    exit;
}
?>
