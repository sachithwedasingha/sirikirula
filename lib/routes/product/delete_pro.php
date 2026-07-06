<?php

include_once('../../function/productFunction.php');

$obj = new Product();

echo $obj->delete_product($_POST['uid']);

?>