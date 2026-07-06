<?php

include_once('../../function/salesFunction.php');

$obj = new Sales();

echo $obj->saveSale(
    $_POST['sale_date'],
    $_POST['customer_id'],
    $_POST['createdby'],
    $_POST['products'],
    $_POST['services'],
    $_POST['collection_type'],
    $_POST['payment_method'],
    $_POST['advance_amount'],
    $_POST['balance_amount'],
    $_POST['create_all_new_items']
);

?>