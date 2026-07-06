<?php

include_once('../../function/authFunction.php');

$obj = new Auth();

echo $obj->verifyPin($_POST['pin']);

?>