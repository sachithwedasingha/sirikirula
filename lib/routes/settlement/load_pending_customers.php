<?php

include_once('../../function/settlementFunction.php');

$obj=new Settlement();

echo $obj->loadPendingCustomers(
    $_GET['search'] ?? ''
);