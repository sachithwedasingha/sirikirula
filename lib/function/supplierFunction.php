<?php

session_start();

include_once('main.php');
include_once('auto_id.php');

class Supplier extends Main{

public function addsupplier($name,$address,$phone,$email){

    $sqlSelect = "SELECT * FROM supplier_tbl 
    WHERE (email = ? OR phone = ?) 
    AND d_status = 0;";

    $stmt = $this->dbResult->prepare($sqlSelect);

    $stmt->bind_param("ss",$email,$phone);

    $stmt->execute();

    $sqlResult = $stmt->get_result();

    $nor = $sqlResult->num_rows;

    if($nor > 0){

    $stmt->close();

    return("04");

    }else{

    $stmt->close();

    $autoNumber = new AutoNumber;

    $id = $autoNumber->NumberGenaration("id","supplier_tbl","SUP");

    $sqlInsert = "INSERT INTO supplier_tbl 
    VALUES(?,?,?,?,?,0);";

    $stmt = $this->dbResult->prepare($sqlInsert);

    $stmt->bind_param("sssss",$id,$name,$address,$phone,$email);

    $sqlResult = $stmt->execute();

    $stmt->close();

    if($sqlResult > 0){
    return("01");
    }else{
    return("02");
    }

    }

}

public function supplierList(){

    $sqlSelect = "SELECT * FROM supplier_tbl 
    WHERE d_status = 0 
    ORDER BY id DESC;";

    $stmt = $this->dbResult->prepare($sqlSelect);

    $stmt->execute();

    $sqlResult = $stmt->get_result();

    $nor = $sqlResult->num_rows;

    if($nor > 0){

    while($rec = $sqlResult->fetch_assoc()){

    echo('
    <tr>
    <td>'.htmlspecialchars($rec['name']).'</td>
    <td>'.htmlspecialchars($rec['phone']).'</td>
    <td>'.htmlspecialchars($rec['email']).'</td>
    <td>
    <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="'.$rec['id'].'">Edit</button>
    <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="'.$rec['id'].'">Delete</button>
    </td>
    </tr>');

    }

    }else{

    echo('
    <tr>
    <td colspan="4">
    <div class="alert alert-danger mb-0">
    No Suppliers Found
    </div>
    </td>
    </tr>');

    }

    $stmt->close();

}

public function supplierdrop(){

  $sqlSelect = "SELECT * FROM supplier_tbl
  WHERE d_status = 0
  ORDER BY name ASC";

  $stmt = $this->dbResult->prepare($sqlSelect);

  if(!$stmt){

    echo($this->dbResult->error);

    return;

  }

  $stmt->execute();

  $result = $stmt->get_result();

  if($result->num_rows > 0){

    echo('<option value="" selected disabled>Select Supplier</option>');

    while($rec = $result->fetch_assoc()){

      echo('<option value="'.$rec['id'].'">'.htmlspecialchars($rec['name']).'</option>');

    }

  }else{

    echo('<option value="">No Suppliers Found</option>');

  }

  $stmt->close();

}

public function editSupplier($id,$name,$address,$phone,$email){

    $sqlCheck = "SELECT * FROM supplier_tbl 
    WHERE (email = ? OR phone = ?) 
    AND id != ? 
    AND d_status = 0;";

    $stmt = $this->dbResult->prepare($sqlCheck);

    $stmt->bind_param("sss",$email,$phone,$id);

    $stmt->execute();

    $sqlResult = $stmt->get_result();

    if($sqlResult->num_rows > 0){

    $stmt->close();

    return("04");

    }

    $stmt->close();

    $sqlUpdate = "UPDATE supplier_tbl 
    SET name=?, address=?, phone=?, email=? 
    WHERE id=?;";

    $stmt = $this->dbResult->prepare($sqlUpdate);

    $stmt->bind_param("sssss",$name,$address,$phone,$email,$id);

    $sqlResult = $stmt->execute();

    $stmt->close();

    if($sqlResult > 0){
    return("01");
    }else{
    return("02");
    }

}

public function delete_supplier($uid){

    $sqlDelete = "UPDATE supplier_tbl 
    SET d_status = 1 
    WHERE id = ?;";

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

public function supplierdata($uid){

    $sqlSelect = "SELECT * FROM supplier_tbl 
    WHERE id = ?;";

    $stmt = $this->dbResult->prepare($sqlSelect);

    $stmt->bind_param("s",$uid);

    $stmt->execute();

    $sqlResult = $stmt->get_result();

    if($sqlResult->num_rows > 0){

    $rec = $sqlResult->fetch_assoc();

    $stmt->close();

    return json_encode($rec);

    }

    $stmt->close();

}

}
?>