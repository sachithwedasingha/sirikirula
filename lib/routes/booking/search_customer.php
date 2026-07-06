<?php

include_once('../../function/bookingFunction.php');

$obj = new Booking();

$search = $_GET['search'] ?? '';

$obj->searchCustomer($search);

?>