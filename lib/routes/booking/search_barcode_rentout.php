<?php

include_once('../../function/bookingFunction.php');

$obj=new Booking();

echo $obj->searchBarcoderentout($_GET['barcode']);

?>