<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Stock extends Main{

public function stockList(){

    $user = $_SESSION['user'];

    $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";

    $stmt = $this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();

    $res = $stmt->get_result();

    $station = "";

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();
        $station = $row['station'];
    }

    $stmt->close();

    $sqlSelect = "SELECT 
    product_tbl.id,
    product_tbl.product_name,
    product_tbl.product_code,
    product_tbl.product_image,
    COUNT(product_item_tbl.id) AS total_items
    FROM product_tbl
    LEFT JOIN product_item_tbl
    ON product_item_tbl.product_id = product_tbl.id
    AND product_item_tbl.station_id = ?
    AND product_item_tbl.d_status = 0
    WHERE product_tbl.d_status = 0
    GROUP BY product_tbl.id
    ORDER BY total_items DESC";

    $stmt = $this->dbResult->prepare($sqlSelect);

    $stmt->bind_param("s",$station);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        while($rec = $result->fetch_assoc()){

            $image = "../../assets/ui/noimage.jpg";

            if(
                $rec['product_image'] != '' &&
                file_exists(__DIR__."/../uploads/product/".$rec['product_image'])
            ){
                $image = "../uploads/product/".$rec['product_image'];
            }

            $total = $rec['total_items'] ?? 0;
            $rowClass = "";

            if($total <= 0){
                $rowClass = "out-stock";
            }else if($total <= 3){
                $rowClass = "low-stock";
            }

            echo('
            <tr class="'.$rowClass.'">
            <td class="py-0">
            <img src="'.$image.'" loading="lazy" class="py-0">
            </td>
            <td>'.htmlspecialchars($rec['product_code']).'</td>
            <td>'.htmlspecialchars($rec['product_name']).'</td>
            <td>
            '.$total.'
            </td>
            <td>');

            if($total <= 0){
                echo('<span class="badge bg-danger">Out Of Stock</span>');
            }else if($total <= 3){
                echo('<span class="badge bg-warning text-dark">Low Stock</span>');
            }else{
                echo('<span class="badge bg-success">Available</span>');
            }
            echo('</td>
            </tr>');
        }
    }else{
        echo('
        <tr>
        <td colspan="5">
        <div class="alert alert-danger mb-0">
        No Products Found
        </div>
        </td>
        </tr>');
    }
    $stmt->close();
}

public function addStockProductList(){

    $user = $_SESSION['user'];

    $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";

    $stmt = $this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();

    $res = $stmt->get_result();

    $station = "";

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();
        $station = $row['station'];
    }

    $stmt->close();

    $sqlSelect = "SELECT 
    product_tbl.id,
    product_tbl.product_code,
    product_tbl.product_name,
    product_tbl.product_image,
    COUNT(product_item_tbl.id) AS total_qty

    FROM product_tbl

    LEFT JOIN product_item_tbl
    ON product_item_tbl.product_id = product_tbl.id
    AND product_item_tbl.station_id = ?
    AND product_item_tbl.d_status = 0

    WHERE product_tbl.d_status = 0

    GROUP BY product_tbl.id

    ORDER BY product_tbl.product_name ASC";

    $stmt = $this->dbResult->prepare($sqlSelect);
    $stmt->bind_param("s",$station);
    $stmt->execute();
    $result = $stmt->get_result();

    while($rec = $result->fetch_assoc()){
        $image = "../../assets/ui/noimage.jpg";
        if(
            $rec['product_image'] != '' &&
            file_exists(__DIR__."/../uploads/product/".$rec['product_image'])
        ){
            $image = "../uploads/product/".$rec['product_image'];
        }
        echo('
        <tr>
        <td>
        '.$rec['product_code'].'
        </td>
        <td>
        '.$rec['product_name'].'
        </td>
        <td>
        <span class="badge bg-primary">
        '.$rec['total_qty'].'
        </span>
        </td>
        <td>
        <input 
        type="number"
        class="form-control stock-qty"
        data-id="'.$rec['id'].'"
        min="0"
        value="0">
        </td>
        </tr>
        ');
    }
    $stmt->close();
}

// ADD STOCK
public function addStock($product_id,$qty){

    $user = $_SESSION['user'];

    $sqlStation = "SELECT station FROM login_tbl WHERE loginId=?";

    $stmt = $this->dbResult->prepare($sqlStation);
    $stmt->bind_param("s",$user);
    $stmt->execute();

    $res = $stmt->get_result();

    $station = "";

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();
        $station = $row['station'];
    }

    $stmt->close();

    $autoNumber = new AutoNumber;

    for($i=1;$i<=$qty;$i++){

        $id = $autoNumber->NumberGenaration("id","product_item_tbl","PIT");

        $sqlInsert = "INSERT INTO product_item_tbl(id,product_id,station_id,created_by,updated_by,d_status) VALUES(?,?,?,?,?,0)";

        $stmt = $this->dbResult->prepare($sqlInsert);
        $stmt->bind_param("sssss",$id,$product_id,$station,$user,$user);

        $sqlResult = $stmt->execute();

        $stmt->close();

    }

    if($sqlResult > 0){
        return("01");
    }else{
        return("02");
    }

}

public function addMultipleStock($products){

    foreach($products as $product){

        if($product['qty'] > 0){

            $this->addStock(
                $product['product_id'],
                $product['qty']
            );

        }

    }

    return("01");

}

}

?>