<?php
class Db{
		
	private static $instance = null;
	private $dbHost     = "localhost";
    private $dbUsername = "root";
    private $dbPassword = "";
    private $dbName     = "unicornio";
	protected $p; 
	protected $dbh; 
	
    private function __construct(){
        // Connect to the database
        try{
            date_default_timezone_set('America/Caracas');
            setlocale(LC_ALL,"es_VE.UTF-8","es_VE","esp");

            $conn = new PDO("mysql:host=".$this->dbHost.";dbname=".$this->dbName, $this->dbUsername, $this->dbPassword,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbh = $conn;
        }catch(PDOException $e){
            die("Failed to connect with MySQL: " . $e->getMessage());
        }
    }

    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new Db();
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->dbh;
    }
	
	public function SetNames()
	{
		return $this->dbh->query("SET NAMES 'utf8'");
	}

###### FIN DE CLASE #####	

}	
?>