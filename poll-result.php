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
  $getVoterInfo =  getVoterByEmail($conn, $voterEmail, $hostID);
  $preferences = getPreferences($conn);
  $invalidLink = false;
} else {
  $invalidLink = true;
}

if ($invalidLink) {
  header("location:./");
} elseif (!$invalidLink && $pollStatus != "active" && $pollStatus != "completed") {
  header("location:poll-booth?poll_id=" . $pollID . "&voterEmail=" . $voterEmail . "&session_id=" . $getVoterInfo['voteSessionID']);
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
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
  <link rel="stylesheet" href="dist/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="dist/vendors/font-awesome/css/font-awesome.min.css">
  <style>
    .poll-container {
      transition: background 1s ease;
    }
  </style>
</head>

<body>
  <div id="root">
    <div id="efset" role="status">
      <div class="flex min-h-screen flex-col items-stretch justify-center overflow-y-auto bg-background">
        <main class="z-10 flex h-full flex-grow items-center justify-center p-4 flex-col !p-0">
          <?php if (!$invalidLink && isset($getVoterInfo['voteSessionID']) && $pollSessionID == $getVoterInfo['voteSessionID']) { ?>
            <div class="flex h-full max-w-[100vw] flex-1 flex-col self-center justify-start lg:mb-28">
              <div class="w-full px-4 py-11 lg:max-w-5xl lg:px-10 lg:py-13">
                <h1 class="text-2xl font-bold md:text-3.3xl text-center !text-3.3xl lg:!text-5xl mb-10 lg:mb-16"><?= ucfirst($getPollInfo['title']); ?> Poll Results</h1>
                <div class="flex flex-col items-center justify-center gap-x-4 gap-y-20 lg:items-start lg:flex-row">
                  <div class="w-full max-w-[300px] shrink-0 overflow-hidden rounded">

                    <div class="relative flex flex-col items-center justify-center poll-container"
                      style="background: linear-gradient(rgb(11, 189, 41), rgb(4, 128, 25));">
                      <img src="images/pattern-arrow-shape.png" class="_bottomBgImage_1xhdo_37">
                      <img src="images/pattern-arrow-shape.png" class="_leftBgImage_1xhdo_49">
                      <img src="images/pattern-arrow-shape.png" class="_rightBgImage_1xhdo_60">
                      <div class="p-7">
                        <div class="text-base font-bold lg:text-2xl text-center mb-4 !text-1.5xl text-white tracking-ultra-wide">
                          <div id="countdown" class="mt-2" style="font-size: 1.5rem;">00:00:00:00</div>
                        </div>
                        <div class="relative m-auto _progress_1xhdo_70" style="width: 200px; height: 200px;">
                          <div class="absolute flex items-center justify-center" style="width: 200px; height: 200px;">
                            <span class="text-6xl text-light !text-6.42xl font-black tracking-ultra-wide poll-status-text"
                              style="font-size:2.5rem !important;">Closed</span>
                          </div>
                          <svg style="width: 200px; height: 200px;">
                            <circle class="_placeholder_1qey4_7 stroke-chart-placeholder-section-zero/30" cx="93" cy="93" r="93"
                              style="transform: translate(7px, 7px); width: 200px; height: 200px; stroke-width: 7; stroke-dasharray: 584.336;">
                            </circle>
                            <circle id="progressCircle" class="origin-center -rotate-90 stroke-light" cx="93" cy="93" r="93"
                              style="stroke-dashoffset: 584.336; translate: 7px -7px; transition: stroke-dashoffset 0.5s; width: 200px; height: 200px; stroke-width: 7; stroke-dasharray: 584.336;">
                            </circle>
                          </svg>
                        </div>

                        <!-- Countdown for closing poll -->
                        <script type="text/javascript">
                          function startCountdownWithProgress(startDate, endDate) {
                            const countdownElement = document.getElementById('countdown');
                            const progressCircle = document.querySelector('svg circle:last-child');
                            const pollStatusText = document.querySelector('.poll-status-text');
                            const pollContainer = document.querySelector('.poll-container');
                            const totalLength = 584.336;
                            const start = new Date(startDate).getTime();
                            const end = new Date(endDate).getTime();
                            const totalDuration = end - start;

                            if (isNaN(start) || isNaN(end) || totalDuration <= 0) {
                              countdownElement.innerHTML = "<span class='badge p-2 badge-success mb-1'>POLL IS NOW CLOSED</span>";
                              pollStatusText.textContent = "Closed";
                              progressCircle.style.strokeDashoffset = totalLength;
                              pollContainer.style.background = 'linear-gradient(rgb(189, 11, 11), rgb(128, 4, 4))';
                              return;
                            }

                            pollStatusText.textContent = "Live";

                            const x = setInterval(function() {
                              const now = new Date().getTime();
                              const distance = end - now;
                              const elapsed = now - start;
                              const progress = Math.max(elapsed / totalDuration, 0);
                              const dashOffset = totalLength * (1 - progress);

                              let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                              let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                              let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                              let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                              countdownElement.innerHTML = `
                              <p class="text-sm font-medium text-center mb-2 mt-3 !font-bold text-white tracking-ultra-wide"> Poll Ends In: </p>
                              <span class='badge p-2 badge-danger mb-1'>${days}:${hours}:${minutes}:${seconds}</span>`;
                              progressCircle.style.strokeDashoffset = dashOffset.toFixed(3);

                              if (distance <= 0) {
                                clearInterval(x);
                                countdownElement.innerHTML = "<span class='badge p-2 badge-success mb-1'>POLL IS NOW CLOSED</span>";
                                pollStatusText.textContent = "Closed";
                                progressCircle.style.strokeDashoffset = totalLength;
                                pollContainer.style.background = 'linear-gradient(rgb(189, 11, 11), rgb(128, 4, 4))';
                              }
                            }, 1000);
                          }

                          // Start countdown with progress
                          const pollStartDate = "<?= $getPollInfo['startDate']; ?>";
                          const pollEndDate = "<?= $getPollInfo['endDate']; ?>";
                          if (pollStartDate && pollEndDate) {
                            startCountdownWithProgress(pollStartDate, pollEndDate);
                          } else {
                            console.error("Start or End date is not available");
                          }
                        </script>
                      </div>
                    </div>

                    <section class="bg-white p-8">
                      <div class="mx-auto flex w-min flex-col items-center">
                        <h4 class="text-base text-center font-bold"><?= ucfirst($getPollInfo['visibility']);  ?> Poll QR Code</h4>
                        <!-- Div to display QR code and download button -->
                        <div class="text-center" style="margin: 0 auto">
                          <div class="mt-3"><img style="height:100px;width:100px;position:relative;z-index:999" id="qrCodeContainer" /></div>
                        </div>
                        <!-- Poll QR Code Goes Here -->
                        <div class="mt-4 text-center">
                          <h4 class="text-base text-center font-bold"><u id="downloadPollQRBtn" style="display:none;cursor:pointer;" onclick="downloadQRCode()">Download QR Code</u></h4>
                          <button class="flex min-h-[48px] items-center justify-center gap-x-2.5 rounded-full px-9 py-2 bg-light hover:brightness-110 shadow-button m-auto mt-4 whitespace-nowrap !px-5" data-link="<?= $preferences['siteURL']; ?>/start-poll?poll_id=<?= $getPollInfo['pollID']; ?>&token=g63ag77hwzgxbb2&poll=<?= $getPollInfo['title']; ?>" id="copyLinkBtn">
                            <i class="fa fa-link"></i>
                            <span class="font-medium text-light-contrast">Copy &amp; Share Poll Link</span>
                          </button>
                        </div>
                      </div>
                    </section>
                  </div>
                  <!-- Live result Display -->
                  <div class="flex w-full flex-grow flex-col gap-y-5 lg:gap-y-4" id="liveResultContainer">

                  </div>
                </div>
              </div>
            </div>
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
        </main>
        <section class="w-full bg-background-card pt-0 mb-4">
          <p class="text-base text-light-contrast leading-7 text-center p-4">© Reohampton University. Online Polling & Voting System, presented by Ridwan Olalekan Oguntola. All rights reserved.
          </p>
        </section>
      </div>
    </div>

  </div>
</body>

<script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
<script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
<script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
<script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
<script src="dist/js/qrious.js"></script>

<!-- Scripts -->
<script src="js/poll-booth.js"></script>
<script>
  $(document).ready(function() {
    //Generate QR Code on Load
    generateQRCode();

    //Get Live result
    getLiveResult();

    //Live result interval
    setInterval(function() {
      getLiveResult();
    }, 10000);

  });

  //Helper function to copy Link address
  document.getElementById('copyLinkBtn').addEventListener('click', function() {
    var copyText = this.getAttribute('data-link');
    navigator.clipboard.writeText(copyText).then(function() {
      swal("Link copied to clipboard:", copyText, "success");
    });
  });

  //Helper function to generate a QR code
  function generateQRCode() {
    let qr = window.qr = new QRious({
      element: document.getElementById('qrCodeContainer'), //Where the code should be displayed
      size: 400,
      background: 'white', // Set the background color here
      foreground: 'rgb(2, 2, 43) ', // Set the foreground color if needed
      level: 'Q', // Optional: Set the error correction level (L, M, Q, H)
      value: document.getElementById("copyLinkBtn").getAttribute('data-link'), //url where QR code navigates to
    });

    const qrImageDataUrl = qr.toDataURL(); // Generate the image URL (base64)

    // Set the QR image data as the source for the image element
    document.getElementById('qrCodeContainer').src = qrImageDataUrl;

    document.getElementById('downloadPollQRBtn').style.display = 'inline'; // Show the download button
  }

  // Helper function to download the generated QR code as an image
  function downloadQRCode() {
    const qrImage = document.getElementById('qrCodeContainer');
    const pollTitle = document.getElementById("copyLinkBtn").getAttribute('data-link');
    if (!qrImage.src) {
      alert("QR code has not been generated yet!");
      return;
    }

    // Create a temporary link to trigger the download
    const link = document.createElement('a');
    link.href = qrImage.src; // Use the QR code image source as the download URL
    link.download = pollTitle.replace(/\s+/g, '_') + '_QR_code.png'; // Specify the file name for download

    // Simulate a click event on the link to trigger the download
    link.click();
  }

  // Helper function to toggle expandable content
  function toggleExpandableContent(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('svg');
    if (content.classList.contains('hidden')) {
      content.classList.remove('hidden');
      icon.classList.add('rotate-180');
    } else {
      content.classList.add('hidden');
      icon.classList.remove('rotate-180');
    }
  }

  // Helper function to get live poll result
  liveResultLoaded = false;

  function getLiveResult() {
    var pollID = "<?= $pollID; ?>";
    var voterEmail = "<?= $voterEmail; ?>";
    var sessionID = "<?= $pollSessionID; ?>";

    $.ajax({
      url: "controllers/get-poll-result",
      type: "POST",
      async: true,
      data: {
        requestLiveResult: true,
        pollID: pollID,
        voterEmail: voterEmail,
        sessionID: sessionID,
      },
      beforeSend: function() {
        // Show a loading spinner or message
        // $("#liveResultContainer").html("<span>Loading live results...</span>");
        if (!liveResultLoaded) {
          $("#liveResultContainer").html("").show();
          showPollSummarySkeletonLoader();
        }
      },
      success: function(response) {
        liveResultLoaded = true;
        // Update the live result container with the response
        $("#liveResultContainer").html(response);
      },
      error: function(xhr, status, error) {
        // Handle any errors
        $("#liveResultContainer").html("<span>Error loading live results. Please try again later.</span>");
      }
    });
  }

  function showPollSummarySkeletonLoader() {
    var skeletonHtml = `
    <div class="rounded bg-white p-4 md:p-8 animate-pulse">
      <p class="h-6 bg-gray-300 rounded w-1/2 mx-auto md:mx-0"></p>
      <p class="h-4 bg-gray-300 rounded w-3/4 mt-2 mx-auto md:mx-0"></p>
      <div class="mt-5 flex flex-col content-around justify-between rounded bg-gradient-to-b from-transparent to-result-certificate-bg-to px-6 py-6 md:flex-row md:border md:bg-background md:px-10 lg:px-12">
        <div class="flex flex-wrap gap-4" style="height:26rem;overflow:auto;">
          <p class="h-6 bg-gray-300 rounded w-full"></p>
          <!-- Candidate Skeleton -->
          <div class="relative rounded border border-input-border py-2 px-2 flex flex-col items-center" style="width:10rem; margin: 0 auto;">
            <div class="h-24 w-24 bg-gray-300 rounded-full"></div>
            <div class="text-center mt-2 w-full">
              <div class="h-4 bg-gray-300 rounded w-3/4 mx-auto"></div>
              <div class="h-3 bg-gray-300 rounded w-1/2 mx-auto mt-2"></div>
            </div>
          </div>
          <div class="relative rounded border border-input-border py-2 px-2 flex flex-col items-center" style="width:10rem; margin: 0 auto;">
            <div class="h-24 w-24 bg-gray-300 rounded-full"></div>
            <div class="text-center mt-2 w-full">
              <div class="h-4 bg-gray-300 rounded w-3/4 mx-auto"></div>
              <div class="h-3 bg-gray-300 rounded w-1/2 mx-auto mt-2"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="relative flex w-full flex-col items-stretch rounded bg-white p-4 animate-pulse">
        <h2 class="h-6 bg-gray-300 rounded w-1/3"></h2>
        <div class="mt-4 mb-2 flex flex-col gap-4">
          <div class="pt-5.5 lg:pt-7 md:bg-background">
            <div class="max-w-full overflow-auto py-2 px-2">
              <table class="border-separate border-spacing-2 w-full">
                <thead>
                  <tr>
                    <td class="h-6 bg-gray-300 rounded w-1/4"></td>
                    <td class="h-6 bg-gray-300 rounded w-1/4"></td>
                    <td class="h-6 bg-gray-300 rounded w-1/4"></td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="h-4 bg-gray-300 rounded w-1/4 mt-2"></td>
                    <td class="h-4 bg-gray-300 rounded w-1/4 mt-2"></td>
                    <td class="h-4 bg-gray-300 rounded w-1/4 mt-2"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
    document.getElementById("liveResultContainer").innerHTML = skeletonHtml;
  }
</script>

</html>