<?php
/**
 * Middleware Multi-Tenant
 * Maneja el contexto del tenant actual basado en la sesión
 */

class TenantContext {
    private static $current_tenant_id = null;
    
    // Configura el tenant actual para esta solicitud
    public static function setTenantId($tenant_id) {
        self::$current_tenant_id = (int)$tenant_id;
    }
    
    // Obtiene el tenant actual. Si no está definido, retorna 1 (Legacy)
    // Esto asegura compatibilidad hacia atrás
    public static function getTenantId() {
        if (self::$current_tenant_id === null) {
            // Intenta obtener de la sesión si existe
            if (session_status() === PHP_SESSION_NONE) {
                // Evitar start session si no es necesario o ya fue cerrado
                // @session_start(); 
            }
            
            if (isset($_SESSION['tenant_id'])) {
                self::$current_tenant_id = (int)$_SESSION['tenant_id'];
            } else {
                // Fallback a Legacy (ID 1)
                self::$current_tenant_id = 1;
            }
        }
        return self::$current_tenant_id;
    }
    
    /**
     * Inyecta filtro de tenant en un WHERE clause SQL
     * Uso: $sql = TenantContext::addTenantFilter($sql);
     */
    public static function addTenantFilter($sql, $tableAlias = '') {
        $tenant_id = self::getTenantId();
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        
        // Evita inyectar si ya existe (prevención básica)
        if (strpos($sql, "{$prefix}tenant_id") !== false) {
            return $sql;
        }

        // Simple injection para WHERE existente
        if (stripos($sql, 'WHERE') !== false) {
            // Reemplaza el primer WHERE (case insensitive)
            $sql = preg_replace('/WHERE/i', "WHERE {$prefix}tenant_id = {$tenant_id} AND", $sql, 1);
        } else {
            // Si no hay WHERE (ej: SELECT * FROM productos)
            // Cuidado con ORDER BY, LIMIT, GROUP BY al final
            $append = " WHERE {$prefix}tenant_id = {$tenant_id}";
            
            // Detectar cláusulas finales para insertar antes
            $pattern = '/(GROUP BY|ORDER BY|LIMIT)/i';
            if (preg_match($pattern, $sql, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1];
                $sql = substr_replace($sql, $append . " ", $pos, 0);
            } else {
                $sql .= $append;
            }
        }
        
        return $sql;
    }
}
?>
