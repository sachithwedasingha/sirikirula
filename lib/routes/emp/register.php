<?php
// Include the necessary functions or classes
include_once('../../function/empFunction.php');

// Retrieve data from the POST request
$customerFname = $_POST['Customer_fname'] ?? '';
$customerLname = $_POST['Customer_lname'] ?? '';
$customerNIC = $_POST['Customer_nic'] ?? '';
$customerTelnum = $_POST['Customer_telnum'] ?? '';
$customerGender = $_POST['Customer_gender'] ?? '';
$customerEmail = $_POST['Customer_email'] ?? '';
$customerBirthday = $_POST['Customer_birthday'] ?? '';
$customerAge = $_POST['Customer_age'] ?? '';
$userType = $_POST['userType'] ?? '';
$customerAddress = $_POST['Customer_address'] ?? '';
$customerPswd = $_POST['Customer_pswd'] ?? '';
$location = $_POST['location'] ?? '';

// Instantiate the user object
$userObj = new Employee();

// Call the user registration method (assuming you have such a method)
$result = $userObj->empRegistration(
    $customerFname,
    $customerLname,
    $customerNIC,
    $customerTelnum,
    $customerGender,
    $customerEmail,
    $customerBirthday,
    $customerAddress,
    $customerPswd,
    $userType,
    $location
);

// Output the result (success or error message)
echo($result);
?>
