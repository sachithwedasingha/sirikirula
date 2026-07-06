<?php

//include function page 
include_once('../../function/stationFunction.php');

//call the class and create an object 
$userObj = new Station();

$result = $userObj -> proList();

echo($result);


?>