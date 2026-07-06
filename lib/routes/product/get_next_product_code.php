<?php

include_once('../../function/productFunction.php');

$obj=new Product();

echo $obj->getNextProductCode(
    $_GET['category_id']
);

?>