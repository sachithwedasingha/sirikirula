<?php

include_once('../../function/supplierFunction.php');

$obj = new Supplier();

echo $obj->addsupplier(
$_POST['name'],
$_POST['address'],
$_POST['phone'],
$_POST['email']
);

?>