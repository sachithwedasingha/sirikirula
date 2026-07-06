<?php

include_once('../../function/salesFunction.php');

$obj=new Sales();

echo $obj->loadSales(
    $_GET['from_date'],
    $_GET['to_date'],
    $_GET['customer_id'],
    $_GET['sale_id']
);

?>