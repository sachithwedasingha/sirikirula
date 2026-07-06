<?php

include_once('../../function/stockTransferFunction.php');

$obj=new StockTransfer();

echo $obj->viewRequest($_GET['id']);