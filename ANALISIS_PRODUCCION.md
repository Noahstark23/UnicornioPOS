# Reporte de Auditoría para Paso a Producción

Este documento detalla los hallazgos críticos y las "opciones faltantes" para que el proyecto pueda ser desplegado en producción de manera segura y mantenible.

## 1. Seguridad (Crítico)

*   **Credenciales Harcodeadas:**
    *   **Base de Datos:** `class/classconexion.php` contiene usuario `root` y contraseña vacía.
    *   **Email:** `class/class.php` contiene credenciales reales de Gmail (`elsaiya@gmail.com` y contraseña en texto plano).
    *   **Solución:** Mover todas las credenciales a variables de entorno (`.env`) o un archivo de configuración fuera del control de versiones (ej. `includes/db.php` está en gitignore pero no se usa en la clase).

*   **Protección CSRF (Cross-Site Request Forgery):**
    *   **Hallazgo:** Los formularios (ej. `index.php`, `configuracion.php`, `ventas.php`) no incluyen tokens anti-CSRF.
    *   **Riesgo:** Un atacante puede forzar acciones en nombre de un usuario logueado.
    *   **Solución:** Implementar generación y validación de tokens CSRF en todos los formularios `POST`.

*   **Vulnerabilidad XSS (Cross-Site Scripting):**
    *   **Hallazgo:** Se imprimen variables directamente en el HTML sin escapar (ej. `<?php echo $doc[$i]['documento'] ?>`).
    *   **Riesgo:** Inyección de scripts maliciosos si los datos en BD fueron comprometidos o ingresados maliciosamente.
    *   **Solución:** Usar `htmlspecialchars()` o funciones de escape equivalentes al imprimir datos.

*   **Hashing de Contraseñas Débil:**
    *   **Hallazgo:** Se usa `sha1(md5($password))`.
    *   **Riesgo:** Algoritmos obsoletos y vulnerables.
    *   **Solución:** Migrar a `password_hash()` (bcrypt/argon2) y `password_verify()` de PHP nativo.

*   **Archivos Peligrosos:**
    *   `backup.php` y `restore.php` en la raíz pública representan un riesgo crítico si no están estrictamente protegidos por autenticación y IP.

## 2. Arquitectura y Mantenimiento

*   **Gestión de Dependencias:**
    *   **Falta:** No existe `composer.json`.
    *   **Problema:** Librerías como `fpdf` están copiadas manualmente ("vendoring"). Dificulta actualizaciones de seguridad.
    *   **Solución:** Inicializar Composer y gestionar dependencias formalmente.

*   **Estructura del Proyecto:**
    *   **Estado:** "Spaghetti Code". Más de 100 archivos en la raíz mezclando lógica, vista y acceso a datos.
    *   **Solución:** Organizar en carpetas (`controllers/`, `views/`, `models/`, `public/`).

*   **Manejo de Errores:**
    *   **Estado:** `class/classconexion.php` usa `die()` mostrando errores de base de datos al usuario. `class/class.php` configura `ini_set` con valores peligrosos (`memory_limit -1`).
    *   **Solución:** Centralizar manejo de errores (como en `api/common.php`) y desactivar `display_errors` en producción.

## 3. Limpieza

*   **Archivos Basura:**
    *   Se detectaron numerosos archivos de debug que deben eliminarse: `debug_*.php`, `fix_*.php`, `block_*.txt`, `line_output.txt`.

## 4. Checklist para Producción

1.  [ ] Eliminar archivos `debug_*` y `fix_*`.
2.  [ ] Crear archivo de configuración externo para credenciales DB y SMTP.
3.  [ ] Implementar sistema de logs de errores (no mostrar en pantalla).
4.  [ ] Asegurar que `backup.php` no sea accesible públicamente.
5.  [ ] Configurar HTTPS (SSL) en el servidor web.
6.  [ ] Cambiar contraseñas de correos y base de datos que fueron expuestas en el código.

---
**Nota:** El archivo `api/common.php` muestra buenas prácticas modernas que deberían replicarse en el resto del sistema legacy.
