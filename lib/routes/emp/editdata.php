<?php

//include function page 
include_once('../../function/empFunction.php');

//call the class and create an object 
$serObj = new Employee();

$result = $serObj -> editdata($_POST['idCustomer'],$_POST['Customer_fname'],$_POST['Customer_lname'],
$_POST['Customer_telnum'],$_POST['Customer_email'],$_POST['Customer_birthday'],$_POST['Customer_address'], $_POST['gender'],
$_POST['userType'], $_POST['location'], $_POST['Customer_nic']);


echo($result);


?>