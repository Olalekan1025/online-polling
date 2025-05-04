<?php
session_start();
include("db_connect.php");
include("globalFunctions.php");
header("Content-Type:application/json");

//FUNCTION TO VERIFY EMAIL ADDRESS |||||| Starts >>>> 
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['validationCode']) && !empty($_POST['verificationEmail']) && $_POST['authEmailVerification'] == true) {

  $userEmail = mysqli_real_escape_string($conn, $_POST['verificationEmail']);
  $validationCode = mysqli_real_escape_string($conn, $_POST['validationCode']);

  //get users Data
  $getUser = getHostInfo($conn, $userEmail);
  if (!password_verify($validationCode, $getUser['verificationCode'])) { //check if validation code is correct
    $response = array("status" => "error", "message" => "Invalid Verification Code, please check and try again.", "redirectPage" => '', "header" => "Error");
    echo json_encode($response);
  } else {
    if (count($getUser) > 0) {
      if ($getUser['email'] === $userEmail && password_verify($validationCode, $getUser['verificationCode'])) {

        //update update users verification and isVerified status  
        $stmt = $conn->prepare("UPDATE users SET `lastDeviceIP`=?, `isVerified`='1', `verificationCode` ='' WHERE `email` =?");
        $stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $userEmail);
        $statusUpdateSuccess = $stmt->execute() or die($stmt->error);
        $stmt->close();
        if ($statusUpdateSuccess) {
          $response = array("status" => "success", "message" => "You're all set! Feel free to log in to your account now that your email has been confirmed.", "redirectPage" => "" . '&&userEmail=' . $userEmail, "header" => "Successful Verification");

          // Take Activity Log
          $userId = $userEmail;
          $eventType = "emailVerification";
          $eventDescription = $getUser["email"] . " verification was successfully";
          $status = "success";
          $errorMessage = null;
          $logActivity = insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);
        }
      } else {
        $response = array("status" => "error", "message" => "Verification link has been tampered with, please check your email for valid verification link", "redirectPage" => "");
      }
      echo json_encode($response);
    } else {
      $response = array("status" => "warning", "message" => "Unable to verify user, please try again later", "redirectPage" => '');
      echo json_encode($response);
    }
  }
  exit();
}
//FUNCTION TO VERIFY EMAIL ADDRESS |||||| Ends >>>> 
