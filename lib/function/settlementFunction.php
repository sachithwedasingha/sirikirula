<?php
//we need to start the sessions 
session_start();

//include main.php
include_once('main.php');

//include auto number module 
include_once('auto_id.php');

class Settlement extends Main{

public function loadPendingCustomers($search){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT DISTINCT
    customer_tbl.id,
    customer_tbl.customer_name,
    customer_tbl.phone
    FROM pending_payment_tbl
    JOIN customer_tbl
    ON customer_tbl.id=pending_payment_tbl.customer_id
    WHERE pending_payment_tbl.status='PENDING'
    AND pending_payment_tbl.station=?";

    if($search!=''){
        $sql.=" AND (
        customer_tbl.customer_name LIKE ?
        OR customer_tbl.phone LIKE ?
        )";
    }

    $stmt=$this->dbResult->prepare($sql);

    if($search!=''){
        $like="%".$search."%";
        $stmt->bind_param("sss",$station,$like,$like);
    }else{
        $stmt->bind_param("s",$station);
    }

    $stmt->execute();

    $result=$stmt->get_result();

    $data=[];

    while($row=$result->fetch_assoc()){

        $data[]=[
            "id"=>$row['id'],
            "text"=>$row['customer_name']." - ".$row['phone']
        ];

    }

    $stmt->close();

    return json_encode($data);

}

public function loadPendingPayments($customer_id){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT *
    FROM pending_payment_tbl
    WHERE customer_id=?
    AND station=?
    AND status='PENDING'
    ORDER BY id DESC";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("ss",$customer_id,$station);
    $stmt->execute();

    $result=$stmt->get_result();

    $table='';

    $totalAmount=0;
    $totalPaid=0;
    $totalBalance=0;

    while($rec=$result->fetch_assoc()){

        $totalAmount+=$rec['amount'];
        $totalPaid+=$rec['paid_amount'];
        $totalBalance+=$rec['balance_amount'];

        $badge=$rec['type']=='SALE'
        ? '<span class="badge bg-success">SALE</span>'
        : '<span class="badge bg-primary">RENTAL</span>';

        $table.='
        <tr>

            <td>'.$rec['type_id'].'</td>

            <td>'.$badge.'</td>

            <td></td>

            <td class="text-end">
                '.number_format($rec['amount'],2).'
            </td>

            <td class="text-end">
                '.number_format($rec['paid_amount'],2).'
            </td>

            <td class="text-end text-danger">
                '.number_format($rec['balance_amount'],2).'
            </td>

            <td>
                <span class="badge bg-warning">
                    '.$rec['status'].'
                </span>
            </td>

            <td>

                <button
                type="button"
                class="btn btn-success btn-sm btn-settle-payment"
                data-id="'.$rec['type_id'].'"
                data-type="'.$rec['type'].'"
                data-balance="'.$rec['balance_amount'].'">

                Pay

                </button>

            </td>

        </tr>';

    }

    if($table==''){

        $table='
        <tr>
            <td colspan="8">
                <div class="alert alert-info mb-0">
                    No Pending Payments
                </div>
            </td>
        </tr>';

    }

    $stmt->close();

    return json_encode([
        "table"=>$table,
        "total_amount"=>$totalAmount,
        "total_paid"=>$totalPaid,
        "total_balance"=>$totalBalance
    ]);

}

public function saveSettlement(
    $type,
    $reference_id,
    $amount,
    $payment_method,
    $received_by
){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="UPDATE pending_payment_tbl
    SET
    paid_amount=IFNULL(paid_amount,0)+?,
    balance_amount=balance_amount-?
    WHERE type=?
    AND type_id=?
    AND station=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param(
        "ddsss",
        $amount,
        $amount,
        $type,
        $reference_id,
        $station
    );

    $stmt->execute();
    $stmt->close();

    $part='ADVANCE PAYMENT';

    $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station)
    VALUES(?,?,?,?,?,?)";

    $stmt=$this->dbResult->prepare($sqlIncome);

    $stmt->bind_param(
        "ssdsss",
        $type,
        $reference_id,
        $part,
        $amount,
        $received_by,
        $station
    );

    $stmt->execute();
    $stmt->close();

    $sqlClose="UPDATE pending_payment_tbl
    SET status='PAID'
    WHERE type=?
    AND type_id=?
    AND station=?
    AND balance_amount<=0";

    $stmt=$this->dbResult->prepare($sqlClose);

    $stmt->bind_param(
        "sss",
        $type,
        $reference_id,
        $station
    );

    $stmt->execute();
    $stmt->close();

    return json_encode([
        "status"=>"success"
    ]);

}
 
}
?>