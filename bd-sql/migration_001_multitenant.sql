-- Migration 001: Multi-tenant Support
-- Date: 2025-11-27
-- Description: Adding tenant_id column to main tables for multi-tenancy support

-- Table: productos
ALTER TABLE `productos` 
ADD COLUMN `tenant_id` INT NOT NULL DEFAULT 0;

CREATE INDEX `idx_tenant_productos` ON `productos` (`tenant_id`);

-- Table: ventas
ALTER TABLE `ventas` 
ADD COLUMN `tenant_id` INT NOT NULL DEFAULT 0;

CREATE INDEX `idx_tenant_ventas` ON `ventas` (`tenant_id`);

-- Table: clientes
ALTER TABLE `clientes` 
ADD COLUMN `tenant_id` INT NOT NULL DEFAULT 0;

CREATE INDEX `idx_tenant_clientes` ON `clientes` (`tenant_id`);

-- Table: usuarios
ALTER TABLE `usuarios` 
ADD COLUMN `tenant_id` INT NOT NULL DEFAULT 0;

CREATE INDEX `idx_tenant_usuarios` ON `usuarios` (`tenant_id`);
