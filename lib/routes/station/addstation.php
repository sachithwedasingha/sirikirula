<?php
// Include the function page
include_once('../../function/stationFunction.php');

$serObj = new Station();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $result = $serObj ->  addstation($_POST['name'], $_POST['discription'],$_POST['address'],$_POST['contact_no']);

    echo $result;
}
?>
