<?php

include_once('../../function/productFunction.php');

$obj=new Product();

echo $obj->checkProductAvailability(
    $_POST['product_id'],
    $_POST['qty']
);