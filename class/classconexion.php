<?php
class Db{
		
	private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "unicornio";
	protected $p; 
	protected $dbh; 
	
    public function __construct(){
        if(!isset($this->dbh)){
            // Connect to the database
            try{
	
	            date_default_timezone_set('America/Caracas');
                setlocale(LC_ALL,"es_VE.UTF-8","es_VE","esp");

                // Load configuration if available
                $configPath = __DIR__ . '/../includes/config.php';
                if (file_exists($configPath)) {
                    require_once($configPath);
                }

                $host = defined('DB_HOST') ? DB_HOST : $this->dbHost;
                $user = defined('DB_USER') ? DB_USER : $this->dbUsername;
                $pass = defined('DB_PASS') ? DB_PASS : $this->dbPassword;
                $name = defined('DB_NAME') ? DB_NAME : $this->dbName;
	
                $conn = new PDO("mysql:host=".$host.";dbname=".$name, $user, $pass,
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->dbh = $conn;
            }catch(PDOException $e){
                die("Failed to connect with MySQL: " . $e->getMessage());
            }
        }
    }
	
		public function SetNames()
	{
		return $this->dbh->query("SET NAMES 'utf8'");
	}

###### FIN DE CLASE #####	

}	
?>