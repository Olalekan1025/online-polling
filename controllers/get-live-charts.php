<?php
session_start();
include("globalFunctions.php");

//GET POSITION AND POSITION CANDIDATE RESULTS |||||>>>Starts  
if (isset($_POST['getPositionResults'], $_POST['positionID'], $_POST['pollID'])) {
  $positionID = $_POST["positionID"];
  $pollID = $_POST["pollID"];
  $getPollInfo = getPollByID($conn, $pollID  ?? '');
  $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';

  // echo $positionID;
  // die();

  // Fetch poll candidates for the position
  $pollCandidates = getCandidatesForPosition($conn, $pollID, $hostID, $positionID);

  // Fetch votes for the current position
  $stmt = $conn->prepare("SELECT * FROM votes WHERE position = ? AND pollID = ? AND hostID = ?");
  $stmt->bind_param("sss", $positionID, $pollID, $hostID);
  $stmt->execute();
  $voteResult = $stmt->get_result();
  $pollVotes = [];
  while ($row = $voteResult->fetch_assoc()) {
    $pollVotes[] = $row['candidateID'];
  }

  // If there are no candidates or votes for the current position, exit
  if (empty($pollCandidates) || empty($pollVotes)) {
    echo '<div class="alert alert-warning">No data available for this position.</div>';
    exit();
  }

  // Define colors for the progress bars
  $colors = array(
    "#1ee0ac",
    "#ffc107",
    "#17a2b8",
    "#f64e60",
    "#6f42c1",
    "#007bff",
    "#28a745",
    "#dc3545",
    "#6610f2",
    "#fd7e14",
    "#20c997",
    "#ff5722",
    "#7952b3",
    "#2196f3",
    "#4caf50",
    "#e91e63"
  );
  $colorIndex = 0;

?>
  <div class="card-content">
    <div class="card-body p-0">
      <ul class="list-group list-unstyled">
        <?php
        foreach ($pollCandidates as $candidate) {
          $color = $colors[$colorIndex];
          $colorIndex = ($colorIndex + 1) % count($colors);

          $candidateID = $candidate['candidateID'];
          $votesForCandidate = array_count_values($pollVotes)[$candidateID] ?? 0;
          $totalVotes = count($pollVotes);

          // Calculate vote percentage
          $percentage = ($totalVotes > 0) ? ($votesForCandidate / $totalVotes) * 100 : 0;

          // Get candidate's passport path
          $passportPath = $candidate["imagePath"];
          $defaultImage = "images/no-preview.jpeg";
        ?>
          <li class="p-4 border-bottom">
            <div class="w-100">
              <a href="javascript:void(0);">
                <img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>"
                  alt=""
                  class="img-fluid ml-0 mb-2 rounded-circle"
                  style="width:50px;height:50px">
              </a>
              <div class="media-body align-self-center pl-2">
                <span class="mb-0 h6 font-w-900">
                  <b><?php echo strtoupper($candidate['sname'] . " " . $candidate['fname']); ?></b>
                  (<?php echo number_format($votesForCandidate); ?> Votes)
                </span><br>
              </div>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                  style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>"
                  role="progressbar"
                  aria-valuenow="<?php echo $percentage; ?>"
                  aria-valuemin="0"
                  aria-valuemax="100">
                  <b style="font-size: 18px;"><?php echo round($percentage); ?>%</b>
                </div>
              </div>
            </div>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
<?php
  exit();
}
//GET POSITION AND POSITION CANDIDATE RESULTS |||||>>>Ends

//GET LEADING CANDIDATES TABLE |||||>>>Starts
if (isset($_POST['getLeadingCandidate'])) {
  $pollID = $_POST["pollID"] ?? '';
  $getPollInfo = getPollByID($conn, $pollID);
  $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';

  // Fetch all positions for the poll
  $positions = getPositionsByHostID($conn, $hostID);

  if (empty($positions)) {
    echo '<div class="alert alert-warning">No positions available for this poll.</div>';
    exit();
  }

?>
  <div class="card-content">
    <div class="card-body p-0">
      <ul class="list-group list-unstyled">
        <?php
        foreach ($positions as $position) {
          $positionID = $position['positionID'];
          $positionName = $position['name'];

          // Fetch candidates for the position
          $pollCandidates = getCandidatesForPosition($conn, $pollID, $hostID, $positionID);

          // Fetch votes for the current position
          $stmt = $conn->prepare("SELECT candidateID FROM votes WHERE position = ? AND pollID = ? AND hostID = ?");
          $stmt->bind_param("sss", $positionID, $pollID, $hostID);
          $stmt->execute();
          $voteResult = $stmt->get_result();
          $pollVotes = [];
          while ($row = $voteResult->fetch_assoc()) {
            $pollVotes[] = $row['candidateID'];
          }

          // If there are no candidates or votes for the current position, skip
          if (empty($pollCandidates) || empty($pollVotes)) {
            continue;
          }

          // Determine the leading candidate
          $leadingCandidate = null;
          $maxVotes = 0;
          foreach ($pollCandidates as $candidate) {
            $candidateID = $candidate['candidateID'];
            $votesForCandidate = array_count_values($pollVotes)[$candidateID] ?? 0;

            if ($votesForCandidate > $maxVotes) {
              $maxVotes = $votesForCandidate;
              $leadingCandidate = $candidate;
            }
          }

          if ($leadingCandidate) {
            $passportPath = $leadingCandidate["imagePath"];
            $defaultImage = "images/no-preview.jpeg";
        ?>
            <li class="p-2 border-bottom zoom">
              <div class="media d-flex w-100">
                <a href="#"><img src="<?php echo (file_exists("../" . $passportPath) ? $passportPath : $defaultImage); ?>" alt="" class="img-fluid ml-0 mt-2 rounded-circle" style="width:50px;height:50px;"></a>
                <div class="media-body align-self-center pl-2">
                  <span class="mb-0 h6 font-w-900"><?php echo strtoupper($leadingCandidate['sname'] . " " . $leadingCandidate['fname']); ?></span><br>
                  <span class="mb-0 font-w-500 tx-s-12"><?php echo $positionName; ?></span>
                </div>
                <div class="media-body align-self-center pl-2">
                  <span class="mb-0 h6 font-w-900"><?php echo number_format($maxVotes); ?> Votes</span><br>
                </div>
              </div>
            </li>
        <?php
          }
        }
        ?>
      </ul>
    </div>
  </div>
<?php
  exit();
}
//GET LEADING CANDIDATES TABLE |||||>>>Ends

//GET LIVE STATISTICS DATA TABLE |||||>>>Starts    
if (isset($_POST['getLiveStatisticData'])) {
  $pollID = $_POST["pollID"];
  $getPollInfo = getPollByID($conn, $pollID  ?? '');
  $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';
?>

  <!-- Election Count Down -->
  <div class="d-flex">
    <div class="media-body align-self-center ">
      <span class="mb-0 h5 font-w-600"><img id="liveNotification" src="images/live.gif" style="width: 15px;height:15px;" /> Live Stats Reports </span><br>
    </div>
    <div class="ml-auto p-2 text-dark font-w-800 h4 border-0 " id="countdown">
      <span class="mb-0">00:00:00</span>
    </div>
  </div>
  <!-- Election Count Down -->

  <div class="d-flex mt-4">
    <div class="border-0 outline-badge-info w-100 p-3 rounded text-center">
      <?php
      $registeredVoters = getTotalVotersForPoll($conn, $pollID, $hostID);
      ?>
      <span class="h2"><?php echo number_format($registeredVoters); ?></span><br />
      <span class="h4 mb-0">Total Registered Voters</span>
    </div>
    <!-- <div class="border-0 outline-badge-primary w-50 p-3 rounded ml-2 text-center">

      <span class="h3">
        <?php // echo number_format($accreditedVoters); 
        ?>
      </span><br />
      <span class="h4 mb-0">Accredited Voters</span>
    </div> -->
  </div>

  <div class="d-flex mt-3">
    <div class="border-0 outline-badge-primary w-50 p-3 rounded text-center">
      <?php
      $voterCount = getTotalVotesForPoll($conn, $pollID, $hostID);
      ?>
      <span class="h3"><?php echo number_format($voterCount); ?></span><br />
      <span class="h4 mb-0">Total Votes</span>
    </div>
    <div class="border-0 outline-badge-danger w-50 p-3 rounded ml-2 text-center">
      <span class="h3"><?php echo number_format($registeredVoters - $voterCount); ?></span><br />
      <span class="h4 mb-0">Yet to vote</span>
    </div>
  </div>

  <script>
    // Election Countdown Set Timeout >>>Starts
    <?php
    $electionDeadline = !empty($getPollInfo['endDate']) ? $getPollInfo['endDate'] : date("Y-m-d H:i:s", strtotime("+1 hour")); // Default to 1 hour from now if endDate is not set
    $timeFormat = date("Y/m/d H:i:s", strtotime($electionDeadline)); // Format the date string in a way compatible with JavaScript Date constructor 
    ?>

    var targetDate = new Date("<?php echo $timeFormat; ?>");

    function updateCountdown() {
      const currentDate = new Date();
      const timeLeft = targetDate - currentDate;

      if (timeLeft <= 0) {
        document.getElementById('countdown').innerHTML = '<span class="text-danger h4"> Election Closed!</span>';
        closeElectionActivity(); // Trigger the close election Activity
      } else {
        const hours = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
        const minutes = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
        const seconds = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, '0');

        document.getElementById('countdown').innerHTML = `
            <span>${hours} :</span>
            <span>${minutes} :</span>
            <span>${seconds}</span>
        `;
      }
    }

    // Initial call to set the countdown on page load
    updateCountdown();

    // Call the updateCountdown function every second
    setInterval(updateCountdown, 1000);

    function closeElectionActivity() {
      $("#liveNotification").hide();
    }
    // Election Countdown Set Timeout >>>Ends
  </script>

<?php
  exit();
}
//GET LIVE STATISTICS DATA TABLE |||||>>>Ends 

//GET LIVE STATISTICS DATA TABLE |||||>>>Starts 
if (isset($_POST['setPollIDSession'])) {
  $pollID = $_POST["pollID"];
  $_SESSION['pollID'] = $pollID;
  $response = array("status" => "success", "sessionStatus" => "true");

  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//GET LIVE STATISTICS DATA TABLE |||||>>>Ends 
?>