<?php
session_start();
include("globalFunctions.php");

//GET VOTES RESULT BASED ON OFFICES AND CREATE A CHART |||||| Starts>>>>>
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['get_poll_result'], $_POST['pollID'])) {

  $pollID = isset($_POST['pollID']) ?  $_POST['pollID'] : '';
  $getPollInfo = getPollByID($conn, $pollID  ?? '');
  $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';
  // $pollStatus = getPollStatus($conn, $pollID ?? '');
  // $preferences = getPreferences($conn);

  // Fetch poll positions
  $pollPositions = getPositionsByHostID($conn, $hostID);

  // Constructing data for each position
  $positionData = [];

  // Iterate over each position
  foreach ($pollPositions as $position) {
    $positionId = $position['positionID'];

    //Get Position Candidates
    $pollCandidates = getCandidatesForPosition($conn, $pollID, $hostID, $positionId);

    // Fetch votes for the current position
    $stmt = $conn->prepare("SELECT candidateID FROM votes WHERE position = ? AND pollID=? AND hostID=?");
    $stmt->bind_param("sss", $positionId, $pollID, $hostID);
    $stmt->execute();
    $voteResult = $stmt->get_result();
    $pollVotes = [];
    while ($row = $voteResult->fetch_assoc()) {
      $pollVotes[] = $row['candidateID'];
    }

    // If there are no candidates or votes for the current position, skip it
    if (empty($pollCandidates) || empty($pollVotes)) {
      continue;
    }

    // Constructing data for Morris chart for the current position
    $candidatesData = [];
    foreach ($pollCandidates as $candidate) {
      $candidateId = $candidate['candidateID'];
      // Extract the first character of the first name as the initial
      $initial = substr($candidate['sname'], 0, 1);
      // Construct the abbreviated name
      $abbreviatedName = $initial . '. ' . $candidate['fname'];
      $votesForCandidate = array_count_values($pollVotes)[$candidateId] ?? 0;
      $candidatesData[] = ['name' => $abbreviatedName, 'votes' => $votesForCandidate];
    }

    // Add position data to the result array
    $positionData[] = ['position' => $position['name'], 'candidates' => $candidatesData, 'poll_title' => $getPollInfo['title']];
  }

  // Set the content type to JSON
  header('Content-Type: application/json');

  // If there is no data available for any position, return a message
  if (empty($positionData)) {
    echo json_encode(['message' => 'No data available for any position']);
  } else {
    // Return position data as JSON
    echo json_encode($positionData);
  }
  exit();
}
//GET VOTES RESULT BASED ON OFFICES AND CREATE A CHART |||||| Ends>>>>>
