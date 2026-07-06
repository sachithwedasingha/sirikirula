<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

$obj->searchProducts(
$_GET['search'],
$_GET['booking_date'],
$_GET['return_date']
);

?>