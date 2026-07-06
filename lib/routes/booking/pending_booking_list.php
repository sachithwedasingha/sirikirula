<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

$obj->pendingBookingList($_GET['date']);

?>