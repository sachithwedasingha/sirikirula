<?php
//we need to start the sessions 
session_start();

//include main.php
include_once('main.php');

//include auto number module 
include_once('auto_id.php');

class Station extends Main{

  public function addstation($Name, $discription,$address,$contact_no){

    $sqlSelect = "SELECT * FROM station_tbl WHERE (name = ?) AND d_status = 0;";
    $stmt = $this->dbResult->prepare($sqlSelect);

    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->bind_param("s",$Name);
    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $nor = $sqlResult->num_rows;

    if($nor > 0){   
      $stmt->close();
      return("04");
    }else{

      $stmt->close();
      $autoNumber = new AutoNumber;
      $id = $autoNumber->NumberGenaration("id","station_tbl","STA");
      $sqlInsert="INSERT INTO station_tbl(id,name,details,address,contact_no,d_status) VALUES(?,?,?,?,?,0)";
      $stmt = $this->dbResult->prepare($sqlInsert);
      if($this->dbResult->error){
        echo($this->dbResult->error);
        exit;
      }
      $stmt->bind_param("sssss",$id,$Name,$discription,$address,$contact_no);
      $sqlResult = $stmt->execute();
      $stmt->close();
      if($sqlResult > 0){
        return("01");
      }else{
        return("02");
      }
    }
  }

  public function editPackage($id,$productName,$discription,$address,$contact_no){

    $sqlInsert="UPDATE station_tbl SET name=?,details=?,address=?,contact_no=? WHERE id=?";
    $stmt = $this->dbResult->prepare($sqlInsert);
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->bind_param("sssss",$productName,$discription,$address,$contact_no,$id);
    $sqlResult = $stmt->execute();
    $stmt->close();
    if($sqlResult > 0){
      return("01");
    }else{
      return("02");
    }
  }

  public function proList(){

    $sqlSelect = "SELECT * FROM station_tbl WHERE d_status = 0 ORDER BY id ASC;";
    $stmt = $this->dbResult->prepare($sqlSelect);
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $nor = $sqlResult->num_rows;
    if($nor > 0){
      while($rec = $sqlResult->fetch_assoc()){
        echo('
        <tr>
          <td>'.htmlspecialchars($rec['name']).'</td>
          <td>'.htmlspecialchars($rec['details']).'</td>
          <td>'.htmlspecialchars($rec['address']).'</td>
          <td>'.htmlspecialchars($rec['contact_no']).'</td>
          <td>
            <button type="button" class="btn btn-warning btn-edit" data-id="'.$rec['id'].'">Edit</button>
            <button type="button" class="btn btn-danger btn-delete" data-id="'.$rec['id'].'">Delete</button>
          </td>
        </tr>');
      }

    }else{

      echo('
      <tr>
        <td colspan="5">
          <div class="alert alert-danger mb-0" role="alert">
            No Branches Are Found!
          </div>
        </td>
      </tr>');
    }
    $stmt->close();
  }

  public function stationdrop(){

    $sqlSelect = "SELECT * FROM station_tbl WHERE d_status = 0 ORDER BY id ASC;";
    $stmt = $this->dbResult->prepare($sqlSelect);
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $nor = $sqlResult->num_rows;

    if($nor > 0){
      echo('<option value="" selected>Select Branch</option>');
      while($rec = $sqlResult->fetch_assoc()){
        echo('<option value="'.$rec['id'].'">'.htmlspecialchars($rec['name']).'</option>');
      }

    }else{
      echo('<option value="0">No Branches are Found</option>');
    }
    $stmt->close();
  }

  public function delete_station($uid){

    $update1 = "UPDATE station_tbl SET d_status = 1 WHERE id = ? AND d_status = 0;";
    $stmt = $this->dbResult->prepare($update1);
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->bind_param("s",$uid);
    $sqlResult = $stmt->execute();
    $stmt->close();
    if($sqlResult > 0){
      return("ok"); 
    }else{
      return("erroe"); 
    }

  }   

  public function prodata($uid){

    $sqlSelect = "SELECT * FROM station_tbl WHERE id = ?;";
    $stmt = $this->dbResult->prepare($sqlSelect);
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->bind_param("s",$uid);
    $stmt->execute();
    $sqlResult = $stmt->get_result();

    $nor = $sqlResult->num_rows;

    if($nor > 0){
      $rec = $sqlResult->fetch_assoc();
      $stmt->close();
      return json_encode($rec);
    }

    $stmt->close();
  }
}
?>