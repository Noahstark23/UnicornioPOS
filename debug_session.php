<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

echo "=== DEBUG: Variables de Sesión ===\n\n";

if (empty($_SESSION)) {
    echo "⚠️ LA SESIÓN ESTÁ VACÍA\n";
    echo "El usuario no ha iniciado sesión o la sesión expiró.\n\n";
} else {
    echo "✅ SESIÓN ACTIVA\n\n";
    echo "Variables disponibles:\n";
    foreach ($_SESSION as $key => $value) {
        if (is_array($value) || is_object($value)) {
            echo "  • $key = " . json_encode($value) . "\n";
        } else {
            echo "  • $key = $value\n";
        }
    }
}

echo "\n=== Datos para Cajero ===\n";
echo "nombre_usuario: " . ($_SESSION['nombre_usuario'] ?? 'NO EXISTE') . "\n";
echo "usuario: " . ($_SESSION['usuario'] ?? 'NO EXISTE') . "\n";
echo "nombre: " . ($_SESSION['nombre'] ?? 'NO EXISTE') . "\n";
echo "acceso: " . ($_SESSION['acceso'] ?? 'NO EXISTE') . "\n";
echo "id_usuario: " . ($_SESSION['id_usuario'] ?? 'NO EXISTE') . "\n";

echo "\n=== Nombre Seleccionado (Lógica Aplicada) ===\n";
$nombreCajero = $_SESSION['nombre_usuario'] ?? $_SESSION['usuario'] ?? $_SESSION['nombre'] ?? 'Cajero';
echo "Nombre que se usará: $nombreCajero\n";
?>
