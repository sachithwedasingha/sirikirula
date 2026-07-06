<?php

include_once('../../function/supplierFunction.php');

$obj = new Supplier();

echo $obj->supplierdata($_GET['uid']);

?>