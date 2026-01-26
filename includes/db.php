<?php
class UnicornDB {
    private static $instance = null;
    private $conn;
    private $host;
    private $db;
    private $user;
    private $pass;

    private function __construct() {
        try {
            // Configuración Híbrida: Cloud (Env) vs Local (XAMPP)
            $this->host = getenv('DB_HOST') ?: 'localhost';
            $this->db   = getenv('DB_NAME') ?: 'unicornio';
            $this->user = getenv('DB_USER') ?: 'root';
            $this->pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
            // 1. Configurar Timezone de PHP
            date_default_timezone_set('America/Managua');

            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            // 2. Sincronizar Timezone de MySQL con el de PHP
            $offset = date('P'); // Ej: -06:00
            $this->conn->exec("SET time_zone = '$offset';");
            $this->conn->exec("SET lc_time_names = 'es_ES';"); // Opcional: Para nombres de días/meses en español
            
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error crítico de conexión: ' . $e->getMessage()]);
            exit;
        }
    }
    public static function connect() {
        if (!self::$instance) self::$instance = new UnicornDB();
        return self::$instance->conn;
    }
}
class_alias('UnicornDB', 'DB');
