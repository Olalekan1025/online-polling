<?php
session_start();
if (isset($_GET['poll_id'], $_GET['session_id'], $_GET['voterEmail']) && !empty($_GET['poll_id'])  && !empty($_GET['voterEmail'])) {
  require("controllers/globalFunctions.php");
  $pollSessionID = isset($_GET['session_id']) ?  $_GET['session_id'] : '';
  $pollID = isset($_GET['poll_id']) ?  $_GET['poll_id'] : '';
  $voterEmail = isset($_GET['voterEmail']) ?  $_GET['voterEmail'] : '';
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
  $invalidLink = false;
} else {
  $invalidLink = true;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="images/roe.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="NOINDEX,FOLLOW" />
  <title>Online Polling &amp; Voting System </title>
  <link rel="stylesheet" href="dist/vendors/homdroid/css/style.css" />
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="dist/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
</head>

<body style="overflow-x: hidden;">
  <div id="root">
    <div id="efset" role="status">

      <!-- Phase One Panel -->
      <div class="flex min-h-screen flex-col items-stretch justify-center overflow-y-auto bg-background" id="phaseOnePanel">
        <div style="margin: 0 auto;margin-top:4rem;margin-bottom:2px">
          <a href="./"><img src="images/roe.png" style="width: 5rem;height:5rem;"></a>
        </div>
        <main class="z-10 flex h-full flex-grow items-center justify-center p-4">
          <?php
          //:::: 01 If this is a private poll then check voters eligibility 
          if (isset($isPrivatePoll) && $isPrivatePoll && !$privateEligibilityValid) {  ?>

            <!-- Ineligible Access Panel -->
            <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full !p-0" style="max-width: 600px;">
              <div class="flex w-full flex-col items-center rounded-t-lg bg-background-warning p-6 md:p-10">
                <h3 style="font-size:3rem; color:red"><i class="fa fa-exclamation-triangle"></i></h3>
                <div class="flex flex-col items-center gap-4 md:flex-row">
                  <h1 class="text-2xl font-bold md:text-3.3xl text-center">Access Blocked!</h1>
                </div>
                <p class="text-base lg:text-xl text-center mt-3 md:mt-4">
                  This is a private poll and you are not Eligible to take this poll.
                </p>
              </div>

              <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full" style="max-width: 600px;">
                <ul class="ml-4 flex list-disc flex-col items-start gap-y-6 self-stretch lg:ml-6 mb-6 md:mb-8">
                  <li class="text-base text-left">
                    <p>The email address you entered is not eligible for this poll. </p>
                  </li>
                  <li class="text-base text-left">
                    <p>You might be attempting to access multiple polls simultaneously in the same browser.Please reach out to the poll host for further assistance.</p>
                  </li>
                </ul>
                <a class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 w-full md:w-auto" data-test-id="test-cancelled-card-redirect-button" href="./">
                  <span class="font-medium text-primary-contrast">Go Back Home</span>
                </a>
              </div>
            </div>

            <?php
            //:::: 02 If link is valid and voter is eligible for the poll 
          } elseif (!$invalidLink && isset($getVoterInfo['voteSessionID']) && $pollSessionID == $getVoterInfo['voteSessionID']) {
            //Check if this poll is active and in session
            if ($pollStatus == "upcoming"):
            ?>
              <!-- Upcoming Poll Panel -->
              <div class="flex w-full flex-col items-center">
                <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full !px-0"
                  style="max-width: 600px;">
                  <div class="flex flex-col items-center self-stretch px-6 lg:px-10">
                    <h1 class="text-2xl font-bold md:text-3.3xl text-center mb-3"><?= !empty($getPollInfo['title']) ? ucfirst($getPollInfo['title']) : 'Get Started'; ?></h1>
                    <p class="text-base lg:text-xl text-center lg:mb-7">Hi <b><?= $getVoterInfo['fname'] ?></b>, the poll has not started yet. It will be available and active at:</p>
                  </div>
                  <div class="my-6 flex w-full flex-wrap justify-evenly gap-y-9 bg-background-section-card-primary py-8 lg:my-7">
                    <div id="countdown" class="mt-2" style="font-size: 4rem;">00:00:00:00</div>

                    <!-- Countdown for upcoming poll -->
                    <script type="text/javascript">
                      // Countdown Timer
                      function startCountdown(endDate) {
                        var countdownElement = document.getElementById('countdown');
                        var end = new Date(endDate).getTime();
                        var refreshPollBtn = document.getElementById('refreshPollButton');


                        if (isNaN(end)) {
                          console.error("Invalid end date:", endDate);
                          countdownElement.innerHTML = "Invalid date for countdown!";
                          return;
                        }

                        var x = setInterval(function() {
                          var now = new Date().getTime();
                          var distance = end - now;

                          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                          countdownElement.innerHTML = "<span class='badge p-2 badge-danger mb-1'>" + days + "d " + hours + "h " + minutes + "m " + seconds + "s</span>";
                          refreshPollBtn.disabled = true;

                          if (distance < 0) {
                            clearInterval(x);
                            countdownElement.innerHTML = "<span class='badge p-2 badge-success mb-1' style='font-size: 0.8rem;'>POLL IS NOW ACTIVE</span>";
                            refreshPollBtn.disabled = false;

                          }
                        }, 1000);
                      }

                      // Start countdown with a fallback to a valid date format
                      var pollEndDate = "<?php echo $getPollInfo['startDate']; ?>";
                      if (pollEndDate) {
                        startCountdown(pollEndDate);
                      } else {
                        console.error("End date is not available");
                      }
                    </script>
                  </div>
                  <!-- Refresh Poll Button -->
                  <div class="flex flex-col items-center gap-y-4.5 self-stretch px-6 lg:gap-y-8 lg:px-10">
                    <div class="flex flex-col items-stretch justify-center gap-5 self-stretch lg:flex-row lg:items-center lg:gap-6">
                      <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 disable"
                        data-test-id="test-transition-primary-action-button" disabled="" id="refreshPollButton" onclick="location.reload();">
                        <span class="font-medium text-primary-contrast">Refresh Poll</span>
                      </button>
                    </div>
                  </div>

                  <div class="flex justify-center mt-3">
                    <a href="./"><u>Go Back Home</u></a>
                  </div>
                </div>
              </div>

            <?php elseif ($pollStatus == "active"):  ?>

              <!-- Active Poll Panel -->
              <div class="flex w-full flex-col items-center">
                <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full !px-0"
                  style="max-width: 600px;">
                  <div class="flex flex-col items-center self-stretch px-6 lg:px-10">
                    <h1 class="text-2xl font-bold md:text-3.3xl text-center mb-3"><?= !empty($getPollInfo['title']) ? ucfirst($getPollInfo['title']) : 'Get Started'; ?></h1>
                    <p class="text-base lg:text-xl text-center lg:mb-7">Hi <b><?= $getVoterInfo['fname'] ?></b>, <?= !empty($getPollInfo['description']) ? $getPollInfo['description']  : 'the above poll ' ?> having the following information:</p>
                  </div>
                  <div class="my-6 flex w-full flex-wrap justify-evenly gap-y-9 bg-background-section-card-primary py-8 lg:my-7">
                    <!-- Visibility Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4">
                      <span style="font-size:3rem"><i class="fa fa-eye h-13 w-13 mb-2 fill-primary-dark"></i></span>
                      <p class="text-base text-center">Visibility</p>
                      <p class="text-base text-center font-bold"><?= !empty($getPollInfo['visibility']) ? ucfirst($getPollInfo['visibility']) : "<span class='text-danger'>Not Stated</span>"; ?></p>
                    </div>

                    <!-- Positions Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4">
                      <span style="font-size:3rem"><i class="fa fa-user h-13 w-13 mb-2 fill-primary"></i></span>
                      <p class="text-base text-center">Vying Positions</p>
                      <p class="text-base text-center font-bold"><?= number_format($totalPosition); ?></p>
                    </div>

                    <!-- Candidates Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4 ">
                      <span style="font-size:3rem"><i class="fa fa-users h-13 w-13 mb-2 fill-primary"></i></span>
                      <p class="text-base text-center">Candidates</p>
                      <p class="text-base text-center font-bold"><?= number_format($totalCandidate); ?></p>
                    </div>
                  </div>

                  <div class="flex flex-col items-center gap-y-4.5 self-stretch px-6 lg:gap-y-8 lg:px-10">
                    <div class="flex flex-col items-stretch justify-center gap-5 self-stretch lg:flex-row lg:items-center lg:gap-6 text-white">
                      <?php if (!empty($totalPosition) && !empty($totalCandidate)): ?>
                        <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110"
                          data-test-id="test-transition-primary-action-button" data-poll-id="<?= $pollID; ?>" data-voter-email="<?= $voterEmail; ?>" data-host-id="<?= $hostID; ?>" data-session-id="<?= $getVoterInfo['voteSessionID']; ?>" id="booth-start-btn">
                          <span class="font-medium text-primary-contrast">Continue</span>
                        </button>
                      <?php else: ?>
                        <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110" onclick="location.reload();">Refresh Poll</button>
                      <?php endif; ?>
                    </div>
                    <span class="text-error">This poll will end By: <b><?= structureTimestamp($getPollInfo['endDate']); ?></b> </span>
                  </div>
                </div>
              </div>

            <?php elseif ($pollStatus == "completed"):  ?>

              <!-- Complete Poll Panel -->
              <div class="flex w-full flex-col items-center">
                <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full !px-0"
                  style="max-width: 600px;">
                  <div class="flex flex-col items-center self-stretch px-6 lg:px-10">
                    <h1 class="text-2xl font-bold md:text-3.3xl text-center mb-3"><?= !empty($getPollInfo['title']) ? ucfirst($getPollInfo['title']) : 'Get Started'; ?></h1>
                    <p class="text-base lg:text-xl text-center lg:mb-7">Hi <b><?= $getVoterInfo['fname'] ?></b>, This Poll Ended <?= structureTimestamp($getPollInfo['endDate']); ?></p>
                  </div>
                  <div class="my-6 flex w-full flex-wrap justify-evenly gap-y-9 bg-background-section-card-primary py-8 lg:my-7">
                    <!-- Visibility Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4">
                      <span style="font-size:3rem"><i class="fa fa-eye h-13 w-13 mb-2 fill-primary-dark"></i></span>
                      <p class="text-base text-center">Visibility</p>
                      <p class="text-base text-center font-bold"><?= !empty($getPollInfo['visibility']) ? ucfirst($getPollInfo['visibility']) : "<span class='text-danger'>Not Stated</span>"; ?></p>
                    </div>

                    <!-- Positions Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4">
                      <span style="font-size:3rem"><i class="fa fa-user h-13 w-13 mb-2 fill-primary"></i></span>
                      <p class="text-base text-center">Vying Positions</p>
                      <p class="text-base text-center font-bold"><?= number_format($totalPosition); ?></p>
                    </div>

                    <!-- Candidates Icon (Font Awesome) -->
                    <div class="flex flex-col items-center w-1/2 lg:w-1/4 ">
                      <span style="font-size:3rem"><i class="fa fa-users h-13 w-13 mb-2 fill-primary"></i></span>
                      <p class="text-base text-center">Candidates</p>
                      <p class="text-base text-center font-bold"><?= number_format($totalCandidate); ?></p>
                    </div>
                  </div>

                  <div class="flex flex-col items-center gap-y-4.5 self-stretch px-6 lg:gap-y-8 lg:px-10">
                    <div
                      class="flex flex-col items-stretch justify-center gap-5 self-stretch lg:flex-row lg:items-center lg:gap-6">
                      <a href="poll-result?poll_id=<?= $pollID; ?>&voterEmail=<?= $voterEmail; ?>&session_id=<?= $getVoterInfo['voteSessionID']; ?>"
                        class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110"
                        data-test-id="test-transition-primary-action-button">
                        <span id="view-poll-result-btn" class="font-medium text-primary-contrast">View Result</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>

            <?php endif;  ?>
          <?php } else { ?>
            <!-- Some Went Wrong Information Panel -->
            <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full !p-0" style="max-width: 600px;">
              <div class="flex w-full flex-col items-center rounded-t-lg bg-background-error p-6 md:p-10">
                <h3 style="font-size:3rem; color:red"><i class="fa fa-exclamation-triangle"></i></h3>
                <div class="flex flex-col items-center gap-4 md:flex-row">
                  <h1 class="text-2xl font-bold md:text-3.3xl text-center">Something went wrong!</h1>
                </div>
                <p class="text-base lg:text-xl text-center mt-3 md:mt-4">
                  It looks like the poll session is either expired or invalid. Please reach out to the poll host for further assistance.
                </p>
              </div>

              <div class="flex flex-col items-center rounded-lg bg-background-card px-6 py-10 lg:px-10 w-full" style="max-width: 600px;">
                <ul class="ml-4 flex list-disc flex-col items-start gap-y-6 self-stretch lg:ml-6 mb-6 md:mb-8">
                  <li class="text-base text-left">
                    <p>Your poll session or link may have expired or become invalid.</p>
                  </li>
                  <li class="text-base text-left">
                    <p>You might be attempting to access multiple polls simultaneously in the same browser, which could cause conflicts.</p>
                  </li>
                  <li class="text-base text-left">
                    <p>You may not be authorized to participate in the poll with the provided information.</p>
                  </li>
                </ul>
                <a class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 w-full md:w-auto" data-test-id="test-cancelled-card-redirect-button" href="javascript:void(0);" onclick="history.go(-1);">
                  <span class="font-medium text-primary-contrast">Try Again</span>
                </a>
              </div>
            </div>
          <?php } ?>

          <!-- SVG Pattern Style  -->
          <svg xmlns="http://www.w3.org/2000/svg"
            class="max-w-[302px] fill-background-pattern fixed left-0 top-1/2 hidden w-full -translate-y-1/2 md:block"
            viewBox="0 0 302 622">
            <polygon points="0 10.13 0 10.84 300.64 311.48 0 612.12 0 612.83 301.35 311.48 0 10.13"></polygon>
            <polygon points="0 32.25 0 32.96 278.52 311.48 0 590 0 590.71 279.23 311.48 0 32.25"></polygon>
            <polygon points="0 54.37 0 55.08 256.45 311.53 0 567.98 0 568.68 257.16 311.53 0 54.37"></polygon>
            <polygon points="0 76.49 0 77.2 234.28 311.48 0 545.76 0 546.47 234.99 311.48 0 76.49"></polygon>
            <polygon points="0 98.61 0 99.32 212.16 311.48 0 523.64 0 524.35 212.87 311.48 0 98.61"></polygon>
            <polygon points="0 120.73 0 121.44 190.04 311.48 0 501.52 0 502.23 190.75 311.48 0 120.73"></polygon>
            <polygon points="0 142.95 0 143.65 167.83 311.48 0 479.31 0 480.01 168.53 311.48 0 142.95"></polygon>
            <polygon points="0 165.07 0 165.78 145.71 311.48 0 457.19 0 457.89 146.41 311.48 0 165.07"></polygon>
            <polygon points="0 187.18 0 187.89 123.64 311.53 0 435.16 0 435.87 124.34 311.53 0 187.18"></polygon>
            <polygon points="0 209.31 0 210.01 101.47 311.48 0 412.95 0 413.66 102.18 311.48 0 209.31"></polygon>
            <polygon points="0 231.43 0 232.13 79.35 311.48 0 390.83 0 391.54 80.05 311.48 0 231.43"></polygon>
            <polygon points="0 253.55 0 254.25 57.23 311.48 0 368.71 0 369.41 57.93 311.48 0 253.55"></polygon>
            <polygon points="0 275.76 0 276.47 35.01 311.48 0 346.49 0 347.2 35.72 311.48 0 275.76"></polygon>
            <polygon points="0 297.88 0 298.59 12.89 311.48 0 324.37 0 325.08 13.6 311.48 0 297.88"></polygon>
          </svg>

          <svg xmlns="http://www.w3.org/2000/svg"
            class="max-w-[302px] fill-background-pattern fixed right-0 top-1/2 hidden w-full -translate-y-1/2 rotate-180 md:block"
            viewBox="0 0 302 622">
            <polygon points="0 10.13 0 10.84 300.64 311.48 0 612.12 0 612.83 301.35 311.48 0 10.13"></polygon>
            <polygon points="0 32.25 0 32.96 278.52 311.48 0 590 0 590.71 279.23 311.48 0 32.25"></polygon>
            <polygon points="0 54.37 0 55.08 256.45 311.53 0 567.98 0 568.68 257.16 311.53 0 54.37"></polygon>
            <polygon points="0 76.49 0 77.2 234.28 311.48 0 545.76 0 546.47 234.99 311.48 0 76.49"></polygon>
            <polygon points="0 98.61 0 99.32 212.16 311.48 0 523.64 0 524.35 212.87 311.48 0 98.61"></polygon>
            <polygon points="0 120.73 0 121.44 190.04 311.48 0 501.52 0 502.23 190.75 311.48 0 120.73"></polygon>
            <polygon points="0 142.95 0 143.65 167.83 311.48 0 479.31 0 480.01 168.53 311.48 0 142.95"></polygon>
            <polygon points="0 165.07 0 165.78 145.71 311.48 0 457.19 0 457.89 146.41 311.48 0 165.07"></polygon>
            <polygon points="0 187.18 0 187.89 123.64 311.53 0 435.16 0 435.87 124.34 311.53 0 187.18"></polygon>
            <polygon points="0 209.31 0 210.01 101.47 311.48 0 412.95 0 413.66 102.18 311.48 0 209.31"></polygon>
            <polygon points="0 231.43 0 232.13 79.35 311.48 0 390.83 0 391.54 80.05 311.48 0 231.43"></polygon>
            <polygon points="0 253.55 0 254.25 57.23 311.48 0 368.71 0 369.41 57.93 311.48 0 253.55"></polygon>
            <polygon points="0 275.76 0 276.47 35.01 311.48 0 346.49 0 347.2 35.72 311.48 0 275.76"></polygon>
            <polygon points="0 297.88 0 298.59 12.89 311.48 0 324.37 0 325.08 13.6 311.48 0 297.88"></polygon>
          </svg>

          <!-- Error Alert -->
          <dialog class="w-full bg-transparent p-4 transition-opacity duration-150 backdrop:bg-text-primary/80"
            style="max-width: calc(600px + 2rem);" id="errorAlertModal">
            <div
              class="relative flex flex-col items-center gap-y-8 rounded-lg bg-background-card px-3 pb-4 pt-10 shadow-option md:p-10 w-full _top_1zqyg_8 !p-0">
              <div class="flex w-full flex-col items-center rounded-t-lg bg-background-error p-6 md:p-10">
                <div class="flex flex-col items-center gap-4 md:flex-row"><svg xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 !h-7 !w-7 md:!h-8 md:!w-8 fill-error" viewBox="0 -960 960 960" data-test-id="warning-icon">
                    <path
                      d="M96.522-112.652q-12.1 0-20.925-5.707-8.826-5.708-13.554-14.38-5.297-8.296-5.714-18.868-.416-10.571 5.714-21.306L445.521-835q6.131-10.261 15.232-14.891 9.102-4.631 19.305-4.631t19.247 4.631q9.043 4.63 15.174 14.891l383.478 662.087q6.13 10.735 5.714 21.306-.417 10.572-5.714 18.868-4.826 8.446-13.603 14.266-8.777 5.821-20.876 5.821H96.522ZM484.175-238.13q13.15 0 22.618-9.644 9.468-9.643 9.468-22.793 0-13.149-9.643-22.335-9.644-9.185-22.793-9.185-13.15 0-22.618 9.361-9.468 9.36-9.468 22.51 0 13.15 9.643 22.618 9.644 9.468 22.793 9.468Zm0-109.87q12.825 0 21.325-8.625T514-378v-159.478q0-12.75-8.675-21.375-8.676-8.625-21.5-8.625-12.825 0-21.325 8.625t-8.5 21.375V-378q0 12.75 8.675 21.375 8.676 8.625 21.5 8.625Z">
                    </path>
                  </svg>
                  <h1 class="text-2xl font-bold md:text-3.3xl text-center modal-header-title">Oops! Something went wrong</h1>
                </div>
                <p class="text-base lg:text-xl text-center mt-3 md:mt-4 modal-alert-message">An error has occurred. Please try again.</p>
              </div>
              <div class="w-full px-6 pb-6 pt-2 md:px-10 md:pb-10 md:pt-5">
                <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 m-auto w-full md:w-auto" id="modal-control-button"><span class="font-medium text-primary-contrast">Try again</span>
                </button>
              </div>
            </div>
          </dialog>
        </main>


        <p class="text-base text-light-contrast leading-7 text-center p-4">© Reohampton University. Online Polling & Voting System, presented by Ridwan Olalekan Oguntola. All rights reserved.
        </p>
      </div>

      <!-- Phase Two Panel :: Main Poll Phase -->
      <div id="phaseTwoPanel" style="display:none">
        <div id="show-booth"></div>
      </div>

      <!-- Alert Pop up -->
      <dialog class="w-full bg-transparent p-4 transition-opacity duration-150 backdrop:bg-text-primary/80"
        data-test-id="idle-timer-warning-modal" style="max-width: calc(600px + 2rem);" id="idle-timer-dialog">
        <div
          class="relative flex flex-col items-center gap-y-8 rounded-lg bg-background-card px-3 pb-4 pt-10 shadow-option md:p-10 w-full _top_1zqyg_8 !p-0">
          <div class="flex w-full flex-col items-center rounded-t-lg bg-background-error p-6 md:p-10">
            <div class="flex flex-col items-center gap-4 md:flex-row"><svg xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 !h-7 !w-7 md:!h-8 md:!w-8 fill-error" viewBox="0 -960 960 960"
                data-test-id="warning-icon">
                <path
                  d="M96.522-112.652q-12.1 0-20.925-5.707-8.826-5.708-13.554-14.38-5.297-8.296-5.714-18.868-.416-10.571 5.714-21.306L445.521-835q6.131-10.261 15.232-14.891 9.102-4.631 19.305-4.631t19.247 4.631q9.043 4.63 15.174 14.891l383.478 662.087q6.13 10.735 5.714 21.306-.417 10.572-5.714 18.868-4.826 8.446-13.603 14.266-8.777 5.821-20.876 5.821H96.522ZM484.175-238.13q13.15 0 22.618-9.644 9.468-9.643 9.468-22.793 0-13.149-9.643-22.335-9.644-9.185-22.793-9.185-13.15 0-22.618 9.361-9.468 9.36-9.468 22.51 0 13.15 9.643 22.618 9.644 9.468 22.793 9.468Zm0-109.87q12.825 0 21.325-8.625T514-378v-159.478q0-12.75-8.675-21.375-8.676-8.625-21.5-8.625-12.825 0-21.325 8.625t-8.5 21.375V-378q0 12.75 8.675 21.375 8.676 8.625 21.5 8.625Z">
                </path>
              </svg>
              <h1 class="text-2xl font-bold md:text-3.3xl text-center">Continue to the next page</h1>
            </div>
            <p class="text-base lg:text-xl text-center mt-3 md:mt-4">Your test session is about to time out.</p>
          </div>
          <div class="flex w-full flex-col items-center px-6 pb-6 pt-2 md:px-10 md:pb-10 md:pt-5"><svg width="100%"
              height="100%" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid meet"
              class="h-24 w-24 -scale-x-100">
              <circle cx="100" cy="100" r="100" class="fill-primary"></circle>
              <g transform="translate(100 100) rotate(-90) scale(1 -1)">
                <path
                  class="transition-all duration-150 ease-in-out fill-primary-light opacity-40 fill-bg-primary-contrast"
                  d="M 0 0 L 100 0 A 100 100 0 0 0 100 0 L 0 0"></path>
              </g>
            </svg>
            <p class="mb-7.5 mt-3 text-3.3xl font-medium md:mb-10">00:00</p>
            <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-primary hover:brightness-110 w-full md:w-auto"
              data-test-id="idle-timer-warning-modal-confirm-button" onclick="closeDialog();">
              <span class="font-medium text-primary-contrast">Continue to the next page</span>
            </button>
          </div>
        </div>
      </dialog>

      <!-- Toggle Full Screen -->
      <div class="fixed bottom-5 right-8">
        <div class="group relative"><button
            class="flex items-center justify-center w-12 h-12 bg-light hover:brightness-110 rounded-option peer !h-9.5 !w-9.5"
            data-test-id="full-screen-toggle-icon-button"><svg xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 !w-6 !h-6 fill-light-contrast" viewBox="0 0 48 48" data-test-id="full-screen-enter-icon">
              <path d="M43.8 0l-9.9 0 0 4 7.6 0L28 17.5l2.8 2.8 13-13 0 6.5 3.9 0 0-9.8 0-4Z"></path>
              <path d="M17.2 27.7l-13 13 0-6.5 -3.9 0 0 9.8 0 4 3.9 0 9.9 0 0-4 -7.6 0L20 30.5Z"></path>
            </svg></button>
          <div class="_base_15z1o_1 _left_15z1o_14 group-[:hover]:visible">
            <div
              class="_tooltip_15z1o_33 _left_15z1o_14 relative flex w-max flex-col gap-2 rounded-lg bg-text-primary p-4 text-left text-xs text-white !rounded !p-2.5"
              role="dialog">
              <p class="text-sm text-left" id="full-screen-text">Go to fullscreen</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>

<script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
<script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
<script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
<script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>


<!-- Scripts -->
<script src="js/poll-booth.js"></script>

</html>