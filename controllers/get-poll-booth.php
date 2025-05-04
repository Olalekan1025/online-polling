<?php
session_start();
include("globalFunctions.php");
// header("Content-Type:application/json");


/** Sanitize input data (for additional user inputs if needed)*/

function sanitizeInput($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}

//::||GET POLLS, CANDIDATES, AND AVAILABLE OFFICES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['preparePollBooth'], $_POST['pollID'], $_POST['voterEmail'], $_POST['hostID'], $_POST['sessionID']) && $_POST['preparePollBooth'] == "true") {

  $pollID = sanitizeInput($_POST['pollID']);
  $hostID = sanitizeInput($_POST['hostID']);
  $sessionID = sanitizeInput($_POST['sessionID']);
  $voterEmail = sanitizeInput($_POST['voterEmail']);
  $pollInfo = getPollByID($conn, $pollID);
  $getVoterInfo =  getVoterByEmail($conn, $voterEmail, $hostID);
  $voteStatus = getVotersVotesStatus($conn, $voterEmail, $hostID, $pollID);
  $isVoteSubmitted = !empty($voteStatus) && $voteStatus["status"] == "voted" ? true : false;
  // error_log(print_r($voterVotes, true)); // Check if votes are retrieved correctly

  // Get poll status and validate
  $pollStatus = getPollStatus($conn, $pollID);

  if ($pollStatus !== "active") {
    echo "Poll is not currently active.";
    exit();
  }

  // Fetch available positions once and store in session
  if (!isset($_SESSION['positions']) || isset($_POST['resetPositions'])) {
    $_SESSION['positions'] = getPositionsForPollWithCandidates($conn, $pollID, $hostID);
    $_SESSION['position_index'] = 0; // Reset index on new session
  }

  $positions = $_SESSION['positions'];

  // Check if positions exist
  if (empty($positions)) {
    echo "No positions available for this poll.";
    exit();
  }

  // Handle navigation between positions
  if (isset($_POST['navigate'])) {
    if ($_POST['navigate'] === "next" && $_SESSION['position_index'] < count($positions) - 1) {
      $_SESSION['position_index']++;
    } elseif ($_POST['navigate'] === "prev" && $_SESSION['position_index'] > 0) {
      $_SESSION['position_index']--;
    }
  }


  // Save session to prevent unwanted resets
  session_write_close();

  // Retrieve current position
  $currentPosition = $positions[$_SESSION['position_index']];
  $positionID = $currentPosition['positionID'];
  $positionName = $currentPosition['name'];

  // Fetch only candidates for the current position  
  $candidates = getCandidatesForPoll($conn, $pollID, $hostID);
  $totalCandidates = count(array_filter($candidates, fn($c) => $c['position'] == $positionID));

  // error_log("Total positions: " . count($positions)); // Debugging
  // error_log("Current index: " . $_SESSION['position_index']);
?>

  <div class="flex flex-col _container_1qxyg_2 booth-container">
    <nav class="z-30 border-b-4 border-b-primary bg-primary-contrast shrink-0">
      <div class="mx-auto flex h-10 max-w-7xl flex-row items-center gap-4 px-4 py-1 md:px-10 lg:h-12 lg:gap-5.5">
        <div style="margin: 0 auto;margin-top:2rem;margin-bottom:2rem">
          <a href="./"><img src="images/roe.png" style="width: 2rem;height:2rem;"></a>
        </div>
      </div>
    </nav>

    <main class="h-full grow overflow-hidden">
      <div class="h-full w-full flex flex-col items-stretch overflow-y-auto lg:flex-row">
        <section class="h-max shrink-0 overflow-y-auto bg-background-card lg:h-auto lg:flex-1">
          <div class="mx-auto w-full max-w-7xl/2 px-4 pb-11 pt-8 md:px-10 lg:ml-auto lg:mr-0 lg:py-18 xl:pl-10 xl:pr-14">
            <?php if (!$isVoteSubmitted) { //If vote has not been submitted then show the header 
            ?>
              <p class="text-base lg:text-xl text-left select-none font-medium">
                Select your best choice for <span class="rounded bg-primary-light px-2 py-0 font-bold uppercase"><?php echo strtoupper($positionName); ?></span>
              </p>
              <p>You can only select <span class="rounded bg-primary-light px-2 py-1 font-bold uppercase">ONE</span> candidate.</p>

              <div class="flex flex-col gap-6 mt-6">
                <div class="flex items-center gap-5">
                  <div class="relative">
                    <button class="flex items-center justify-center w-19 h-19 bg-primary hover:brightness-110 rounded-full">
                      <h1 style="font-size:3rem" class="text-white"><i class="fa fa-check-circle"></i></h1>
                    </button>
                  </div>
                  <div class="text-base">
                    <p>Current Position: <span class="font-medium"><b><?php echo $positionName; ?></b></span></p>
                    <p>Available Candidates: <span class="font-medium"><b><?php echo $totalCandidates; ?></b></span></p>
                  </div>
                </div>
              </div>
            <?php } ?>
            <div id="mc-question-5613" class="w-full mt-3 select-none duration-150 px-6 pb-8 pt-10 md:rounded-lg bg-background-section-card-primary">
              <p class="mb-3 rounded bg-primary-light px-2 py-3  font-bold uppercase">Your Selected Candidates: </p>
              <div class="flex flex-wrap gap-4" role="radiogroup">
                <?php
                $getSelectedCandidates = getVoterSelectedCandidates($conn, $voterEmail, $hostID, $pollID);
                if (!empty($getSelectedCandidates)) {
                  // Output the selected candidates
                  foreach ($getSelectedCandidates as $myCandidate) {
                ?>
                    <label class="flex items-center gap-x-4 p-4 text-left bg-white shadow-option rounded-option transition-colors duration-100 ease-in w-full sm:w-auto" aria-checked="false" role="radio">
                      <img class="square-full border-2 border-primary-contrast" src="<?= $myCandidate['imagePath']; ?>" style="width:100px;height: 100px;" alt="<?= ucfirst($myCandidate['sname']) . " " . ucfirst($myCandidate['fname']); ?>">
                      <div class="flex-grow">
                        <h2 class="font-medium"><?= ucfirst($myCandidate['sname']) . " " . ucfirst($myCandidate['fname']) . " " . ucfirst($myCandidate['oname']) ?></h2>
                        <p><b class="font-medium"><b>For:</b> <?= ucfirst($myCandidate['positionName']); ?></b></p>
                      </div>
                    </label>
                  <?php
                  }
                } else { ?>
                  <label class="flex items-center gap-x-4 p-4 text-left bg-white shadow-option rounded-option transition-colors duration-100 ease-in w-full sm:w-auto">
                    You haven't selected any candidates yet.
                  </label>
                <?php } ?>


              </div>

            </div>
          </div>
        </section>

        <!-- Candidates Section -->
        <section class="flex-1 overflow-y-visible bg-background lg:overflow-y-auto">
          <div class="relative mx-auto w-full max-w-7xl/2 md:px-4 lg:ml-0 lg:mr-auto lg:px-10">
            <?php
            //If voter has submitted a vote
            if ($isVoteSubmitted) {
            ?>

              <div class="notranslate flex flex-col items-center gap-y-2 overflow-y-visible pt-2 lg:pt-12">
                <span class="p-4 text-2xl font-bold"><b>VOTE RECORDED FOR <span class="text-primary"><?php echo strtoupper(htmlspecialchars($pollInfo['title'])); ?></span></b></span>
                <div class="relative flex flex-col items-center gap-y-8 rounded-lg bg-background-card px-3 pb-4 pt-10 shadow-option md:p-10 w-full _top_1zqyg_8 !p-0">
                  <div class="flex w-full flex-col items-center rounded-t-lg p-6 md:p-10" style="background-color:#e9f9f0;">
                    <h3 class="text-success" style="font-size: 5rem;color: #009c63;"><i class="fa fa-check-circle"></i></h3>
                    <div class="flex flex-col items-center gap-4 md:flex-row" style="color: #009c63;">
                      <h1 class="text-2xl font-bold md:text-3.3xl text-center modal-header-title">Your Vote Has Been Submitted</h1>
                    </div>
                    <p class="text-base lg:text-xl text-center mt-3 md:mt-4 modal-alert-message">Thank you for voting! You can now view the live results.</p>
                  </div>
                  <div class="w-full text-center px-6 pb-6 pt-2 md:px-10 md:pb-10 md:pt-5">
                    <a href="poll-result?poll_id=<?= $pollID; ?>&voterEmail=<?= $voterEmail; ?>&session_id=<?= $getVoterInfo['voteSessionID']; ?>" class="inline-flex items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 m-auto w-full md:w-auto">
                      <span class="font-medium text-primary-contrast">View Live Results</span>
                    </a>
                  </div>
                </div>
              </div>

            <?php
              //If Voter has not yet submit their vote 
            } else {
            ?>
              <div class="notranslate flex flex-col items-center gap-y-2 overflow-y-visible pt-2 lg:pt-12">
                <span class="p-4" style="font-size:2rem !important"><b>CLICK ON A CHOICE FOR <span class="text-primary"><?php echo strtoupper($positionName); ?></span></b></span>

                <?php foreach ($candidates as $candidate) {
                  if ($candidate['position'] == $positionID) {
                    //Get Voters Candidates for vote Entry table
                    $votersCandidate = getVotersCandidate($conn, $voterEmail, $pollID, $hostID, $candidate['position']);
                    $isChecked = !empty($votersCandidate) && $candidate['candidateID'] == $votersCandidate['candidateID']  ? "checked" : "unchecked";
                ?>
                    <div class="w-full select-none duration-150 px-6 pb-8 pt-10 md:rounded-lg md:max-w-select-field bg-background-section-card-primary">
                      <label class="flex cursor-pointer items-center gap-x-4 p-4 rounded-option text-left transition-colors duration-100 ease-in text-primary-contrast shadow-option flex-col bg-primary w-full">
                        <div class="flex justify-center mb-3">
                          <img src="<?= $candidate['imagePath']; ?>" alt="Candidate Image" class="h-24 w-24 object-cover rounded-full border-2 border-primary-contrast" />
                        </div>
                        <div class="flex flex-col items-center gap-y-2">
                          <input name="vote" id="candidate-<?= $candidate['candidateID']; ?>" value="<?= $candidate['candidateID']; ?>" data-position-id="<?= $positionID; ?>" data-host-id="<?= $candidate['hostID']; ?>" data-poll-id="<?= $candidate['pollID']; ?>" data-session-id="<?= $sessionID; ?>" class="peer hidden" type="radio" <?= $isChecked; ?> />
                          <span class="hidden h-8 w-8 items-center justify-center rounded-full border-2 border-primary-contrast peer-checked:flex bg-primary-contrast">
                            <div class="h-4 w-4 fa fa-check text-primary"></div>
                          </span>
                          <span class="text-lg font-semibold mt-2"><?= htmlspecialchars($candidate['sname'] . " " . $candidate['fname']); ?></span>
                        </div>
                      </label>
                    </div>
                <?php }
                } ?>
              </div>

              <div class="mx-auto flex w-full justify-between items-center px-4 pb-4 pt-10 md:max-w-select-field md:px-0">
                <!-- Left side (Previous Button) -->
                <div>
                  <?php if ($_SESSION['position_index'] > 0) { ?>
                    <button id="prevBtn" class="nav-btn bg-secondary text-white px-9 py-2 rounded-full hover:opacity-90">
                      Previous
                    </button>
                  <?php } ?>
                </div>

                <!-- Right side (Next or Submit Button) -->
                <div>
                  <?php if ($_SESSION['position_index'] == count($positions) - 1) { ?>
                    <!-- Submit Button on Last Position -->
                    <form id="voteForm">
                      <input type="hidden" name="pollID" value="<?php echo $pollID; ?>">
                      <input type="hidden" name="hostID" value="<?php echo $hostID; ?>">
                      <input type="hidden" name="voterEmail" value="<?php echo $voterEmail; ?>">
                      <input type="hidden" name="sessionID" value="<?php echo $sessionID; ?>">
                      <input type="hidden" name="positionID" value="<?php echo $positionID; ?>">
                      <button type="button" id="submitVoteBtn" class="bg-dark text-white px-9 py-2 rounded-full hover:opacity-90">
                        Submit Vote
                      </button>
                    </form>

                    <!-- Confirm Vote Submission Alert -->
                    <dialog class="w-full bg-transparent p-4 transition-opacity duration-150 backdrop:bg-text-primary/80"
                      style="max-width: calc(600px + 2rem);" id="voteSubmitModal">
                      <div class="relative flex flex-col items-center gap-y-8 rounded-lg bg-background-card px-3 pb-4 pt-10 shadow-option md:p-10 w-full _top_1zqyg_8 !p-0">
                        <div class="flex w-full flex-col items-center rounded-t-lg p-6 md:p-10" style="background-color:#e9f9f0;">
                          <h3 class="text-success" style="font-size: 5rem;color: #009c63;"><i class="fa fa-question"></i></h3>
                          <div class="flex flex-col items-center gap-4 md:flex-row" style="color: #009c63;">
                            <h1 class="text-2xl font-bold md:text-3.3xl text-center modal-header-title">Confirm Vote Submission</h1>
                          </div>
                          <p class="text-base lg:text-xl text-center mt-3 md:mt-4 modal-alert-message">Please confirm that you would like to submit your vote. Ensure that all your selections are correct before proceeding.</p>
                        </div>
                        <div class="w-full text-center px-6 pb-6 pt-2 md:px-10 md:pb-10 md:pt-5">
                          <button class="inline-flex items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 m-auto w-full md:w-auto" id="modal-confirm-button">
                            <span class="font-medium text-primary-contrast">Submit</span>
                          </button>
                          <button class="inline-flex items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-error hover:brightness-110 m-auto w-full md:w-auto" id="modal-cancel-button"><span class="font-medium text-primary-contrast">Cancel</span>
                          </button>
                        </div>
                      </div>
                    </dialog>
                  <?php } else { ?>
                    <!-- Next Button (Always in the right position) -->
                    <button id="nextBtn" class="nav-btn bg-primary text-white px-9 py-2 rounded-full hover:opacity-90">
                      Next
                    </button>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>

            <p class="text-base text-light-contrast leading-7 text-center p-4">© <?= date("Y"); ?> Copyright University of Roehampton Polling & Voting System | Presented by Ridwan Olalekan Oguntola</p>
          </div>
        </section>
      </div>

      <script>
        // Function to get navigation buttons
        function loadPosition(action) {
          $.post("controllers/get-poll-booth", {
            preparePollBooth: "true",
            pollID: "<?php echo $pollID; ?>",
            hostID: "<?php echo $hostID; ?>",
            sessionID: "<?php echo $sessionID; ?>",
            voterEmail: "<?php echo $voterEmail; ?>",
            navigate: action
          }, function(response) {
            $(".booth-container").html(response);
          });
        }

        //Navigate different Position for this poll.
        $(document).off("click", ".nav-btn").on("click", ".nav-btn", function() {
          loadPosition($(this).attr("id") === "nextBtn" ? "next" : "prev");
        });

        // Save vote entry
        $(document).off("click", "input[name='vote']").on("click", "input[name='vote']", function() {
          var selectedCandidate = $(this);
          var candidateID = selectedCandidate.val();
          var positionID = selectedCandidate.data("position-id");
          var hostID = selectedCandidate.data("host-id");
          var pollID = selectedCandidate.data("poll-id");
          var sessionID = selectedCandidate.data("session-id");

          $.ajax({
            url: "controllers/get-poll-booth",
            type: "POST",
            data: {
              saveVoteEntry: true,
              candidateID: candidateID,
              positionID: positionID,
              hostID: hostID,
              pollID: pollID,
              voterEmail: "<?= $voterEmail; ?>",
              sessionID: sessionID,
            },
            success: function(response) {
              var status = response.status;
              var message = response.message;
              if (status === "success") {
                // console.log(message);
                //loadPosition("next"); // Move to the next position

                //Reload the booth container to refresh the selected candidates
                $.post("controllers/get-poll-booth", {
                  preparePollBooth: "true",
                  pollID: "<?php echo $pollID; ?>",
                  hostID: "<?php echo $hostID; ?>",
                  sessionID: "<?php echo $sessionID; ?>",
                  voterEmail: "<?php echo $voterEmail; ?>"
                }, function(response) {
                  $(".booth-container").html(response);
                });
              } else {
                swal("Failed", message, status);
              }
            },
            error: function(xhr, status, error) {
              console.error("Error submitting vote:", error);
              swal("Failed", "An error occurred while submitting your vote. Please try again.", "error");
            }
          });
        });

        // Show confirmation modal on submit button click
        $(document).off("click", "#submitVoteBtn").on("click", "#submitVoteBtn", function() {
          $("#voteSubmitModal")[0].showModal();
        });

        // Handle confirm button click
        $(document).off("click", "#modal-confirm-button").on("click", "#modal-confirm-button", function() {
          var formData = $("#voteForm").serializeArray();
          formData.push({
            name: "submitVote",
            value: "true"
          });
          formData.push({
            name: "saveVoteEntry",
            value: "true"
          });
          $.ajax({
            url: "controllers/get-poll-booth",
            type: "POST",
            data: formData,
            success: function(response) {
              var status = response.status;
              var message = response.message;
              if (status === "success") {
                swal("Success", message, status);
                //Reload the booth container to refresh the selected candidates
                $.post("controllers/get-poll-booth", {
                  preparePollBooth: "true",
                  pollID: "<?php echo $pollID; ?>",
                  hostID: "<?php echo $hostID; ?>",
                  sessionID: "<?php echo $sessionID; ?>",
                  voterEmail: "<?php echo $voterEmail; ?>"
                }, function(response) {
                  $(".booth-container").html(response);
                });
              } else {
                swal("Failed", message, status);
              }
            },
            error: function(xhr, status, error) {
              console.error("Error submitting vote:", error);
              swal("Failed", "An error occurred while submitting your vote. Please try again.", "error");
            }
          });
          $("#voteSubmitModal")[0].close();
        });

        // Handle cancel button click
        $(document).off("click", "#modal-cancel-button").on("click", "#modal-cancel-button", function() {
          $("#voteSubmitModal")[0].close();
        });
      </script>
    </main>
  </div>
<?php
  exit();
}
//:::GET POLLS, CANDIDATES AND AVAILABLE OFFICES

//:::SAVE VOTERS POLL ENTRY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveVoteEntry'], $_POST['positionID'], $_POST['hostID'], $_POST['pollID'], $_POST['voterEmail'], $_POST['sessionID']) && $_POST['saveVoteEntry'] == "true") {

  header("Content-Type:application/json");
  $candidateID = isset($_POST['candidateID']) ?  sanitizeInput($_POST['candidateID']) : "";
  $positionID = sanitizeInput($_POST['positionID']);
  $hostID = sanitizeInput($_POST['hostID']);
  $pollID = sanitizeInput($_POST['pollID']);
  $voterEmail = sanitizeInput($_POST['voterEmail']);
  $sessionID = sanitizeInput($_POST['sessionID']);

  if (isset($_POST['submitVote']) && $_POST['submitVote'] == "true") {
    // Update the poll_voters table to mark the voter as having voted
    $updateQuery = "UPDATE poll_voters SET status = 'voted' WHERE voterEmail = ? AND pollID = ? AND hostID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sss", $voterEmail, $pollID, $hostID);
    $updateStmt->execute();
    $updateStmt->close();
    $response = ["status" => "success", "message" => "Vote submitted successfully."];
    echo json_encode($response);

    exit();
  }

  // Check if the voter has already voted for this position
  $query = "SELECT * FROM votes WHERE voterEmail = ? AND pollID = ? AND position = ? AND hostID = ? AND voteSessionID != ''";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ssss", $voterEmail, $pollID, $positionID, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Update the existing vote entry
    $query = "UPDATE votes SET candidateID = ?, voteDate = NOW() WHERE voterEmail = ? AND pollID = ? AND position = ? AND hostID = ? AND voteSessionID != ''";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $candidateID, $voterEmail, $pollID, $positionID, $hostID);
  } else {
    // Insert a new vote entry
    $query = "INSERT INTO votes (voterEmail, pollID, hostID, candidateID, position, voteSessionID, voteDate) VALUES (?, ?, ?, ?, ?,?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $voterEmail, $pollID, $hostID, $candidateID, $positionID, $sessionID);
  }

  if ($stmt->execute()) {
    $response = ["status" => "success", "message" => "Vote recorded successfully."];
    echo json_encode($response);
  } else {
    $response = ["status" => "error", "message" => "An error occurred while recording your vote. Please try again."];
    echo json_encode($response);
  }

  $stmt->close();

  exit();
}
//:::SAVE VOTERS POLL ENTRY
