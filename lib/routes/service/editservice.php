<?php

include_once('../../function/serviceFunction.php');

$obj = new Service();

if($_SERVER['REQUEST_METHOD']=='POST'){

    echo $obj->editService(
        $_POST['id'],
        $_POST['service_name']
    );

}
?>
