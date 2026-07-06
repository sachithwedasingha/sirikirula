<?php

include_once('../../function/pendingRequestFunction.php');

$obj=new PendingRequest();

echo $obj->completeRequest(
    $_POST['id']
);