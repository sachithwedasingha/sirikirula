<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->loadRequests(
    $_GET['status'] ?? '',
    $_GET['request_no'] ?? ''
);