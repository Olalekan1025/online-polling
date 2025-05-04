<?php
session_start();
include("globalFunctions.php");
//::||Get Positions using pollID, and HostID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pollID']) && !empty($_POST['pollID'])) {
  $pollID = mysqli_real_escape_string($conn, $_POST['pollID']);
  $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';
  $positions = getPositionsByHostID($conn, $hostID);
  $response = [];

  if ($positions) {
    foreach ($positions as $position) {
      $response[] = ["name" => $position['name'], "positionID" => $position['positionID'], "abbr" => $position['abbr'], "status" => $position['status'], "regDate" => $position['regDate']];
    }
  }

  // Return the subcategories as JSON
  header("content-Type: application/json");
  echo json_encode($response);
  exit();
}
//::||Get Positions using pollID, and HostID