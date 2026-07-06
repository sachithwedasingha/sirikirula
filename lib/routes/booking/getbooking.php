<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

echo $obj->getBooking($_GET['booking_id']);

?>