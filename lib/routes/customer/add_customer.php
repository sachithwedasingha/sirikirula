<?php

include_once('../../function/customerFunction.php');

$obj = new Customer();

echo $obj->addCustomer(
$_POST['customer_type'],
$_POST['customer_name'],
$_POST['phone'],
$_POST['nic'],
$_POST['address']
);

?>