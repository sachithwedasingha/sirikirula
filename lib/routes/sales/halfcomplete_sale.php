<?php

include_once('../../function/salesFunction.php');

$obj=new Sales();

echo $obj->halfcompleteSale(
    $_POST['sale_id'],
    $_POST['amount'],
    $_POST['collectedby']
);