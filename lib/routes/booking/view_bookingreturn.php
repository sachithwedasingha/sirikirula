<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

$obj->viewBookingreturn($_GET['booking_id']);

?>