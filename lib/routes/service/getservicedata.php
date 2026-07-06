<?php

include_once('../../function/serviceFunction.php');

$obj = new Service();

echo $obj->serviceData($_GET['uid']);

?>