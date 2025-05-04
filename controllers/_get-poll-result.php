<?php
session_start();
include("globalFunctions.php");
// header("Content-Type:application/json");


/** Sanitize input data (for additional user inputs if needed)*/

function sanitizeInput($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}

//::||GET POLL LIVE RESULT BREAKDOWN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['requestLiveResult'], $_POST['pollID'], $_POST['voterEmail'], $_POST['sessionID']) && $_POST['requestLiveResult'] == "true") {

  $pollID = sanitizeInput($_POST['pollID']);
  $sessionID = sanitizeInput($_POST['sessionID']);
  $voterEmail = sanitizeInput($_POST['voterEmail']);
  $getPollInfo = getPollByID($conn, $pollID  ?? '');
  $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';
  $pollStatus = getPollStatus($conn, $pollID ?? '');
  $getHostInfo = getHostInfo($conn, $hostID ?? '');
  $getVoterInfo =  getVoterByEmail($conn, $voterEmail, $hostID);
  $voterPollEmail = !empty($pollID) ?  getVotersPollByEmail($conn, $getPollInfo['hostID'], $pollID, $voterEmail) : '';
  $isPrivatePoll = !empty($getPollInfo) && $getPollInfo['visibility'] == "private" ? true : false;
  $privateEligibilityValid = !empty($pollID) && !empty($voterPollEmail)  ? true : false;
  $totalCandidate = getTotalCandidatesForPoll($conn, $pollID, $hostID);
  $totalPosition = getTotalPositionsForPollWithCandidates($conn, $pollID, $hostID);
  $preferences = getPreferences($conn);



  /**
   * Helper functions 
   * */
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
?>
  <div class="rounded bg-white p-4 md:p-8">
    <p class="text-base font-bold lg:text-2xl text-left mt-6 text-center !text-lg md:mt-0 lg:!text-xl md:text-left">
      Poll Summary And Result Breakdown
    </p>

    <p class="text-base text-left mt-2 text-center font-light md:text-left">Below are your selected candidates</p>
    <div class="mt-5 flex flex-col content-around justify-between rounded bg-gradient-to-b from-transparent to-result-certificate-bg-to px-6 py-6 md:flex-row md:border md:bg-background md:px-10 lg:px-12">
      <div class="flex flex-wrap gap-4" style="height:26rem;overflow:auto;">
        <p class="text-base font-bold lg:text-2xl text-left w-full">Your Selected Candidates:</p>

        <?php
        $getSelectedCandidates = getVoterSelectedCandidates($conn, $voterEmail, $hostID, $pollID);
        if (!empty($getSelectedCandidates)) {
          // Output the selected candidates
          foreach ($getSelectedCandidates as $candidate) { ?>
            <div class="relative rounded border border-input-border py-2 px-2 flex flex-col items-center" style="width:10rem; margin: 0 auto; ">
              <img class="rounded-full border-2 border-primary-contrast" src="<?= $candidate['imagePath']; ?>" style="width:100px;height: 100px;" alt="<?= ucfirst($candidate['sname']) . " " . ucfirst($candidate['fname']); ?>">
              <div class="text-center mt-2">
                <h2 class="font-medium"><?= ucfirst($candidate['sname']) . " " . ucfirst($candidate['fname']); ?></h2>
                <p><b class="font-medium">For:</b> <strong><?= ucfirst($candidate['positionName']); ?></strong></p>
              </div>
            </div>
          <?php
          }
        } else { ?>
          <div class="w-full text-center text-yellow-500 font-bold">No candidates selected.</div>

        <?php
        }
        ?>
      </div>

    </div>
  </div>

  <div class="relative flex w-full flex-col items-stretch rounded bg-white p-4">
    <h2 class="text-base font-bold lg:text-2xl text-left grow !text-lg lg:ml-2 lg:!text-xl">Real Time Statistics</h2>
    <div class="flex flex-row">
      <!-- Information tooltip -->
      <div class="absolute top-2 right-1">
        <div class="group relative">
          <button class="flex items-center justify-center w-12 h-12 bg-light hover:brightness-110 rounded-full peer"
            data-test-id="score-comparison-table-hint-icon-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-text-primary" viewBox="0 -960 960 960" data-test-id="info-icon">
              <path d="M483.056-273.782q15.417 0 25.789-10.413 10.373-10.412 10.373-25.805v-173.782q0-15.393-10.429-25.805Q498.36-520 482.944-520q-15.417 0-25.789 10.413-10.373 10.412-10.373 25.805V-310q0 15.393 10.429 25.805 10.429 10.413 25.845 10.413Zm-3.024-311.739q17.642 0 29.544-11.638 11.903-11.638 11.903-28.841 0-18.689-11.92-30.584t-29.541-11.895q-18.257 0-29.877 11.895-11.62 11.895-11.62 30.301 0 17.557 11.935 29.159 11.934 11.603 29.576 11.603Zm.312 519.652q-86.203 0-161.506-32.395-75.302-32.395-131.741-88.833-56.438-56.439-88.833-131.738-32.395-75.299-32.395-161.587 0-86.288 32.395-161.665t88.745-131.345q56.349-55.968 131.69-88.616 75.34-32.648 161.676-32.648 86.335 0 161.779 32.604t131.37 88.497q55.926 55.893 88.549 131.452 32.623 75.559 32.623 161.877 0 86.281-32.648 161.575-32.648 75.293-88.616 131.478-55.968 56.186-131.426 88.765-75.459 32.58-161.662 32.58Zm.156-79.218q139.239 0 236.826-97.732 97.587-97.732 97.587-237.681 0-139.239-97.4-236.826-97.399-97.587-237.796-97.587-139.021 0-236.826 97.4-97.804 97.399-97.804 237.796 0 139.021 97.732 236.826 97.732 97.804 237.681 97.804ZM480-480Z">
              </path>
            </svg>
          </button>
          <div class="_base_15z1o_1 _left_15z1o_14 group-[:hover]:visible peer-[.active]:visible">
            <div class="_tooltip_15z1o_33 _left_15z1o_14 relative flex w-max flex-col gap-2 rounded-lg bg-text-primary p-4 text-left text-xs text-white max-w-[250px] !rounded !p-2.5 md:max-w-sm" role="dialog">
              <p class="text-sm text-left max-w-sm">The statistics displayed here are based on real-time data collected from the poll. The number of candidates, voters, and submitted votes are updated dynamically as votes are cast and counted. This ensures that the results you see are accurate and reflect the current state of the poll.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class=" mt-4 mb-2 flex flex-col gap-4">
      <div class="pt-5.5 lg:pt-7 md:bg-background">
        <div class="max-w-full overflow-auto py-2 px-2">
          <!-- Desktop Display -->
          <div class="hidden lg:block px-4">
            <table class="border-separate border-spacing-2 m-center">
              <tbody>
                <tr>
                  <td class="p-3 font-bold text-base text-left whitespace-nowrap" style="width:35%">FIGURE</td>
                  <td class="p-3 align-top max-w-[130px] ScoreComparisonTable_unselectedItem___E4kM" style="width:20%">
                    <div class="text-center text-base">
                      <div class="font-bold text-sm">Candidates</div>
                    </div>
                  </td>
                  <td class="p-3 align-top max-w-[130px] ScoreComparisonTable_unselectedItem___E4kM" style="width:20%">
                    <div class="text-center text-base">
                      <div class="font-bold text-sm">Voters</div>
                    </div>
                  </td>
                  <td class="p-3 align-top max-w-[130px] ScoreComparisonTable_unselectedItem___E4kM" style="width:30%">
                    <div class="text-center text-base">
                      <div class="font-bold text-sm">Submitted Votes</div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="p-3 font-bold text-base text-left whitespace-nowrap" style="width:35%">VALUE</td>
                  <td class="p-3 align-top max-w-[130px] ScoreComparisonTable_unselectedItem___E4kM" style="width:20%">
                    <div class="text-center text-base">
                      <div class="font-bold"><?= $totalCandidate; ?></div>
                    </div>
                  </td>
                  <td class="p-3 align-top max-w-[130px] ScoreComparisonTable_unselectedItem___E4kM" style="width:20%">
                    <div class="text-center text-base">
                      <div class="font-bold"><?= getTotalVotersForPoll($conn, $pollID, $hostID); ?></div>
                    </div>
                  </td>
                  <td class="p-3 align-top max-w-[130px] bg-chart-green text-white" style="width:30%">
                    <div class="text-center text-base">
                      <div class="font-bold"><?= getTotalVotesForPoll($conn, $pollID, $hostID); ?></div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Display -->
          <div class="visible lg:hidden px-3.5">
            <table class="border-separate border-spacing-2 w-full">
              <thead>
                <tr>
                  <td class="px-2 font-bold text-base text-left">FIGURE</td>
                  <td class="px-2 font-bold text-base text-center">VALUE</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="p-2.5 align-top ScoreComparisonTable_unselectedItem___E4kM" style="width:50%">
                    <div class="text-left text-base">
                      <p class="font-bold">Candidates</p>
                    </div>
                  </td>
                  <td class="p-2.5 align-top ScoreComparisonTable_unselectedItem___E4kM" style="width:50%">
                    <div class="text-center text-base">
                      <p class="font-bold"><?= $totalCandidate; ?></p>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="p-2.5 align-top ScoreComparisonTable_unselectedItem___E4kM" style="width:50%">
                    <div class="text-left text-base">
                      <p class="font-bold">Voters</p>
                    </div>
                  </td>
                  <td class="p-2.5 align-top ScoreComparisonTable_unselectedItem___E4kM" style="width:50%">
                    <div class="text-center text-base">
                      <p class="font-bold"><?= getTotalVotersForPoll($conn, $pollID, $hostID); ?></p>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="p-2.5 align-top ScoreComparisonTable_unselectedItem___E4kM" style="width:50%">
                    <div class="text-left text-base">
                      <p class="font-bold">Submitted Votes</p>
                    </div>
                  </td>
                  <td class="p-2.5 align-top bg-chart-green text-white" style="width:50%">
                    <div class="text-center text-base">
                      <p class="font-bold"><?= getTotalVotesForPoll($conn, $pollID, $hostID); ?></p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php


  $positions = getPositionsForPollWithCandidates($conn, $pollID, $hostID);
  if (!empty($positions)) {
    foreach ($positions as $position) {
      $candidates = getCandidatesForPosition($conn, $pollID, $hostID, $position['positionID']);
      if (!empty($candidates)) {
        foreach ($candidates as &$candidate) {
          $candidate['votes'] = getCandidateVotes($conn, $pollID, $candidate['candidateID']);
        }
        usort($candidates, function ($a, $b) {
          return $b['votes'] - $a['votes'];
        });
        $topCandidate = array_shift($candidates);
  ?>
        <section class="rounded bg-white p-6 lg:p-10">
          <h2 class="text-base text-left mb-3 font-bold lg:text-2xl text-left !text-lg lg:!text-xl">Candidates For <?= ucfirst($position['name']); ?></h2>
          <div class="flex flex-col gap-x-14 lg:flex-row">
            <div class="flex flex-row items-center lg:flex-col">
              <div class="relative" style="width: 90px; height: 90px;">
                <div class="absolute flex items-center justify-center" style="width: 90px; height: 90px;">
                  <?php
                  $totalVotes = getTotalVotesForPoll($conn, $pollID, $hostID);
                  $percentage = $totalVotes > 0 ? ($topCandidate['votes'] / $totalVotes) * 100 : 0;
                  $radius = 41;
                  $circumference = 2 * pi() * $radius;
                  $offset = $circumference - ($percentage / 100 * $circumference);
                  ?>
                  <span class="text-6xl text-chart-green !text-2xl font-bold"><?= round($percentage); ?>%</span>
                </div>
                <svg style="width: 90px; height: 90px;">
                  <circle class="_placeholder_1qey4_7 stroke-chart-placeholder-section-zero" cx="41" cy="41" r="41" style="transform: translate(4px, 4px); width: 90px; height: 90px; stroke-width: 4; stroke-dasharray: 257.611;"></circle>
                  <circle class="origin-center -rotate-90 stroke-chart-green" cx="41" cy="41" r="41" style="stroke-dashoffset: <?= $offset; ?>; translate: 4px -4px; transition-delay: 0s; transition-duration: 0.5s; transition-property: stroke-dashoffset; width: 90px; height: 90px; stroke-width: 4; stroke-dasharray: <?= $circumference; ?>;"></circle>
                </svg>
              </div>
              <div class="ml-3 lg:ml-0">
                <div class="flex flex-row gap-1.5 lg:gap-2 lg:hidden">
                  <div class="flex items-center gap-2" data-test="section-indicator-reading">
                    <span><i class="fa fa-user"></i></span>
                  </div>
                  <h3 class="text-base lg:text-xl text-left !text-lg !font-bold lg:!text-xl"><?= ucfirst($topCandidate['surname']) . " " . ucfirst($topCandidate['fname']); ?></h3>
                </div>
                <p class="text-base text-left mt-2 lg:w-28 lg:text-center"><strong>Top with</strong> <br>
                  <span class="capitalize"><?= $topCandidate['votes']; ?> votes</span>
                </p>
              </div>
            </div>
            <div class="flex flex-grow flex-col items-stretch">
              <div class="flex flex-row gap-1.5 lg:gap-2 hidden lg:flex">
                <div class="flex items-center gap-2" data-test="section-indicator-reading">
                  <span><i class="fa fa-user"></i></span>
                </div>
                <h3 class="text-base lg:text-xl text-left !text-lg !font-bold lg:!text-xl"><?= ucfirst($topCandidate['sname']) . " " . ucfirst($topCandidate['fname']); ?></h3>
              </div>
              <p class="text-base text-left mt-2 flex-grow font-light">The candidate with the highest number of submitted votes is displayed by default. Use the collapse button below to view other candidates.</p>
              <div class="mt-5">
                <div class="flex cursor-pointer flex-row items-center rounded hover:bg-disabled" role="button" data-test-id="score-explanation-expandable" onclick="toggleExpandableContent(this)">
                  <p class="text-base text-left">Other Candidates</p><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-1 transition duration-300 fill-text-primary" viewBox="0 -960 960 960" data-test-id="expand-more-icon">
                    <path d="M480-347.956q-7.696 0-14.957-3.131-7.261-3.13-13.391-9.261L254.217-557.782q-11.826-11.827-11.826-27.783 0-15.957 11.826-27.783t28.283-11.826q16.457 0 28.283 11.826L480-444.131l169.217-169.217q11.826-11.826 27.783-11.826t27.783 11.826q11.826 11.826 11.826 28.283 0 16.456-11.826 28.283L508.348-360.348q-6.13 6.131-13.391 9.261-7.261 3.131-14.957 3.131Z"></path>
                  </svg>
                </div>
                <div class="expandable-content hidden mt-2">
                  <?php foreach ($candidates as $candidate) { ?>
                    <p class="text-base text-left"><?= ucfirst($candidate['sname']) . " " . ucfirst($candidate['fname']) . " - " . $candidate['votes'] . " votes"; ?></p>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </section>

        <style>
          /* Basic styling */
          .progress-bar-container {
            width: 100%;
            background-color: #e2e8f0;
            /* light gray background */
            border-radius: 9999px;
            /* rounded edges */
            height: 10px;
            /* adjust height as needed */
            position: relative;
          }

          .progress-bar-fill {
            background-color: #e6b31e;
            /* your custom color */
            height: 100%;
            border-radius: 9999px;
            transition: width 0.7s ease-in-out;
            /* smooth transition for filling */
          }

          /* Optional text styles */
          .progress-text {
            font-size: 0.75rem;
            /* adjust as needed */
            font-weight: bold;
            color: #e6b31e;
            /* your custom color */
          }
        </style>

        <div class="flex items-center gap-3 mb-1">
          <div class="w-12 h-12 rounded-full overflow-hidden shrink-0">
            <img src="https://images.unsplash.com/photo-1600486913747-55e5470d6f40?w=400&amp;auto=format&amp;fit=crop&amp;q=60&amp;ixlib=rb-4.0.3" alt="Candidate A" class="w-full h-full object-cover">
          </div>
          <div class="flex-1">
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium text-gray-900">Candidate A</span>
              <span class="text-sm font-bold text-[rgb(230,179,30)]">40%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar-fill" style="width: 40%;"></div>
            </div>
            <span class="text-xs text-gray-700">1245 votes</span>
          </div>
        </div>

      <?php
      } else {
      ?>
        <section class="rounded bg-white p-6 lg:p-10">
          <h2 class="text-base text-left mb-3 font-bold lg:text-2xl text-left !text-lg lg:!text-xl">Candidates For <?= ucfirst($position['name']); ?></h2>
          <p class="text-base text-left mt-2 flex-grow font-light">No candidates available for this position.</p>
        </section>
    <?php
      }
    }
  } else {
    ?>
    <section class="rounded bg-white p-6 lg:p-10">
      <h2 class="text-base text-left mb-3 font-bold lg:text-2xl text-left !text-lg lg:!text-xl">Poll Positions</h2>
      <p class="text-base text-left mt-2 flex-grow font-light">No positions available for this poll.</p>
    </section>
<?php
  }

  exit();
}
//:::GET POL LIVE RESULT BREAKDOWN