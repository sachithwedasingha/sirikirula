<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->readyBooking($_POST['booking_id'],$_POST['readyby']);

?>