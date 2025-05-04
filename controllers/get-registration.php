<?php
session_start();

include("db_connect.php");
include("globalFunctions.php");
header('Content-Type: application/json');

//USER REGISTRATION |||| Starts.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['email'], $_POST['regUser'], $_POST['confirmPassword'])) {
  date_default_timezone_set("Africa/Lagos");
  $hostID = "re" . generateNumericStrings(10);
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
  $hashPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);
  $verificationCode = generateVerificationCode();
  $hashVerificationCode = password_hash($verificationCode, PASSWORD_DEFAULT);

  // Check if account has been registered
  $getDuplicate = getHostInfo($conn, $email);

  if ($getDuplicate && $getDuplicate['isVerified'] == 1) {
    $response = array(
      'status' => 'error',
      'message' => 'This Account already exists with ' . $email . ' as an email',
      'header' => 'Duplicate Account',
      'feedbackResponse' => false
    );
  } elseif ($getDuplicate && $getDuplicate['isVerified'] == "0") {
    // Update Account Instead
    $stmt = $conn->prepare("UPDATE users SET verificationCode = ?, isVerified = '0', regDate = now(), username = ?, accountType = ?, password = ? WHERE email = ?");
    $stmt->bind_param("sssss", $hashVerificationCode, $username, $type, $hashPassword, $email);
    $success = $stmt->execute();

    if ($success) {
      sendVerificationEmail($email, $username, $verificationCode);
      logActivity($conn, $email, "userRegistration", $email . " registered successfully as " . $username);
      $response = array(
        'status' => 'success',
        'message' => 'Registration was successful, a verification code has been sent to ' . $email . ' kindly check to confirm your email!',
        'header' => 'Successful Registration',
        'feedbackResponse' => true
      );
    } else {
      $response = array(
        'status' => 'error',
        'message' => 'Unable to register.',
        'header' => 'Registration Failed',
        'feedbackResponse' => false
      );
    }
  } else {
    // Insert user registration
    $stmt = $conn->prepare("INSERT INTO users (`hostID`, `username`, `email`, `accountType`, `verificationCode`, `password`, `regDate`) VALUES(?,?,?,?,?,?,now())");
    $stmt->bind_param("ssssss", $hostID, $username, $email, $type, $hashVerificationCode, $hashPassword);
    $success = $stmt->execute();

    if ($success) {
      sendVerificationEmail($email, $username, $verificationCode);
      logActivity($conn, $email, "userRegistration", $email . " registered successfully as " . $username);
      $response = array(
        'status' => 'success',
        'message' => 'Registration was successful, a verification code has been sent to ' . $email . ' kindly check to confirm your email!',
        'header' => 'Successful Registration',
        'feedbackResponse' => true
      );
    } else {
      $response = array(
        'status' => 'error',
        'message' => 'Unable to register.',
        'header' => 'Registration Failed',
        'feedbackResponse' => false
      );
    }
  }

  $stmt->close();
  echo json_encode($response);
  exit();
}

function sendVerificationEmail($email, $username, $verificationCode)
{
  $otherPlaceholderArray = [];
  $subject = "Account Verification";
  $emailTitle = "Notification - Confirm Your Email Address";
  $template_file = "../emailTemplates/verificationEmail.php";
  $name = $username;
  $siteAddress = "https://poll.homdroid.com";
  $customURL = "https://poll.homdroid.com/verification?token=" . generateRandomAlphaNumericStrings(100) . "&&isVerifyCode=true&&redirectLink=" . $siteAddress . "/login?type=user&&dataQuery=user&&userEmail=" . $email;
  $customText = $verificationCode;
  sendEmail($email, $subject, $emailTitle, $name, $siteAddress, $customURL, $customText, $template_file, $otherPlaceholderArray);
}

function logActivity($conn, $userId, $eventType, $eventDescription)
{
  $status = "success";
  $errorMessage = null;
  insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);
}
//USER REGISTRATION |||| Ends.

//VERIFY USER EMAIL ENTRY BEFORE SUBMITTING |||||| Starts >>>>>>>
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userEmailEntryVer'])) {
  if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ? AND isVerified= 1 ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($getDuplicate);
    $stmt->fetch();
    $stmt->close();

    if ($getDuplicate > 0) {
      $response = array("status" => true, "message" => "Email already exists.");
    } else {
      $response = array("status" => false, "message" => "Email is unique.");
    }
  } else {
    $response = array("status" => false, "message" => "Email not provided.");
  }
  echo json_encode($response);
  exit();
}
//VERIFY USER EMAIL ENTRY BEFORE SUBMITTING |||||| Ends >>>>>>>

//VERIFY USER USERNAME ENTRY BEFORE SUBMITTING |||||| Starts >>>>>>>
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userUsernameEntryVer'])) {
  if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ? AND isVerified= 1 ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($getDuplicate);
    $stmt->fetch();
    $stmt->close();

    if ($getDuplicate > 0) {
      $response = array("status" => true, "message" => "Username already exists.");
    } else {
      $response = array("status" => false, "message" => "Username is unique.");
    }
  } else {
    $response = array("status" => false, "message" => "Username not provided.");
  }
  echo json_encode($response);
  exit();
}
//VERIFY USER USERNAME ENTRY BEFORE SUBMITTING |||||| Ends >>>>>>>