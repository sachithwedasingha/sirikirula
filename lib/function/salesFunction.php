<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Sales extends Main{

    public function saveSale($sale_date, $customer_id, $user, $products, $services, $collection_type, $payment_method, $advance_amount, $balance_amount, $create_all_new_items){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $products=json_decode($products,true);
        $services=json_decode($services,true);
        $autoNumber=new AutoNumber();
        $saleId=$autoNumber->NumberGenaration("id","sale_tbl","SAL");
        

        $total=0;

        foreach($products as $item){
            $total += $item['amount'];
        }

        foreach($services as $item){
            $total += $item['amount'];
        }

        $sql="INSERT INTO sale_tbl(id,sale_date,customer_id,station,sale_amount,collection_type,payment_method,advance_amount,balance_amount,created_by,d_status) VALUES(?,?,?,?,?,?,?,?,?,?,0)";
        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("ssssdssdds",$saleId,$sale_date,$customer_id,$station,$total,$collection_type,$payment_method,$advance_amount,$balance_amount,$user);
        $result=$stmt->execute();
        $stmt->close();



        if(!$result){
            return json_encode([
            "status"=>"error",
            "message"=>"Sale Save Failed"
            ]);

        }

        if($advance_amount>0){

            $part=($balance_amount<=0) ? 'FULL PAYMENT' : 'ADVANCE PAYMENT';
           $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('SALE',?,?,?,?,?)";
            $stmtIncome=$this->dbResult->prepare($sqlIncome);
            $stmtIncome->bind_param("ssdss",$saleId,$part,$advance_amount,$user,$station);
            $stmtIncome->execute();
            $stmtIncome->close();

        }

        if($payment_method == 'MONTH_END' && $balance_amount>0){

           $sqlPending="INSERT INTO pending_payment_tbl(type,type_id,customer_id,station,amount,paid_amount,balance_amount,status) VALUES('SALE',?,?,?,?,?,?,'PENDING')";
            $stmtPending=$this->dbResult->prepare($sqlPending);
            $paidAmount=$advance_amount;
            $stmtPending->bind_param("sssddd",$saleId,$customer_id,$station,$total,$paidAmount,$balance_amount);
            $stmtPending->execute();
            $stmtPending->close();
        }

        foreach($products as $item){

            $sqlItem="INSERT INTO sale_item_tbl(sale_id,product_id,qty,unit_price,amount,item_type) VALUES(?,?,?,?,?,'PRODUCT')";
            $stmt=$this->dbResult->prepare($sqlItem);
            $stmt->bind_param("ssddd",$saleId,$item['product_id'],$item['qty'],$item['unit_price'],$item['amount']);
            $itemResult=$stmt->execute();
            $saleItemId=$this->dbResult->insert_id;
            $stmt->close();

            if(!$itemResult){
                return json_encode([
                    "status"=>"error",
                    "message"=>"Sale Item Save Failed"
                ]);
            }
            if($create_all_new_items==1){
                $requestQty=(int)$item['qty'];
            }else{
                $normalQty=(int)$item['normal_qty'];

                for($i=0;$i<$normalQty;$i++){

                    $sqlGetItem="SELECT pi.id
                    FROM product_item_tbl pi
                    WHERE pi.product_id=?
                    AND pi.station_id=?
                    AND pi.d_status=0
                    AND NOT EXISTS(
                        SELECT 1
                        FROM booking_item_tbl bi
                        JOIN booking_tbl b
                        ON b.id=bi.booking_id
                        WHERE bi.product_item_id=pi.id
                        AND b.status IN('BOOKED','READY','RENTED')
                        AND b.return_date>=CURDATE()
                    )
                    ORDER BY pi.id DESC
                    LIMIT 1";
                    $stmtGet=$this->dbResult->prepare($sqlGetItem);
                    $stmtGet->bind_param( "ss", $item['product_id'], $station );
                    $stmtGet->execute();
                    $resultItem=$stmtGet->get_result();

                    if($resultItem->num_rows>0){
                        $productItemId=$resultItem->fetch_assoc()['id'];
                        $stmtGet->close();
                        $sqlUpdate="UPDATE product_item_tbl SET d_status=1 WHERE id=?";

                        $stmtUpdate=$this->dbResult->prepare($sqlUpdate);
                        $stmtUpdate->bind_param( "i", $productItemId );
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                    }else{
                        $stmtGet->close();
                    }
                }
                $requestQty=(int)$item['new_qty'];
            }
            if($requestQty>0){
                $sqlReq="INSERT INTO stock_request_tbl(product_id,qty,request_date,type,reference_id,status) 
                VALUES( ?, ?, ?, 'SELL', ?, 'PENDING' )";
                $stmtReq=$this->dbResult->prepare($sqlReq);
                $stmtReq->bind_param(  "sdss", $item['product_id'], $requestQty, $sale_date, $saleItemId );
                $stmtReq->execute();
                $stmtReq->close();
            }
        }
        foreach($services as $item){
            $sqlItem="INSERT INTO sale_item_tbl( sale_id, product_id, qty, unit_price, amount, item_type ) VALUES(?,?,?,?,?,'SERVICE')";
            $stmt=$this->dbResult->prepare($sqlItem);
            $qty=1;
            $stmt->bind_param( "ssddd", $saleId, $item['service_id'], $qty, $item['unit_price'], $item['amount'] );
            $itemResult=$stmt->execute();
            $stmt->close();
            if(!$itemResult){
                return json_encode([
                "status"=>"error",
                "message"=>"Service Save Failed"
                ]);
            }
        }
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
                $type="SALE";
                $stmt->bind_param("sss",$saleId,$type,$barcode);
                $barcodeSave=$stmt->execute();
                $stmt->close();

        return json_encode([
        "status"=>"success",
        "sale_id"=>$saleId
        ]);

    }

    //all order view
    public function getSale($sale_id){

        $sql="SELECT
            sale_tbl.*,
            customer_tbl.customer_name,
            customer_tbl.phone,
            customer_tbl.nic,
            barcode_tbl.barcode,
            employer_tbl.emp_FirstName,
            employer_tbl.emp_SecondName

            FROM sale_tbl
            LEFT JOIN customer_tbl
            ON customer_tbl.id=sale_tbl.customer_id
            LEFT JOIN barcode_tbl
            ON barcode_tbl.linked_id=sale_tbl.id
            AND barcode_tbl.type='SALE'
            LEFT JOIN employer_tbl
            ON employer_tbl.emp_Id=sale_tbl.created_by
            WHERE sale_tbl.id=?";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$sale_id);
        $stmt->execute();
        $result=$stmt->get_result();
        $sale=$result->fetch_assoc();
        $stmt->close();
        $sqlItems="SELECT
            sale_item_tbl.*,

            CASE
                WHEN sale_item_tbl.item_type='PRODUCT'
                THEN product_tbl.product_name
                ELSE service_tbl.service_name
                END AS product_name,
            CASE
                WHEN sale_item_tbl.item_type='PRODUCT'
                THEN product_tbl.product_code
                ELSE 'SERVICE'
                END AS product_code
                FROM sale_item_tbl
                LEFT JOIN product_tbl
                ON product_tbl.id=sale_item_tbl.product_id
                AND sale_item_tbl.item_type='PRODUCT'
                LEFT JOIN service_tbl
                ON service_tbl.id=sale_item_tbl.product_id
                AND sale_item_tbl.item_type='SERVICE'
                WHERE sale_item_tbl.sale_id=?";

        $stmt=$this->dbResult->prepare($sqlItems);
        $stmt->bind_param("s",$sale_id);
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
            "sale"=>$sale,
            "items"=>$items,
            "station"=>$stationData
        ]);

    }

    //this for all orders
    public function loadSales($from_date,$to_date,$customer_id,$sale_id){
        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();


       $sql="SELECT sale_tbl.*,customer_tbl.customer_name,customer_tbl.phone,
        CASE
            WHEN COUNT(stock_request_tbl.id)=0 THEN 'READY'
            WHEN SUM(CASE WHEN stock_request_tbl.status='PENDING' THEN 1 ELSE 0 END)>0 THEN 'PENDING'
            ELSE 'READY'
        END AS production_status
        FROM sale_tbl
        LEFT JOIN customer_tbl
        ON customer_tbl.id=sale_tbl.customer_id
        LEFT JOIN barcode_tbl
        ON barcode_tbl.linked_id=sale_tbl.id
        AND barcode_tbl.type='SALE'
        LEFT JOIN stock_request_tbl
        ON stock_request_tbl.reference_id=sale_tbl.id
        AND stock_request_tbl.type='SELL'
        WHERE sale_tbl.d_status=0
        AND sale_tbl.station=?
        AND sale_tbl.collection_type!='COLLECT_LATER'";

        $params=[$station];
        $types="s";

        if($from_date!='' && $to_date!=''){
            $sql.=" AND sale_tbl.sale_date BETWEEN ? AND ?";
            $types.="ss";
            $params[]=$from_date;
            $params[]=$to_date;
        }

        if($customer_id!=''){
            $sql.=" AND sale_tbl.customer_id=?";
            $types.="s";
            $params[]=$customer_id;
        }

        if($sale_id!=''){
            $sql.=" AND sale_tbl.id LIKE ?";
            $types.="s";
            $params[]="%".$sale_id."%";
        }

        $sql.=" GROUP BY sale_tbl.id";
        $sql.=" ORDER BY sale_tbl.created_at DESC";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param($types,...$params);

        $stmt->execute();
        $result=$stmt->get_result();
        $table='';

        while($rec=$result->fetch_assoc()){

            $paymentType='';

            if($rec['balance_amount']>0){

                $paymentType='<span class="badge bg-warning text-dark">ADVANCE + BALANCE</span>';

                $reprintButtons='

                <button
                type="button"
                class="btn btn-info btn-sm btn-reprint-advance"
                data-id="'.$rec['id'].'">
                Advance
                </button>

                <button
                type="button"
                class="btn btn-secondary btn-sm btn-reprint-sale"
                data-id="'.$rec['id'].'">
                Full
                </button>';

            }else{

                $paymentType='<span class="badge bg-success">FULL PAYMENT</span>';

                $reprintButtons='

                <button
                type="button"
                class="btn btn-secondary btn-sm btn-reprint-sale"
                data-id="'.$rec['id'].'">
                Full
                </button>';

            }

            $table.='
            <tr>

                <td>'.$rec['id'].'</td>

                <td>'.date('Y-m-d h:i A',strtotime($rec['created_at'])).'</td>

                <td>'.$rec['customer_name'].'</td>

                <td>'.$rec['phone'].'</td>

                <td class="text-end">
                    '.number_format($rec['sale_amount'],2).'
                </td>

                <td>
                    '.$paymentType.'
                </td>

                <td>
                    '.$reprintButtons.'
                </td>

            </tr>';
        }

        if($table==''){

            $table='
            <tr>
                <td colspan="7">
                    <div class="alert alert-info mb-0">
                        No Sales Found
                    </div>
                </td>
            </tr>';
        }

        $stmt->close();
        return json_encode([
            "table"=>$table
        ]);

    }

    //these for pending orders
    public function loadallpendingSales(){
        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

       $sql="SELECT sale_tbl.*,customer_tbl.customer_name,customer_tbl.phone,
        CASE
            WHEN COUNT(stock_request_tbl.id)=0 THEN 'READY'
            WHEN SUM(CASE WHEN stock_request_tbl.status='PENDING' THEN 1 ELSE 0 END)>0 THEN 'PENDING'
            ELSE 'READY'
        END AS production_status
        FROM sale_tbl
        LEFT JOIN customer_tbl
        ON customer_tbl.id=sale_tbl.customer_id
        LEFT JOIN barcode_tbl
        ON barcode_tbl.linked_id=sale_tbl.id
        AND barcode_tbl.type='SALE'
        LEFT JOIN stock_request_tbl
        ON stock_request_tbl.reference_id=sale_tbl.id
        AND stock_request_tbl.type='SELL'
        WHERE sale_tbl.d_status=0
        AND sale_tbl.station=?
        AND sale_tbl.collection_type='COLLECT_LATER'
        GROUP BY sale_tbl.id
        ORDER BY sale_tbl.created_at DESC";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$station);
        $stmt->execute();
        $result=$stmt->get_result();
        $table='';

        while($rec=$result->fetch_assoc()){

         $statusBadge='';

            if($rec['production_status']=='READY'){
                $statusBadge='<span class="badge bg-success">READY</span>';
            }else{
                $statusBadge='<span class="badge bg-warning text-dark">PENDING</span>';
            }

            $table.='
            <tr>
                <td>
                    '.$rec['id'].'
                </td>
                <td>
                    '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                </td>
                <td>
                    '.$rec['customer_name'].'
                </td>
                <td>
                    '.$rec['phone'].'
                </td>
                <td class="text-end">
                    '.number_format($rec['sale_amount'],2).'
                </td>
                <td class="text-end">
                    '.number_format($rec['balance_amount'],2).'
                </td>

                <td>
                    '.$statusBadge.'
                </td>
                <td>
                    <button
                    type="button"
                    class="btn btn-info btn-sm btn-view-sale"
                    data-id="'.$rec['id'].'">
                    View
                    </button>

                        '.(
                            $rec['production_status']=='READY'
                            ? '
                            <button
                            type="button"
                            class="btn btn-success btn-sm btn-complete-sale"
                            data-id="'.$rec['id'].'">
                            Collect
                            </button>
                            '
                            : ''
                        ).'

                    <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-reprint-sale"
                    data-id="'.$rec['id'].'">
                    Reprint
                    </button>
                </td>
            </tr>';
        }

        if($table==''){

            $table='
            <tr>
                <td colspan="6">
                    <div class="alert alert-info mb-0">
                        No Sales Found
                    </div>
                </td>
            </tr>';
        }

        $stmt->close();
        return json_encode([
            "status"=>"success",
            "table"=>$table
        ]);

    }

    public function loadpendingSales($from_date,$customer_id,$sale_id){
        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $sql="SELECT sale_tbl.*,customer_tbl.customer_name,customer_tbl.phone,CASE
                WHEN COUNT(stock_request_tbl.id)=0 THEN 'READY'
                WHEN SUM(CASE WHEN stock_request_tbl.status='PENDING' THEN 1 ELSE 0 END)>0 THEN 'PENDING'
                ELSE 'READY'
                END AS production_status FROM sale_tbl LEFT JOIN customer_tbl
        ON customer_tbl.id=sale_tbl.customer_id LEFT JOIN barcode_tbl ON barcode_tbl.linked_id=sale_tbl.id AND barcode_tbl.type='SALE' 
        LEFT JOIN stock_request_tbl
        ON stock_request_tbl.reference_id=sale_tbl.id
        AND stock_request_tbl.type='SELL'
        WHERE sale_tbl.d_status=0  AND sale_tbl.station=? AND sale_tbl.collection_type ='COLLECT_LATER'";

        $params=[$station];
        $types="s";

        if($from_date!=''){
            $sql.=" AND sale_tbl.sale_date = ?";
            $types.="s";
            $params[]=$from_date;
        }

        if($customer_id!=''){
            $sql.=" AND sale_tbl.customer_id=?";
            $types.="s";
            $params[]=$customer_id;
        }

        if($sale_id!=''){

            $sqlCheck="SELECT
            sale_tbl.id,
            sale_tbl.collection_type

            FROM sale_tbl

            LEFT JOIN barcode_tbl
            ON barcode_tbl.linked_id=sale_tbl.id
            AND barcode_tbl.type='SALE'
            WHERE barcode_tbl.barcode=?";
            $stmtCheck=$this->dbResult->prepare($sqlCheck);
            $stmtCheck->bind_param("s",$sale_id);
            $stmtCheck->execute();
            $checkResult=$stmtCheck->get_result();
            if($checkResult->num_rows>0){
                $saleRec=$checkResult->fetch_assoc();
                if($saleRec['collection_type']=='FULL'){
                    return json_encode([
                        "status"=>"error",
                        "message"=>"This sale has been fully completed."
                    ]);
                }
                if($saleRec['collection_type']=='COLLECTED'){
                    return json_encode([
                        "status"=>"error",
                        "message"=>"This order has already been collected."
                    ]);
                }

            }else{
                return json_encode([
                    "status"=>"error",
                    "message"=>"Sale not found."
                ]);
            }
            $stmtCheck->close();
        }
        $sql.=" GROUP BY sale_tbl.id";
        $sql.=" ORDER BY sale_tbl.created_at DESC";
        $stmt=$this->dbResult->prepare($sql);

        if(count($params)>0){
            $stmt->bind_param($types,...$params);
        }
        $stmt->execute();
        $result=$stmt->get_result();
        $table='';

        while($rec=$result->fetch_assoc()){

        $statusBadge='';

            if($rec['production_status']=='READY'){
                $statusBadge='<span class="badge bg-success">READY</span>';
            }else{
                $statusBadge='<span class="badge bg-warning text-dark">PENDING</span>';
            }

            $table.='
            <tr>
                <td>
                    '.$rec['id'].'
                </td>
                <td>
                    '.date('Y-m-d h:i A',strtotime($rec['created_at'])).'
                </td>
                <td>
                    '.$rec['customer_name'].'
                </td>
                <td>
                    '.$rec['phone'].'
                </td>
                <td class="text-end">
                    '.number_format($rec['sale_amount'],2).'
                </td>
                <td class="text-end">
                    '.number_format($rec['balance_amount'],2).'
                </td>

                <td>
                    '.$statusBadge.'
                </td>
                <td>
                <button
                    type="button"
                    class="btn btn-info btn-sm btn-view-sale"
                    data-id="'.$rec['id'].'">
                    View
                    </button>

                '.(
                    $rec['production_status']=='READY'
                    ? '
                    <button
                    type="button"
                    class="btn btn-success btn-sm btn-complete-sale"
                    data-id="'.$rec['id'].'">
                    Collect
                    </button>
                    '
                    : ''
                ).'

                    <button
                    type="button"
                    class="btn btn-secondary btn-sm btn-reprint-sale"
                    data-id="'.$rec['id'].'">
                    Reprint
                    </button>
                </td>
            </tr>';
        }

        if($table==''){

            $table='
            <tr>
                <td colspan="8">
                    <div class="alert alert-info mb-0">
                        No Sales Found
                    </div>
                </td>
            </tr>';
        }

        $stmt->close();
        return json_encode([
            "status"=>"success",
            "table"=>$table
        ]);

    }

    public function getSaleDetails($sale_id){

        $sql="SELECT
        sale_tbl.*,
        customer_tbl.customer_name,
        customer_tbl.phone

        FROM sale_tbl

        LEFT JOIN customer_tbl
        ON customer_tbl.id=sale_tbl.customer_id

        WHERE sale_tbl.id=?";

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param("s",$sale_id);

        $stmt->execute();

        $sale=$stmt->get_result()->fetch_assoc();

        $stmt->close();

        $sqlItem="SELECT
        sale_item_tbl.*,

        CASE
            WHEN sale_item_tbl.item_type='PRODUCT'
            THEN product_tbl.product_name
            ELSE service_tbl.service_name
        END AS product_name,

        product_tbl.product_code

        FROM sale_item_tbl

        LEFT JOIN product_tbl
        ON product_tbl.id=sale_item_tbl.product_id
        AND sale_item_tbl.item_type='PRODUCT'

        LEFT JOIN service_tbl
        ON service_tbl.id=sale_item_tbl.product_id
        AND sale_item_tbl.item_type='SERVICE'

        WHERE sale_item_tbl.sale_id=?";

        $stmt=$this->dbResult->prepare($sqlItem);

        $stmt->bind_param("s",$sale_id);

        $stmt->execute();

        $result=$stmt->get_result();

        $items=[];

        while($row=$result->fetch_assoc()){

            $items[]=$row;

        }

        $stmt->close();

        // Production Summary

        $production=$this->getSaleProduction($sale_id);

        // Production Item List

        $sqlProductionItems="SELECT
        stock_request_tbl.qty,
        stock_request_tbl.status,
        product_tbl.product_name,
        product_tbl.product_code

        FROM stock_request_tbl

        JOIN product_tbl
        ON product_tbl.id=stock_request_tbl.product_id

        WHERE stock_request_tbl.reference_id=?
        AND stock_request_tbl.type='SELL'";

        $stmt=$this->dbResult->prepare($sqlProductionItems);

        $stmt->bind_param("s",$sale_id);

        $stmt->execute();

        $result=$stmt->get_result();

        $production_items=[];

        while($row=$result->fetch_assoc()){

            $production_items[]=$row;

        }

        $stmt->close();

        return json_encode([
            'sale'=>$sale,
            'items'=>$items,
            'production'=>$production,
            'production_items'=>$production_items
        ]);

    }

    public function getSaleProduction($sale_id){

        $sql="SELECT

        IFNULL(SUM(qty),0) requested_qty,

        IFNULL(
            SUM(
                CASE
                    WHEN status='DONE'
                    THEN qty
                    ELSE 0
                END
            ),
        0) completed_qty
        FROM stock_request_tbl
        WHERE reference_id=?
        AND type='SELL'";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$sale_id);
        $stmt->execute();
        $result=$stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result;

    }

    public function paySaleAdvance($sale_id,$amount,$payedby){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $amount=(double)$amount;

        $sql="SELECT
        customer_id,
        sale_amount,
        advance_amount,
        balance_amount
        FROM sale_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$sale_id);
        $stmt->execute();
        $sale=$stmt->get_result()->fetch_assoc();
        $stmt->close();
        if(!$sale){
            return json_encode([
                "status"=>"error",
                "message"=>"Sale Not Found"
            ]);
        }
        $newAdvance=$sale['advance_amount']+$amount;
        $newBalance=$sale['balance_amount']-$amount;
        if($newBalance<0){
            $newBalance=0;
        }
        $sqlUpdate="UPDATE sale_tbl SET advance_amount=?, balance_amount=? WHERE id=?";
        $stmt=$this->dbResult->prepare($sqlUpdate);
        $stmt->bind_param( "dds", $newAdvance, $newBalance, $sale_id );
        $result=$stmt->execute();
        $stmt->close();
        if(!$result){
            return json_encode([
                "status"=>"error",
                "message"=>"Sale Update Failed"
            ]);

        }
        // Income Entry
        $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('SALE',?,'ADVANCE PAYMENT',?,?,?)";
        $stmt=$this->dbResult->prepare($sqlIncome);
        $stmt->bind_param("sdss",$sale_id,$amount,$payedby,$station);
        $stmt->execute();
        $stmt->close();

        // Pending Payment Update
        $sqlPending="UPDATE pending_payment_tbl SET paid_amount=IFNULL(paid_amount,0)+?,balance_amount=balance_amount-? WHERE type='SALE' AND type_id=? AND station=?";
        $stmt=$this->dbResult->prepare($sqlPending);
        $stmt->bind_param("ddss",$amount,$amount,$sale_id,$station);
        $stmt->execute();
        $stmt->close();

        // Auto Close Pending Record
        // $sqlClose="UPDATE pending_payment_tbl SET status='PAID' WHERE type='SALE' AND type_id=? AND station=? AND balance_amount<=0";
        // $stmt=$this->dbResult->prepare($sqlClose);
        // $stmt->bind_param("ss",$sale_id,$station);
        // $stmt->execute();
        // $stmt->close();

        return json_encode([
            "status"=>"success"
        ]);

    }

    public function completeSale($sale_id,$amount,$collectedby){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $amount=(double)$amount;

        $sql="SELECT
        customer_id,
        advance_amount,
        balance_amount
        FROM sale_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$sale_id);
        $stmt->execute();
        $sale=$stmt->get_result()->fetch_assoc();
        $stmt->close();
        if(!$sale){
            return json_encode([
                "status"=>"error",
                "message"=>"Sale Not Found"
            ]);
        }
        if($amount!=$sale['balance_amount']){
            return json_encode([
                "status"=>"error",
                "message"=>"Balance Amount Mismatch"
            ]);
        }

        $sqlUpdate="UPDATE sale_tbl
        SET
        balance_payed=?,
        collection_type='COLLECTED',
        balance_payed_by=?,
        balance_pay_date=NOW()
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlUpdate);
        $stmt->bind_param(
            "dss",
            $amount,
            $collectedby,
            $sale_id
        );
        $result=$stmt->execute();
        $stmt->close();
        if(!$result){

            return json_encode([
                "status"=>"error",
                "message"=>"Sale Update Failed"
            ]);

        }

        // Income Entry

        $sqlIncome="INSERT INTO income_tbl(
        type,
        type_id,
        part,
        amount,
        created_by
        ) VALUES(
        'SALE',
        ?,
        'BALANCE PAYMENT',
        ?,
        ?)";

        $stmt=$this->dbResult->prepare($sqlIncome);

        $stmt->bind_param(
            "sds",
            $sale_id,
            $amount,
            $collectedby
        );

        $stmt->execute();

        $stmt->close();

        // Pending Payment Update

        $sqlPending="UPDATE pending_payment_tbl
        SET
        paid_amount=IFNULL(paid_amount,0)+?,
        balance_amount=0,
        status='PAID'
        WHERE type='SALE'
        AND type_id=?";

        $stmt=$this->dbResult->prepare($sqlPending);

        $stmt->bind_param(
            "ds",
            $amount,
            $sale_id
        );

        $stmt->execute();
        $stmt->close();

        return json_encode([
            "status"=>"success"
        ]);

    }

    public function halfcompleteSale($sale_id,$amount,$collectedby){

        $user = $_SESSION['user'];
        $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
        $stmt = $this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $station = $row['station'];
        $stmt->close();

        $amount=(double)$amount;

        $sql="SELECT
        customer_id,
        advance_amount,
        balance_amount
        FROM sale_tbl
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$sale_id);
        $stmt->execute();
        $sale=$stmt->get_result()->fetch_assoc();
        $stmt->close();
        if(!$sale){
            return json_encode([
                "status"=>"error",
                "message"=>"Sale Not Found"
            ]);
        }
        if($amount>$sale['balance_amount']){
            return json_encode([
                "status"=>"error",
                "message"=>"Balance Amount Mismatch"
            ]);
        }
        $newAdvance=$sale['advance_amount']+$amount;
        $sqlUpdate="UPDATE sale_tbl
        SET
        advance_amount=?,
        collection_type='COLLECTED',
        balance_payed_by=?,
        balance_pay_date=NOW()
        WHERE id=?";

        $stmt=$this->dbResult->prepare($sqlUpdate);
        $stmt->bind_param(
            "dss",
            $newAdvance,
            $collectedby,
            $sale_id
        );
        $result=$stmt->execute();
        $stmt->close();
        if(!$result){
            return json_encode([
                "status"=>"error",
                "message"=>"Sale Update Failed"
            ]);
        }
        // Income Entry

        $sqlIncome="INSERT INTO income_tbl(type,type_id,part,amount,created_by,station) VALUES('SALE',?,'BALANCE PAYMENT',?,?,?)";
        $stmt=$this->dbResult->prepare($sqlIncome);
        $stmt->bind_param("sdss",$sale_id,$amount,$collectedby,$station);
        $stmt->execute();
        $stmt->close();

        // Pending Payment Update

        $sqlPending="UPDATE pending_payment_tbl SET paid_amount=IFNULL(paid_amount,0)+?,balance_amount=balance_amount-? WHERE type='SALE' AND type_id=? AND station=?";
        $stmt=$this->dbResult->prepare($sqlPending);
        $stmt->bind_param("ddss",$amount,$amount,$sale_id,$station);
        $stmt->execute();
        $stmt->close();

        return json_encode([
            "status"=>"success"
        ]);

    }

}
?>