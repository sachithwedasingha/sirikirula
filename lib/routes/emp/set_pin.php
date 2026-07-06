<?php

//include function page 
include_once('../../function/empFunction.php');

//call the class and create an object 
$userObj = new Employee();

echo $userObj->setPin(
  $_POST['uid'],
  $_POST['pin']
);

?>