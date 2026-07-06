<?php

include_once('../../function/categoryFunction.php');

$catObj = new Category();

echo $catObj->delete_category($_POST['uid']);

?>