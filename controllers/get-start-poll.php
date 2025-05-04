<?php
session_start();
include("globalFunctions.php");
header("Content-Type:application/json");

/** Sanitize input data (for additional user inputs if needed)*/

function sanitizeInput($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}

//::||Validate Voter Email Address And OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['validatePollEmailAddressRequest'], $_POST['voterEmail']) && $_POST['validatePollEmailAddressRequest'] == "true") {


  $pollID = isset($_POST['pollID']) ? sanitizeInput($_POST['pollID']) : ''; //::||Sanitize PollID
  $voterEmailOTP = isset($_POST['voterEmailOTP']) ? sanitizeInput($_POST['voterEmailOTP']) : ''; //::||Sanitize OTP
  $voterEmail = filter_var(sanitizeInput($_POST['voterEmail']), FILTER_VALIDATE_EMAIL); //::||Sanitize Email Address
  $pollInfo = getPollByID($conn, $pollID);
  $hostID = isset($pollInfo['hostID']) ? $pollInfo['hostID'] : '';
  $voterPollEmail = !empty($pollID) ?  getVotersPollByEmail($conn,  $hostID, $pollID, $voterEmail) : '';
  $isPrivatePoll = !empty($pollInfo) && $pollInfo['visibility'] == "private" ? true : false;
  $privateEligibilityValid = !empty($pollID) && !empty($voterPollEmail)  ? true : false;
  $voterID = "VT" . strtoupper(generateRandomAlphaNumericStrings(8));

  if (!$voterEmail) {
    $response = ["status" => "error", "message" => "Invalid email address"];
  } elseif (!empty($voterEmailOTP)) {
    if (isset($_SESSION['OTPinSession']) && password_verify($voterEmailOTP, $_SESSION['OTPinSession'])) {
      /**Every Other Condition is passed here */
      //::01 Check the visibility status of the election (Private/Public)
      if ($isPrivatePoll && !$privateEligibilityValid) {
        $response = ["status" => "ineligible", "message" => "This is a private poll and you are not Eligible to take this poll", "header" => "Access Blocked"];
      } else {
        //:02 update the current otp as the voteSessionID
        $otpInSession =  password_hash($_SESSION['OTPinSession'], PASSWORD_DEFAULT);
        // Check if the voter email already exists for the given hostID
        $checkQuery = "SELECT COUNT(*) FROM voters WHERE email = ? AND hostID = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('ss', $voterEmail, $hostID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_row();
        $count = $row[0];

        // If the email doesn't exist, insert it into the database
        if ($count == 0) {
          $insertQuery = "INSERT INTO voters (voterID, email, hostID, voteSessionID) VALUES (?, ?, ?, ?)";
          $stmt = $conn->prepare($insertQuery);
          $stmt->bind_param('ssss', $voterID, $voterEmail, $hostID, $otpInSession);
        } else {
          // Update the voteSessionID for the existing voter
          $stmt = $conn->prepare("UPDATE voters SET voteSessionID=? WHERE email=? AND hostID=?");
          $stmt->bind_param("sss", $otpInSession, $voterEmail, $hostID);
        }
        if ($stmt->execute()) {
          //::02b  Get The voters Information from the voters Table if available
          $getVoterInfo =  getVoterByEmail($conn, $voterEmail, $pollInfo['hostID']);
          $voterInfo = !empty($getVoterInfo) ?  ["fname" => $getVoterInfo['fname'], "sname" => $getVoterInfo['sname'], "gender" => $getVoterInfo['gender']] : null;
          $response = ["status" => "eligible", "message" => "Voter is eligible to take poll", "voterInfo" => $voterInfo];
        }
      }
    } else {
      $response = ["status" => "error", "message" => "Please enter the correct OTP and try again.", "header" => "Invalid OTP!"];
    }
  } else {
    $emailToken = generateVerificationCode(); //::||Generate a random OTP
    $_SESSION["OTPinSession"] = password_hash($emailToken, PASSWORD_DEFAULT);

    //::||Prepare and send verification email
    $emailData = [
      "{OTP}" => $emailToken ?? 'N/A'
    ];


    $sendEmail = sendEmail(
      $voterEmail,
      "Online Poll Email Verification",
      "Email Verification",
      $voterEmail,
      "https://poll.homdroid.com",
      "https://poll.homdroid.com",
      "",
      "../emailTemplates/otpEmail.php",
      $emailData
    );
    if ($sendEmail) {
      $response = ["status" => "success", "message" => "otp generated"];
    } else {
      $response = ["status" => "error", "message" => "Email Failed"];
    }
  }

  echo json_encode($response);
  exit();
}
//:::Validate Voter Email Address And OTP

//:::Start Voters Poll
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pollID"], $_POST["voterEmail"], $_POST["fname"], $_POST["lname"], $_POST["gender"], $_POST["processStartPoll"]) && !empty($_POST["voterEmail"]) && !empty($_POST["fname"]) && !empty($_POST["lname"]) && !empty($_POST["gender"]) && $_POST["processStartPoll"] == "true") {

  $pollID = isset($_POST['pollID']) ? sanitizeInput($_POST['pollID']) : null; //::||Sanitize PollID
  $voterEmail = isset($_POST['voterEmail']) ? sanitizeInput($_POST['voterEmail']) : null; //::||Sanitize Voter Email
  $fname = isset($_POST['fname']) ? sanitizeInput($_POST['fname']) : null; //::||Sanitize Voter fname
  $lname = isset($_POST['lname']) ? sanitizeInput($_POST['lname']) : null; //::||Sanitize Voter lname
  $gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : null; //::||Sanitize Voter gender
  $pollInfo = getPollByID($conn, $pollID);
  $hostID = $pollInfo['hostID'];
  $getVoterInfo =  getVoterByEmail($conn, $voterEmail, $hostID);

  if (password_verify($_SESSION['OTPinSession'], $getVoterInfo['voteSessionID'])) {
    //:::01 Update Voters Information
    $updateStmt = $conn->prepare("UPDATE voters SET fname=?, sname=?, gender=? WHERE email=?");
    $updateStmt->bind_param("ssss", $fname, $lname, $gender, $voterEmail);

    //:::02 Get Poll Ready For Voter
    // Check if the voter email already exists for the given pollID and hostID
    $checkQuery = "SELECT COUNT(*) FROM poll_voters WHERE pollID = ? AND hostID = ? AND voterEmail = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('sss', $pollID, $hostID, $voterEmail);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_row();
    $count = $row[0];

    // If the email doesn't exist, insert it into the database
    $insertStmt = null;
    if ($count == 0) {
      $insertQuery = "INSERT INTO poll_voters (hostID, pollID, voterEmail, `registrationType`) VALUES (?, ?, ?, 'registered')";
      $insertStmt = $conn->prepare($insertQuery);
      $insertStmt->bind_param('sss', $hostID, $pollID, $voterEmail);
      $insertStmt->execute();
    }

    // Execute update and ensure insertion was successful
    $updateSuccess = $updateStmt->execute();
    $insertSuccess = ($insertStmt && $insertStmt->affected_rows > 0);

    // Determine if the response should be success
    if ($updateSuccess && ($count > 0 || $insertSuccess)) {
      $response = [
        "status" => "success",
        "message" => "Redirecting...",
        "header" => "Loading Booth Panel",
        "redirectLink" => "poll-booth?poll_id=" . $pollID . "&voterEmail=" . $voterEmail . "&session_id=" . $getVoterInfo['voteSessionID']
      ];
    } else {
      $response = ["status" => "error", "message" => "Something went wrong. Please try again.", "header" => "Error", "redirectLink" => ""];
    }
  } else {
    $response = ["status" => "error", "message" => "Something went wrong. Please try again.", "header" => "Failed", "redirectLink" => ""];
  }

  echo json_encode($response);
  exit();
}
//:::Start Voters Poll
