<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->getRentalSummary($_GET['booking_id']);

?>