<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

echo $obj->rentoutBooking(
    $_POST['booking_id'],
    $_POST['handoverby'],
    $_POST['balance_payment'],
    $_POST['booking_balance']
);

?>