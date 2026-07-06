<?php

//include function page 
include_once('../../function/stationFunction.php');

//call the class and create an object 
$serObj = new Station();

$result = $serObj -> prodata($_GET['uid']);


echo($result);


?>