<?php

include_once('../../function/supplierFunction.php');

$obj = new Supplier();

echo $obj->editSupplier(
$_POST['id'],
$_POST['name'],
$_POST['address'],
$_POST['phone'],
$_POST['email']
);

?>