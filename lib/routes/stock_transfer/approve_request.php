<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->approveRequest(
    $_POST['request_id'],
    $_POST['approved_by'],
    $_POST['items']
);