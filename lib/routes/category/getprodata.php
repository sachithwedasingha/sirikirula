<?php

include_once('../../function/categoryFunction.php');

$catObj = new Category();

echo $catObj->categorydata($_GET['uid']);

?>