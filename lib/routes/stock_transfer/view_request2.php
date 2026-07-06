<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->viewTransferRequest(
    $_GET['id']
);