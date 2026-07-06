<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Service extends Main{

    public function addService($service_name){

        $sqlCheck="SELECT id FROM service_tbl
        WHERE service_name=? AND d_status=0";

        $stmt=$this->dbResult->prepare($sqlCheck);
        $stmt->bind_param("s",$service_name);
        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows>0){

            $stmt->close();
            return "04";

        }

        $stmt->close();

        $auto=new AutoNumber();
        $id=$auto->NumberGenaration("id","service_tbl","SER");

        $sqlInsert="INSERT INTO service_tbl(
        id,
        service_name,
        d_status
        ) VALUES(
        ?,
        ?,
        0
        )";

        $stmt=$this->dbResult->prepare($sqlInsert);
        $stmt->bind_param(
        "ss",
        $id,
        $service_name
        );

        $sqlResult=$stmt->execute();

        $stmt->close();

        if($sqlResult){
            return "01";
        }else{
            return "02";
        }

    }

    public function editService($id,$service_name){

        $sqlUpdate="UPDATE service_tbl
        SET service_name=?
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlUpdate);

        $stmt->bind_param(
        "ss",
        $service_name,
        $id
        );

        $sqlResult=$stmt->execute();

        $stmt->close();

        if($sqlResult){
            return "01";
        }else{
            return "02";
        }

    }

    public function deleteService($uid){

        $sqlDelete="UPDATE service_tbl
        SET d_status=1
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlDelete);

        $stmt->bind_param(
        "s",
        $uid
        );

        $sqlResult=$stmt->execute();

        $stmt->close();

        if($sqlResult){
            return "ok";
        }else{
            return "error";
        }

    }

    public function serviceList(){

        $sqlSelect="SELECT *
        FROM service_tbl
        WHERE d_status=0
        ORDER BY service_name ASC";

        $stmt=$this->dbResult->prepare($sqlSelect);

        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows>0){

            while($rec=$result->fetch_assoc()){

                echo '
                <tr>
                    <td>'.htmlspecialchars($rec['service_name']).'</td>
                    <td>

                        <button
                        type="button"
                        class="btn btn-warning btn-sm btn-edit"
                        data-id="'.$rec['id'].'">
                        Edit
                        </button>

                        <button
                        type="button"
                        class="btn btn-danger btn-sm btn-delete"
                        data-id="'.$rec['id'].'">
                        Delete
                        </button>

                    </td>
                </tr>';

            }

        }else{

            echo '
            <tr>
                <td colspan="2">
                    <div class="alert alert-danger mb-0">
                        No Services Found
                    </div>
                </td>
            </tr>';

        }

        $stmt->close();

    }

    public function serviceData($uid){

        $sqlSelect="SELECT *
        FROM service_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlSelect);

        $stmt->bind_param(
        "s",
        $uid
        );

        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows>0){

            $rec=$result->fetch_assoc();

            $stmt->close();

            return json_encode($rec);

        }

        $stmt->close();

    }

    public function serviceDropDown(){

        $sql = "SELECT *
        FROM service_tbl
        WHERE d_status=0
        ORDER BY service_name ASC";

        $stmt = $this->dbResult->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();

        echo '<option value="">Select Service</option>';

        while($rec = $result->fetch_assoc()){

            echo '<option value="'.$rec['id'].'">
            '.$rec['service_name'].'
            </option>';

        }

        $stmt->close();

    }

}

?>
