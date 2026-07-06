<?php

include_once('../../function/supplierFunction.php');

$obj = new Supplier();

echo $obj->delete_supplier($_POST['uid']);

?>