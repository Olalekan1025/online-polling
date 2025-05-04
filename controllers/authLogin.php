<?php
session_start();
include("globalFunctions.php");
header("Content-Type:application/json");


//FUNCTION TO LOGIN USER |||||| Starts >>>>
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['authUserLogin']) && $_POST['authUserLogin'] == true) {

  $loginID = mysqli_real_escape_string($conn, $_POST['userLoginID']);
  $password = mysqli_real_escape_string($conn, $_POST['userPassword']);
  date_default_timezone_set("Africa/Lagos");
  $date = date("j-m-Y, g:i a");


  $getUser = getHostInfo($conn, $loginID); //get users Data
  $getPreferenceData = getPreferences($conn); //Get Access Control From Preference

  if ($getPreferenceData['portalAccess'] != "enable") { //check for portal access control
    $response = array("status" => "error", "message" => "Access Denied", "redirectPage" => '', "header" => "Error");
    echo json_encode($response);
  } else {
    if (!empty($getUser)) {

      if (!empty($getUser) && $getUser["isVerified"] != "1") {

        $response = array("status" => "warning", "message" => "Your account is pending email verification. Please check your inbox or spam folder to verify your account. If you did not get an email, kindly register account again. Remember to verify your account before it expires in 30 days. Thank you!", "redirectPage" => '', 'header' => 'Pending Email Verification');
      } elseif (password_verify($password, $getUser['password'])) {


        // Update user login status
        $stmt = $conn->prepare("UPDATE users SET onlineStatus = '1', lastAccess = now(), lastDeviceIP = ? WHERE username = ? OR email = ?");
        $stmt->bind_param("sss", $_SERVER['REMOTE_ADDR'], $loginID, $loginID);
        $statusUpdateSuccess = $stmt->execute() or die($stmt->error);
        $stmt->close();

        if ($statusUpdateSuccess) {
          //prepare user sessions
          $_SESSION['hostID'] = $getUser["hostID"];
          $_SESSION['hostEmail'] = $getUser["email"];
          $_SESSION['hostRole'] = $getUser["role"];
          $_SESSION['portalAccess'] = $getPreferenceData["portalAccess"];
          $redirectPage = isset($_SESSION["previousPage"]) ? $_SESSION["previousPage"]  : './dashboard';

          $response = array("status" => "success", "message" => "<em class='icon ni ni-check'></em> Credentials Confirmed", "redirectPage" => $redirectPage, "header" => "Successful Login");

          // Take Activity Log
          $userId = $getUser["hostID"];
          $eventType = "login";
          $eventID = $getUser["hostID"];
          $eventDescription = $getUser["email"] . " logged in successfully";
          $status = "success";
          $errorMessage = null;
          $logActivity = insertLogEvent($conn, $userId, $eventType, $eventID,  $eventDescription, $status, $errorMessage);
        }
      } else {
        $response = array("status" => "error", "message" => "Invalid Authentication", "redirectPage" => "", "header" => "Failed Login");
      }
      echo json_encode($response);
    } else { //*** Give feedback as no records found*/
      $response = array("status" => "warning", "message" => "Account does not seem to exist on our database. ", "redirectPage" => '', "header" => "No records");
      echo json_encode($response);
    }
  }

  exit();
}
//FUNCTION TO LOGIN USER |||||| Ends >>>> 