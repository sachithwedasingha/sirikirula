<?php

include_once('../../function/incomeFunction.php');

$obj=new Income();

echo $obj->loadIncome(
    $_GET['single_date'] ?? '',
    $_GET['from_date'] ?? '',
    $_GET['to_date'] ?? '',
    $_GET['customer_id'] ?? '',
    $_GET['income_type'] ?? '',
    $_GET['reference_id'] ?? ''
);