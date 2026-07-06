<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->getPenaltyDetails(
    $_GET['booking_id']
);