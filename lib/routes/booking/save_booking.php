<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

echo $obj->saveBooking(
    $_POST['booking_date'],
    $_POST['return_date'],
    $_POST['customer_id'],

    $_POST['collection_type'],
    $_POST['other_customer_name'],
    $_POST['other_customer_phone'],
    $_POST['other_customer_nic'],

    $_POST['booking_amount'],
    $_POST['advance_amount'],
    $_POST['balance_amount'],
    $_POST['payment_method'],

    $_POST['hold_amount'],
    $_POST['paid_amount'],
    $_POST['hold_amount_type'],
    $_POST['bank_details'],

    $_POST['remarks'],

    $_POST['createdby'],

    json_decode($_POST['products'], true)
);