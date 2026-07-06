<?php

include_once('../../function/productFunction.php');

$obj=new Product();

$id = $_GET['id'];

echo $obj->productFullView($id);