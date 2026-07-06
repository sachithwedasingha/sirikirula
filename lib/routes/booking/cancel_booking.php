<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->cancelBooking(
$_POST['booking_id'],
$_POST['cancelby']
);

?>