<?php

include_once('../../function/stockFunction.php');

$obj = new Stock();

echo $obj->addMultipleStock(
json_decode($_POST['products'],true)
);

?>