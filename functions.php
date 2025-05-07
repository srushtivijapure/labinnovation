<?php 
class class_functions {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "GATEPASS");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConn() {
        return $this->conn;
    }
}
?>