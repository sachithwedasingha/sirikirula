
<?php

include_once('../../function/serviceFunction.php');

$obj = new Service();

if($_SERVER['REQUEST_METHOD']=='POST'){

    echo $obj->addService(
        $_POST['service_name']
    );

}
?>
