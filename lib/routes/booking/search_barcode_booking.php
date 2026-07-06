<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->searchBarcodeBooking($_GET['barcode']);

?>