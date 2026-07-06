<?php
// include function page(productFunction.php)

include_once('../../function/stationFunction.php');

$proObj = new Station();

$result = $proObj->delete_station($_POST['uid']);

echo($result);

?>