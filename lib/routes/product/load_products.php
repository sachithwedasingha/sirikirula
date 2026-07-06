<?php

include_once('../../function/productFunction.php');

$obj = new Product();

echo $obj->loadProducts($_GET['search'] ?? '');

?>