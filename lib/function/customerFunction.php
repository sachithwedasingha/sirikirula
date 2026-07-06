<?php
session_start();
include_once('main.php');
include_once('auto_id.php');

class Customer extends Main{

    public function addCustomer($customer_type, $customer_name, $phone, $nic, $address ){

        $sqlCheck = "SELECT * FROM customer_tbl WHERE (phone = ? OR nic = ? ) AND d_status = 0";

        $stmt = $this->dbResult->prepare($sqlCheck);
        $stmt->bind_param(
            "ss",
            $phone,
            $nic
        );
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $stmt->close();
            return("04");
        }

        $stmt->close();
        $autoNumber = new AutoNumber;
        $id = $autoNumber->NumberGenaration(
            "id",
            "customer_tbl",
            "CUS"
        );
        $sqlInsert = "INSERT INTO customer_tbl
        (
            id,
            customer_type,
            customer_name,
            phone,
            nic,
            address,
            d_status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            0
        )";

        $stmt = $this->dbResult->prepare($sqlInsert);

        $stmt->bind_param(
            "ssssss",
            $id,
            $customer_type,
            $customer_name,
            $phone,
            $nic,
            $address
        );
        $sqlResult = $stmt->execute();
        $stmt->close();
        if($sqlResult > 0){
            return("01");
        }else{
            return("02");
        }
    }

    public function updateCustomer(
            $customer_id,
            $customer_type,
            $customer_name,
            $phone,
            $nic,
            $address
        ){

        $sqlCheck="SELECT id
            FROM customer_tbl
            WHERE id<>?
            AND (
                phone=?
                OR nic=?
            )
            LIMIT 1";

            $stmt=$this->dbResult->prepare($sqlCheck);

            $stmt->bind_param(
                "sss",
                $customer_id,
                $phone,
                $nic
            );

            $stmt->execute();

            $result=$stmt->get_result();

            if($result->num_rows>0){

                $stmt->close();

                return json_encode([
                    "status"=>"02",
                    "message"=>"Phone Number or NIC already exists"
                ]);

            }

            $stmt->close();

        $sql="UPDATE customer_tbl SET

        customer_type=?,
        customer_name=?,
        phone=?,
        nic=?,
        address=?

        WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param(
            "ssssss",
            $customer_type,
            $customer_name,
            $phone,
            $nic,
            $address,
            $customer_id
        );

        $result=$stmt->execute();

        $stmt->close();

        if($result){

            return json_encode([
                "status"=>"01"
            ]);

        }

        return json_encode([
            "status"=>"02",
            "message"=>"Customer Update Failed"
        ]);

    }

    public function loadCustomersTable($search){

        $sql="SELECT *
        FROM customer_tbl
        WHERE d_status=0";

        $params=[];
        $types="";

        if($search!=''){

            $sql.=" AND (
                customer_name LIKE ?
                OR phone LIKE ?
                OR nic LIKE ?
                OR customer_type LIKE ?
            )";

            $search='%'.$search.'%';

            $types.="ssss";

            $params[]=$search;
            $params[]=$search;
            $params[]=$search;
            $params[]=$search;

        }

        $sql.=" ORDER BY customer_name ASC";

        $stmt=$this->dbResult->prepare($sql);

        if(count($params)>0){

            $stmt->bind_param($types,...$params);

        }

        $stmt->execute();

        $result=$stmt->get_result();

        $table='';

        while($rec=$result->fetch_assoc()){

            $badge='';

            if($rec['customer_type']=='SALON'){

                $badge='<span class="badge bg-info">SALON</span>';

            }else{

                $badge='<span class="badge bg-secondary">INDIVIDUAL</span>';

            }

            $table.='
            <tr>

                <td>
                    '.$rec['id'].'
                </td>

                <td>
                    '.$badge.'
                </td>

                <td>
                    '.$rec['customer_name'].'
                </td>

                <td>
                    '.$rec['phone'].'
                </td>

                <td>
                    '.$rec['nic'].'
                </td>

                <td>

                    <button
                    type="button"
                    class="btn btn-warning btn-sm btn-edit-customer"
                    data-id="'.$rec['id'].'">

                        <i class="bi bi-pencil-fill"></i>

                    </button>

                </td>

            </tr>';

        }

        if($table==''){

            $table='
            <tr>

                <td colspan="6">

                    <div class="alert alert-info mb-0">

                        No Customers Found

                    </div>

                </td>

            </tr>';

        }

        $stmt->close();

        return json_encode([
            "table"=>$table
        ]);

    }

    public function getCustomer($customer_id){

        $sql="SELECT *
        FROM customer_tbl
        WHERE id=?
        LIMIT 1";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param(
            "s",
            $customer_id
        );

        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows<=0){

            $stmt->close();

            return json_encode([
                "status"=>"error",
                "message"=>"Customer Not Found"
            ]);

        }

        $customer=$result->fetch_assoc();

        $stmt->close();

        return json_encode($customer);

    }

    public function customerDropdown(){

        $sql="SELECT id,customer_name,phone,nic FROM customer_tbl WHERE d_status=0 ORDER BY customer_name ASC";
        $result=$this->dbResult->query($sql);
        echo '<option value="">Select Customer</option>';
        while($rec=$result->fetch_assoc()){
            echo '<option value="'.$rec['id'].'">'.$rec['customer_name'].' | '.$rec['phone'].' | '.$rec['nic'].'</option>';
        }

    }

   public function loadCustomers($search){

        $sql="SELECT
        id,
        customer_name,
        phone,
        nic
        FROM customer_tbl
        WHERE d_status=0
        AND (
            customer_name LIKE ?
            OR phone LIKE ?
            OR nic LIKE ?
        )
        ORDER BY customer_name ASC
        LIMIT 20";

        $like="%".$search."%";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("sss",$like,$like,$like);
        $stmt->execute();

        $result=$stmt->get_result();

        $data=[];

        while($rec=$result->fetch_assoc()){

            $data[]=[
                'id'=>$rec['id'],
                'text'=>$rec['customer_name'].' | '.$rec['phone'].' | '.$rec['nic']
            ];

        }

        $stmt->close();

        return json_encode($data);

}

}
?>