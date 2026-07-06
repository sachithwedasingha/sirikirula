<?php

include_once('../../function/serviceFunction.php');

$obj = new Service();

echo $obj->deleteService($_POST['uid']);

?>