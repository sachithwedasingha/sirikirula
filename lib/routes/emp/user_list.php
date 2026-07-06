<?php

//include function page 
include_once('../../function/empFunction.php');

//call the class and create an object 
$userObj = new Employee();

$result = $userObj -> userList();

echo($result);


?>