<?php
// used to get mysql database connection
class Database{

    private $db_host = "localhost";
    private $db_name = "ruts";
    private $db_user = "root";
    private $db_password = "";
    private $conn;

    public function getConnection(){

        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
            $this->conn->exec("SET NAMES utf8");
        }catch(PDOException $exception){
            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>