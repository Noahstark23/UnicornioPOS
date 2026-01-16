# Informe de Preparación para Producción

Este documento detalla el análisis del código y las acciones realizadas para preparar el sistema para producción, así como los pasos restantes que debe realizar el administrador.

## 🛠 Acciones Realizadas

1.  **Seguridad de Credenciales de Base de Datos:**
    *   Se eliminaron las credenciales "hardcoded" (quemadas en código) de `class/classconexion.php` y `backup.php`.
    *   Se implementó un sistema de carga de configuración centralizado.
    *   Se creó la plantilla `includes/config.example.php`.

2.  **Limpieza de Archivos:**
    *   Se eliminaron scripts de depuración y pruebas (`debug_*.php`, `fix_*.php`, `block_*.txt`, etc.) que no deben estar en un entorno productivo.

3.  **Estandarización:**
    *   `backup.php` ahora utiliza las mismas constantes de configuración que el resto del sistema (si se configura `includes/config.php`).

## 🚨 Pasos Críticos Pendientes (Lo que falta)

Para lanzar a producción, **debe** realizar las siguientes acciones:

1.  **Configuración del Servidor:**
    *   Copie el archivo `includes/config.example.php` a `includes/config.php`.
    *   Edite `includes/config.php` y coloque las credenciales reales de su base de datos de producción.
    *   **Importante:** Verifique si el nombre de su base de datos es `unicornio` (usado en `classconexion.php`) o `softventas` (usado en `backup.php`). Defina el correcto en `DB_NAME`.

2.  **Permisos de Archivos:**
    *   Asegúrese de que `includes/config.php` tenga permisos restrictivos (ej. `640` o `600`) para que otros usuarios del servidor no puedan leerlo.
    *   Asegúrese de que los directorios `assets/` y `fotos/` tengan permisos de escritura si el sistema permite subir archivos, pero **sin** permisos de ejecución de scripts.

3.  **Seguridad Adicional (Recomendada):**
    *   **HTTPS:** Configure su servidor web (Apache/Nginx) para forzar el uso de HTTPS.
    *   **Password Change:** El archivo `password.php` actualmente envía la contraseña actual en un campo oculto (`<input type="hidden">`). Esto es inseguro. Se recomienda refactorizar para solicitar la contraseña actual al usuario explícitamente en el formulario en lugar de precargarla.
    *   **Session Management:** Verifique que `php.ini` tenga `session.cookie_httponly = 1` y `session.cookie_secure = 1` (si usa HTTPS).

4.  **Base de Datos:**
    *   Asegúrese de que la estructura de la base de datos coincida con lo esperado. Se detectaron discrepancias en los nombres de la BD en el código legado.

5.  **Pendientes en Código (TODOs):**
    *   En `api/caja_control.php`, hay una nota: `TODO: Agregar column formapago a abonoscreditosventas`. Verifique si esta funcionalidad es requerida para el cierre de caja correcto.

## 📋 Archivos Clave Modificados

*   `class/classconexion.php`: Ahora busca `includes/config.php`.
*   `backup.php`: Ahora busca `includes/config.php`.
*   `includes/config.example.php`: Nueva plantilla de configuración.
