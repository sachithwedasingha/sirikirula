<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->returnBooking(
$_POST['booking_id'],
$_POST['handoverby'],
$_POST['return_note'],
$_POST['claim_amount']
);

?>