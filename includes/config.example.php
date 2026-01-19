<?php
/**
 * PLANTILLA DE CONFIGURACIÓN - Unicornio POS
 * 
 * INSTRUCCIONES:
 * 1. Copie este archivo como "config.php" en el mismo directorio
 * 2. Modifique los valores según su entorno local/producción
 * 3. NUNCA suba config.php a GitHub (debe estar en .gitignore)
 */

// ========== CONFIGURACIÓN DE BASE DE DATOS ==========
define('DB_HOST', 'localhost');           // Servidor MySQL
define('DB_USER', 'root');                // Usuario de BD
define('DB_PASS', '');                    // Contraseña de BD
define('DB_NAME', 'unicornio');           // Nombre de la base de datos

// ========== CONFIGURACIÓN DE TIMEZONE ==========
define('APP_TIMEZONE', 'America/Managua'); // Zona horaria
define('APP_LOCALE', 'es_NI.UTF-8');       // Locale del sistema

// ========== CONFIGURACIÓN DE SESIÓN ==========
define('SESSION_LIFETIME', 3600);          // Duración de sesión (segundos)

// ========== INFORMACIÓN DE INSTALADOR (MARCA COMERCIAL) ==========
define('INSTALLER_COMPANY', 'Tu Empresa Tech');
define('INSTALLER_PHONE', '8888-8888');
define('INSTALLER_EMAIL', 'ventas@tuempresa.com');
define('INSTALLER_WEBSITE', 'www.tuempresa.com');
?>
