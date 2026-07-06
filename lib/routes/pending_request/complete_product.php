<?php

include_once('../../function/pendingRequestFunction.php');

$obj=new PendingRequest();

echo $obj->completeProduct(
    $_POST['product_id']
);