<?php

include_once('../../function/pendingRequestFunction.php');

$obj = new PendingRequest();

$obj->viewPendingRequest($_GET['id']);

?>