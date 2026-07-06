<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

$obj->viewBookingrentout($_GET['booking_id']);

?>