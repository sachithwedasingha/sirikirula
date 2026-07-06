<?php

include_once('../../function/productFunction.php');

$obj = new Product();

echo $obj->productdata($_GET['uid']);

?>