<?php

include_once('../../function/customerFunction.php');

$obj=new Customer();

echo $obj->loadCustomersTable(
    isset($_GET['search']) ? $_GET['search'] : ''
);

?>