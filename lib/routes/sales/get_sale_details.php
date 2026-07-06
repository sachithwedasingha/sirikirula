<?php

include_once('../../function/salesFunction.php');

$obj=new Sales();

echo $obj->getSaleDetails(
    $_GET['sale_id']
);