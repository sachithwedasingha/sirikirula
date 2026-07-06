<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

$obj->viewBooking($_GET['booking_id']);

?>