<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class PendingRequest extends Main{

    public function pendingRequestList($group_by='REFERENCE'){

        $sql="SELECT
            product_tbl.product_name,
            product_tbl.product_code,
            stock_request_tbl.product_id,
            stock_request_tbl.request_date,
            stock_request_tbl.type,
            stock_request_tbl.status,
            stock_request_tbl.id,

            CASE

            WHEN stock_request_tbl.type='Rental'
            THEN (
                SELECT booking_id
                FROM booking_item_tbl
                WHERE booking_item_tbl.id=stock_request_tbl.reference_id
                LIMIT 1
            )

            WHEN stock_request_tbl.type='Sell'
            THEN (
                SELECT sale_id
                FROM sale_item_tbl
                WHERE sale_item_tbl.id=stock_request_tbl.reference_id
                LIMIT 1
            )

            ELSE stock_request_tbl.reference_id

            END AS reference_no,

            COUNT(*) AS qty

            FROM stock_request_tbl

            LEFT JOIN product_tbl
            ON product_tbl.id=stock_request_tbl.product_id

            WHERE stock_request_tbl.status='PENDING'

            GROUP BY
            stock_request_tbl.product_id,
            stock_request_tbl.request_date,
            stock_request_tbl.type,
            stock_request_tbl.reference_id,
            stock_request_tbl.status";

        if($group_by=='PRODUCT'){

            $sql.=" ORDER BY
            product_tbl.product_name,
            stock_request_tbl.request_date ASC";

        }else{

            $sql.=" ORDER BY
            stock_request_tbl.type,
            reference_no,
            stock_request_tbl.request_date ASC";

        }

        $stmt=$this->dbResult->prepare($sql);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){

            $currentGroup='';

            while($rec=$result->fetch_assoc()){

                if($group_by=='PRODUCT'){

                    $groupValue=$rec['product_name'];

                    if($currentGroup!=$groupValue){

                        echo '
                            <tr class="group-header">

                                <td colspan="7">
                                    PRODUCT : '.$groupValue.'
                                </td>

                                <td colspan="2" class="text-end">

                                    <button
                                    type="button"
                                    class="btn btn-success btn-sm btn-complete-product"
                                    data-product="'.$rec['product_id'].'">

                                    Complete Product

                                    </button>

                                </td>

                            </tr>';

                        $currentGroup=$groupValue;
                    }

                }else{

                    $groupValue=$rec['type'].' : '.$rec['reference_no'];

                    if($currentGroup!=$groupValue){

                        echo '
                        <tr class="group-header">
                            <td colspan="9">
                                '.$groupValue.'
                            </td>
                        </tr>';

                        $currentGroup=$groupValue;
                    }
                }

                $days=floor(
                    (
                        strtotime(date('Y-m-d'))
                        -
                        strtotime($rec['request_date'])
                    ) / 86400
                );

                if($days<=2){

                    $ageBadge='
                    <span class="badge request-age-green">
                        '.$days.' Day(s)
                    </span>';

                }elseif($days<=4){

                    $ageBadge='
                    <span class="badge request-age-orange">
                        '.$days.' Day(s)
                    </span>';

                }else{

                    $ageBadge='
                    <span class="badge request-age-red">
                        '.$days.' Day(s)
                    </span>';

                }

                echo '
                <tr>

                    <td>
                        '.$rec['product_code'].'
                    </td>

                    <td>
                        '.$rec['product_name'].'
                    </td>

                    <td class="text-center">
                        '.$rec['qty'].'
                    </td>

                    <td>
                        '.$rec['request_date'].'
                    </td>

                    <td>
                        '.$ageBadge.'
                    </td>

                    <td>
                        '.$rec['type'].'
                    </td>

                    <td>
                        '.$rec['reference_no'].'
                    </td>

                    <td>
                        <span class="badge bg-warning">
                            '.$rec['status'].'
                        </span>
                    </td>

                    <td>

                        <button
                        type="button"
                        class="btn btn-primary btn-sm btn-view"
                        data-id="'.$rec['id'].'">

                        View

                        </button>

                        <button
                        type="button"
                        class="btn btn-success btn-sm btn-complete-request"
                        data-id="'.$rec['id'].'"
                        data-product="'.$rec['product_id'].'">

                        Redy

                        </button>

                    </td>

                </tr>';
            }

        }else{

            echo '
            <tr>
                <td colspan="9">
                    <div class="alert alert-info mb-0">
                        No Pending Requests Found
                    </div>
                </td>
            </tr>';

        }

        $stmt->close();

    }

    public function viewPendingRequest($id){

        $sql="SELECT
        stock_request_tbl.*,
        product_tbl.product_name,
        product_tbl.product_code
        FROM stock_request_tbl
        LEFT JOIN product_tbl
        ON product_tbl.id=stock_request_tbl.product_id
        WHERE stock_request_tbl.id=?";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param("s",$id);

        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows<=0){

            echo '
            <div class="alert alert-danger">
                Request Not Found
            </div>';

            return;
        }

        $rec=$result->fetch_assoc();
        $stmt->close();
        echo '
        <div class="card border-0">
            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <tr>
                        <th width="180">
                            Request ID
                        </th>
                        <td>
                            '.$rec['id'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Product Code
                        </th>
                        <td>
                            '.$rec['product_code'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Product Name
                        </th>
                        <td>
                            '.$rec['product_name'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Request Date
                        </th>
                        <td>
                            '.$rec['request_date'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Request Type
                        </th>
                        <td>
                            '.$rec['type'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Reference ID
                        </th>
                        <td>
                            '.$rec['reference_id'].'
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Status
                        </th>
                        <td>
                            <span class="badge bg-warning">
                                '.$rec['status'].'
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Created At
                        </th>
                        <td>
                            '.$rec['created_at'].'
                        </td>
                    </tr>
                </table>
            </div>
        </div>';
    }

    public function completeRequest($id){

        $sql="SELECT *
        FROM stock_request_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("i",$id);
        $stmt->execute();

        $request=$stmt->get_result()->fetch_assoc();
        $stmt->close();

        if(!$request){

            return json_encode([
                "status"=>"error",
                "message"=>"Request not found"
            ]);

        }

        if($request['type']=='Rental'){

            $sqlBooking="SELECT *
            FROM booking_item_tbl
            WHERE id=?";

            $stmt=$this->dbResult->prepare($sqlBooking);
            $stmt->bind_param("i",$request['reference_id']);
            $stmt->execute();

            $bookingItem=$stmt->get_result()->fetch_assoc();
            $stmt->close();

            if($bookingItem){

                if(
                    $bookingItem['product_item_id']=='' ||
                    $bookingItem['product_item_id']==0 ||
                    $bookingItem['product_item_id']==null
                ){

                    $user=$_SESSION['user'];

                    $sqlStation="SELECT station
                    FROM login_tbl
                    WHERE loginId=?";

                    $stmt=$this->dbResult->prepare($sqlStation);
                    $stmt->bind_param("s",$user);
                    $stmt->execute();

                    $station=$stmt->get_result()->fetch_assoc()['station'];

                    $stmt->close();
                    $autoNumber = new AutoNumber;

                    $productItemId = $autoNumber->NumberGenaration("id","product_item_tbl","PIT");

                    $sqlInsert = "INSERT INTO product_item_tbl(id,product_id,station_id,created_by,updated_by,d_status) VALUES(?,?,?,?,?,0)";

                    $stmt = $this->dbResult->prepare($sqlInsert);
                    $stmt->bind_param("sssss",$productItemId,$request['product_id'],$station,$user,$user);

                    $sqlResult = $stmt->execute();

                    $stmt->close();


                    $sqlUpdateBooking="UPDATE booking_item_tbl
                    SET product_item_id=?
                    WHERE id=?";

                    $stmt=$this->dbResult->prepare($sqlUpdateBooking);

                    $stmt->bind_param(
                        "ss",
                        $productItemId,
                        $bookingItem['id']
                    );

                    $stmt->execute();
                    $stmt->close();
                }
            }
        }

        $sqlDone="UPDATE stock_request_tbl
        SET status='DONE'
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlDone);
        $stmt->bind_param("i",$id);

        $result=$stmt->execute();

        $stmt->close();

        if($result){

            return json_encode([
                "status"=>"success"
            ]);

        }

        return json_encode([
            "status"=>"error",
            "message"=>"Update failed"
        ]);

    }

    public function completeProduct($product_id){

        $user=$_SESSION['user'];

        $sqlStation="SELECT station
        FROM login_tbl
        WHERE loginId=?";

        $stmt=$this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $station=$stmt->get_result()->fetch_assoc()['station'];

        $stmt->close();

        $sql="SELECT *
        FROM stock_request_tbl
        WHERE product_id=?
        AND status='PENDING'
        ORDER BY id ASC";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$product_id);
        $stmt->execute();

        $result=$stmt->get_result();

        
        $stmt->close();

        $count=0;

        while($request=$result->fetch_assoc()){

            if($request['type']=='Rental'){

                $sqlBooking="SELECT *
                FROM booking_item_tbl
                WHERE id=?";

                $stmtBooking=$this->dbResult->prepare($sqlBooking);

                $stmtBooking->bind_param(
                    "i",
                    $request['reference_id']
                );

                $stmtBooking->execute();

                $bookingItem=$stmtBooking->get_result()->fetch_assoc();

                $stmtBooking->close();

                if($bookingItem){

                    if(
                        $bookingItem['product_item_id']=='' ||
                        $bookingItem['product_item_id']==0 ||
                        $bookingItem['product_item_id']==null
                    ){

                        $autoNumber = new AutoNumber;

                        $productItemId = $autoNumber->NumberGenaration("id","product_item_tbl","PIT");

                        $sqlInsert = "INSERT INTO product_item_tbl(id,product_id,station_id,created_by,updated_by,d_status) VALUES(?,?,?,?,?,0)";

                        $stmt = $this->dbResult->prepare($sqlInsert);
                        $stmt->bind_param("sssss",$productItemId,$product_id,$station,$user,$user);

                        $sqlResult = $stmt->execute();

                        $stmt->close();

                        $sqlUpdateBooking="UPDATE booking_item_tbl
                        SET product_item_id=?
                        WHERE id=?";

                        $stmtUpdate=$this->dbResult->prepare($sqlUpdateBooking);

                        $stmtUpdate->bind_param(
                            "ii",
                            $productItemId,
                            $bookingItem['id']
                        );

                        $stmtUpdate->execute();

                        $stmtUpdate->close();
                    }
                }
            }

            $sqlDone="UPDATE stock_request_tbl
            SET status='DONE'
            WHERE id=?";

            $stmtDone=$this->dbResult->prepare($sqlDone);

            $stmtDone->bind_param(
                "i",
                $request['id']
            );

            $stmtDone->execute();

            $stmtDone->close();

            $count++;

        }

        

        return json_encode([
            "status"=>"success",
            "completed"=>$count
        ]);

    }

}
?>
