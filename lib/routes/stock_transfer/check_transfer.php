<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->checkTransfer(
    $_POST['request_id'],
    $_POST['items']
);