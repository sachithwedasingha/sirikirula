<?php
include_once('db_conn.php');

class Main {

    protected $dbResult;

    public function __construct() {

        // ⚠ DO NOT USE ROOT USER IN PRODUCTION
        $this->connObj = new Connection(
            "localhost",
            "root",       // create a dedicated user
            "sachith@123", // give permission only to one database
            "db_sirikirula"
        );

        $this->dbResult = $this->connObj->Conn();
    }
}
?>
