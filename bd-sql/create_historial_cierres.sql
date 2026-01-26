CREATE TABLE IF NOT EXISTS historial_cierres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL DEFAULT 1,
    codarqueo INT,
    codresponsable INT,
    nombre_cajero VARCHAR(100),
    fecha_cierre DATETIME,
    diferencia DECIMAL(12,2),
    tipo_diferencia ENUM('SOBRA', 'FALTA', 'EXACTO'),
    comentarios TEXT,
    billetaje JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (tenant_id),
    INDEX (codresponsable)
);
