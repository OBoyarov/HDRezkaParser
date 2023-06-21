<?php
class MySQL {

    protected string $db_host;
    protected string $db_name;
    protected string $db_user;
    protected string $db_pass;
    protected object $db_obj;

    function __construct() {
        $this->db_host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_obj = $this->createPDOConnect();
    }

    function createPDOConnect(): PDO {
        return new PDO("mysql:dbname=$this->db_name;host=".$this->db_host, $this->db_user, $this->db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    }

    function getData($query, $array = []):array {
        $stmt = $this->db_obj->prepare($query);
        $stmt->execute($array);
        if (isset($stmt) and $stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return array();
    }

    function changeData($query, $array = []):array {
        $stmt = $this->db_obj->prepare($query);
        $stmt->execute($array);
        return array();
    }

}