<?php
//we need to start the sessions 
session_start();

//include main.php
include_once('main.php');

//include auto number module 
include_once('auto_id.php');

class Expense extends Main{

public function saveExpense($expense_date,$expense_category,$payment_method,$amount,$description,$createdby){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

    $sql="INSERT INTO expense_tbl(expense_date,expense_category,payment_method,amount,description,station,created_by) VALUES(?,?,?,?,?,?,?)";
    $stmt=$this->dbResult->prepare($sql);

    $stmt->bind_param(
        "sssdsss",
        $expense_date,
        $expense_category,
        $payment_method,
        $amount,
        $description,
        $station,
        $createdby
    );
    $result=$stmt->execute();
    $stmt->close();

    if($result){
        return json_encode([
            "status"=>"success"
        ]);
    }
    return json_encode([
        "status"=>"error",
        "message"=>"Expense Save Failed"
    ]);

}

public function loadTodayExpenses(){

    $user=$_SESSION['user'];
    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT expense_tbl.*,employer_tbl.emp_FirstName,employer_tbl.emp_SecondName
    FROM expense_tbl
    LEFT JOIN employer_tbl
    ON employer_tbl.emp_Id=expense_tbl.created_by
    WHERE expense_tbl.expense_date=CURDATE()
    AND expense_tbl.station=?
    AND expense_tbl.d_status=0
    ORDER BY expense_tbl.id DESC";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$station);
    $stmt->execute();

    $result=$stmt->get_result();

    $table='';
    $total=0;

    while($rec=$result->fetch_assoc()){

        $total+=$rec['amount'];
        $table.='<tr>
            <td>
                '.date(
                    'h:i A',
                    strtotime($rec['created_at'])
                ).'
            </td>
            <td>'.$rec['expense_category'].'</td>
            <td>'.$rec['description'].'</td>
            <td class="text-end">
                '.number_format(
                    $rec['amount'],
                    2
                ).'
            </td>
        </tr>';
    }

    if($table==''){
        $table='<tr>
            <td colspan="4">
                <div class="alert alert-info mb-0">
                    No Expenses Found
                </div>
            </td>
        </tr>';
    }
    $stmt->close();

    return json_encode([
        "table"=>$table,
        "total"=>$total
    ]);

}

public function loadExpenses($from_date,$to_date,$expense_category,$payment_method){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT
    expense_tbl.*,
    employer_tbl.emp_FirstName,
    employer_tbl.emp_SecondName
    FROM expense_tbl
    LEFT JOIN employer_tbl
    ON employer_tbl.emp_Id=expense_tbl.created_by
    WHERE expense_tbl.d_status=0
    AND expense_tbl.station=?";

    $params=[$station];
    $types='s';

    if($from_date!='' && $to_date!=''){
        $sql.=" AND expense_tbl.expense_date BETWEEN ? AND ?";
        $types.="ss";
        $params[]=$from_date;
        $params[]=$to_date;
    }

    if($expense_category!=''){
        $sql.=" AND expense_tbl.expense_category=?";
        $types.="s";
        $params[]=$expense_category;
    }

    if($payment_method!=''){
        $sql.=" AND expense_tbl.payment_method=?";
        $types.="s";
        $params[]=$payment_method;
    }

    $sql.=" ORDER BY expense_tbl.id DESC";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param($types,...$params);
    $stmt->execute();

    $result=$stmt->get_result();

    $table='';
    $totalExpense=0;
    $totalRecords=0;

    while($rec=$result->fetch_assoc()){
        $totalExpense+=$rec['amount'];
        $totalRecords++;
        $table.='<tr>
            <td>'.$rec['id'].'</td>
            <td>'.$rec['expense_date'].'</td>
            <td>'.$rec['expense_category'].'</td>
            <td>'.$rec['payment_method'].'</td>
            <td>'.$rec['description'].'</td>
            <td>
            '.$rec['emp_FirstName'].' '.$rec['emp_SecondName'].'
            </td>
            <td class="text-end">
            '.number_format($rec['amount'],2).'
            </td>
            <td>
                <button
                type="button"
                class="btn btn-info btn-sm btn-view-expense"
                data-id="'.$rec['id'].'">
                View
                </button>
            </td>
        </tr>';
    }

    if($table==''){
        $table='<tr>
            <td colspan="8">
                <div class="alert alert-info mb-0">
                    No Expense Records Found
                </div>
            </td>
        </tr>';
    }

    $averageExpense=0;
    if($totalRecords>0){$averageExpense=$totalExpense/$totalRecords;}
    return json_encode([
        'table'=>$table,
        'total_expense'=>$totalExpense,
        'total_records'=>$totalRecords,
        'average_expense'=>$averageExpense
    ]);

}

public function getExpense($expense_id){

    $sql="SELECT * FROM expense_tbl WHERE id=?";
    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param( "i", $expense_id );
    $stmt->execute();
    $result=$stmt->get_result()->fetch_assoc();
    $stmt->close();
    return json_encode($result);

}

}
?>