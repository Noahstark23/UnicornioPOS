<?php

/**
 * Class TenantContext
 *
 * Middleware to handle tenant detection and context storage.
 * Implements a Singleton pattern to ensure a consistent tenant ID throughout the request lifecycle.
 */
class TenantContext {
    /**
     * @var TenantContext|null The single instance of the class.
     */
    private static $instance = null;

    /**
     * @var int The detected tenant ID.
     */
    private $tenantId;

    /**
     * Private constructor to prevent direct instantiation.
     * Detects and sets the tenant ID upon initialization.
     */
    private function __construct() {
        // Detect tenant from session
        // If $_SESSION['tenant_id'] is present and not empty, use it.
        // Otherwise, force tenant_id = 1 (Legacy/Local mode).
        if (isset($_SESSION['tenant_id']) && !empty($_SESSION['tenant_id'])) {
            $this->tenantId = (int) $_SESSION['tenant_id'];
        } else {
            $this->tenantId = 1;
        }
    }

    /**
     * Get the singleton instance of the TenantContext.
     *
     * @return TenantContext
     */
    private static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the current secure Tenant ID.
     *
     * This is the main access point for other parts of the application
     * to know "who is the current user/tenant".
     *
     * @return int
     */
    public static function getTenantId() {
        return self::getInstance()->tenantId;
    }
}
