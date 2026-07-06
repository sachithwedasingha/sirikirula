<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

echo $obj->checkProductQty(
$_POST['product_id'],
$_POST['qty'],
$_POST['booking_date'],
$_POST['return_date']
);

?>