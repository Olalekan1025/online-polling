<?php
session_start();

include("db_connect.php");
include("globalFunctions.php");
header('Content-Type: application/json');

//SEND RESET PASSWORD LINK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetPasswordEmail'], $_POST["authResetPasswordEmail"], $_POST["authType"]) && $_POST['authResetPasswordEmail'] == true) {
  date_default_timezone_set("Africa/Lagos");
  $date = date("j-m-Y, g:i a");
  $email = $_POST['resetPasswordEmail'];
  $authType = $_POST['authType'];

  // Generate verification code
  $verificationCode = generateVerificationCode();
  $hashVerificationCode = password_hash($verificationCode, PASSWORD_DEFAULT);

  //Get auth database
  if ($authType === "user") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? ");
  }

  $stmt->bind_param("s", $email);
  $result = $stmt->execute() ? $stmt->get_result() : false;
  $getPasswordResetRecord = $result ? $result->fetch_array() : false;

  if (!empty($getPasswordResetRecord)) {
    //check if the user already did email account verification
    if ($getPasswordResetRecord['isVerified'] != 1) {
      $response = array('status' => 'warning', 'message' => 'This account ' . $email . ' has not been verified, kindly verify account to proceed with password reset', 'header' => 'Pending Verification', 'feedbackResponse' => false);
    } elseif ($getPasswordResetRecord['isVerified'] == 1) {
      // Update Account with verification code
      if ($authType === "user") {
        $stmt = $conn->prepare("UPDATE users SET verificationCode =? WHERE email =? ");
      }
      $stmt->bind_param("ss", $hashVerificationCode, $email);
      $verificationCodeUpdate = $stmt->execute();

      if ($verificationCodeUpdate) {
        $otherPlaceholderArray = [];
        //Send Verification Email
        $subject = "Password Reset";
        $emailTitle = "Notification - Password Reset";
        $template_file = "../emailTemplates/resetPasswordEmail.php";
        $name = !empty($getPasswordResetRecord['fname']) ? $getPasswordResetRecord['fname'] : $getPasswordResetRecord['email'];
        $siteAddress = "https://poll.homdroid.com";
        $customURL = "https://poll.homdroid.com/reset-password?token=" . generateRandomAlphaNumericStrings(100) . "&&isVerifyCode=" . $hashVerificationCode . "&&redirectLink=" . $siteAddress . "/index?type=" . $authType . "&&dataQuery=" . $authType . "&&userEmail=" . $email;
        $customText = "";
        sendEmail($email, $subject, $emailTitle, $name, $siteAddress, $customURL, $customText, $template_file, $otherPlaceholderArray);

        $response = array('status' => 'success', 'message' => 'Verification link has been sent to ' . $email . ' kindly check to reset password!', 'header' => 'Password Reset Link Sent', 'feedbackResponse' => true);

        // Take Activity Log
        $userId = $email;
        $eventType = "sentPasswordResetLink";
        $eventDescription = $email . " Customer Received A Password Reset Link  ";
        $status = "success";
        $errorMessage = null;
        $logActivity = insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);
      }
    }
  } else {
    $response = array('status' => 'error', 'message' => 'Account does not seems to exist on our database', 'header' => 'Record not found!', 'feedbackResponse' => false);
  }


  $stmt->close();
  echo json_encode($response);
  exit();
}
//SEND RESET PASSWORD LINK


//FUNCTION TO RESET USER PASSWORD |||||| Starts >>>> 
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['confirmPassword']) && !empty($_POST['userEmail']) && $_POST['authPasswordReset'] == true) {

  $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']);
  $validationCode =  $_POST['verificationCode'];
  $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
  $hashPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);
  $redirectLink = mysqli_real_escape_string($conn, $_POST['redirectLink']);
  $dataQuery = mysqli_real_escape_string($conn, $_POST['dataQuery']);

  //Get auth database
  if ($dataQuery === "user") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? ");
  }

  //get users Data
  $stmt->bind_param("s", $userEmail);
  $stmt->execute() or die($stmt->error);
  $userResult = $stmt->get_result();
  $getUser = $userResult->fetch_array();
  $stmt->close();

  if ($getUser['verificationCode'] != $validationCode) { //check if validation code is correct
    $response = array("status" => "error", "message" => "Password reset encountered an issue. Please verify the link and try again.", "redirectPage" => '', "header" => "Password Reset Failed");
    echo json_encode($response);
  } else {
    if ($userResult->num_rows > 0) {
      if ($getUser['email'] === $userEmail && $getUser['verificationCode'] === $validationCode) {

        //update update user verification and isVerified status
        if ($dataQuery === "user") {
          $stmt = $conn->prepare("UPDATE users SET `password` =?, `verificationCode` ='' WHERE `email` =?");
        }
        $stmt->bind_param("ss", $hashPassword, $userEmail);
        $statusUpdateSuccess = $stmt->execute() or die($stmt->error);
        $stmt->close();

        if ($statusUpdateSuccess) {
          $response = array("status" => "success", "message" => "You're all set! Feel free to log in to your account with your new password.", "redirectPage" => $redirectLink . '&&userEmail=' . $userEmail, "header" => "Password Changed");

          // Take Activity Log
          $userId = $userEmail;
          $eventType = "changedPassword";
          $eventDescription = $getUser["email"] . " password reset was successfully";
          $status = "success";
          $errorMessage = null;
          $logActivity = insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);
        }
      } else {
        $response = array("status" => "error", "message" => "The password reset verification link appears to be broken or expired. Please review and try again.", "redirectPage" => "", "header" => "Broken Link");
      }
      echo json_encode($response);
    } else { //*** Give feedback as no records found*/
      $response = array("status" => "warning", "message" => "Unable to verify user. Please review and try again.", "redirectPage" => '', "header" => "Invalid Verification");
      echo json_encode($response);
    }
  }

  exit();
}
//FUNCTION TO RESET USER PASSWORD |||||| Ends >>>> 