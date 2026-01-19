<?php
// Cargar configuración centralizada
require_once __DIR__ . '/../includes/config.php';

class Db{
	
	private $dbHost;
    private $dbUsername;
    private $dbPassword;
    private $dbName;
	protected $p; 
	protected $dbh; 
	
    public function __construct(){
		// Consumir variables de config.php
		$this->dbHost     = DB_HOST;
		$this->dbUsername = DB_USER;
		$this->dbPassword = DB_PASS;
		$this->dbName     = DB_NAME;
		
        if(!isset($this->dbh)){
            // Connect to the database
            try{
	
	            date_default_timezone_set(APP_TIMEZONE);
                setlocale(LC_ALL, APP_LOCALE, "es_NI", "esp");
	
                $conn = new PDO("mysql:host=".$this->dbHost.";dbname=".$this->dbName, $this->dbUsername, $this->dbPassword,
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