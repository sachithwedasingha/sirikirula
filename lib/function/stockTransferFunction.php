<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class StockTransfer extends Main{

public function loadStations(){

    $user=$_SESSION['user'];

    $sql="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $myStation=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT id,name
    FROM station_tbl
    WHERE d_status=0
    AND id<>?
    ORDER BY name";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$myStation);
    $stmt->execute();

    $result=$stmt->get_result();

    $data='<option value="">Select Station</option>';

    while($row=$result->fetch_assoc()){

        $data.='<option value="'.$row['id'].'">'.$row['name'].'</option>';

    }

    $stmt->close();

    return $data;
    }

    public function loadAvailableProducts($station){

        $today=date('Y-m-d');

        $sql="SELECT
        p.id,
        p.product_code,
        p.product_name,
        p.product_image,
        COUNT(pi.id) available_qty

        FROM product_tbl p

        INNER JOIN product_item_tbl pi
        ON pi.product_id=p.id

        WHERE pi.station_id=?
        AND pi.d_status=0

        AND NOT EXISTS(

            SELECT 1

            FROM booking_item_tbl bi

            INNER JOIN booking_tbl b
            ON b.id=bi.booking_id

            WHERE bi.product_item_id=pi.id
            AND b.status IN('BOOKED','RENTED')
            AND b.booking_date<=?
            AND b.return_date>=?

        )

        GROUP BY
        p.id,
        p.product_code,
        p.product_name,
        p.product_image

        HAVING COUNT(pi.id)>0

        ORDER BY p.product_name";

        $stmt=$this->dbResult->prepare($sql);

        if(!$stmt){
            die("SQL Error : ".$this->dbResult->error);
        }

        $stmt->bind_param("sss",$station,$today,$today);

        $stmt->execute();

        $result=$stmt->get_result();

        $html='';

        while($row=$result->fetch_assoc()){

            $image=$row['product_image']=='' ? '../../assets/ui/noimage.jpg' : '../uploads/product/'.$row['product_image'];

            $html.='
            <tr>
                <td>
                    <input type="checkbox" class="request-product" data-id="'.$row['id'].'">
                </td>
                <td>
                    <img src="'.$image.'" style="width:60px;height:60px;object-fit:cover;">
                </td>
                <td>'.$row['product_code'].'</td>
                <td>'.$row['product_name'].'</td>
                <td class="text-center">'.$row['available_qty'].'</td>
                <td>
                    <input type="number" class="form-control request-qty" data-max="'.$row['available_qty'].'" min="0" max="'.$row['available_qty'].'">
                </td>
            </tr>';
        }

        $stmt->close();

        return $html;
    }

    public function saveRequest($station_id,$products,$createdby){

    $products=json_decode($products,true);

    if(count($products)==0){

        return json_encode([
            "status"=>"error",
            "message"=>"No Products Selected"
        ]);

    }

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$createdby);
    $stmt->execute();
    $fromStation=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sqlNo="SELECT IFNULL(MAX(id),0)+1 nextNo FROM stock_transfer_request_tbl";
    $result=$this->dbResult->query($sqlNo);
    $nextNo=$result->fetch_assoc()['nextNo'];

    $requestNo='REQ'.str_pad($nextNo,5,'0',STR_PAD_LEFT);

    $sql="INSERT INTO stock_transfer_request_tbl(
    request_no,
    from_station,
    to_station,
    created_by
    ) VALUES(?,?,?,?)";

    $stmt=$this->dbResult->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $requestNo,
        $fromStation,
        $station_id,
        $createdby
    );

    $result=$stmt->execute();

    $requestId=$this->dbResult->insert_id;

    $stmt->close();

    if(!$result){

        return json_encode([
            "status"=>"error",
            "message"=>"Request Save Failed"
        ]);

    }

    foreach($products as $item){

        $sqlItem="INSERT INTO stock_transfer_request_item_tbl(
        request_id,
        product_id,
        qty
        ) VALUES(?,?,?)";

        $stmt=$this->dbResult->prepare($sqlItem);

        $stmt->bind_param(
            "isi",
            $requestId,
            $item['product_id'],
            $item['qty']
        );

        $stmt->execute();

        $stmt->close();

    }

    return json_encode([
        "status"=>"success",
        "request_no"=>$requestNo
    ]);

}

public function loadMyRequests(){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT
    r.*,
    s.name station_name,
    COUNT(i.id) item_count,
    IFNULL(SUM(i.qty),0) total_qty

    FROM stock_transfer_request_tbl r

    LEFT JOIN station_tbl s
    ON s.id=r.to_station

    LEFT JOIN stock_transfer_request_item_tbl i
    ON i.request_id=r.id

    WHERE r.from_station=?

    GROUP BY r.id

    ORDER BY r.created_at DESC";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("s",$station);
    $stmt->execute();

    $result=$stmt->get_result();

    $table='';

    while($rec=$result->fetch_assoc()){

        if($rec['status']=='PENDING'){
            $status='<span class="badge bg-warning">PENDING</span>';
        }elseif($rec['status']=='APPROVED'){
            $status='<span class="badge bg-info">APPROVED</span>';
        }elseif($rec['status']=='COMPLETED'){
            $status='<span class="badge bg-success">COMPLETED</span>';
        }else{
            $status='<span class="badge bg-danger">REJECTED</span>';
        }

        $table.='

        <tr>

            <td>
                '.$rec['request_no'].'
            </td>

            <td>
                '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
            </td>

            <td>
                '.$rec['station_name'].'
            </td>

            <td class="text-center">
                '.$rec['item_count'].'
            </td>

            <td class="text-end">
                '.number_format($rec['total_qty']).'
            </td>

            <td>
                '.$status.'
            </td>

            <td>
                '.($rec['approved_date']=='' ? '-' : date('Y-m-d h:i A',strtotime($rec['approved_date']))).'
            </td>

            <td>

                <button
                type="button"
                class="btn btn-info btn-sm btn-view-request"
                data-id="'.$rec['id'].'">

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

                    No Requests Found

                </div>

            </td>

        </tr>';

    }

    $stmt->close();

    return $table;

}

public function viewRequest($id){

    $sql="SELECT
    i.qty,
    p.product_code,
    p.product_name

    FROM stock_transfer_request_item_tbl i

    JOIN product_tbl p
    ON p.id=i.product_id

    WHERE i.request_id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();

    $result=$stmt->get_result();

    $html='

    <table class="table table-bordered">

        <thead>

            <tr>
                <th>Code</th>
                <th>Product</th>
                <th class="text-end">Qty</th>
            </tr>

        </thead>

        <tbody>';

    while($row=$result->fetch_assoc()){

        $html.='

        <tr>

            <td>'.$row['product_code'].'</td>

            <td>'.$row['product_name'].'</td>

            <td class="text-end">
                '.number_format($row['qty']).'
            </td>

        </tr>';
    }

    $html.='

        </tbody>

    </table>';

    $stmt->close();

    return $html;

}

public function loadRequests($status,$request_no){

    $user=$_SESSION['user'];

    $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
    $stmt=$this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();
    $station=$stmt->get_result()->fetch_assoc()['station'];
    $stmt->close();

    $sql="SELECT
    r.*,
    s.name station_name,
    COUNT(i.id) item_count,
    IFNULL(SUM(i.qty),0) total_qty

    FROM stock_transfer_request_tbl r

    LEFT JOIN station_tbl s
    ON s.id=r.from_station

    LEFT JOIN stock_transfer_request_item_tbl i
    ON i.request_id=r.id

    WHERE r.to_station=? AND r.status='PENDING'";

    $params=[$station];
    $types="s";

    if($status!=''){

        $sql.=" AND r.status=?";
        $types.="s";
        $params[]=$status;

    }

    if($request_no!=''){

        $sql.=" AND r.request_no LIKE ?";
        $types.="s";
        $params[]="%".$request_no."%";

    }

    $sql.=" GROUP BY r.id ORDER BY r.created_at DESC";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param($types,...$params);
    $stmt->execute();

    $result=$stmt->get_result();

    $table='';

    while($rec=$result->fetch_assoc()){

        if($rec['status']=='PENDING'){
            $badge='<span class="badge bg-warning">PENDING</span>';
        }elseif($rec['status']=='APPROVED'){
            $badge='<span class="badge bg-info">APPROVED</span>';
        }elseif($rec['status']=='TRANSFERRED'){
            $badge='<span class="badge bg-success">TRANSFERRED</span>';
        }else{
            $badge='<span class="badge bg-danger">REJECTED</span>';
        }

        $table.='

        <tr>

            <td>'.$rec['request_no'].'</td>

            <td>'.$rec['station_name'].'</td>

            <td class="text-center">'.$rec['item_count'].'</td>

            <td class="text-end">'.number_format($rec['total_qty']).'</td>

            <td>'.date('Y-m-d h:i A',strtotime($rec['created_at'])).'</td>

            <td>'.$badge.'</td>

            <td>

                <button
                type="button"
                class="btn btn-primary btn-sm btn-view-request"
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
                <div class="alert alert-info mb-0">
                    No Requests Found
                </div>
            </td>
        </tr>';

    }

    $stmt->close();

    return $table;

}

public function viewTransferRequest($id){

    $sql="SELECT
    r.*,
    s.name station_name

    FROM stock_transfer_request_tbl r

    LEFT JOIN station_tbl s
    ON s.id=r.from_station

    WHERE r.id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();

    $request=$stmt->get_result()->fetch_assoc();

    $stmt->close();

    $sql="SELECT
    i.*,
    p.product_code,
    p.product_name

    FROM stock_transfer_request_item_tbl i

    LEFT JOIN product_tbl p
    ON p.id=i.product_id

    WHERE i.request_id=?";

    $stmt=$this->dbResult->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();

    $result=$stmt->get_result();

    $items='';

    while($row=$result->fetch_assoc()){

        $items.='

        <tr
            data-item-id="'.$row['id'].'"
            data-product-id="'.$row['product_id'].'">

            <td>'.$row['product_code'].'</td>

            <td>'.$row['product_name'].'</td>

            <td class="text-center">'.$row['qty'].'</td>

            <td class="text-center">
                <span class="badge bg-secondary">
                    Check
                </span>
            </td>

            <td>

                <input
                type="number"
                class="form-control transfer_qty"
                value="'.$row['qty'].'">

            </td>

        </tr>';

    }

    $stmt->close();

    if($request['status']=='PENDING'){
        $badge='<span class="badge bg-warning">PENDING</span>';
    }elseif($request['status']=='APPROVED'){
        $badge='<span class="badge bg-info">APPROVED</span>';
    }elseif($request['status']=='TRANSFERRED'){
        $badge='<span class="badge bg-success">TRANSFERRED</span>';
    }else{
        $badge='<span class="badge bg-danger">REJECTED</span>';
    }

    return json_encode([
        'request'=>$request,
        'status_badge'=>$badge,
        'items'=>$items
    ]);

}

public function checkTransfer($request_id,$items){

    $items=json_decode($items,true);

    $errors=[];

    foreach($items as $item){

        if($item['qty']<=0){
            continue;
        }

        $sqlProduct="SELECT product_name
        FROM product_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlProduct);
        $stmt->bind_param("s",$item['product_id']);
        $stmt->execute();

        $productName=$stmt->get_result()->fetch_assoc()['product_name'];

        $stmt->close();

        $sqlAvailable="SELECT COUNT(pi.id) qty

        FROM product_item_tbl pi

        WHERE pi.product_id=?
        AND pi.d_status=0

        AND pi.id NOT IN(

            SELECT bi.product_item_id

            FROM booking_item_tbl bi

            INNER JOIN booking_tbl b
            ON b.id=bi.booking_id

            WHERE b.status IN('BOOKED','RENTED')

        )";

        $stmt=$this->dbResult->prepare($sqlAvailable);

        $stmt->bind_param(
            "s",
            $item['product_id']
        );

        $stmt->execute();

        $available=$stmt->get_result()->fetch_assoc()['qty'];

        $stmt->close();

        if($available<$item['qty']){

            $errors[]=$productName.
            ' (Requested '.$item['qty'].
            ', Available '.$available.')';

        }

    }

    if(count($errors)>0){

        return json_encode([
            'status'=>'error',
            'message'=>implode('<br>',$errors)
        ]);

    }

    return json_encode([
        'status'=>'success'
    ]);

}

public function approveRequest($request_id,$approved_by,$items){

        $user=$_SESSION['user'];

        $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";
        $stmt=$this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $fromstation=$stmt->get_result()->fetch_assoc()['station'];
        $stmt->close();

        $items=json_decode($items,true);

        foreach($items as $item){

            if($item['qty']<=0){
                continue;
            }
            $sqlRequest="SELECT from_station
            FROM stock_transfer_request_tbl
            WHERE id=?";

            $stmt=$this->dbResult->prepare($sqlRequest);
            $stmt->bind_param("i",$request_id);
            $stmt->execute();

            $toStation=$stmt->get_result()->fetch_assoc()['from_station'];
            $stmt->close();

            $sqlItems="SELECT pi.id
            FROM product_item_tbl pi
            WHERE pi.product_id=? AND pi.station_id=?
            AND pi.d_status=0
            AND pi.id NOT IN(
                SELECT bi.product_item_id
                FROM booking_item_tbl bi
                INNER JOIN booking_tbl b
                ON b.id=bi.booking_id
                WHERE b.status IN('BOOKED','RENTED')
            )

            LIMIT ".$item['qty'];

            $stmt=$this->dbResult->prepare($sqlItems);
            $stmt->bind_param(
                "ss",
                $item['product_id'],$fromstation
            );

            $stmt->execute();
            $result=$stmt->get_result();
            $transferred=0;
            while($row=$result->fetch_assoc()){
                $sqlUpdate="UPDATE product_item_tbl SET station_id=? WHERE id=?";

                $stmtUpdate=$this->dbResult->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $toStation, $row['id'] );
                $stmtUpdate->execute();
                $stmtUpdate->close();
                $transferred++;
            }

            $stmt->close();
            $sql="UPDATE stock_transfer_request_item_tbl SET approved_qty=? WHERE id=?";

            $stmt=$this->dbResult->prepare($sql);
            $stmt->bind_param( "ii", $transferred,  $item['item_id'] );
            $stmt->execute();
            $stmt->close();

        }

        $sql="UPDATE stock_transfer_request_tbl
        SET status='APPROVED', approved_by=?, approved_date=NOW()  WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param("si", $approved_by, $request_id );
        $result=$stmt->execute();
        $stmt->close();

        if($result){
            return json_encode([
                'status'=>'success'
            ]);
        }

        return json_encode([
            'status'=>'error',
            'message'=>'Approval Failed'
        ]);

    }

}

?>