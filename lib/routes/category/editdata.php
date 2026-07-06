<?php

include_once('../../function/categoryFunction.php');

$catObj = new Category();

$result = $catObj->editCategory(
    $_POST['id'],
    $_POST['name']
);

echo $result;

?>