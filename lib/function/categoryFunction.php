<?php

// START SESSION
session_start();

// INCLUDE FILES
include_once('main.php');
include_once('auto_id.php');

class Category extends Main {

  // =========================================
  // ADD CATEGORY
  // =========================================
  public function addcategory($name){

    $name = trim($name);

    // VALIDATION
    if(empty($name)){
      return("Category name required");
    }

    // CHECK DUPLICATE
    $sqlCheck = "SELECT * FROM category_tbl
                 WHERE name = ?
                 AND d_status = 0";

    $stmt = $this->dbResult->prepare($sqlCheck);

    if(!$stmt){
      return($this->dbResult->error);
    }

    $stmt->bind_param("s", $name);

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

      $stmt->close();

      return("04");

    }

    $stmt->close();

    // GENERATE ID
    $autoNumber = new AutoNumber();

    $id = $autoNumber->NumberGenaration(
      "id",
      "category_tbl",
      "CAT"
    );

    // INSERT
    $sqlInsert = "INSERT INTO category_tbl
                  (id, name, d_status)
                  VALUES (?, ?, 0)";

    $stmt = $this->dbResult->prepare($sqlInsert);

    if(!$stmt){
      return($this->dbResult->error);
    }

    $stmt->bind_param("ss", $id, $name);

    $result = $stmt->execute();

    $stmt->close();

    if($result){

      return("01");

    }else{

      return("02");

    }

  }

  // =========================================
  // CATEGORY LIST
  // =========================================
  public function categoryList(){

    $sqlSelect = "SELECT * FROM category_tbl
                  WHERE d_status = 0
                  ORDER BY id ASC";

    $stmt = $this->dbResult->prepare($sqlSelect);

    if(!$stmt){

      echo($this->dbResult->error);

      return;
    }

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

      while($rec = $result->fetch_assoc()){

        echo('

          <tr>

            <td>'.htmlspecialchars($rec['name']).'</td>

            <td>

              <button
                type="button"
                class="btn btn-outline-primary btn-sm btn-edit"
                data-id="'.$rec['id'].'">

                Edit

              </button>

              <button
                type="button"
                class="btn btn-outline-danger btn-sm btn-delete"
                data-id="'.$rec['id'].'">

                Delete

              </button>

            </td>

          </tr>

        ');

      }

    }else{

      echo('

        <tr>

          <td colspan="2" class="text-center text-danger">

            No Categories Found

          </td>

        </tr>

      ');

    }

    $stmt->close();

  }


  public function categorydrop(){

    $sqlSelect = "SELECT * FROM category_tbl
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

      echo('<option value="" selected disabled>Select Category</option>');

      while($rec = $result->fetch_assoc()){

        echo('<option value="'.$rec['id'].'">'.htmlspecialchars($rec['name']).'</option>');

      }

    }else{

      echo('<option value="">No Categories Found</option>');

    }

    $stmt->close();

  }

  // =========================================
  // EDIT CATEGORY
  // =========================================
  public function editCategory($id, $name){

    $id = trim($id);
    $name = trim($name);

    // VALIDATION
    if(empty($id) || empty($name)){
      return("Required fields missing");
    }

    // CHECK DUPLICATE
    $sqlCheck = "SELECT * FROM category_tbl
                 WHERE name = ?
                 AND id != ?
                 AND d_status = 0";

    $stmt = $this->dbResult->prepare($sqlCheck);

    if(!$stmt){
      return($this->dbResult->error);
    }

    $stmt->bind_param("ss", $name, $id);

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

      $stmt->close();

      return("04");

    }

    $stmt->close();

    // UPDATE
    $sqlUpdate = "UPDATE category_tbl
                  SET name = ?
                  WHERE id = ?";

    $stmt = $this->dbResult->prepare($sqlUpdate);

    if(!$stmt){
      return($this->dbResult->error);
    }

    $stmt->bind_param("ss", $name, $id);

    $result = $stmt->execute();

    $stmt->close();

    if($result){

      return("01");

    }else{

      return("02");

    }

  }

  // =========================================
  // DELETE CATEGORY
  // =========================================
  public function delete_category($uid){

    $uid = trim($uid);

    if(empty($uid)){
      return("Invalid ID");
    }

    $sqlDelete = "UPDATE category_tbl
                  SET d_status = 1
                  WHERE id = ?";

    $stmt = $this->dbResult->prepare($sqlDelete);

    if(!$stmt){
      return($this->dbResult->error);
    }

    $stmt->bind_param("s", $uid);

    $result = $stmt->execute();

    $stmt->close();

    if($result){

      return("ok");

    }else{

      return("error");

    }

  }

  // =========================================
  // GET CATEGORY DATA
  // =========================================
  public function categorydata($uid){

    $uid = trim($uid);

    if(empty($uid)){
      return json_encode([]);
    }

    $sqlSelect = "SELECT * FROM category_tbl
                  WHERE id = ?";

    $stmt = $this->dbResult->prepare($sqlSelect);

    if(!$stmt){
      return json_encode([]);
    }

    $stmt->bind_param("s", $uid);

    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

      $rec = $result->fetch_assoc();

      $stmt->close();

      return json_encode($rec);

    }

    $stmt->close();

    return json_encode([]);

  }

}
?>