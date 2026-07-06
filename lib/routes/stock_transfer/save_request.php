<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->saveRequest(
    $_POST['station_id'],
    $_POST['products'],
    $_POST['createdby']
);