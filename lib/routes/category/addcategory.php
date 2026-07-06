<?php

include_once('../../function/categoryFunction.php');

$catObj = new Category();

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $result = $catObj->addcategory(
        $_POST['name']
    );

    echo $result;
}
?>