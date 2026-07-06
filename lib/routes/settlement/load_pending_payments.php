<?php

include_once('../../function/settlementFunction.php');

$obj=new Settlement();

echo $obj->loadPendingPayments(
    $_GET['customer_id']
);