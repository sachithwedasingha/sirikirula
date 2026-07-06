<?php

include_once('../../function/pendingRequestFunction.php');

$obj = new PendingRequest();

$obj->pendingRequestList($_GET['group_by'] ?? 'REFERENCE');

?>