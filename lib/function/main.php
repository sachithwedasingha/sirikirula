<?php
include_once('db_conn.php');

$config = require __DIR__ . '/../../config/database.php';

class Main {

    protected $dbResult;

    public function __construct() {

        // ⚠ DO NOT USE ROOT USER IN PRODUCTION
      $this->connObj = new Connection(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']
        );

        $this->dbResult = $this->connObj->Conn();
    }
}
?>
