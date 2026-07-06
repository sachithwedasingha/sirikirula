<?php

include_once('../../function/salesFunction.php');

$obj=new Sales();

echo $obj->loadpendingSales(
    $_GET['from_date'],
    $_GET['customer_id'],
    $_GET['sale_id']
);

?>