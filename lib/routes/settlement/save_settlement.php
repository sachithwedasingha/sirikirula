<?php

include_once('../../function/settlementFunction.php');

$obj=new Settlement();

echo $obj->saveSettlement(
    $_POST['type'],
    $_POST['reference_id'],
    $_POST['amount'],
    $_POST['payment_method'],
    $_POST['received_by']
);