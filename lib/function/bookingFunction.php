<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Booking extends Main{

public function searchCustomer($search){

    $sql = "SELECT id,customer_name,phone,nic
            FROM customer_tbl
            WHERE d_status = 0
            AND (
                customer_name LIKE ?
                OR phone LIKE ?
                OR nic LIKE ?
            )
            ORDER BY customer_name
            LIMIT 20";

    $like = "%".$search."%";

    $stmt = $this->dbResult->prepare($sql);
    $stmt->bind_param("sss",$like,$like,$like);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];

    while($row = $result->fetch_assoc()){

        $data[] = [
            "id" => $row['id'],
            "text" => $row['customer_name']." | ".$row['phone']." | ".$row['nic']
        ];
    }

    echo json_encode($data);

    $stmt->close();
}

public function searchProducts($search,$booking_date,$return_date){

    $user = $_SESSION['user'];

    $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";

    $stmt = $this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    $station = $row['station'];

    $stmt->close();

    $sql = "SELECT *
            FROM product_tbl
            WHERE (
                product_name LIKE ?
                OR product_code LIKE ?
            )
            AND d_status=0
            LIMIT 20";

    $like = "%".$search."%";

    $stmt = $this->dbResult->prepare($sql);
    $stmt->bind_param("ss",$like,$like);
    $stmt->execute();

    $result = $stmt->get_result();

    $data = [];

    while($rec = $result->fetch_assoc()){

        $sqlCount = "SELECT COUNT(*) AS total
                     FROM product_item_tbl
                     WHERE product_id=?
                     AND station_id=?
                     AND d_status=0
                     AND id NOT IN(
                        SELECT product_item_id
                        FROM booking_item_tbl
                        JOIN booking_tbl
                        ON booking_tbl.id = booking_item_tbl.booking_id
                        WHERE booking_tbl.status IN('BOOKED','RENTED')
                        AND (
                            (booking_tbl.booking_date <= ? AND booking_tbl.return_date >= ?)
                            OR
                            (booking_tbl.booking_date <= ? AND booking_tbl.return_date >= ?)
                        )
                     )";

        $stmt2 = $this->dbResult->prepare($sqlCount);

        $stmt2->bind_param(
            "ssssss",
            $rec['id'],
            $station,
            $booking_date,
            $booking_date,
            $return_date,
            $return_date
        );

        $stmt2->execute();

        $available = $stmt2->get_result()->fetch_assoc()['total'];

        // if($available > 0){

        //     $data[] = [
        //         "id" => $rec['id'],
        //         "product_name" => $rec['product_name'],
        //         "available" => $available,
        //         "text" => $rec['product_name']." | ".$rec['product_code']." | Available : ".$available
        //     ];
        // }

            $data[] = [
                "id" => $rec['id'],
                "product_name" => $rec['product_name'],
                "available" => $available,
                "text" => $rec['product_name']." | ".$rec['product_code']." | Available : ".$available
            ];
    }

    echo json_encode($data);

    $stmt->close();
}

//check product qty when + item count
public function checkProductQty($product_id,$qty,$booking_date,$return_date){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];

        $stmt->close();

        $sqlCount = "SELECT COUNT(*) AS total FROM product_item_tbl WHERE product_id=? AND station_id=? AND d_status=0 AND id NOT IN(
        SELECT product_item_id FROM booking_item_tbl JOIN booking_tbl 
        ON booking_tbl.id = booking_item_tbl.booking_id
        WHERE booking_tbl.status IN('BOOKED','RENTED')
        AND ((booking_tbl.booking_date <= ? AND booking_tbl.return_date >= ?) OR (booking_tbl.booking_date <= ? AND booking_tbl.return_date >= ?)) )";

        $stmt = $this->dbResult->prepare($sqlCount);

        $stmt->bind_param(
            "ssssss",
            $product_id,
            $station,
            $booking_date,
            $booking_date,
            $return_date,
            $return_date
        );

        $stmt->execute();
        $result = $stmt->get_result();
        $rec = $result->fetch_assoc();
        $available = $rec['total'];
        $stmt->close();

        if($qty <= $available){
            return("01");
        }else{
            return("02");
        }

}

// SAVE BOOKING
public function saveBooking( $booking_date,$return_date,$customer_id,$collection_type,$other_customer_name,$other_customer_phone,$other_customer_nic,
    $booking_amount,$advance_amount,$balance_amount,$payment_method,$hold_amount,$paid_amount,$hold_amount_type,$bank_details,$remarks,$createdby,$products){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $autoNumber = new AutoNumber;
        $bookingId = $autoNumber->NumberGenaration("id","booking_tbl","BOO");

        $sqlInsert="INSERT INTO booking_tbl(id,booking_date,return_date,customer_id,collection_type,other_customer_name,other_customer_phone,other_customer_nic,station_id,booking_amount,
        advance_amount,balance_amount,payment_method,hold_amount,hold_payed_amount,hold_amount_type,bank_details,remarks,status,created_by,d_status) 
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'BOOKED', ?,0)";

        $stmt=$this->dbResult->prepare($sqlInsert);

        $stmt->bind_param("sssssssssdddsddssss",$bookingId,$booking_date,$return_date,$customer_id,$collection_type,$other_customer_name,$other_customer_phone,$other_customer_nic,$station,$booking_amount,
        $advance_amount,$balance_amount,$payment_method,$hold_amount,$paid_amount,$hold_amount_type,$bank_details,$remarks,$createdby);

        $sqlResult=$stmt->execute();
        $stmt->close();

        if($advance_amount>0){
            $part=($advance_amount>=$booking_amount) ? 'Full Payment' : 'Advance Payment';
            $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('RENTAL',?,?,?,?,?)";
            $stmt=$this->dbResult->prepare($sqlIncome);
            $stmt->bind_param("ssdss",$bookingId,$part,$advance_amount,$createdby,$station);
            $stmt->execute();
            $stmt->close();
        }

        if($payment_method=='Pay by Month End' && $balance_amount>0){
            $sqlPending="INSERT INTO pending_payment_tbl(type,type_id,customer_id,amount,paid_amount,balance_amount,status)
            VALUES('BOOKING',?,?,?,0,?,'PENDING')";
            $stmt=$this->dbResult->prepare($sqlPending);
            $stmt->bind_param(  "ssdd", $bookingId, $customer_id, $balance_amount, $balance_amount );
            $stmt->execute();
            $stmt->close();
        }
        
        if(!$sqlResult){
            return json_encode([
            "status"=>"error",
            "message"=>"Booking Save Failed"
            ]);
        }

        foreach($products as $product){

            for($i=1;$i<=$product['qty'];$i++){

                $sqlItem = "SELECT id FROM product_item_tbl WHERE product_id=? AND station_id=? AND d_status=0 AND id NOT IN(
                SELECT product_item_id FROM booking_item_tbl JOIN booking_tbl ON booking_tbl.id = booking_item_tbl.booking_id 
                WHERE booking_tbl.status IN('BOOKED','RENTED')
                AND ((booking_tbl.booking_date <= ? AND booking_tbl.return_date >= ?) OR (booking_tbl.booking_date <= ? 
                AND booking_tbl.return_date >= ?))) ORDER BY id ASC LIMIT 1";

                $stmt = $this->dbResult->prepare($sqlItem);
                $stmt->bind_param("ssssss",$product['product_id'],$station,$booking_date,$booking_date,$return_date,$return_date);
                $stmt->execute();

                $res = $stmt->get_result();
               if($res->num_rows <= 0){

                    $stmt->close();

                    $sqlBookItem = "INSERT INTO booking_item_tbl(booking_id,product_id,product_item_id,qty,status)
                    VALUES(?,?,NULL,?,'PENDING_STOCK')";
                    $stmtPending = $this->dbResult->prepare($sqlBookItem);
                    $qty = 1;
                    $stmtPending->bind_param("ssi",$bookingId,$product['product_id'],$qty);

                    $stmtPending->execute();
                    $bookingItemId = $this->dbResult->insert_id;
                    $stmtPending->close();
                    
                    $sqlRequest = "INSERT INTO stock_request_tbl(product_id,request_date,created_at,type,reference_id,status)
                    VALUES(?,?,NOW(),'Rental',?,'PENDING')";

                    $stmtRequest = $this->dbResult->prepare($sqlRequest);
                    $stmtRequest->bind_param(
                        "ssi",
                        $product['product_id'],
                        $booking_date,
                        $bookingItemId
                    );
                    $stmtRequest->execute();
                    $stmtRequest->close();

                    continue;
                }else{
                    $item = $res->fetch_assoc();
                    $stmt->close();
                    $sqlBookItem = "INSERT INTO booking_item_tbl(booking_id,product_id,product_item_id,qty,status) VALUES(?,?,?,?, 'BOOKED')";
                    $stmt = $this->dbResult->prepare($sqlBookItem);
                    $qty = 1;

                    $stmt->bind_param("sssi",$bookingId,$product['product_id'],$item['id'],$qty);
                    $itemSave = $stmt->execute();
                    if(!$itemSave){
                        $stmt->close();
                        return json_encode([
                        "status"=>"error",
                        "message"=>"Booking Item Save Failed"
                        ]);
                    }
                    $stmt->close();
                }

            }
        }
        $recipts="one";
        if($collection_type == "OTHER_HOLD"){$recipts="two";}
        $barcode='';
            do{
                $barcode=date('ymdHis').rand(1000,9999);
                $sqlCheck="SELECT id FROM barcode_tbl WHERE barcode=?";
                $stmt=$this->dbResult->prepare($sqlCheck);
                $stmt->bind_param("s",$barcode);
                $stmt->execute();
                $checkResult=$stmt->get_result();
                $exists=$checkResult->num_rows>0;
                $stmt->close();

            }while($exists);
                $sqlBarcode="INSERT INTO barcode_tbl(linked_id,type,barcode) VALUES(?,?,?)";
                $stmt=$this->dbResult->prepare($sqlBarcode);
                $type="BOOKING";
                $stmt->bind_param("sss",$bookingId,$type,$barcode);
                $barcodeSave=$stmt->execute();
                $stmt->close();
        return json_encode([
            "status"=>"success",
            "booking_id"=>$bookingId,
            "printtype"=>$recipts
        ]);
}

//load all booking details for booking recipt
public function getBooking($booking_id){

    $sqlBooking = "SELECT 
    booking_tbl.*,
    customer_tbl.customer_name,
    customer_tbl.phone,
    customer_tbl.nic,
    station_tbl.name AS station_name,
    employer_tbl.emp_FirstName,
    employer_tbl.emp_SecondName,
    barcode_tbl.barcode

    FROM booking_tbl

    LEFT JOIN customer_tbl
    ON customer_tbl.id = booking_tbl.customer_id

    LEFT JOIN station_tbl
    ON station_tbl.id = booking_tbl.station_id

    LEFT JOIN employer_tbl
    ON employer_tbl.emp_Id = booking_tbl.created_by

    LEFT JOIN barcode_tbl
    ON barcode_tbl.linked_id=booking_tbl.id
    AND barcode_tbl.type='BOOKING'

    WHERE booking_tbl.id=?";

    $stmt = $this->dbResult->prepare($sqlBooking);

    $stmt->bind_param("s",$booking_id);

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows <= 0){

        return json_encode([
        "status"=>"error",
        "message"=>"Booking Not Found"
        ]);

    }

    $booking = $result->fetch_assoc();

    $stmt->close();

    $sqlItems="SELECT booking_item_tbl.product_id,product_tbl.product_name,product_tbl.product_code,product_tbl.product_image,COUNT(*) AS qty FROM booking_item_tbl LEFT JOIN product_tbl ON product_tbl.id=booking_item_tbl.product_id WHERE booking_item_tbl.booking_id=? GROUP BY booking_item_tbl.product_id";
    

    $stmt=$this->dbResult->prepare($sqlItems);

    $stmt->bind_param("s",$booking_id);

    $stmt->execute();

    $result=$stmt->get_result();

    $items=[];

    while($row=$result->fetch_assoc()){

        $items[]=$row;

    }

    $stmt->close();

    $user = $_SESSION['user'];

        $sqlStation = "SELECT station_tbl.name,station_tbl.address,station_tbl.contact_no
        FROM login_tbl
        LEFT JOIN station_tbl ON station_tbl.id=login_tbl.station
        WHERE login_tbl.loginId=?";

        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $resStation = $stmt->get_result();

        $stationData = [];

        if($resStation->num_rows > 0){
            $stationData = $resStation->fetch_assoc();
        }

        $stmt->close();

    return json_encode([
    "status"=>"success",
    "booking"=>$booking,
    "items"=>$items,
    "station"=>$stationData
    ]);

}

//load pending booking list
public function pendingBookingList($date){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic FROM booking_tbl JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id 
        WHERE (booking_tbl.booking_date=? OR booking_tbl.return_date=? OR (? BETWEEN booking_tbl.booking_date AND booking_tbl.return_date)) AND (booking_tbl.status='READY' OR booking_tbl.status='BOOKED')
        AND booking_tbl.station_id=? AND booking_tbl.d_status=0 ORDER BY booking_tbl.created_at DESC";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("ssss",$date,$date,$date,$station);
        $stmt->execute();
        $result=$stmt->get_result();
        $table="";
        $all=0;
        $pending=0;
        $ready=0;

        while($rec=$result->fetch_assoc()){

            $all++;
            $statusBadge='';

            if($rec['status']=="BOOKED"){
                    $pending++;
                if(date('Y-m-d') > $rec['return_date']){
                    $statusBadge='<span class="badge bg-danger">BOOKING EXPIRED</span>';
                }else if(date('Y-m-d') >= $rec['booking_date']){
                    $statusBadge='<span class="badge bg-info">BOOKING PERIOD</span>';
                }else{
                    $statusBadge='<span class="badge bg-warning text-dark">PENDING</span>';
                }
            }else if($rec['status']=="READY"){
                    $ready++;
                if(date('Y-m-d') > $rec['return_date']){
                    $statusBadge='<span class="badge bg-danger">BOOKING EXPIRED</span>';
                }else if(date('Y-m-d') >= $rec['booking_date']){
                    $statusBadge='<span class="badge bg-info">BOOKING PERIOD</span>';
                }else{
                    $statusBadge='<span class="badge bg-warning text-dark">READY</span>';
                }
               
            }else{
                $statusBadge='<span class="badge bg-primary">'.$rec['status'].'</span>';
            }

            $otherCustomer = '';

            if($rec['collection_type'] == 'OTHER_HOLD'){

                $otherCustomer = '
                <div class="card border-danger mt-2">
                    <div class="card-header bg-danger text-white py-1">
                        <small><b>Collected By Another Customer</b></small>
                    </div>
                    <div class="card-body p-2">
                        <small>
                            <b>Name :</b> '.$rec['other_customer_name'].'<br>
                            <b>Phone :</b> '.$rec['other_customer_phone'].'<br>
                            <b>NIC :</b> '.$rec['other_customer_nic'].'
                        </small>
                    </div>
                </div>';
            }

            $table.='<tr><td>
                '.$rec['id'].'
                </td>
                <td>
                '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                </td>
                <td>
                <b>From :</b> '.$rec['booking_date'].'<br>
                <b>To :</b> '.$rec['return_date'].'
                </td>
                <td>
                <b>'.$rec['customer_name'].'</b><br>
                '.$rec['phone'].'<br>
                '.$rec['nic'].'<br>'.$otherCustomer.'
                </td>
                <td>
                    <div class="border border-success rounded p-2 mb-2 bg-light">
                        <b>Total :</b> '.number_format($rec['booking_amount'],2).'<br>
                        <b>Advance :</b> '.number_format($rec['advance_amount'],2).'<br>
                        <b>Balance :</b> '.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'
                    </div>

                    <div class="border border-warning rounded p-2">
                        <b>Hold :</b> '.number_format($rec['hold_amount'],2).'<br>
                        <b>Advance :</b> '.number_format($rec['hold_payed_amount'],2).'<br>
                        <b>Balance :</b> '.number_format(($rec['hold_amount']-$rec['hold_payed_amount']),2).'
                    </div>
                </td>
                <td>
                '.$statusBadge.'
                </td>
                <td>
                <button 
                type="button"
                class="btn btn-info btn-sm mb-1 btn-view-booking"
                data-id="'.$rec['id'].'">
                View
                </button> <button 
                type="button"
                class="btn btn-warning btn-sm mb-1 btn-print-barcode"
                data-id="'.$rec['id'].'">
                Barcode
                </button>';
                if($rec['collection_type']=="OTHER_HOLD"){
                     $table.=' <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint"
                            data-id="'.$rec['id'].'">
                            Booking Receipt
                            </button>
                            <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint2"
                            data-id="'.$rec['id'].'">
                            Receipt 2
                            </button>';
                }else{
                    $table.=' <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint"
                            data-id="'.$rec['id'].'">
                            Booking Receipt
                            </button>';
                }
                
                if(
                        $rec['status']=="BOOKED" &&
                        date('Y-m-d') < $rec['booking_date']
                    ){

                        $table.='

                        <button 
                        type="button"
                        class="btn btn-danger btn-cancel btn-sm mb-1"
                        data-id="'.$rec['id'].'">
                        Cancel Booking
                        </button>

                        <button 
                        type="button"
                        class="btn btn-warning btn-edit btn-sm mb-1"
                        data-id="'.$rec['id'].'">
                        Edit
                        </button>

                        <button 
                        type="button"
                        class="btn btn-success btn-ready btn-sm mb-1"
                        data-id="'.$rec['id'].'">
                        Ready
                        </button>';

                    }else if($rec['status']=="BOOKED" && date('Y-m-d') >= $rec['booking_date']){
                        $table.='
                        <button 
                        type="button"
                        class="btn btn-success btn-ready btn-sm mb-1"
                        data-id="'.$rec['id'].'">
                        Ready
                        </button>';
                    }else if($rec['status']=="READY" &&  date('Y-m-d') <= $rec['return_date']){
                        $table.='
                        <button 
                        type="button"
                        class="btn btn-success btn-rentout btn-sm mb-1"
                        data-id="'.$rec['id'].'">
                        Rentout
                        </button>';
                    }
                    else if($rec['status']=="RENTED" && $rec['collection_type'] == 'OTHER_HOLD'){
                        $table.='
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                            data-id="'.$rec['id'].'">
                            Rented Receipt
                            </button>
                            <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent2"
                            data-id="'.$rec['id'].'">
                            Rented Receipt 2
                            </button>';
                    }else if($rec['status']=="RENTED" && $rec['collection_type'] != 'OTHER_HOLD'){
                        $table.='
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                            data-id="'.$rec['id'].'">
                            Rented Receipt
                            </button>';
                    }

                $table.='</td></tr>';
        }

        $stmt->close();

        echo json_encode([
        'table'=>$table,
        'all'=>$all,
        'pending'=>$pending,
        'ready'=>$ready
        ]);

}

//search barcode in pening booking for barcode
public function searchBarcodeBooking($barcode){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic FROM barcode_tbl JOIN booking_tbl ON booking_tbl.id=barcode_tbl.linked_id JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id WHERE barcode_tbl.barcode=? AND barcode_tbl.type='BOOKING' LIMIT 1";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$barcode);
    $stmt->execute();

    $result=$stmt->get_result();

    $table="";

       while($rec=$result->fetch_assoc()){

            if($rec['status']=="RENTED"){
                        echo json_encode([
                            'status'=>false,
                            'message'=>'This booking is already rented out.'
                        ]);
            }else if($rec['status']=="RETURND"){
                        echo json_encode([
                            'status'=>false,
                            'message'=>'This booking has already been returned.'
                        ]);
            }else if($rec['status']=="CANCEL"){
                echo json_encode([
                        'status'=>false,
                        'message'=>'This booking has been cancelled.'
                    ]);
            }else{
                $statusBadge='';

                    if($rec['status']=="BOOKED"){
                        if(date('Y-m-d') > $rec['return_date']){
                            $statusBadge='<span class="badge bg-danger">BOOKING EXPIRED</span>';
                        }else if(date('Y-m-d') >= $rec['booking_date']){
                            $statusBadge='<span class="badge bg-info">BOOKING PERIOD</span>';
                        }else{
                            $statusBadge='<span class="badge bg-warning text-dark">PENDING</span>';
                        }
                    }else if($rec['status']=="READY"){
                        if(date('Y-m-d') > $rec['return_date']){
                            $statusBadge='<span class="badge bg-danger">BOOKING EXPIRED</span>';
                        }else if(date('Y-m-d') >= $rec['booking_date']){
                            $statusBadge='<span class="badge bg-info">BOOKING PERIOD</span>';
                        }else{
                            $statusBadge='<span class="badge bg-warning text-dark">READY</span>';
                        }
                    
                    }else{
                        $statusBadge='<span class="badge bg-primary">'.$rec['status'].'</span>';
                    }

                    $otherCustomer = '';

                    if($rec['collection_type'] == 'OTHER_HOLD'){

                        $otherCustomer = '
                        <div class="card border-danger mt-2">
                            <div class="card-header bg-danger text-white py-1">
                                <small><b>Collected By Another Customer</b></small>
                            </div>
                            <div class="card-body p-2">
                                <small>
                                    <b>Name :</b> '.$rec['other_customer_name'].'<br>
                                    <b>Phone :</b> '.$rec['other_customer_phone'].'<br>
                                    <b>NIC :</b> '.$rec['other_customer_nic'].'
                                </small>
                            </div>
                        </div>';
                    }

                    $table.='<tr><td>
                        '.$rec['id'].'
                        </td>
                        <td>
                        '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                        </td>
                        <td>
                        <b>From :</b> '.$rec['booking_date'].'<br>
                        <b>To :</b> '.$rec['return_date'].'
                        </td>
                        <td>
                        <b>'.$rec['customer_name'].'</b><br>
                        '.$rec['phone'].'<br>
                        '.$rec['nic'].'<br>'.$otherCustomer.'
                        </td>
                        <td>
                            <div class="border border-success rounded p-2 mb-2 bg-light">
                                <b>Total :</b> '.number_format($rec['booking_amount'],2).'<br>
                                <b>Advance :</b> '.number_format($rec['advance_amount'],2).'<br>
                                <b>Balance :</b> '.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'
                            </div>

                            <div class="border border-warning rounded p-2">
                                <b>Hold :</b> '.number_format($rec['hold_amount'],2).'<br>
                                <b>Advance :</b> '.number_format($rec['hold_payed_amount'],2).'<br>
                                <b>Balance :</b> '.number_format(($rec['hold_amount']-$rec['hold_payed_amount']),2).'
                            </div>
                        </td>
                        <td>
                        '.$statusBadge.'
                        </td>
                        <td>
                        <button 
                        type="button"
                        class="btn btn-info btn-sm mb-1 btn-view-booking"
                        data-id="'.$rec['id'].'">
                        View
                        </button> <button 
                        type="button"
                        class="btn btn-warning btn-sm mb-1 btn-print-barcode"
                        data-id="'.$rec['id'].'">
                        Barcode
                        </button>';
                        if($rec['collection_type']=="OTHER_HOLD"){
                            $table.=' <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint"
                                    data-id="'.$rec['id'].'">
                                    Booking Receipt
                                    </button>
                                    <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint2"
                                    data-id="'.$rec['id'].'">
                                    Receipt 2
                                    </button>';
                        }else{
                            $table.=' <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint"
                                    data-id="'.$rec['id'].'">
                                    Booking Receipt
                                    </button>';
                        }
                        
                        if(
                                $rec['status']=="BOOKED" &&
                                date('Y-m-d') < $rec['booking_date']
                            ){

                                $table.='

                                <button 
                                type="button"
                                class="btn btn-danger btn-cancel btn-sm mb-1"
                                data-id="'.$rec['id'].'">
                                Cancel Booking
                                </button>

                                <button 
                                type="button"
                                class="btn btn-warning btn-edit btn-sm mb-1"
                                data-id="'.$rec['id'].'">
                                Edit
                                </button>

                                <button 
                                type="button"
                                class="btn btn-success btn-ready btn-sm mb-1"
                                data-id="'.$rec['id'].'">
                                Ready
                                </button>';

                            }else if($rec['status']=="BOOKED" && date('Y-m-d') >= $rec['booking_date']){
                                $table.='
                                <button 
                                type="button"
                                class="btn btn-success btn-ready btn-sm mb-1"
                                data-id="'.$rec['id'].'">
                                Ready
                                </button>';
                            }else if($rec['status']=="READY" &&  date('Y-m-d') <= $rec['return_date']){
                                $table.='
                                <button 
                                type="button"
                                class="btn btn-success btn-rentout btn-sm mb-1"
                                data-id="'.$rec['id'].'">
                                Rentout
                                </button>';
                            }
                            else if($rec['status']=="RENTED" && $rec['collection_type'] == 'OTHER_HOLD'){
                                $table.='
                                <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt
                                    </button>
                                    <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent2"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt 2
                                    </button>';
                            }else if($rec['status']=="RENTED" && $rec['collection_type'] != 'OTHER_HOLD'){
                                $table.='
                                <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt
                                    </button>';
                            }

                        $table.='</td></tr>';

                         $stmt->close();

                    echo json_encode([
                        'status'=>true,
                        'table'=>$table
                        ]);
                    }

               
       }

}

// READY BOOKING
public function readyBooking($booking_id,$redyby){

    $sql="UPDATE booking_tbl SET status='READY',readyby=?,readyat=NOW() WHERE id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("ss",$redyby,$booking_id);
    $sqlResult=$stmt->execute();
    $stmt->close();
    if($sqlResult>0){
        return("01");
    }else{
        return("02");
    }
}

//canscel booking
public function cancelBooking($booking_id,$cancelby){

    $sql="UPDATE booking_tbl SET status='CANCEL',cancelby=?,cancelat=NOW() WHERE id=?";
    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("ss",$cancelby,$booking_id);
    $sqlResult=$stmt->execute();
    $stmt->close();
    if($sqlResult>0){
        return("01");
    }else{
        return("02");
    }
}

//booking details view option for redy page
public function viewBooking($booking_id){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic,customer_tbl.address FROM booking_tbl JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id WHERE booking_tbl.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows<=0){
        return;
    }

    $rec=$result->fetch_assoc();

    $stmt->close();

    $days=(strtotime($rec['return_date'])-strtotime($rec['booking_date']))/86400+1;

    echo('
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booking Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Booking ID</th>
                                <td>'.$rec['id'].'</td>
                            </tr>
                            <tr>
                                <th>Booked Date Time</th>
                                <td>'.date('Y-m-d h:i A',strtotime($rec['created_at'])).'</td>
                            </tr>
                            <tr>
                                <th>Booking From</th>
                                <td>'.$rec['booking_date'].'</td>
                            </tr>
                            <tr>
                                <th>Return Date</th>
                                <td>'.$rec['return_date'].'</td>
                            </tr>
                            <tr>
                                <th>Total Days</th>
                                <td>'.$days.' Days</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-warning">'.$rec['status'].'</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Remarks</th>
                                <td>'.nl2br($rec['remarks']).'</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Customer Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Customer</th>
                                <td>'.$rec['customer_name'].'</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>'.$rec['phone'].'</td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>'.$rec['nic'].'</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>'.$rec['address'].'</td>
                            </tr>
                        </table>
                    ');
            if($rec['collection_type'] != 'SELF'){

                echo('
                <div class="mt-3">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <strong>Collected By Another Customer</strong>
                        </div>

                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Name</th>
                                    <td>'.$rec['other_customer_name'].'</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>'.$rec['other_customer_phone'].'</td>
                                </tr>
                                <tr>
                                    <th>NIC</th>
                                    <td>'.$rec['other_customer_nic'].'</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>');
            }
        echo('</div>
                </div>
            </div></div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Payment Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Booking Amount</th>
                                <td>'.number_format($rec['booking_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Advance Amount</th>
                                <td>'.number_format($rec['advance_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Balance Amount</th>
                                <td>'.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'</td>
                            </tr>
                             <tr>
                                <th>Payment Method</th>
                                <td>'.$rec['payment_method'].'</td>
                            </tr>
                            <tr>
                                <th>Hold Amount</th>
                                <td>'.number_format($rec['hold_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Hold Paid Amount</th>
                                <td>'.number_format($rec['hold_payed_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Hold Balance Amount</th>
                                <td>');
                            if($rec['hold_amount'] > $rec['hold_payed_amount']){

                                echo '<span class="badge bg-danger fs-6">'.
                                number_format(
                                    $rec['hold_amount'] - $rec['hold_payed_amount'],
                                    2
                                ).
                                '</span>';
                            }else{
                                echo '<span class="badge bg-success fs-6">0.00</span>';
                            }
                            echo '</td>
                            </tr>
                            <tr>
                                <th>Hold Amount Type</th>
                                <td>'.$rec['hold_amount_type'].'</td>
                            </tr>';
                            echo('
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booked Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th width="80">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>');
                                $sqlItems="SELECT product_tbl.product_name,product_tbl.product_code,product_tbl.product_image,COUNT(*) AS qty FROM booking_item_tbl JOIN product_tbl ON product_tbl.id=booking_item_tbl.product_id WHERE booking_item_tbl.booking_id=? GROUP BY booking_item_tbl.product_id";
                                $stmt=$this->dbResult->prepare($sqlItems);
                                $stmt->bind_param("s",$booking_id);
                                $stmt->execute();
                                $items=$stmt->get_result();
                                while($item=$items->fetch_assoc()){
                                    $image="../../assets/ui/noimage.jpg";
                                    if($item['product_image']!='' && file_exists(__DIR__.'/../uploads/product/'.$item['product_image'])){
                                        $image='../uploads/product/'.$item['product_image'];
                                    }
                                    echo('
                                                                <tr class="booking-item-row">
                                                                    <td>
                                                                        <img src="'.$image.'" style="width:55px;height:55px;object-fit:cover;border-radius:10px;">
                                                                    </td>
                                                                    <td>'.$item['product_name'].'</td>
                                                                    <td>'.$item['product_code'].'</td>
                                                                    <td>'.$item['qty'].'</td>
                                                                </tr>');
                                }
                                $stmt->close();
                                echo('
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');

}

//load rentout booking details for rentout page
public function viewBookingrentout($booking_id){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic,customer_tbl.address FROM booking_tbl JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id WHERE booking_tbl.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows<=0){
        return;
    }

    $rec=$result->fetch_assoc();

    $stmt->close();

    $days=(strtotime($rec['return_date'])-strtotime($rec['booking_date']))/86400+1;

    echo('
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mb-3" >
                <div class="card border-0 shadow-sm h-100" style="background:#fff5f5;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booking Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Booking ID</th>
                                <td>'.$rec['id'].'</td>
                            </tr>
                            <tr>
                                <th>Booked Date Time</th>
                                <td>'.date('Y-m-d h:i A',strtotime($rec['created_at'])).'</td>
                            </tr>
                            <tr>
                                <th>Booking From</th>
                                <td>'.$rec['booking_date'].'</td>
                            </tr>
                            <tr>
                                <th>Return Date</th>
                                <td>'.$rec['return_date'].'</td>
                            </tr>
                            <tr>
                                <th>Total Days</th>
                                <td>'.$days.' Days</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-warning">'.$rec['status'].'</span>
                                </td>
                            </tr>
                             <tr>
                                <th>Remarks</th>
                                <td>'.nl2br($rec['remarks']).'</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background:#f0fff4;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Customer Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Customer</th>
                                <td>'.$rec['customer_name'].'</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>'.$rec['phone'].'</td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>'.$rec['nic'].'</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>'.$rec['address'].'</td>
                            </tr>
                        </table>
                        ');
            if($rec['collection_type'] != 'SELF'){

                echo('
                <div class="mt-3">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <strong>Collected By Another Customer</strong>
                        </div>

                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Name</th>
                                    <td>'.$rec['other_customer_name'].'</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>'.$rec['other_customer_phone'].'</td>
                                </tr>
                                <tr>
                                    <th>NIC</th>
                                    <td>'.$rec['other_customer_nic'].'</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>');
            }
        echo('</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3" >
                <div class="card border-0 shadow-sm h-100" style="background:#eef7ff;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Payment Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Booking Amount</th>
                                <td>'.number_format($rec['booking_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Advance Amount</th>
                                <td>'.number_format($rec['advance_amount'],2).'</td>
                            </tr>
                            <tr style="background:#ff6347;color:white;font-weight:700;">
                                <th style="background:#ff6347;color:white;font-weight:700;">
                                    Balance Amount
                                </th>
                                <td style="background:#ff6347;color:white;font-weight:700;">
                                    '.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td>'.$rec['payment_method'].'</td>
                            </tr>
                            <tr>
                                <th>Hold Amount</th>
                                <td>'.number_format($rec['hold_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Hold Paid Amount</th>
                                <td>'.number_format($rec['hold_payed_amount'],2).'</td>
                            </tr>
                            <tr style="background:#ffc107;color:black;font-weight:700;">
                                <th style="background:#ffc107;color:black;font-weight:700;">
                                    Hold Balance Amount
                                </th>
                                <td style="background:#ffc107;color:black;font-weight:700;">'.
                                    number_format(
                                        max(0,$rec['hold_amount']-$rec['hold_payed_amount']),
                                        2
                                    ).'
                                </td>
                            </tr>
                            <tr>
                                <th>Hold Amount Type</th>
                                <td>'.$rec['hold_amount_type'].'</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background:#fffdf0;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booked Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th width="80">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>');
                                    $sqlItems="SELECT product_tbl.product_name,product_tbl.product_code,product_tbl.product_image,COUNT(*) AS qty FROM booking_item_tbl JOIN product_tbl ON product_tbl.id=booking_item_tbl.product_id WHERE booking_item_tbl.booking_id=? GROUP BY booking_item_tbl.product_id";
                                    $stmt=$this->dbResult->prepare($sqlItems);
                                    $stmt->bind_param("s",$booking_id);
                                    $stmt->execute();
                                    $items=$stmt->get_result();
                                    while($item=$items->fetch_assoc()){
                                        $image="../../assets/ui/noimage.jpg";
                                        if($item['product_image']!='' && file_exists(__DIR__.'/../uploads/product/'.$item['product_image'])){
                                            $image='../uploads/product/'.$item['product_image'];
                                        }
                                        echo('
                                                                    <tr class="booking-item-row">
                                                                        <td>
                                                                            <img src="'.$image.'" style="width:55px;height:55px;object-fit:cover;border-radius:10px;">
                                                                        </td>
                                                                        <td>'.$item['product_name'].'</td>
                                                                        <td>'.$item['product_code'].'</td>
                                                                        <td>'.$item['qty'].'</td>
                                                                    </tr>');
                                    }
                                    $stmt->close();
                                    echo('
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');
}

//load return booking details
public function viewBookingreturn($booking_id){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic,customer_tbl.address FROM booking_tbl JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id WHERE booking_tbl.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows<=0){
        return;
    }

    $rec=$result->fetch_assoc();

    $stmt->close();

    $days=(strtotime($rec['return_date'])-strtotime($rec['booking_date']))/86400+1;

    $lateDays = 0;

    $returnDate = strtotime($rec['return_date']);
    $today = strtotime(date('Y-m-d'));

    if($today > $returnDate){
        $lateDays = floor(($today - $returnDate) / 86400);
    }

    echo('
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 mb-3" >
                <div class="card border-0 shadow-sm h-100" style="background:#fff5f5;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booking Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Booking ID</th>
                                <td>'.$rec['id'].'</td>
                            </tr>
                            <tr>
                                <th>Booked Date Time</th>
                                <td>'.date('Y-m-d h:i A',strtotime($rec['created_at'])).'</td>
                            </tr>
                            <tr>
                                <th>Booking From</th>
                                <td>'.$rec['booking_date'].'</td>
                            </tr>
                            <tr>
                                <th>Return Date</th>
                                <td>'.$rec['return_date'].'</td>
                            </tr>
                            <tr>
                                <th>Total Days</th>
                                <td>'.$days.' Days</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-warning">'.$rec['status'].'</span>
                                </td>
                            </tr>
                             <tr>
                                <th>Remarks</th>
                                <td>'.nl2br($rec['remarks']).'</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background:#f0fff4;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Customer Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="180">Customer</th>
                                <td>'.$rec['customer_name'].'</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>'.$rec['phone'].'</td>
                            </tr>
                            <tr>
                                <th>NIC</th>
                                <td>'.$rec['nic'].'</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>'.$rec['address'].'</td>
                            </tr>
                        </table> ');
            if($rec['collection_type'] != 'SELF'){

                echo('
                <div class="mt-3">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <strong>Collected By Another Customer</strong>
                        </div>

                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Name</th>
                                    <td>'.$rec['other_customer_name'].'</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>'.$rec['other_customer_phone'].'</td>
                                </tr>
                                <tr>
                                    <th>NIC</th>
                                    <td>'.$rec['other_customer_nic'].'</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>');
            }
        echo('</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3" >
                <div class="card border-0 shadow-sm h-100" style="background:#eef7ff;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Payment Details</h6>
                       <table class="table table-sm">
                            <tr>
                                <th width="180">Booking Amount</th>
                                <td>'.number_format($rec['booking_amount'],2).'</td>
                            </tr>
                           
                            <tr>
                                <th>Payment Method</th>
                                <td>'.$rec['payment_method'].'</td>
                            </tr>
                            <tr style="background:#ffc107;color:black;font-weight:700;">
                                <th style="background:#ffc107;color:black;font-weight:700;" >Hold Amount</th>
                                <td style="background:#ffc107;color:black;font-weight:700;">'.number_format($rec['hold_amount'],2).'</td>
                            </tr>
                            <tr>
                                <th>Hold Amount Type</th>
                                <td>'.$rec['hold_amount_type'].'</td>
                            </tr>');
                             if($rec['hold_amount_type']=='Bank Transfer' && trim($rec['bank_details'])!=''){

                                echo '
                                 <tr><td colspan="2">
                                <div class="border rounded p-2 bg-light mt-2">
                                    <b>Bank Details</b><br>
                                    '.nl2br($rec['bank_details']).'
                                </div></td></tr>';
                            };
                            echo('
                        </table>
                    </div>
                    <div class=" p-3 rounded mb-3" style="border:2px solid #ffc107;background:#fffaf0;">

                        <h6 class="fw-bold text-warning mb-3">
                            Late Return Penalty
                        </h6>

                        <div class="row">

                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    Late Days
                                </label>

                                <input
                                type="number"
                                class="form-control"
                                id="late_days"
                                value="'.$lateDays.'"
                                readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    One Day Penalty
                                </label>

                                <input
                                type="number"
                                class="form-control"
                                id="one_day_penalty"
                                value="2000"
                                min="0"
                                step="0.01">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    Total Penalty
                                </label>

                                <input
                                type="number"
                                class="form-control"
                                id="total_penalty"
                                value="0"
                                readonly>
                            </div>

                        </div>

                    </div>

                    <div class="mt-3 p-3 rounded" style="border:2px solid #ff6347;background:#fff5f5;">

                    <h6 class="fw-bold text-danger mb-3">
                        Return Inspection Notice
                    </h6>

                    <div class="mb-3">

                        <label class="form-label text-danger fw-bold">
                            Missing Items / Damages / Remarks
                        </label>

                        <textarea 
                        class="form-control border-danger"
                        id="damage_note"
                        rows="4"
                        placeholder="Please note any missing items, damages, defects, scratches, broken parts, lost items, or other observations. If all items are in good condition, leave this field blank."></textarea>

                    </div>

                    <div class="mb-2">

                        <label class="form-label text-danger fw-bold">
                            Claim Amount
                        </label>

                        <input 
                        type="number"
                        class="form-control border-danger"
                        id="claim_amount"
                        value="0"
                        min="0"
                        step="0.01"
                        placeholder="Enter claim amount if applicable">

                    </div>

                    <small class="text-danger fw-bold">
                        If all rented items are returned in good condition with no damages or missing items, leave the above fields blank or keep Claim Amount as 0.00.
                    </small>

                </div>
                <hr>

                <div class="row">

                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Hold Amount Available
                        </label>

                        <input
                        type="text"
                        class="form-control"
                        id="hold_amount_available"
                        value="'.($rec['hold_amount']).'"
                        readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Hold Balance After Deductions
                        </label>

                        <input
                        type="text"
                        class="form-control fw-bold"
                        id="final_hold_balance"
                        readonly>
                    </div>

                </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background:#fffdf0;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Booked Items</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th width="80">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>');

                                $sqlItems="SELECT product_tbl.product_name,product_tbl.product_code,product_tbl.product_image,COUNT(*) AS qty FROM booking_item_tbl JOIN product_tbl ON product_tbl.id=booking_item_tbl.product_id WHERE booking_item_tbl.booking_id=? GROUP BY booking_item_tbl.product_id";

                                $stmt=$this->dbResult->prepare($sqlItems);
                                $stmt->bind_param("s",$booking_id);
                                $stmt->execute();

                                $items=$stmt->get_result();

                                while($item=$items->fetch_assoc()){

                                    $image="../../assets/ui/noimage.jpg";

                                    if($item['product_image']!='' && file_exists(__DIR__.'/../uploads/product/'.$item['product_image'])){
                                        $image='../uploads/product/'.$item['product_image'];
                                    }

                                    echo('
                                                                <tr class="booking-item-row">
                                                                    <td>
                                                                        <img src="'.$image.'" style="width:55px;height:55px;object-fit:cover;border-radius:10px;">
                                                                    </td>
                                                                    <td>'.$item['product_name'].'</td>
                                                                    <td>'.$item['product_code'].'</td>
                                                                    <td>'.$item['qty'].'</td>
                                                                </tr>');
                                }

                                $stmt->close();

                                echo('
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');

}

//rentout complete
public function rentoutBooking($booking_id,$handoverby,$balance_payment,$hold_balance_payment){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

    $sqlBooking="SELECT booking_amount,advance_amount,balance_payed_amount FROM booking_tbl WHERE id=?";

    $stmt=$this->dbResult->prepare($sqlBooking);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $rec=$stmt->get_result()->fetch_assoc();
    $stmt->close();

    $sql="UPDATE booking_tbl
    SET status='RENTED', handoverby=?, handoverat=NOW(), balance_payed_amount=IFNULL(balance_payed_amount,0)+?, hold_balance_payed_amount=IFNULL(hold_balance_payed_amount,0)+?
     WHERE id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param( "sdds", $handoverby, $balance_payment, $hold_balance_payment, $booking_id );
    $sqlResult=$stmt->execute();

    $stmt->close();

    if(!$sqlResult){
        return("02");
    }

    if($balance_payment>0){
        $part='Balance Payment';

        $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('RENTAL',?,?,?,?,?)";
        $stmt=$this->dbResult->prepare($sqlIncome);
        $stmt->bind_param("ssdss",$booking_id,$part,$balance_payment,$handoverby,$station);

        $stmt->execute();
        $stmt->close();
    }
    return("01");
}

//load pending rented list
public function pendingReturnList(){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic
        FROM booking_tbl
        JOIN customer_tbl
        ON customer_tbl.id=booking_tbl.customer_id
        WHERE booking_tbl.status='RENTED'
        AND booking_tbl.d_status=0
        AND booking_tbl.station_id=?
        ORDER BY booking_tbl.return_date ASC";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$station);
        $stmt->execute();

        $result=$stmt->get_result();
        $table="";
        $all=0;
        $pending=0;
        $overdue=0;

        while($rec=$result->fetch_assoc()){

            $all++;
            $statusBadge='';

            if($rec['return_date']<date('Y-m-d')){
                $statusBadge='<span class="badge bg-danger">OVERDUE</span>';
                $overdue++;

            }else if($rec['return_date']==date('Y-m-d')){
                $statusBadge='<span class="badge bg-warning text-dark">RETURN TODAY</span>';
                $pending++;
            }else{
                $statusBadge='<span class="badge bg-success">RENTED</span>';

            }

             $otherCustomer = '';

            if($rec['collection_type'] == 'OTHER_HOLD'){

                $otherCustomer = '
                <div class="card border-danger mt-2">
                    <div class="card-header bg-danger text-white py-1">
                        <small><b>Collected By Another Customer</b></small>
                    </div>
                    <div class="card-body p-2">
                        <small>
                            <b>Name :</b> '.$rec['other_customer_name'].'<br>
                            <b>Phone :</b> '.$rec['other_customer_phone'].'<br>
                            <b>NIC :</b> '.$rec['other_customer_nic'].'
                        </small>
                    </div>
                </div>';
            }

            $table.='<tr><td>
                '.$rec['id'].'
                </td>
                <td>
                '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                </td>
                <td style="color:red;">
                <b>From :</b> '.$rec['booking_date'].'<br>
                <b >To :</b> '.$rec['return_date'].'
                </td>
                <td>
                <b>'.$rec['customer_name'].'</b><br>
                '.$rec['phone'].'<br>
                '.$rec['nic'].'<br>'.$otherCustomer.'
                </td>
                 <td>
                    <div class="border border-success rounded p-2 mb-2 bg-light">
                        <b>Total :</b> '.number_format($rec['booking_amount'],2).'<br>
                        <b>Advance :</b> '.number_format($rec['advance_amount'],2).'<br>
                        <b>Balance :</b> '.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'
                    </div>

                    <div class="border border-warning rounded p-2">
                        <b>Hold :</b> '.number_format($rec['hold_amount'],2).'<br>
                        <b>Advance :</b> '.number_format($rec['hold_payed_amount'],2).'<br>
                        <b>Balance :</b> '.number_format(($rec['hold_amount']-$rec['hold_payed_amount']),2).'
                    </div>
                </td>
                <td>
                '.$statusBadge.'
                </td>
                <td>
                <button 
                type="button"
                class="btn btn-info btn-sm mb-1 btn-view-booking"
                data-id="'.$rec['id'].'">
                View
                </button>';

                if($rec['collection_type']=="OTHER_HOLD"){
                     $table.=' <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint"
                            data-id="'.$rec['id'].'">
                            Booking Receipt
                            </button>
                            <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint2"
                            data-id="'.$rec['id'].'">
                            Receipt 2
                            </button>';
                }else{
                    $table.=' <button 
                            type="button"
                            class="btn btn-secondary btn-sm mb-1 btn-reprint"
                            data-id="'.$rec['id'].'">
                            Booking Receipt
                            </button>';
                }
                 if($rec['status']=="RENTED" && $rec['collection_type'] == 'OTHER_HOLD'){
                        $table.='
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                            data-id="'.$rec['id'].'">
                            Rented Receipt
                            </button>
                            <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent2"
                            data-id="'.$rec['id'].'">
                            Rented Receipt 2
                            </button>';
                    }else if($rec['status']=="RENTED" && $rec['collection_type'] != 'OTHER_HOLD'){
                        $table.='
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                            data-id="'.$rec['id'].'">
                            Rented Receipt
                            </button>';
                    }
                if($rec['status']=="RENTED"){
                        $table.='
                        <button 
                            type="button"
                            class="btn btn-success btn-sm mb-1 btn-return"
                            data-id="'.$rec['id'].'">
                            Return
                            </button>';
                    }

                $table.='</td></tr>';
        }

        $stmt->close();

        echo json_encode([
        'table'=>$table,
        'all'=>$all,
        'pending'=>$pending,
        'overdue'=>$overdue
        ]);

}

//load pending rented using barcode
public function searchBarcoderentout($barcode){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic FROM barcode_tbl JOIN booking_tbl ON booking_tbl.id=barcode_tbl.linked_id JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id WHERE barcode_tbl.barcode=? AND barcode_tbl.type='BOOKING' LIMIT 1";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$barcode);
    $stmt->execute();

    $result=$stmt->get_result();

    $table="";

    while($rec=$result->fetch_assoc()){

            if($rec['status']=="BOOKED"){
                        echo json_encode([
                            'status'=>false,
                            'message'=>'This booking is still in befor redy period.'
                        ]);
            }else if($rec['status']=="READY"){
                        echo json_encode([
                            'status'=>false,
                            'message'=>'This booking is still in Pre-collect status.'
                        ]);
            }else if($rec['status']=="CANCEL"){
                echo json_encode([
                        'status'=>false,
                        'message'=>'This booking has been Cancelled.'
                    ]);
            }else if($rec['status']=="RETURND"){
                echo json_encode([
                        'status'=>false,
                        'message'=>'This booking has been Returnd.'
                    ]);
            }else{
                $statusBadge='';

                    if($rec['return_date']<date('Y-m-d')){
                        $statusBadge='<span class="badge bg-danger">OVERDUE</span>';
                    
                    }else if($rec['return_date']==date('Y-m-d')){
                        $statusBadge='<span class="badge bg-warning text-dark">RETURN TODAY</span>';
                    
                    }else{
                        $statusBadge='<span class="badge bg-success">RENTED</span>';

                    }

                    $otherCustomer = '';

                    if($rec['collection_type'] == 'OTHER_HOLD'){

                        $otherCustomer = '
                        <div class="card border-danger mt-2">
                            <div class="card-header bg-danger text-white py-1">
                                <small><b>Collected By Another Customer</b></small>
                            </div>
                            <div class="card-body p-2">
                                <small>
                                    <b>Name :</b> '.$rec['other_customer_name'].'<br>
                                    <b>Phone :</b> '.$rec['other_customer_phone'].'<br>
                                    <b>NIC :</b> '.$rec['other_customer_nic'].'
                                </small>
                            </div>
                        </div>';
                    }

                    $table.='<tr><td>
                        '.$rec['id'].'
                        </td>
                        <td>
                        '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                        </td>
                        <td style="color:red;">
                        <b>From :</b> '.$rec['booking_date'].'<br>
                        <b >To :</b> '.$rec['return_date'].'
                        </td>
                        <td>
                        <b>'.$rec['customer_name'].'</b><br>
                        '.$rec['phone'].'<br>
                        '.$rec['nic'].'<br>'.$otherCustomer.'
                        </td>
                        <td>
                            <div class="border border-success rounded p-2 mb-2 bg-light">
                                <b>Total :</b> '.number_format($rec['booking_amount'],2).'<br>
                                <b>Advance :</b> '.number_format($rec['advance_amount'],2).'<br>
                                <b>Balance :</b> '.number_format(($rec['booking_amount']-$rec['advance_amount']),2).'
                            </div>

                            <div class="border border-warning rounded p-2">
                                <b>Hold :</b> '.number_format($rec['hold_amount'],2).'<br>
                                <b>Advance :</b> '.number_format($rec['hold_payed_amount'],2).'<br>
                                <b>Balance :</b> '.number_format(($rec['hold_amount']-$rec['hold_payed_amount']),2).'
                            </div>
                        </td>
                        <td>
                        '.$statusBadge.'
                        </td>
                        <td>
                        <button 
                        type="button"
                        class="btn btn-info btn-sm mb-1 btn-view-booking"
                        data-id="'.$rec['id'].'">
                        View
                        </button>';

                        if($rec['collection_type']=="OTHER_HOLD"){
                            $table.=' <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint"
                                    data-id="'.$rec['id'].'">
                                    Booking Receipt
                                    </button>
                                    <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint2"
                                    data-id="'.$rec['id'].'">
                                    Receipt 2
                                    </button>';
                        }else{
                            $table.=' <button 
                                    type="button"
                                    class="btn btn-secondary btn-sm mb-1 btn-reprint"
                                    data-id="'.$rec['id'].'">
                                    Booking Receipt
                                    </button>';
                        }
                        if($rec['status']=="RENTED" && $rec['collection_type'] == 'OTHER_HOLD'){
                                $table.='
                                <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt
                                    </button>
                                    <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent2"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt 2
                                    </button>';
                            }else if($rec['status']=="RENTED" && $rec['collection_type'] != 'OTHER_HOLD'){
                                $table.='
                                <button 
                                    type="button"
                                    class="btn btn-danger btn-sm mb-1 btn-reprintrent"
                                    data-id="'.$rec['id'].'">
                                    Rented Receipt
                                    </button>';
                            }
                        if($rec['status']=="RENTED"){
                                $table.='
                                <button 
                                    type="button"
                                    class="btn btn-success btn-sm mb-1 btn-return"
                                    data-id="'.$rec['id'].'">
                                    Return
                                    </button>';
                            }

                        $table.='</td></tr>';

                         $stmt->close();

                        echo json_encode([
                            'status'=>true,
                            'table'=>$table
                        ]);
            }
           
        }
       
}

//returnd items
public function returnBooking($booking_id,$handoverby,$return_note,$claim_amount){

    $return_note=trim($return_note);
    $claim_amount=(double)$claim_amount;

    if($return_note=='' && $claim_amount<=0){

        $sql="UPDATE booking_tbl SET status='RETURND',collectby=?,collectat=NOW() WHERE id=?";
        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("ss",$handoverby,$booking_id);
        $sqlResult=$stmt->execute();
        $stmt->close();

    }else{

        $sql="UPDATE booking_tbl SET status='RETURND',collectby=?,collectat=NOW(),return_note=?,claim_amount=? WHERE id=?";
        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("ssds",$handoverby,$return_note,$claim_amount,$booking_id);
        $sqlResult=$stmt->execute();
        $stmt->close();

        if($sqlResult && $claim_amount>0){

            $user=$_SESSION['user'];

            $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
            $stmtStation=$this->dbResult->prepare($sqlStation);
            $stmtStation->bind_param("s",$user);
            $stmtStation->execute();
            $station=$stmtStation->get_result()->fetch_assoc()['station'];
            $stmtStation->close();

            $part=$return_note=='' ? 'Penalty Charge' : $return_note;

            $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('PENALTY',?,?,?,?,?)";
            $stmtIncome=$this->dbResult->prepare($sqlIncome);
            $stmtIncome->bind_param("ssdss",$booking_id,$part,$claim_amount,$handoverby,$station);
            $stmtIncome->execute();
            $stmtIncome->close();
        }
    }

    if($sqlResult){
        return "01";
    }else{
        return "02";
    }

}

//load rental summery
public function searchRentalSummary($barcode,$booking_date,$customer_id){

    $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

   $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,barcode_tbl.barcode
    FROM booking_tbl
    JOIN customer_tbl
    ON customer_tbl.id=booking_tbl.customer_id
    LEFT JOIN barcode_tbl
    ON barcode_tbl.linked_id=booking_tbl.id
    AND barcode_tbl.type='BOOKING'
    WHERE booking_tbl.d_status=0
    AND booking_tbl.station_id=?";

    $params=[$station];
    $types='s';

    if($barcode!=''){
        $sql.=" AND barcode_tbl.barcode=?";
        $types.="s";
        $params[]=$barcode;
    }

    if($booking_date!=''){
        $sql.=" AND ? BETWEEN booking_tbl.booking_date AND booking_tbl.return_date";
        $types.="s";
        $params[]=$booking_date;
    }

    if($customer_id!=''){
        $sql.=" AND booking_tbl.customer_id=?";
        $types.="s";
        $params[]=$customer_id;
    }

    $sql.=" ORDER BY booking_tbl.created_at DESC";
    $stmt=$this->dbResult->prepare($sql);
    if(count($params)>0){
        $stmt->bind_param($types,...$params);
    }

    $stmt->execute();
    $result=$stmt->get_result();
    $table='';
    $bookingId='';

    while($rec=$result->fetch_assoc()){
        if($bookingId==''){
            $bookingId=$rec['id'];
        }
        $table.='
        <tr>
            <td>'.$rec['id'].'</td>
            <td>'.$rec['customer_name'].'</td>
            <td>'.$rec['phone'].'</td>
            <td>'.$rec['booking_date'].'</td>
            <td>'.$rec['return_date'].'</td>
            <td>'.$rec['status'].'</td>
            <td>
                <button
                type="button"
                class="btn btn-info btn-sm btn-view-summary"
                data-id="'.$rec['id'].'">
                View
                </button>
            </td>
        </tr>';
    }

    if($table==''){
        $table='
        <tr>
            <td colspan="7">
                <div class="alert alert-danger mb-0">
                    No Bookings Found
                </div>
            </td>
        </tr>';
    }

    $stmt->close();

    return json_encode([
        "table"=>$table,
        "booking_id"=>$bookingId
    ]);

}


public function getRentalSummary($booking_id){

    $sql="SELECT booking_tbl.*,customer_tbl.customer_name,customer_tbl.phone,customer_tbl.nic,customer_tbl.address,station_tbl.name AS station_name,barcode_tbl.barcode,

    created.emp_FirstName AS created_fname,created.emp_SecondName AS created_lname,
    ready.emp_FirstName AS ready_fname,ready.emp_SecondName AS ready_lname,
    handover.emp_FirstName AS handover_fname,handover.emp_SecondName AS handover_lname,
    collect.emp_FirstName AS collect_fname,collect.emp_SecondName AS collect_lname,
    cancel.emp_FirstName AS cancel_fname,cancel.emp_SecondName AS cancel_lname

    FROM booking_tbl

    LEFT JOIN customer_tbl ON customer_tbl.id=booking_tbl.customer_id
    LEFT JOIN station_tbl ON station_tbl.id=booking_tbl.station_id
    LEFT JOIN barcode_tbl ON barcode_tbl.linked_id=booking_tbl.id AND barcode_tbl.type='BOOKING'

    LEFT JOIN employer_tbl created ON created.emp_Id=booking_tbl.created_by
    LEFT JOIN employer_tbl ready ON ready.emp_Id=booking_tbl.readyby
    LEFT JOIN employer_tbl handover ON handover.emp_Id=booking_tbl.handoverby
    LEFT JOIN employer_tbl collect ON collect.emp_Id=booking_tbl.collectby
    LEFT JOIN employer_tbl cancel ON cancel.emp_Id=booking_tbl.cancelby

    WHERE booking_tbl.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows<=0){
        return '<div class="alert alert-danger">Booking Not Found</div>';
    }

    $rec=$result->fetch_assoc();
    $stmt->close();

    echo '
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">
                        Booking Details
                    </h5>
                    <table class="table table-sm">
                        <tr>
                            <th width="180">Booking ID</th>
                            <td>'.$rec['id'].'</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>'.$rec['barcode'].'</td>
                        </tr>
                        <tr>
                            <th>Booking Date</th>
                            <td>'.$rec['booking_date'].'</td>
                        </tr>
                        <tr>
                            <th>Return Date</th>
                            <td>'.$rec['return_date'].'</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>'.$rec['status'].'</td>
                        </tr>
                        <tr>
                            <th>Station</th>
                            <td>'.$rec['station_name'].'</td>
                        </tr>
                         <tr>
                                <th>Remarks</th>
                                <td>'.nl2br($rec['remarks']).'</td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">
                        Customer Details
                    </h5>
                    <table class="table table-sm">
                        <tr>
                            <th width="180">Customer</th>
                            <td>'.$rec['customer_name'].'</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>'.$rec['phone'].'</td>
                        </tr>
                        <tr>
                            <th>NIC</th>
                            <td>'.$rec['nic'].'</td>
                        </tr>
                      <tr>
                        <th>Address</th>
                        <td>'.$rec['address'].'</td>
                    </tr>
                    </table>';

                    if($rec['collection_type']!='SELF'){

                        echo '
                        <div class="card border-danger mt-3">
                            <div class="card-header bg-danger text-white py-2">
                                Collection By Another Customer
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <th width="120">Type</th>
                                        <td>'.$rec['collection_type'].'</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>'.$rec['other_customer_name'].'</td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>'.$rec['other_customer_phone'].'</td>
                                    </tr>
                                    <tr>
                                        <th>NIC</th>
                                        <td>'.$rec['other_customer_nic'].'</td>
                                    </tr>
                                </table>
                            </div>
                        </div>';
                    }

                    echo '

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="background:#eef7ff;">
                <div class="card-body">
                    <h5 class="mb-3">
                        Payment Details
                    </h5>

                    <table class="table table-sm">
                        <tr>
                            <th width="180">Booking Amount</th>
                            <td>'.number_format($rec['booking_amount'],2).'</td>
                        </tr>

                        <tr>
                            <th>Advance Amount</th>
                            <td>'.number_format($rec['advance_amount'],2).'</td>
                        </tr>

                        <tr style="background:#ff6347;color:white;font-weight:bold;">
                            <th style="background:#ff6347;color:white;">Balance Amount</th>
                            <td style="background:#ff6347;color:white;">'.number_format($rec['balance_amount'],2).'</td>
                        </tr>

                        <tr>
                            <th>Payment Method</th>
                            <td>'.$rec['payment_method'].'</td>
                        </tr>

                        <tr style="background:#ffc107;font-weight:bold;">
                            <th style="background:#ffc107;">Hold Amount</th>
                            <td style="background:#ffc107;">'.number_format($rec['hold_amount'],2).'</td>
                        </tr>

                        <tr>
                            <th>Hold Paid Amount</th>
                            <td>'.number_format($rec['hold_payed_amount'],2).'</td>
                        </tr>

                        <tr style="background:#ffe8a1;font-weight:bold;">
                            <th style="background:#ffe8a1;">Hold Balance Amount</th>
                            <td style="background:#ffe8a1;">'.number_format(max(0,$rec['hold_amount']-$rec['hold_payed_amount']),2).'</td>
                        </tr>

                        <tr>
                            <th>Hold Amount Type</th>
                            <td>'.$rec['hold_amount_type'].'</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card-body">
                <h5 class="mb-3">
                    Booking Items
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Code</th>
                                <th width="100">Qty</th>
                            </tr>
                        </thead>
                        <tbody>';
                            $sqlItems="SELECT product_tbl.product_name,product_tbl.product_code,COUNT(*) qty FROM booking_item_tbl JOIN product_tbl ON product_tbl.id=booking_item_tbl.product_id WHERE booking_item_tbl.booking_id=? GROUP BY booking_item_tbl.product_id";
                            $stmt=$this->dbResult->prepare($sqlItems);
                            $stmt->bind_param("s",$booking_id);
                            $stmt->execute();
                            $items=$stmt->get_result();
                            while($item=$items->fetch_assoc()){
                                echo '<tr>
                                                    <td>'.$item['product_name'].'</td>
                                                    <td>'.$item['product_code'].'</td>
                                                    <td>'.$item['qty'].'</td>
                                                </tr>';

                            }
                            $stmt->close();
                            echo '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="mb-3">
                        Workflow History
                    </h5>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="200">Created</th>
                            <td>'.$rec['created_fname'].' '.$rec['created_lname'].'</td>
                            <td>'.$rec['created_at'].'</td>
                        </tr>';
            if($rec['readyby']!=''){
                echo '<tr>
                            <th>Ready</th>
                            <td>'.$rec['ready_fname'].' '.$rec['ready_lname'].'</td>
                            <td>'.$rec['readyat'].'</td>
                        </tr>';
            }

            if($rec['handoverby']!=''){
                echo '<tr>
                            <th>Rented Out</th>
                            <td>'.$rec['handover_fname'].' '.$rec['handover_lname'].'</td>
                            <td>'.$rec['handoverat'].'</td>
                        </tr>';
            }

            if($rec['collectby']!=''){

                echo '<tr>
                            <th>Returned</th>
                            <td>'.$rec['collect_fname'].' '.$rec['collect_lname'].'</td>
                            <td>'.$rec['collectat'].'</td>
                        </tr>';
            }

            if($rec['cancelby']!=''){

                echo '<tr>
                            <th>Cancelled</th>
                            <td>'.$rec['cancel_fname'].' '.$rec['cancel_lname'].'</td>
                            <td>'.$rec['cancelat'].'</td>
                        </tr>';
            }

            echo '</table>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card border-0 shadow-sm h-100" style="background:#fff5f5;">
                <div class="card-body">
                    <h5 class="text-danger mb-3">
                        Return Inspection
                    </h5>
                    <div class="border rounded p-3">
                        '.($rec['return_note']!='' ? nl2br($rec['return_note']) : 'No Remarks').'
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </div>';

}

public function getPenaltyDetails($booking_id){

    $sql="SELECT
    booking_tbl.id,
    booking_tbl.return_note,
    booking_tbl.claim_amount,
    booking_tbl.return_date,
    customer_tbl.customer_name,
    customer_tbl.phone,
    customer_tbl.nic

    FROM booking_tbl

    LEFT JOIN customer_tbl
    ON customer_tbl.id=booking_tbl.customer_id

    WHERE booking_tbl.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$booking_id);
    $stmt->execute();

    $result=$stmt->get_result();

    if($result->num_rows==0){

        return json_encode([
            "status"=>"error",
            "message"=>"Penalty Record Not Found"
        ]);

    }

    $data=$result->fetch_assoc();

    $stmt->close();

    return json_encode([
        "status"=>"success",
        "booking_id"=>$data['id'],
        "customer_name"=>$data['customer_name'],
        "phone"=>$data['phone'],
        "nic"=>$data['nic'],
        "amount"=>$data['claim_amount'],
        "reason"=>$data['return_note'],
        "return_date"=>$data['return_date']
    ]);

}

}
?>