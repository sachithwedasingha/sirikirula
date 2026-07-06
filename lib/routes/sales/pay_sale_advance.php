<?php

include_once('../../function/salesFunction.php');

$obj=new Sales();

echo $obj->paySaleAdvance(
    $_POST['sale_id'],
    $_POST['amount'],
    $_POST['payedby']
);