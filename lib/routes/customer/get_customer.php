<?php

include_once('../../function/customerFunction.php');

$obj=new Customer();

echo $obj->getCustomer(
    $_GET['customer_id']
);

?>