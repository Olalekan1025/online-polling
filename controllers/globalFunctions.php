<?php

require("db_connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// date_default_timezone_set("Africa/Lagos");

// Define the Gmail's smtp mail server
define('MAILHOST', "smtp.hostinger.com"); //smtp.gmail.com

//Define as a username the email that you use in your Gmail account
define('USERNAME', "support@homdroid.com");

//Define your 16 digit Gmail app-password.
define('PASSWORD', "Vttrader@101");


//Define the email address from which the email is sent.
define('SEND_FROM', "support@homdroid.com");

//Define the name of the website from which the email is sent
define('SEND_FROM_NAME', "Roehampton University");

//Define the reply-to address
define('REPLY_TO', "support@homdroid.com");

//Define the reply-to name
define('REPLY_TO_NAME', "Roehampton University");


// Function to extract browser information from user agent string
function getBrowser($userAgent)
{
  $browser = 'Unknown';
  if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
    $browser = 'Internet Explorer';
  } elseif (preg_match('/Firefox/i', $userAgent)) {
    $browser = 'Firefox';
  } elseif (preg_match('/Chrome/i', $userAgent)) {
    $browser = 'Chrome';
  } elseif (preg_match('/Safari/i', $userAgent)) {
    $browser = 'Safari';
  } elseif (preg_match('/Opera/i', $userAgent)) {
    $browser = 'Opera';
  } elseif (preg_match('/Netscape/i', $userAgent)) {
    $browser = 'Netscape';
  }
  return $browser;
}

//Functions For Random AlphaNumeric String generator
function generateRandomAlphaNumericStrings($length)
{
  $characters = '1234567890';
  $characters .= 'abcdefghijklmnopqrstuvwxyz';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

// Function to generate a random numeric
function generateNumericStrings($length)
{
  $characters = '1234567890';
  //$characters .= 'abcdefghijklmnopqrstuvwxyz';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

// Function to generate a random Numeric verification code
function generateVerificationCode($length = 6)
{
  $characters = '1234567890';
  //$characters .= 'abcdefghijklmnopqrstuvwxyz';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

// Function to insert log events into the logs table
function insertLogEvent($conn, $userId, $eventType, $eventID, $eventDescription, $status = null, $errorMessage = null)
{
  // Prepare INSERT statement
  $stmt = $conn->prepare("INSERT INTO logs (logId, userId, eventType, eventID, eventDescription, status, errorMessage, ipAddress, userAgent, browser, operatingSystem, deviceType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  // Get IP address
  $ipAddress = $_SERVER['REMOTE_ADDR'];

  // Get user agent
  $userAgent = $_SERVER['HTTP_USER_AGENT'];

  // Get browser information
  $browser = getBrowser($userAgent);

  // Get operating system information
  $operatingSystem = php_uname('s');

  // Get device type
  $deviceType = '';
  if (strpos($userAgent, 'Mobile') !== false) {
    $deviceType = 'Mobile';
  } elseif (strpos($userAgent, 'Tablet') !== false) {
    $deviceType = 'Tablet';
  } else {
    $deviceType = 'Desktop';
  }

  // Generate log ID
  $logID = generateRandomAlphaNumericStrings(12);

  // Bind parameters and execute the statement
  $stmt->bind_param("ssssssssssss", $logID, $userId, $eventType, $eventID, $eventDescription, $status, $errorMessage, $ipAddress, $userAgent, $browser, $operatingSystem, $deviceType);
  $result = $stmt->execute();

  // Close the statement
  $stmt->close();

  return $result;
}

//Function to generate Initial
function generateInitials($name)
{
  // Split the name into words
  $words = explode(" ", $name);

  // Initialize an empty string for initials
  $initials = "";

  // Get the first initial from the first name
  if (!empty($words[0])) {
    $initials .= strtoupper(substr($words[0], 0, 1));
  }

  // If there's more than one word, get the first initial from the last name
  if (count($words) > 1 && !empty($words[count($words) - 1])) {
    $initials .= strtoupper(substr($words[count($words) - 1], 0, 1));
  }

  // Return the initials
  return $initials;
}

// Function to structure timestamp
function structureTimestamp($timestamp)
{
  // Set the default timezone
  // date_default_timezone_set("Africa/Lagos");

  // Convert timestamp to a DateTime object
  $dateTime = new DateTime($timestamp);
  $now = new DateTime();

  // Check if the timestamp is from today
  if ($dateTime->format('Y-m-d') == $now->format('Y-m-d')) {
    // Calculate the difference in seconds
    $diff = $now->getTimestamp() - $dateTime->getTimestamp();

    // If the difference is less than 60 seconds, return "Now"
    if ($diff < 60) {
      return "Now";
    }

    // If the difference is less than 3600 seconds (1 hour), return the minutes ago
    if ($diff < 3600) {
      $minutes = floor($diff / 60);
      return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
    }

    // Return the time in the format of Today, H:i am/pm
    return "Today at " . $dateTime->format('g:i A');
  }

  // Check if the timestamp is from yesterday
  $yesterday = new DateTime('yesterday');
  if ($dateTime->format('Y-m-d') == $yesterday->format('Y-m-d')) {
    return "Yesterday at " . $dateTime->format('g:i A');
  }

  // If the timestamp is older than yesterday, return the date and time
  return $dateTime->format('jS F, Y \a\t g:i A');
}

//Function to upload single File
function handleFileUpload($file, $directory, $allowedFormats, $customFileName)
{
  if (isset($file) && !empty($file['name'])) {
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExtension), $allowedFormats)) {
      $filePath = "../{$directory}/" . $customFileName .   "-" . basename($file['name']);
      $fileName = "{$directory}/" . $customFileName .   "-" . basename($file['name']);
      if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $fileName;
      }
    }
  }
  return false;
}

//Function to upload multiple files
function handleMultipleFileUploads($files, $directory, $allowedFormats, $customFileName)
{
  $fileNames = [];
  $count = 0;
  foreach ($files['name'] as $index => $name) {
    $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
    if (in_array(strtolower($fileExtension), $allowedFormats)) {
      $count++;
      // $uniqueFileName = "{$directory}/" . uniqid() . "_" . basename($name);
      // $filePath = "../../{$directory}/" . $uniqueFileName;
      $filePath = "../../{$directory}/" . $customFileName . "-" . $count  . "-" . basename($name);
      $uniqueFileName = "{$directory}/" . $customFileName . "-" . $count  . "-" . basename($name);
      if (move_uploaded_file($files['tmp_name'][$index], $filePath)) {
        $fileNames[] = $uniqueFileName;
      } else {
        return false;
      }
    }
  }
  return $fileNames;
}

// Function to upload CSV file
function uploadCSV($conn, $file, $tableName, $columnsToInsert = [], $customIDs = null, $customIDColumn = null)
{
  // Validate file type by extension (more reliable than MIME type)
  $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
  if (strtolower($fileExtension) !== 'csv') {
    return ['status' => 'error', 'message' => 'Please upload a valid CSV file.'];
  }

  // Open the CSV file for reading
  $fileHandle = fopen($file['tmp_name'], 'r');
  if (!$fileHandle) {
    return ['status' => 'error', 'message' => 'Error opening the CSV file.'];
  }

  // Ensure columnsToInsert is not empty
  if (empty($columnsToInsert)) {
    fclose($fileHandle);
    return ['status' => 'error', 'message' => 'Error: You must specify the columns to insert the data into.'];
  }

  // Validate table name (use a whitelist approach)
  $allowedTables = ['voters', 'votes']; // Add any other valid table names
  if (!in_array($tableName, $allowedTables)) {
    fclose($fileHandle);
    return ['status' => 'error', 'message' => 'Invalid table name.'];
  }

  // Skip the header row if the CSV has one
  $firstRow = true;
  static $customIDIndex = 0;

  while (($row = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
    if ($firstRow) {
      $firstRow = false; // Assuming first row is header
      continue;
    }

    // Ensure row data matches the expected column count
    if (count($row) != count($columnsToInsert)) {
      fclose($fileHandle);
      return ['status' => 'error', 'message' => 'CSV row does not match the required column count.'];
    }

    // Handle custom ID assignment dynamically if provided
    if ($customIDs && $customIDColumn) {
      $customIDColumnIndex = array_search($customIDColumn, $columnsToInsert);
      if ($customIDColumnIndex !== false && isset($customIDs[$customIDIndex])) {
        $row[$customIDColumnIndex] = $customIDs[$customIDIndex];
        $customIDIndex++;
      } else {
        fclose($fileHandle);
        return ['status' => 'error', 'message' => 'Custom ID assignment error.'];
      }
    }

    // Sanitize values and use prepared statements
    $placeholders = implode(", ", array_fill(0, count($columnsToInsert), "?"));
    $columnsList = implode(", ", $columnsToInsert);
    $query = "INSERT INTO $tableName ($columnsList) VALUES ($placeholders)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
      $types = str_repeat("s", count($columnsToInsert));
      $stmt->bind_param($types, ...$row);
      if (!$stmt->execute()) {
        fclose($fileHandle);
        return ['status' => 'error', 'message' => 'Database insert error: ' . $stmt->error];
      }
      $stmt->close();
    } else {
      fclose($fileHandle);
      return ['status' => 'error', 'message' => 'Database query preparation error.'];
    }
  }

  fclose($fileHandle);
  return ['status' => 'success', 'message' => 'CSV file uploaded successfully!'];
}

// Function to send emails using php mail function
/*function sendEmail($email, $subject, $emailTitle, $name, $siteAddress, $customURL, $customText, $template_file)
{
  // Load the template file
  $email_message = file_get_contents($template_file);

  // Replace placeholders with actual data
  $email_message = str_replace("{SITE_ADDR}", $siteAddress, $email_message);
  $email_message = str_replace("{EMAIL_TITLE}", $emailTitle, $email_message);
  $email_message = str_replace("{CUSTOM_URL}", $customURL, $email_message);
  $email_message = str_replace("{NAME}", $name, $email_message);
  $email_message = str_replace("{CUSTOM_TEXT}", $customText, $email_message); // Add replacement for {CUSTOM_TEXT}
  $email_message = str_replace("{TO_EMAIL}", $email, $email_message);

  // Set the email 'from' information
  $email_from = "EngineeringXpress <services@engineeringxpress.org>";

  // Create the email headers
  $email_headers = "From: " . $email_from . "\r\nReply-To: " . $email_from . "\r\n";
  $email_headers .= "MIME-Version: 1.0\r\n";
  $email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  // Send the email
  if (mail($email, $subject, $email_message, $email_headers)) {
    return true; // Email sent successfully
  } else {
    // Email sending failed, return the error message
    return "Failed to send email: " . error_get_last()['message'];
  }
}*/

// Function to send emails using PHPMailer Library
function sendEmail($email, $subject, $emailTitle, $name, $siteAddress, $customURL, $customText, $template_file, $otherPlaceholderArray = [])
{

  // Load the template file
  $email_message = file_get_contents($template_file);

  // Replace placeholders with actual data
  $email_message = str_replace("{SITE_ADDR}", $siteAddress, $email_message);
  $email_message = str_replace("{EMAIL_TITLE}", $emailTitle, $email_message);
  $email_message = str_replace("{CUSTOM_URL}", $customURL, $email_message);
  $email_message = str_replace("{NAME}", $name, $email_message);
  $email_message = str_replace("{CUSTOM_TEXT}", $customText, $email_message); // Add replacement for {CUSTOM_TEXT}
  $email_message = str_replace("{TO_EMAIL}", $email, $email_message);

  // Sanitize and Replace other placeholders from $otherPlaceholderArray
  foreach ($otherPlaceholderArray as $placeholder => $value) {
    if (is_array($value)) {
      // If the value is an array, you can handle it accordingly, maybe serialize it or process its elements
      $value = json_encode($value); // Example of handling array by converting to JSON string
    } else {
      $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Prevent XSS
    }
    $email_message = str_replace($placeholder, $value, $email_message);
  }


  // Create a new PHPMailer instance
  $mail = new PHPMailer(true);

  // Set PHPMailer to use SMTP
  $mail->isSMTP();

  // SMTP settings
  $mail->Host = MAILHOST; // Your SMTP host
  $mail->SMTPAuth = true; // Enable SMTP authentication
  $mail->Username = USERNAME; // SMTP username
  $mail->Password = PASSWORD; // SMTP password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable TLS encryption, `ssl` also accepted  :: ENCRYPTION_STARTTLS
  $mail->Port = 465; // TCP port to connect to //587-gmail smtp

  // Set email 'from' information
  $mail->setFrom(SEND_FROM, SEND_FROM_NAME);

  // Add recipient
  $mail->addAddress($email, $name);

  // Add reply to
  $mail->addReplyTo(REPLY_TO, REPLY_TO_NAME);

  // Add Confirm Html Elements
  $mail->isHTML(true);

  // Set email subject
  $mail->Subject = $subject;

  // Set email body
  $mail->Body = $email_message;

  // Set Alt email body
  $mail->AltBody = $email_message;

  // Send the email
  if ($mail->send()) {
    return true; // Email sent successfully
  } else {
    return false; // Failed to send email
  }
}

function getPreferences($conn)
{
  $stmt = $conn->prepare("SELECT * FROM preferences");
  $stmt->execute();
  $result = $stmt->get_result();
  $preference = $result->fetch_array();

  return $preference;
}

//Functions to get Country
function getCountry($conn)
{
  $stmt = $conn->prepare("SELECT * FROM country");
  $stmt->execute();
  $result = $stmt->get_result();
  $country = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $country[] = $row;
    }
  }
  return $country;
}

//Functions to get State
function getState($countryID, $conn)
{
  $stmt = $conn->prepare("SELECT * FROM state WHERE country_id=? ORDER BY name ASC");
  $stmt->bind_param("i", $countryID);
  $stmt->execute();
  $result = $stmt->get_result();
  $state = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $state[] = $row;
    }
  }
  return $state;
}

//Functions to get host information information
function getHostInfo($conn, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM users WHERE (hostID =? || email =?)");
  $stmt->bind_param("ss", $hostID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $customer = $result->fetch_array();

  if ($result->num_rows > 0) {
    return $customer;
  }
}

//Function to get candidate by PollID and HostID
function getCandidateByID($conn, $candidateID, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM candidates WHERE candidateID = ? AND hostID =?");
  $stmt->bind_param("ss", $candidateID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $candidate = $result->fetch_array();
  return $candidate;
}

//Function to get polls by PollID
function getPollByID($conn, $pollID)
{
  $stmt = $conn->prepare("SELECT * FROM polls WHERE pollID = ?");
  $stmt->bind_param("s", $pollID);
  $stmt->execute();
  $result = $stmt->get_result();
  $polls = $result->fetch_array();
  return $polls;
}

//Function to get polls from polls table
function getPolls($conn, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM polls WHERE hostID =? ORDER BY createdAt DESC");
  $stmt->bind_param("s", $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $polls = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $polls[] = $row;
    }
  }
  return $polls;
}

// Function to get poll status
function getPollStatus($conn, $pollID)
{
  // Query to get the start and end dates from the database using pollID
  $query = "SELECT startDate, endDate FROM polls WHERE pollID = ?";

  $startDate = '';
  $endDate = '';

  if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $pollID);
    $stmt->execute();
    $stmt->bind_result($startDate, $endDate);
    if ($stmt->fetch()) {
      // Convert the start and end dates to DateTime objects
      $startDate = new DateTime($startDate);
      $endDate = new DateTime($endDate);
      $currentDate = new DateTime();

      // Determine the poll status
      if ($currentDate < $startDate) {
        return "upcoming";
      } elseif ($currentDate >= $startDate && $currentDate <= $endDate) {
        return "active";
      } else {
        return "completed";
      }
    } else {
      // Poll ID not found
      return "Poll not found";
    }

    // Close the statement
    $stmt->close();
  } else {
    return "Error in query execution";
  }
}

//Function to Retrieve Candidates for a Particular Poll
function getCandidatesForPoll($conn, $pollID, $hostID)
{
  $sql = "SELECT c.sn, c.candidateID, c.hostID, c.pollID, c.sname, c.fname, c.oname, c.gender, c.email, c.phone, c.address, c.imagePath, c.position, c.status, c.manifesto, c.regDate, c.modifiedDate FROM candidates c JOIN polls p ON c.pollID = p.pollID AND c.hostID = p.hostID WHERE c.pollID = ? AND c.hostID = ?";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $pollID, $hostID);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidates = [];
    while ($row = $result->fetch_assoc()) {
      $candidates[] = $row;
    }
    $stmt->close();
    return $candidates;
  } else {
    // Handle errors
    echo "Error preparing statement: " . $conn->error;
    return [];
  }
}

//Function to Get the Total Number of Candidates for a Particular Poll
function getTotalCandidatesForPoll($conn, $pollID, $hostID)
{
  $totalCandidates = 0;
  $sql = "SELECT COUNT(*) AS totalCandidates FROM candidates c JOIN polls p ON c.pollID = p.pollID AND c.hostID = p.hostID WHERE c.pollID = ? AND c.hostID = ? ";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $pollID, $hostID);
    $stmt->execute();
    $stmt->bind_result($totalCandidates);
    if ($stmt->fetch()) {
      $stmt->close();
      return $totalCandidates;
    } else {
      // Return 0 if no candidates found
      return 0;
    }
  } else {
    // Handle errors
    echo "Error preparing statement: " . $conn->error;
    return 0;
  }
}

// Function to Retrieve Positions for a Particular Poll Based on Registered Candidates
function getPositionsForPollWithCandidates($conn, $pollID, $hostID)
{
  $sql = "SELECT pos.sn, pos.positionID, pos.hostID, pos.name, pos.abbr, pos.status, pos.regDate, pos.modifiedDate
        FROM positions pos
        JOIN candidates c ON c.position = pos.positionID
        JOIN polls p ON pos.hostID = p.hostID
        WHERE p.pollID = ? AND p.hostID = ?
        GROUP BY pos.positionID"; // Group by positionID to ensure unique positions that candidates are registered for

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $pollID, $hostID);
    $stmt->execute();
    $result = $stmt->get_result();
    $positions = [];
    while ($row = $result->fetch_assoc()) {
      $positions[] = $row;
    }
    $stmt->close();
    return $positions;
  } else {
    // Handle errors
    echo "Error preparing statement: " . $conn->error;
    return [];
  }
}

// Function to Get the Total Number of Positions Based on Registered Candidates for a Particular Poll
function getTotalPositionsForPollWithCandidates($conn, $pollID, $hostID)
{
  $totalPositions = 0;
  $sql = "SELECT COUNT(DISTINCT c.position) AS totalPositions FROM candidates c JOIN polls p ON p.hostID = c.hostID AND c.pollID = p.pollID JOIN positions pos ON c.position = pos.positionID WHERE c.pollID = ? AND c.hostID = ? ";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $pollID, $hostID);
    $stmt->execute();
    $stmt->bind_result($totalPositions);
    if ($stmt->fetch()) {
      $stmt->close();
      // Return the total number of positions
      return $totalPositions;
    } else {
      // Return 0 if no positions are found
      return 0;
    }
  } else {
    // Handle errors
    echo "Error preparing statement: " . $conn->error;
    return 0;
  }
}

//Function to get voters by voterID
function getVoterByID($conn, $voterID, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM voters WHERE voterID = ? AND hostID = ?");
  $stmt->bind_param("ss", $voterID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $voter = $result->fetch_array();
  return $voter;
}

//Function to get voters by voter Email
function getVoterByEmail($conn, $voterEmail, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM voters WHERE email = ? AND hostID = ?");
  $stmt->bind_param("ss", $voterEmail, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $voter = $result->fetch_array();
  return $voter;
}

//Function to get votes by hostID
function getVotesByHostID($conn, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM poll_voters WHERE hostID = ? AND status ='voted'");
  $stmt->bind_param("s", $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $votes = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $votes[] = $row;
    }
  }
  return $votes;
}

//Function to get voters candidate for a particular position
function getVotersCandidate($conn, $voterEmail, $pollID, $hostID, $candidatePosition)
{
  $stmt = $conn->prepare("SELECT * FROM votes WHERE voterEmail = ? AND pollID=? AND hostID= ? AND position = ? AND voteSessionID != ''");
  $stmt->bind_param("ssss", $voterEmail, $pollID,  $hostID, $candidatePosition);
  $stmt->execute() or die(mysqli_error($conn));
  $voteResult = $stmt->get_result();
  $getVotersCandidate = $voteResult->fetch_array();

  return $getVotersCandidate;
}

//Function to get voters selected candidates
function getVoterSelectedCandidates($conn, $voterEmail, $hostID, $pollID)
{
  // Prepare SQL query
  $sql = "SELECT p.name AS positionName, v.*,c.* FROM votes v LEFT JOIN candidates c ON v.candidateID = c.candidateID LEFT JOIN positions p ON p.positionID = c.position WHERE v.voterEmail = ? AND v.hostID = ? AND v.pollID =? AND v.voteSessionID !='' ";
  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $voterEmail, $hostID, $pollID);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedCandidates = [];
    while ($row = $result->fetch_array()) {
      $selectedCandidates[] = $row;
    }
    $stmt->close();
    return $selectedCandidates;
  } else {
    return [];
  }
}

//Function to get voters vote status
function getVotersVotesStatus($conn, $voterEmail, $hostID, $pollID)
{
  $query = "SELECT status FROM poll_voters WHERE voterEmail = ? AND pollID = ? AND hostID = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sss", $voterEmail, $pollID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $voteStatus = $result->fetch_assoc();
  $stmt->close();
  return $voteStatus;
}

//Function to get voter's poll by email
function getVotersPollByEmail($conn, $hostID, $pollID, $voterEmail)
{
  $stmt = $conn->prepare("SELECT * FROM poll_voters WHERE hostID = ? AND pollID= ? AND voterEmail =?");
  $stmt->bind_param("sss", $hostID, $pollID, $voterEmail);
  $stmt->execute();
  $result = $stmt->get_result();
  $voter = $result->fetch_array();
  return  $voter;
}

//Function to get positions by HostID
function getPositionsByHostID($conn, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM positions WHERE hostID = ?");
  $stmt->bind_param("s", $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $positions = [];

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
      $positions[] = $row;
    }
  }
  return $positions;
}

//Function to get position By ID
function getPositionByID($conn, $positionID, $hostID)
{
  $stmt = $conn->prepare("SELECT * FROM positions WHERE positionID = ? AND hostID = ?");
  $stmt->bind_param("ss", $positionID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $position = $result->fetch_array();
  return $position;
}

//Function to get candidates for a particular position
function getCandidatesForPosition($conn, $pollID, $hostID, $positionID)
{
  $query = "SELECT * FROM candidates WHERE pollID = ? AND hostID =? AND position = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("sss", $pollID, $hostID, $positionID);
  $stmt->execute();
  $result = $stmt->get_result();
  $candidates = [];
  while ($row = $result->fetch_assoc()) {
    $candidates[] = $row;
  }
  return $candidates;
}

// Function to get total numbers of voters for a poll
function getTotalVotersForPoll($conn, $pollID, $hostID)
{
  $query = "SELECT COUNT(*) as totalVoters FROM poll_voters WHERE pollID = ? AND hostID = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $pollID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['totalVoters'];
}

//Function to get candidate votes
function getCandidateVotes($conn, $pollID, $candidateID)
{
  $query = "SELECT COUNT(*) as votes FROM votes v LEFT JOIN poll_voters p ON p.voterEmail = v.voterEmail WHERE v.pollID = ? AND v.candidateID = ? AND p.status = 'voted' GROUP BY v.candidateID";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $pollID, $candidateID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['votes'];
}

//Function to get total votes for poll
function getTotalVotesForPoll($conn, $pollID, $hostID)
{
  $query = "SELECT COUNT(*) as totalVotes FROM poll_voters  WHERE pollID = ? AND hostID=? AND status = 'voted' ";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $pollID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['totalVotes'];
}

//::: 01. Role Access Matrix Functions
//Function to get roles
function getRoles($conn)
{
  $stmt = $conn->prepare("SELECT * FROM matrix_roles");
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = [];
  while ($roles = $result->fetch_array()) {
    $rows[] = $roles;
  }
  $stmt->close();
  return $rows;
}

//Function to Assign Role To Users by roleID
function assignRoleToUserByID($conn, $hostID, $roleID)
{
  $stmt = $conn->prepare("INSERT INTO matrix_user_roles (hostID, roleID) VALUES (?, ?)");
  $stmt->bind_param("si", $hostID, $roleID);
  return $stmt->execute();
}

//Function to check user role and assign Role(Insert, Update) To User by identifier
function assignRoleToUser($conn, $hostID, $roleIdentifier)
{
  // First, check if the role is provided by roleID or roleName
  if (is_numeric($roleIdentifier)) {
    // If it's numeric, assume it's the roleID
    $roleQuery = "SELECT `roleID`, `roleName` FROM `matrix_roles` WHERE `roleID` = ?";
  } else {
    // Otherwise, assume it's the roleName
    $roleQuery = "SELECT `roleID`, `roleName` FROM `matrix_roles` WHERE `roleName` = ?";
  }

  // Prepare and execute the role query
  $stmt = $conn->prepare($roleQuery);
  $stmt->bind_param("s", $roleIdentifier); // Bind the roleID or roleName
  $stmt->execute();
  $result = $stmt->get_result();
  $roleData = $result->fetch_assoc();
  $stmt->close();

  // Check if the role was found
  if (!$roleData) {
    return [
      'status' => false,
      'message' => 'Role not found.'
    ];
  }

  $roleID = $roleData['roleID'];

  // Check if the user already has a role assigned in the user_roles table
  $checkRoleQuery = "SELECT `hostID`, `roleID` FROM `matrix_user_roles` WHERE `hostID` = ?";
  $checkStmt = $conn->prepare($checkRoleQuery);
  $checkStmt->bind_param("s", $hostID);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();
  $existingRole = $checkResult->fetch_assoc();
  $checkStmt->close();

  if ($existingRole) {
    // User already has a role, update it
    $updateRoleQuery = "UPDATE `matrix_user_roles` SET `roleID` = ? WHERE `hostID` = ?";
    $updateStmt = $conn->prepare($updateRoleQuery);
    $updateStmt->bind_param("ss", $roleID, $hostID);
    $updateStmt->execute();
    $updateStmt->close();

    return [
      'status' => true,
      'message' => 'User role updated successfully.'
    ];
  } else {
    // User does not have a role, insert a new role
    $insertRoleQuery = "INSERT INTO `matrix_user_roles`(`hostID`, `roleID`) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertRoleQuery);
    $insertStmt->bind_param("ss", $hostID, $roleID);
    $insertStmt->execute();
    $insertStmt->close();

    return [
      'status' => true,
      'message' => 'User role assigned successfully.'
    ];
  }
}

//Function For Assigning Permissions to Roles
function assignPermissionToRole($conn, $roleID, $permissionID)
{
  $stmt = $conn->prepare("INSERT INTO matrix_role_permissions (roleID, permissionID) VALUES (?, ?)");
  $stmt->bind_param("ii", $roleID, $permissionID);
  return $stmt->execute();
}

// Function For Removing Permissions from Roles
function removePermissionFromRole($conn, $roleID, $permissionID)
{
  $stmt = $conn->prepare("DELETE FROM matrix_role_permissions WHERE roleID = ? AND permissionID = ?");
  if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
  }
  $stmt->bind_param("ii", $roleID, $permissionID);
  return $stmt->execute();
}

//Function to Checking User Permissions
function hasPermission($conn, $hostID, $permissionName)
{
  $stmt = $conn->prepare("SELECT p.permissionName FROM matrix_user_roles ur JOIN matrix_role_permissions rp ON ur.roleID = rp.roleID JOIN matrix_permissions p ON rp.permissionID = p.permissionID WHERE ur.hostID = ? AND p.permissionName = ?
    ");
  $stmt->bind_param("ss", $hostID, $permissionName);
  $stmt->execute();
  $result = $stmt->get_result();

  return $result->num_rows > 0;
}

//Function for Role Permissions
function checkPermission($roleID, $permissionID, $conn)
{
  $stmt = $conn->prepare("
        SELECT rp.permissionID 
        FROM matrix_role_permissions rp
        WHERE rp.roleID = ? AND rp.permissionID = ?
    ");
  $stmt->bind_param("ii", $roleID, $permissionID);
  $stmt->execute();
  $result = $stmt->get_result();

  return $result->num_rows > 0;
}

//Function to fetch all permissions from the matrix_permissions table.
function getMatrixPermissions($conn)
{
  $stmt = $conn->prepare("SELECT permissionID, permissionName FROM matrix_permissions");
  $stmt->execute();
  $result = $stmt->get_result();

  $permissions = [];
  while ($row = $result->fetch_assoc()) {
    $permissions[] = $row;
  }

  return $permissions;
}

//::: 02. Dynamic Functions
//Function for dynamic delete
function deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks = [])
{
  // Check if the record is referenced in other tables
  $count = 0;
  foreach ($referenceChecks as $refTable => $refColumn) {
    $checkQuery = "SELECT COUNT(*) FROM $refTable WHERE $refColumn = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $deleteID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
      // Record is referenced in $refTable, cannot delete
      return [
        'status' => false,
        'message' => "Operation cannot continue because the record is referenced in the " . strtoupper($refTable) . " table."
      ];
    }
  }

  // Proceed with deletion if no references were found
  $deleteQuery = "DELETE FROM $table WHERE $uniqueColumn = ?";
  $deleteStmt = $conn->prepare($deleteQuery);
  $deleteStmt->bind_param("s", $deleteID);
  $deleteStmt->execute();
  $affectedRows = $deleteStmt->affected_rows;
  $deleteStmt->close();

  if ($affectedRows > 0) {
    return [
      'status' => true,
      'message' => 'Record deleted successfully.'
    ];
  } else {
    return [
      'status' => false,
      'message' => 'Failed to delete record or record does not exist.'
    ];
  }
}

// Function for dynamic force delete
function forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks = [])
{
  $totalAffectedRows = 0;

  // Loop through reference tables and delete record if it exists
  foreach ($referenceChecks as $refTable => $refColumn) {
    $deleteQuery = "DELETE FROM $refTable WHERE $refColumn = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("s", $deleteID);
    $deleteStmt->execute();
    $affectedRows = $deleteStmt->affected_rows;
    $deleteStmt->close();

    if ($affectedRows > 0) {
      $totalAffectedRows += $affectedRows;
    }
  }

  // After deleting from reference tables, delete from the main table
  $deleteQuery = "DELETE FROM $table WHERE $uniqueColumn = ?";
  $deleteStmt = $conn->prepare($deleteQuery);
  $deleteStmt->bind_param("s", $deleteID);
  $deleteStmt->execute();
  $mainTableAffectedRows = $deleteStmt->affected_rows;
  $deleteStmt->close();

  // Total affected rows from reference tables and the main table
  $totalAffectedRows += $mainTableAffectedRows;

  if ($totalAffectedRows > 0) {
    return [
      'status' => true,
      'message' => 'Record and all related references deleted successfully.'
    ];
  } else {
    return [
      'status' => false,
      'message' => 'No record found or deletion failed.'
    ];
  }
}
