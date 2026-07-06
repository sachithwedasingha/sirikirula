<?php
class Connection {

    private $server;
    private $user;
    private $password;
    private $database;

    public function __construct($server, $user, $password, $database) {
        $this->server   = $server;
        $this->user     = $user;
        $this->password = $password;
        $this->database = $database;
    }

    public function Conn() {

        $conn = new mysqli($this->server, $this->user, $this->password, $this->database);

        if ($conn->connect_errno) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Always use UTF-8
        $conn->set_charset("utf8mb4");

        return $conn;
    }
}
?>
