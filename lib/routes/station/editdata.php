<?php
// Include the function page
include_once('../../function/stationFunction.php');

$serObj = new Station();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $id = $_POST['id'];
    $productName = $_POST['name'];
    $discription = $_POST['discription'];

    // Call the function to add the package
    $result = $serObj ->  editPackage($id, $productName, $discription,$_POST['address'],$_POST['contact_no']);

    echo $result;
}
?>