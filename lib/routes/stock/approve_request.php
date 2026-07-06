<?php

include_once('../../function/stockFunction.php');

$obj = new Stock();

echo $obj->approveRequest($_POST['request_id']);

?>