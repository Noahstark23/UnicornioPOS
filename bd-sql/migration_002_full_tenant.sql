-- Migration 002: Infraestructura Full Multi-Tenant
-- Fecha: 2026-01-26
-- Autor: Antigravity Agent
-- Descripción: Establece la tabla maestra de tenants y aisla todas las tablas críticas del sistema.

SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- 1. TABLA MAESTRA DE TENANTS
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `subdomain` VARCHAR(50) NOT NULL,
  `company_name` VARCHAR(255) NOT NULL,
  `status` ENUM('active','suspended','trial','cancelled') DEFAULT 'trial',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_subdomain` (`subdomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar Tenant por Defecto (Legacy) para mantener compatibilidad
-- Usamos INSERT IGNORE para evitar errores si se corre multiplas veces
INSERT IGNORE INTO `tenants` (`id`, `subdomain`, `company_name`, `status`) 
VALUES (1, 'legacy', 'Instalación Local (Legacy)', 'active');

-- --------------------------------------------------------
-- 2. INYECCIÓN DE AISLAMIENTO (tenant_id)
-- --------------------------------------------------------
-- Nota: Usamos DEFAULT 1 para que el código actual siga funcionando 
-- asumiendo que todo pertenece al tenant 1 (Legacy).

-- === MÓDULO VENTAS ===
ALTER TABLE `detalleventas` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_detalleventas` ON `detalleventas` (`tenant_id`);

ALTER TABLE `creditos` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_creditos` ON `creditos` (`tenant_id`);

ALTER TABLE `abonoscreditosventas` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_abonosventas` ON `abonoscreditosventas` (`tenant_id`);

-- === MÓDULO INVENTARIO Y COMPRAS ===
ALTER TABLE `compras` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_compras` ON `compras` (`tenant_id`);

ALTER TABLE `detallecompras` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_detallecompras` ON `detallecompras` (`tenant_id`);

ALTER TABLE `proveedores` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_proveedores` ON `proveedores` (`tenant_id`);

ALTER TABLE `traspasos` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_traspasos` ON `traspasos` (`tenant_id`);

-- Nota: detallestraspasos no estaba explícitamente en la lista "crítica" del usuario,
-- pero se recomienda agregarla en un paso de limpieza posterior si es necesario.

-- === MÓDULO CAJA Y DINERO ===
ALTER TABLE `cajas` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_cajas` ON `cajas` (`tenant_id`);

ALTER TABLE `arqueocaja` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_arqueocaja` ON `arqueocaja` (`tenant_id`);

ALTER TABLE `movimientos` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_movimientos` ON `movimientos` (`tenant_id`);

-- === MÓDULO CATÁLOGOS ===
ALTER TABLE `familias` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_familias` ON `familias` (`tenant_id`);

ALTER TABLE `subfamilias` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_subfamilias` ON `subfamilias` (`tenant_id`);

ALTER TABLE `marcas` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_marcas` ON `marcas` (`tenant_id`);

ALTER TABLE `modelos` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_modelos` ON `modelos` (`tenant_id`);

ALTER TABLE `presentaciones` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_presentaciones` ON `presentaciones` (`tenant_id`);

-- === MÓDULO CONFIGURACIÓN ===
ALTER TABLE `configuracion` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_configuracion` ON `configuracion` (`tenant_id`);

ALTER TABLE `impuestos` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_impuestos` ON `impuestos` (`tenant_id`);

ALTER TABLE `monedas` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_monedas` ON `monedas` (`tenant_id`);

ALTER TABLE `sucursales` ADD COLUMN `tenant_id` INT(11) NOT NULL DEFAULT 1;
CREATE INDEX `idx_tenant_sucursales` ON `sucursales` (`tenant_id`);

SET FOREIGN_KEY_CHECKS=1;
