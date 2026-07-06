<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Product extends Main{

    // ADD PRODUCT
    public function addproduct($supplier,$category,$product_name,$product_details,$product_code,$unit_price,$retail_price,$image){

            $sqlCheck = "SELECT * FROM product_tbl 
            WHERE product_code = ? 
            AND d_status = 0";

            $stmt = $this->dbResult->prepare($sqlCheck);

            $stmt->bind_param("s",$product_code);

            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows > 0){

            $stmt->close();

            return("04");

            }

            $stmt->close();

            $autoNumber = new AutoNumber;

            $id = $autoNumber->NumberGenaration("id","product_tbl","PRO");

            $imageName = "";

            if(isset($image['name']) && $image['name'] != ""){

            $imageName = uniqid().".webp";

            $uploadPath = __DIR__."/../uploads/product/";

            if(!file_exists($uploadPath)){
            mkdir($uploadPath,0777,true);
            }

            $target = $uploadPath.$imageName;

            $source = imagecreatefromstring(
            file_get_contents($image['tmp_name'])
            );

            imagewebp($source,$target,65);

            imagedestroy($source);

            }

            $sqlInsert = "INSERT INTO product_tbl
            (
            id,
            supplier,
            category,
            product_image,
            product_name,
            product_details,
            product_code,
            unit_price,
            retail_price,
            d_status
            )
            VALUES
            (
            ?,?,?,?,?,?,?,?,?,0
            )";

            $stmt = $this->dbResult->prepare($sqlInsert);

            $stmt->bind_param(
            "sssssssdd",
            $id,
            $supplier,
            $category,
            $imageName,
            $product_name,
            $product_details,
            $product_code,
            $unit_price,
            $retail_price
            );

            $sqlResult = $stmt->execute();

            $stmt->close();

            if($sqlResult > 0){
            return("01");
            }else{
            return("02");
            }

    }

    public function getNextProductCode($category_id){

        $categoryCode=substr($category_id,-3);

        $prefix='PC'.$categoryCode;

        $sql="SELECT product_code
        FROM product_tbl
        WHERE product_code LIKE ?
        ORDER BY product_code DESC
        LIMIT 1";

        $search=$prefix.'%';

        $stmt=$this->dbResult->prepare($sql);

        $stmt->bind_param("s",$search);

        $stmt->execute();

        $result=$stmt->get_result();

        if($result->num_rows>0){

            $lastCode=$result->fetch_assoc()['product_code'];

            $runningNo=(int)substr($lastCode,-4);

            $runningNo++;

        }else{

            $runningNo=1;

        }

        $stmt->close();

        $productCode=$prefix.str_pad(
            $runningNo,
            4,
            '0',
            STR_PAD_LEFT
        );

        return json_encode([
            "product_code"=>$productCode
        ]);

    }

    // PRODUCT LIST
    public function productList(){

            $sqlSelect = "SELECT product_tbl.*,
            category_tbl.name AS category_name,
            supplier_tbl.name AS supplier_name
            FROM product_tbl
            LEFT JOIN category_tbl 
            ON category_tbl.id = product_tbl.category
            LEFT JOIN supplier_tbl 
            ON supplier_tbl.id = product_tbl.supplier
            WHERE product_tbl.d_status = 0
            ORDER BY product_tbl.id DESC";

            $stmt = $this->dbResult->prepare($sqlSelect);

            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows > 0){

            while($rec = $result->fetch_assoc()){

            $image = "../uploads/product/default.png";

            if($rec['product_image'] != ''){
            $image = "../uploads/product/".$rec['product_image'];
            }else{
            $image = "../../assets/ui/noimage.jpg";
            }

            echo('
            <tr>

            <td>
            <img src="'.$image.'" 
            style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid #ddd;" loading="lazy">
            </td>

            <td>'.htmlspecialchars($rec['product_code']).'</td>

            <td>'.htmlspecialchars($rec['product_name']).'</td>

            <td>'.($rec['category_name'] != '' ? htmlspecialchars($rec['category_name']) : '<span class="text-danger">No Category</span>').'</td>

            <td>'.($rec['supplier_name'] != '' ? htmlspecialchars($rec['supplier_name']) : '<span class="text-danger">No Supplier</span>').'</td>

            <td>'.number_format($rec['unit_price'],2).'</td>

            <td>'.number_format($rec['retail_price'],2).'</td>

            <td>
            <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="'.$rec['id'].'">Edit</button>

            <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="'.$rec['id'].'">Delete</button>
            
            <button type="button"
                class="btn btn-info btn-sm btn-barcode"
                data-code="'.htmlspecialchars($rec['product_code']).'">
                Print Barcode
            </button>
            </td>

            </tr>');

            }

            }else{

            echo('
            <tr>
            <td colspan="8">
            <div class="alert alert-danger mb-0">
            No Products Found
            </div>
            </td>
            </tr>');

            }

            $stmt->close();

    }

    public function productDropdown(){

            $sqlSelect = "SELECT * FROM product_tbl
            WHERE d_status = 0
            ORDER BY product_name ASC";

            $stmt = $this->dbResult->prepare($sqlSelect);

            $stmt->execute();

            $result = $stmt->get_result();

            echo('<option value="">Select Product</option>');

            while($rec = $result->fetch_assoc()){

            echo('<option value="'.$rec['id'].'">'.
            htmlspecialchars($rec['product_name']).
            ' - '.
            htmlspecialchars($rec['product_code']).
            '</option>');

            }

            $stmt->close();

    }

    // EDIT PRODUCT
    public function editProduct($id,$supplier,$category,$product_name,$product_details,$product_code,$unit_price,$retail_price,$image){

            $sqlCheck = "SELECT * FROM product_tbl
            WHERE product_code = ?
            AND id != ?
            AND d_status = 0";

            $stmt = $this->dbResult->prepare($sqlCheck);

            $stmt->bind_param("ss",$product_code,$id);

            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows > 0){

            $stmt->close();

            return("04");

            }

            $stmt->close();

            $imagePart = "";

            $params = [];

            $types = "";

            if(isset($image['name']) && $image['name'] != ""){

            $imageName = uniqid().".webp";

            $uploadPath = __DIR__."/../uploads/product/";

            if(!file_exists($uploadPath)){
            mkdir($uploadPath,0777,true);
            }

            $target = $uploadPath.$imageName;

            $source = imagecreatefromstring(
            file_get_contents($image['tmp_name'])
            );

            imagewebp($source,$target,65);

            imagedestroy($source);

            $imagePart = ", product_image=?";

            $params[] = $imageName;

            $types .= "s";

            }

            $sqlUpdate = "UPDATE product_tbl
            SET
            supplier=?,
            category=?,
            product_name=?,
            product_details=?,
            product_code=?,
            unit_price=?,
            retail_price=?
            $imagePart
            WHERE id=?";

            $stmt = $this->dbResult->prepare($sqlUpdate);

            $types = "sssssdd".$types."s";

            $params = array_merge(
            [
            $supplier,
            $category,
            $product_name,
            $product_details,
            $product_code,
            $unit_price,
            $retail_price
            ],
            $params,
            [$id]
            );

            $stmt->bind_param($types,...$params);

            $sqlResult = $stmt->execute();

            $stmt->close();

            if($sqlResult > 0){
            return("01");
            }else{
            return("02");
            }

    }

    // DELETE PRODUCT
    public function delete_product($uid){

            $sqlDelete = "UPDATE product_tbl
            SET d_status = 1
            WHERE id=?";

            $stmt = $this->dbResult->prepare($sqlDelete);

            $stmt->bind_param("s",$uid);

            $sqlResult = $stmt->execute();

            $stmt->close();

            if($sqlResult > 0){
            return("ok");
            }else{
            return("error");
            }

    }

    // PRODUCT DATA
    public function productdata($uid){

            $sqlSelect = "SELECT * FROM product_tbl
            WHERE id=?";

            $stmt = $this->dbResult->prepare($sqlSelect);

            $stmt->bind_param("s",$uid);

            $stmt->execute();

            $result = $stmt->get_result();

            if($result->num_rows > 0){

            $rec = $result->fetch_assoc();

            $stmt->close();

            return json_encode($rec);

            }

            $stmt->close();

    }

    //load products for sell
    public function loadProducts($search){

        $user=$_SESSION['user'];

        $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";

        $stmt=$this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $result=$stmt->get_result();
        $station=$result->fetch_assoc()['station'];

        $stmt->close();

        $sql="SELECT
            product_tbl.id,
            product_tbl.product_name,
            product_tbl.product_code,
            product_tbl.retail_price,
            COUNT(DISTINCT product_item_tbl.id) AS stock_qty,
            (SELECT COUNT(*)
                FROM booking_item_tbl
                JOIN booking_tbl
                ON booking_tbl.id=booking_item_tbl.booking_id
                WHERE booking_item_tbl.product_id=product_tbl.id
                AND booking_tbl.station_id=?
                AND booking_tbl.status IN('BOOKED','READY','RENTED')
                AND booking_tbl.booking_date>=CURDATE()
            ) AS future_booking_qty
            FROM product_tbl
            LEFT JOIN product_item_tbl
            ON product_item_tbl.product_id=product_tbl.id
            AND product_item_tbl.station_id=?
            AND product_item_tbl.d_status=0
            WHERE product_tbl.d_status=0
            AND (
                product_tbl.product_name LIKE ?
                OR product_tbl.product_code LIKE ?
            )
            GROUP BY
            product_tbl.id,
            product_tbl.product_name,
            product_tbl.product_code,
            product_tbl.retail_price
            ORDER BY product_tbl.product_name
            LIMIT 20";

        $like="%".$search."%";
        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("ssss",$station,$station,$like,$like);

        $stmt->execute();
        $result=$stmt->get_result();
        $data=[];

        while($rec=$result->fetch_assoc()){
            $available=$rec['stock_qty']-$rec['future_booking_qty'];
            if($available<0){
                $available=0;
            }
            $data[]=[
                'id'=>$rec['id'],
                'text'=>$rec['product_name'].' | '.$rec['product_code'].' | Stock : '.$rec['stock_qty'].' | Available : '.$available,
                'code'=>$rec['product_code'],
                'name'=>$rec['product_name'],
                'price'=>$rec['retail_price'],
                'stock'=>$available,
                'totalstock'=>$rec['stock_qty'],
                'futurebooking'=>$rec['future_booking_qty']
            ];
        }
        $stmt->close();
        return json_encode($data);
    }

    public function productListModal(){

        $sql="SELECT
                product_tbl.id,
                product_tbl.supplier,
                product_tbl.category,
                category_tbl.name,
                product_tbl.product_image,
                product_tbl.product_name,
                product_tbl.product_details,
                product_tbl.product_code,
                product_tbl.unit_price,
                product_tbl.retail_price
            FROM product_tbl
            LEFT JOIN category_tbl
            ON category_tbl.id=product_tbl.category
            WHERE product_tbl.d_status=0
            ORDER BY product_tbl.product_name";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){
            while($rec=$result->fetch_assoc()){
                $image="../uploads/product/".$rec['product_image'];
                if(empty($rec['product_image'])){
                    $image="../../assets/ui/noimage.jpg";
                }

                echo '
                <tr>
                    <td>
                        <img src="'.$image.'"
                        style="width:50px;height:50px;object-fit:cover;"
                        class="img-thumbnail">
                    </td>
                    <td>
                        '.$rec['product_code'].'
                    </td>
                    <td>
                        '.$rec['product_name'].'
                    </td>
                    <td>
                        '.$rec['name'].'
                    </td>
                    <td class="text-end">
                        '.number_format($rec['retail_price'],2).'
                    </td>
                    <td class="text-center">
                        <button
                        type="button"
                        class="btn btn-info btn-sm"
                        onclick="viewProduct(\''.$rec['id'].'\')">
                        View
                        </button>
                    </td>
                </tr>';
            }
        }else{
            echo '
            <tr>
                <td colspan="6"
                class="text-center text-danger">
                No Products Found
                </td>
            </tr>';
        }
        $stmt->close();
    }

    public function productFullView($id){

            $user = $_SESSION['user'];
            $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";
            $stmt = $this->dbResult->prepare($sqlStation);
            $stmt->bind_param("s",$user);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $myStation = $row['station'];

        // Product Details
        $sql="SELECT
            product_tbl.*,
            category_tbl.name,
            supplier_tbl.name AS cname

        FROM product_tbl

        LEFT JOIN category_tbl
        ON category_tbl.id=product_tbl.category

        LEFT JOIN supplier_tbl
        ON supplier_tbl.id=product_tbl.supplier

        WHERE product_tbl.id=?
        AND product_tbl.d_status=0";

        $stmt=$this->dbResult->prepare($sql);
        $stmt->bind_param("s",$id);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows==0){
            return '<div class="alert alert-danger">Product not found.</div>';
        }

        $product=$result->fetch_assoc();

        $image="../../assets/ui/noimage.jpg";

        if(!empty($product['product_image'])){
            $image="../uploads/product/".$product['product_image'];
        }

        ?>

        <div class="row mb-4">
            <div class="col-md-3">
                <img src="<?php echo $image; ?>"
                    class="img-fluid img-thumbnail">

            </div>
            <div class="col-md-9">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Product Code</th>
                        <td><?php echo $product['product_code']; ?></td>
                    </tr>
                    <tr>
                        <th>Product Name</th>
                        <td><?php echo $product['product_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td><?php echo $product['cname']; ?></td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td><?php echo $product['name']; ?></td>
                    </tr>
                    <tr>
                        <th>Unit Price</th>
                        <td><?php echo number_format($product['unit_price'],2); ?></td>
                    </tr>
                    <tr>
                        <th>Retail Price</th>
                        <td><?php echo number_format($product['retail_price'],2); ?></td>
                    </tr>
                    <tr>
                        <th>Details</th>
                        <td><?php echo nl2br($product['product_details']); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <h5 class="mb-3">Stock Availability by Station</h5>
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>Branch</th>
                    <th>Total Stock</th>
                    <th>Future Bookings</th>
                    <th>Available</th>
                </tr>
            </thead>
            <tbody>
        <?php

            $sqlStock="
                SELECT
                product_item_tbl.station_id,
                station_tbl.name,
                COUNT(product_item_tbl.id) AS stock_qty
                FROM product_item_tbl
                INNER JOIN station_tbl
                ON station_tbl.id = product_item_tbl.station_id
                WHERE product_item_tbl.product_id=?
                AND product_item_tbl.d_status=0
                GROUP BY
                product_item_tbl.station_id,
                station_tbl.name
                ";

        $stmt=$this->dbResult->prepare($sqlStock);
        $stmt->bind_param("s",$id);
        $stmt->execute();
        $stocks=$stmt->get_result();

        while($stock=$stocks->fetch_assoc()){

            $station=$stock['station_id'];
            $stationname=$stock['name'];

            $sqlBooked="
            SELECT COUNT(*) total
            FROM booking_item_tbl
            INNER JOIN booking_tbl
                ON booking_tbl.id=booking_item_tbl.booking_id
            INNER JOIN product_item_tbl
                ON product_item_tbl.id=booking_item_tbl.product_item_id
            WHERE booking_item_tbl.product_id=?
            AND product_item_tbl.station_id=?
            AND booking_tbl.status IN('BOOKED','READY')
            AND booking_tbl.booking_date>=CURDATE()";

            $stmt2=$this->dbResult->prepare($sqlBooked);
            $stmt2->bind_param("ss",$id,$station);
            $stmt2->execute();
            $booked=$stmt2->get_result()->fetch_assoc()['total'];
            $available=$stock['stock_qty']-$booked;
            if($available<0){
                $available=0;
            }
            $rowStyle='background:#fff5f5;';
            if($station==$myStation){
                $rowStyle='background:#f0fff4;';
            }
            echo '
            <tr>
                <td style="'.$rowStyle.'">'.$stationname.'</td>
                <td style="'.$rowStyle.'">'.$stock['stock_qty'].'</td>
                <td style="'.$rowStyle.'">'.$booked.'</td>
                <td style="'.$rowStyle.'">
                    <span class="badge bg-success">'.$available.'</span>
                </td>
            </tr>';
        }

        ?>

            </tbody>
        </table>

        <h5 class="mt-4 mb-3">Product Items & Future Bookings</h5>
        <div style="max-height:500px;overflow:auto;">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="120">Branch</th>
                    <th width="40">Item ID</th>
                    <th>Future Bookings</th>
                </tr>
            </thead>
            <tbody>
        <?php

        $sqlItems="
            SELECT
            product_item_tbl.*,
            station_tbl.name AS station_name

            FROM product_item_tbl

            LEFT JOIN station_tbl
            ON station_tbl.id=product_item_tbl.station_id

            WHERE product_item_tbl.product_id=?
            AND product_item_tbl.d_status=0

            ORDER BY
            CASE
                WHEN product_item_tbl.station_id=? THEN 0
                ELSE 1
            END,
            station_tbl.name,
            product_item_tbl.id";

        $stmt=$this->dbResult->prepare($sqlItems);
        $stmt->bind_param("ss",$id,$myStation);
        $stmt->execute();
        $items=$stmt->get_result();

        while($item=$items->fetch_assoc()){

        $rowStyle='background:#fff5f5;';

        if($item['station_id']==$myStation){
            $rowStyle='background:#f0fff4;';
        }


            echo '<tr>';
            echo '<td style="'.$rowStyle.'">'.$item['station_name'].'</td>';
            echo '<td>'.$item['id'].'</td>';
            echo '<td>';

            $sqlBookings="
            SELECT
                booking_tbl.id,
                booking_tbl.booking_date,
                booking_tbl.return_date
            FROM booking_item_tbl
            INNER JOIN booking_tbl
                ON booking_tbl.id=booking_item_tbl.booking_id
            WHERE booking_item_tbl.product_item_id=?
            AND booking_tbl.status IN('BOOKED','READY')
            AND booking_tbl.return_date>=CURDATE()
            ORDER BY booking_tbl.booking_date";

            $stmt3 = $this->dbResult->prepare($sqlBookings);

            if(!$stmt3){
            die("SQL Error : ".$this->dbResult->error);
            }

            $stmt3->bind_param("s",$item['id']);
            $stmt3->bind_param("s",$item['id']);
            $stmt3->execute();

            $bookings=$stmt3->get_result();

            if($bookings->num_rows>0){
                while($bk=$bookings->fetch_assoc()){
                    echo '
                    <span class="badge bg-danger me-1 mb-1">
                        '.$bk['id'].' |
                        '.$bk['booking_date'].' →
                        '.$bk['return_date'].'
                    </span>';
                }
            }else{
                echo '
                <span class="badge bg-success">
                    No Appointments
                </span>';
            }
            echo '</td>';
            echo '</tr>';
        }
        ?>
                    </tbody>
                </table>
            </div>
        <?php

        return ob_get_clean();
    }

    public function checkProductAvailability($product_id,$qty){

        $user=$_SESSION['user'];

        $sqlStation="SELECT station FROM login_tbl WHERE loginId=?";

        $stmt=$this->dbResult->prepare($sqlStation);
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $station=$stmt->get_result()->fetch_assoc()['station'];

        $stmt->close();

        // Total stock in branch

        $sqlStock="SELECT COUNT(*) qty
        FROM product_item_tbl
        WHERE product_id=?
        AND station_id=?
        AND d_status=0";

        $stmt=$this->dbResult->prepare($sqlStock);
        $stmt->bind_param("ss",$product_id,$station);
        $stmt->execute();

        $totalStock=$stmt->get_result()->fetch_assoc()['qty'];

        $stmt->close();

        // Stock already required within next 4 days

        $sqlReserved="SELECT COUNT(*) qty
        FROM booking_item_tbl

        JOIN booking_tbl
        ON booking_tbl.id=booking_item_tbl.booking_id

        WHERE booking_item_tbl.product_id=?
        AND booking_tbl.station_id=?
        AND booking_tbl.status IN('BOOKED','READY','RENTED')
        AND booking_tbl.return_date>=CURDATE()
        AND booking_tbl.booking_date<=DATE_ADD(CURDATE(),INTERVAL 4 DAY)";

        $stmt=$this->dbResult->prepare($sqlReserved);
        $stmt->bind_param("ss",$product_id,$station);
        $stmt->execute();

        $reservedWithin4Days=$stmt->get_result()->fetch_assoc()['qty'];

        $stmt->close();

        // Stock that can be borrowed and replaced

        $replaceableStock=$totalStock-$reservedWithin4Days;

        if($replaceableStock<0){
            $replaceableStock=0;
        }

        if($replaceableStock>=$qty){

            return json_encode([
                'status'=>'CAN_REPLACE',
                'qty'=>$replaceableStock,
                'message'=>'Reserved stock can be temporarily used and replaced within 4 days.'
            ]);

        }

        return json_encode([
            'status'=>'NOT_AVAILABLE',
            'qty'=>$replaceableStock,
            'message'=>'This quantity is required for bookings within the next 4 days and cannot be sold.'
        ]);

    }

    

}
?>