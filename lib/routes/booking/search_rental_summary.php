<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->searchRentalSummary(
$_GET['barcode'] ?? '',
$_GET['booking_date'] ?? '',
$_GET['customer_id'] ?? ''
);

?>