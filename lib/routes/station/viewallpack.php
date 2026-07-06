<?php

//include function page 
include_once('../../function/packageFunction.php');

//call the class and create an object 
$userObj = new Pack();

$result = $userObj -> proListview($_GET['type']);

echo($result);


?>