<?php
include_once('../../function/stockTransferFunction.php');
$obj=new StockTransfer();
echo $obj->loadAvailableProducts($_GET['station_id']);