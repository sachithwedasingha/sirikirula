<?php
//we need to start the sessions 
session_start();

//include main.php
include_once('main.php');

//include auto number module 
include_once('auto_id.php');

class Income extends Main{
public function loadIncome( $single_date, $from_date, $to_date, $customer_id, $income_type, $reference_id ){

    $sql="SELECT
    income_tbl.*, customer_tbl.customer_name, employer_tbl.emp_FirstName, employer_tbl.emp_SecondName
    FROM income_tbl
    LEFT JOIN sale_tbl
    ON sale_tbl.id=income_tbl.type_id
    AND income_tbl.type='SALE'
    LEFT JOIN booking_tbl
    ON booking_tbl.id=income_tbl.type_id
    AND income_tbl.type='RENTAL'
    LEFT JOIN customer_tbl
    ON customer_tbl.id=
    CASE
        WHEN income_tbl.type='SALE'
        THEN sale_tbl.customer_id
        ELSE booking_tbl.customer_id
    END
    LEFT JOIN employer_tbl
    ON employer_tbl.emp_Id=income_tbl.created_by
    WHERE 1=1";

    $params=[];
    $types='';

    if($single_date!=''){
        $sql.=" AND DATE(income_tbl.created_date)=?";
        $types.="s";
        $params[]=$single_date;
    }

    if($from_date!='' && $to_date!=''){
        $sql.=" AND DATE(income_tbl.created_date)
        BETWEEN ? AND ?";
        $types.="ss";
        $params[]=$from_date;
        $params[]=$to_date;
    }

    if($customer_id!=''){
        $sql.=" AND customer_tbl.id=?";
        $types.="s";
        $params[]=$customer_id;
    }

    if($income_type!=''){
        $sql.=" AND income_tbl.type=?";
        $types.="s";
        $params[]=$income_type;
    }

    if($reference_id!=''){
        $sql.=" AND income_tbl.type_id LIKE ?";
        $types.="s";
        $params[]='%'.$reference_id.'%';
    }

    $sql.=" ORDER BY income_tbl.id DESC";
    $stmt=$this->dbResult->prepare($sql);

    if(count($params)>0){
        $stmt->bind_param($types,...$params);
    }
    $stmt->execute();
    $result=$stmt->get_result();
    $table='';
    $totalIncome=0;
    $salesIncome=0;
    $rentalIncome=0;
    $penaltyIncome=0;

    while($rec=$result->fetch_assoc()){
        $amount=(double)$rec['amount'];
        $totalIncome+=$amount;
        if($rec['type']=='SALE'){
            $salesIncome+=$amount;
        }
        if($rec['type']=='RENTAL'){
            $rentalIncome+=$amount;
        }
        if($rec['type']=='PENALTY'){
            $penaltyIncome+=$amount;
        }
        $receivedBy=
        $rec['emp_FirstName'].' '.
        $rec['emp_SecondName'];

        $badge='';
        if($rec['type']=='SALE'){
            $badge='<span class="badge bg-success">SALE</span>';
        }elseif($rec['type']=='RENTAL'){
            $badge='<span class="badge bg-primary">RENTAL</span>';
        }else{
            $badge='<span class="badge bg-danger">PENALTY</span>';
        }
        $table.='<tr>
            <td>'.$rec['type_id'].'</td>
            <td>'.date('Y-m-d h:i A',strtotime($rec['created_date'])).'</td>
            <td>'.$badge.'</td>
            <td>'.($rec['customer_name'] ?? '-').'</td>
            <td>'.$rec['part'].'</td>
            <td class="text-end">'.number_format($amount,2).'</td>
            <td>'.$receivedBy.'</td>
            <td>
                <button
                type="button"
                class="btn btn-info btn-sm btn-view-income"
                data-id="'.$rec['type_id'].'"
                data-type="'.$rec['type'].'">
                View
                </button>
            </td>
        </tr>';
    }
    if($table==''){
        $table='
        <tr>
            <td colspan="8">
                <div class="alert alert-info mb-0">
                    No Income Records Found
                </div>
            </td>
        </tr>';
    }
    $stmt->close();
    return json_encode([
        'table'=>$table,
        'total_income'=>$totalIncome,
        'sales_income'=>$salesIncome,
        'rental_income'=>$rentalIncome,
        'penalty_income'=>$penaltyIncome
    ]);
}
  
}
?>