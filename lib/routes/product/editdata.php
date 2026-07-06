<?php

include_once('../../function/productFunction.php');

$obj = new Product();

echo $obj->editProduct(
$_POST['id'],
$_POST['supplier'],
$_POST['category'],
$_POST['product_name'],
$_POST['product_details'],
$_POST['product_code'],
$_POST['unit_price'],
$_POST['retail_price'],
$_FILES['product_image']
);

?>