<?php
// include function page(userFunction.php)

include_once('../../function/empFunction.php');

$userObj = new Employee();

$result = $userObj->delete_user($_GET['uid']);

echo($result);

?>