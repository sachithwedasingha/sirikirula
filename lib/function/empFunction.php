<?php
//we need to start the sessions 
session_start();


//include main.php
include_once('main.php');

//include auto number module 
include_once('auto_id.php');

//include image upload function
include_once('img_upload.php');

//include auto password genaretor
include_once('passwordgenaretor.php');


class Employee extends Main{

//lets create the Registration method
public function empRegistration($fname, $lname, $phone, $email, $nic, $birthday, $address, $gender, $type, $location){

  //validate user emails for duplicates
  $sqlQuery = "SELECT * FROM login_tbl 
  JOIN employer_tbl ON login_tbl.loginId = employer_tbl.emp_Id 
  WHERE (login_tbl.loginEmail = ? OR employer_tbl.emp_Nic = ?) 
  AND login_tbl.d_status = 0";

  $stmt = $this->dbResult->prepare($sqlQuery);

  //database error checking part
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("ss",$email,$nic);

  $stmt->execute();

  $sqlResult5 = $stmt->get_result();

  //lets count the number of rows
  $nor = $sqlResult5->num_rows;

  if($nor > 0){

    $stmt->close();

    return("04");

  }else{

    $stmt->close();

    //generate new id for a User 
    $autoNumber = new AutoNumber;
    $userId = $autoNumber->NumberGenaration("emp_Id","employer_tbl","EMP");

    //insert data to user table
    $sqlInsert = "INSERT INTO employer_tbl 
    VALUES(?,?,?,?,?,?,?,?,?,?,0);";

    $stmt = $this->dbResult->prepare($sqlInsert);

    //lets check the errors 
    if($this->dbResult->error){
      echo($this->dbResult->error);
      exit;
    }

    $stmt->bind_param(
      "ssssssssss",
      $userId,
      $fname,
      $lname,
      $gender,
      $nic,
      $birthday,
      $address,
      $phone,
      $email,
      $type
    );

    //we need to execute our sql by query 
    $sqlResult = $stmt->execute();

    $stmt->close();

    //lest check the result is 0 or not 
    if($sqlResult > 0){

      //lets create a hash by using PASSWORD HASH
      $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);

      //insert dataset into the login table 
      $insertLogin = "INSERT INTO login_tbl 
      VALUES(?,?,?,'',?,?,1,0);";

      $stmt = $this->dbResult->prepare($insertLogin);

      //lets check the errors 
      if($this->dbResult->error){
        echo($this->dbResult->error);
        exit;
      }

      $stmt->bind_param(
        "sssss",
        $userId,
        $email,
        $hashedPassword,
        $type,
        $location
      );

      $loginResult = $stmt->execute();

      $stmt->close();

      if($loginResult > 0){

        return("01");

      }else{

        return("02");

      }

    }else{

      return("03");

    }

  }

}

//this is user method to get current user Details
public function Current_User_Details() {

  // Check if user ID is set in the session
  if(isset($_SESSION['user'])){

    $id = $_SESSION['user'];

    // Prepare SQL statement
    $sqlSelect = "SELECT 
      employer_tbl.*,
      login_tbl.station,
      station_tbl.name AS station_name

      FROM employer_tbl

      JOIN login_tbl
      ON login_tbl.loginId = employer_tbl.emp_Id

      LEFT JOIN station_tbl
      ON station_tbl.id = login_tbl.station

      WHERE employer_tbl.emp_Id = ?

      ORDER BY employer_tbl.emp_Id DESC";

    $stmt = $this->dbResult->prepare($sqlSelect);

    if($stmt === false){

      echo json_encode([
        "status" => "error",
        "message" => $this->dbResult->error
      ]);

      exit;

    }

    // Bind parameter
    $stmt->bind_param("s",$id);

    // Execute
    if(!$stmt->execute()){

      echo json_encode([
        "status" => "error",
        "message" => $stmt->error
      ]);

      exit;

    }

    // Get result
    $result = $stmt->get_result();

    $data = [];

    if($result->num_rows > 0){

      while($row = $result->fetch_assoc()){

        $data[] = $row;

      }

      echo json_encode([
        "status" => "success",
        "data" => $data
      ]);

    }else{

      echo json_encode([
        "status" => "empty",
        "data" => []
      ]);

    }

    // Clean up
    $result->free();

    $stmt->close();

  }

}

public function userList(){

  $sqlSelect = "SELECT * FROM employer_tbl 
  JOIN login_tbl ON login_tbl.loginId = employer_tbl.emp_Id 
  JOIN station_tbl ON station_tbl.id = login_tbl.station 
  WHERE employer_tbl.d_status = 0 
  ORDER BY employer_tbl.emp_Id DESC;";

  $stmt = $this->dbResult->prepare($sqlSelect);

  //lets check the errors 
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  //sql execute
  $stmt->execute();

  $sqlResult = $stmt->get_result();

  //check the number of rows
  $nor = $sqlResult->num_rows;

  if($nor > 0){

    while($rec = $sqlResult->fetch_assoc()){

      // Calculate age from date of birth
      $dob = new DateTime($rec['emp_Birthday']);

      $today = new DateTime();

      $age = $today->diff($dob)->y;

      echo('
      <tr>
        <td>'.htmlspecialchars($rec['emp_FirstName']).' '.htmlspecialchars($rec['emp_SecondName']).'</td>
        <td>'.htmlspecialchars($rec['emp_Nic']).'</td>
        <td>'.htmlspecialchars($rec['emp_Phone']).'</td>
        <td>'.htmlspecialchars($rec['emp_Gender']).'</td>
        <td>'.htmlspecialchars($rec['emp_Email']).'</td>
        <td>'.$age.'</td>
        <td>'.htmlspecialchars($rec['name']).'</td>
       <td>
      <button type="button" class="btn btn-warning btn-sm" onclick="editacc(\''.$rec['loginId'].'\')">Edit</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="deleteuser(\''.$rec['loginId'].'\')">Delete</button>
      <button type="button" class="btn btn-info btn-sm" onclick="resetPassword(\''.$rec['loginId'].'\')">Reset Password</button>
      ');
      if($rec['loginPin'] == '' || $rec['loginPin'] == null){

      echo('

      <button type="button" class="btn btn-dark btn-sm" onclick="setPin(\''.$rec['loginId'].'\')">
      Set PIN
      </button>

      ');

        }else{

        echo('

        <button type="button" class="btn btn-secondary btn-sm" onclick="setPin(\''.$rec['loginId'].'\')">
        Reset PIN
        </button>

        ');
        }

        echo('

      </td>
      </tr>');

    }

  }else{

    echo('
    <tr>
      <td colspan="8">
        <div class="alert alert-danger mb-0" role="alert">
          No Users Are Found!
        </div>
      </td>
    </tr>');

  }

  $stmt->close();

}

public function delete_user($uid){

  $update1 = "UPDATE employer_tbl 
  SET d_status = 1 
  WHERE emp_Id = ? 
  AND d_status = 0;";

  $stmt = $this->dbResult->prepare($update1);

  //lets check the errors 
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("s",$uid);

  //sql execute
  $sqlResult = $stmt->execute();

  $stmt->close();

  $update2 = "UPDATE login_tbl 
  SET d_status = 1 
  WHERE loginId = ? 
  AND d_status = 0;";

  $stmt = $this->dbResult->prepare($update2);

  //lets check the errors 
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("s",$uid);

  //sql execute
  $sqlResult = $stmt->execute();

  $stmt->close();

  return("ok"); 

}

public function userdata($uid){

  $sqlSelect = "SELECT * FROM employer_tbl 
  JOIN login_tbl ON login_tbl.loginId = employer_tbl.emp_Id 
  WHERE emp_Id = ?;";

  $stmt = $this->dbResult->prepare($sqlSelect);

  //lets check the errors 
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("s",$uid);

  //sql execute
  $stmt->execute();

  $sqlResult = $stmt->get_result();

  //check the number of rows
  $nor = $sqlResult->num_rows;

  if($nor > 0){

    $rec = $sqlResult->fetch_assoc();

    $stmt->close();

    return json_encode($rec);

  }

  $stmt->close();

}

public function editdata($id, $fname, $lname, $phone, $email, $birthday, $address, $gender, $type, $location, $nic) {

  // Check if the email already exists in the login table
  $checkEmailQuery = "SELECT loginEmail FROM login_tbl 
  WHERE loginEmail = ? AND loginId != ?";

  $stmt = $this->dbResult->prepare($checkEmailQuery);

  // Check database errors
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("ss",$email,$id);

  $stmt->execute();

  $result = $stmt->get_result();

  // If email exists, return 04
  if($result->num_rows > 0){

    $stmt->close();

    return "04";

  }

  $stmt->close();

  // Update query
  $update1 = "UPDATE employer_tbl 
  JOIN login_tbl ON login_tbl.loginId = employer_tbl.emp_Id 
  SET 
  login_tbl.loginEmail = ?, 
  login_tbl.loginRole = ?, 
  employer_tbl.emp_JobTitle = ?, 
  login_tbl.station = ?,
  employer_tbl.emp_FirstName = ?, 
  employer_tbl.emp_SecondName = ?, 
  employer_tbl.emp_Email = ?, 
  employer_tbl.emp_Nic = ?, 
  employer_tbl.emp_Phone = ?, 
  employer_tbl.emp_Birthday = ?, 
  employer_tbl.emp_Address = ?,
  employer_tbl.emp_Gender = ?
  WHERE employer_tbl.emp_Id = ? 
  AND employer_tbl.d_status = 0;";

  $stmt = $this->dbResult->prepare($update1);

  // Check for SQL execution errors
  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param(
    "sssssssssssss",
    $email,
    $type,
    $type,
    $location,
    $fname,
    $lname,
    $email,
    $nic,
    $phone,
    $birthday,
    $address,
    $gender,
    $id
  );

  // Execute the update query
  $sqlResult = $stmt->execute();

  $stmt->close();

  if($sqlResult > 0){

    return "ok";

  }else{

    return "error";

  }

}

public function resetPassword($uid){

  $newPassword = password_hash("ABC@123", PASSWORD_DEFAULT);

  $sqlUpdate = "UPDATE login_tbl 
  SET loginPassword = ? 
  WHERE loginId = ?";

  $stmt = $this->dbResult->prepare($sqlUpdate);

  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("ss",$newPassword,$uid);

  $sqlResult = $stmt->execute();

  $stmt->close();

  if($sqlResult > 0){

    return("01");

  }else{

    return("02");

  }

}

public function setPin($uid,$pin){

  $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

  // CHECK DUPLICATE PIN
  $sqlCheck = "SELECT * FROM login_tbl 
  WHERE loginPin IS NOT NULL AND loginId != ?";

  $stmt = $this->dbResult->prepare($sqlCheck);

  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("s",$uid);

  $stmt->execute();

  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()){

    if(password_verify($pin,$row['loginPin'])){

      $stmt->close();

      return("04");

    }

  }

  $stmt->close();

  // UPDATE PIN
  $sqlUpdate = "UPDATE login_tbl 
  SET loginPin = ? 
  WHERE loginId = ?";

  $stmt = $this->dbResult->prepare($sqlUpdate);

  if($this->dbResult->error){
    echo($this->dbResult->error);
    exit;
  }

  $stmt->bind_param("ss",$hashedPin,$uid);

  $sqlResult = $stmt->execute();

  $stmt->close();

  if($sqlResult > 0){

    return("01");

  }else{

    return("02");

  }

}


}
?>