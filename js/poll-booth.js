/*========================================================
    >>>HELPER FUNCTIONS
  ==========================================================*/
// Get the full-screen toggle button
const fullScreenButton = document.querySelector(
  '[data-test-id="full-screen-toggle-icon-button"]'
);

// Function to check if the document is currently in fullscreen
function isFullScreen() {
  return (
    document.fullscreenElement || // Standard
    document.mozFullScreenElement || // Firefox
    document.webkitFullscreenElement || // Chrome, Safari, Opera
    document.msFullscreenElement // IE/Edge
  );
}

// Function to toggle fullscreen mode
function toggleFullScreen() {
  const buttonText = document.getElementById("full-screen-text");
  if (isFullScreen()) {
    // Exit fullscreen if currently in fullscreen
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
      // Firefox
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      // Chrome, Safari, Opera
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      // IE/Edge
      document.msExitFullscreen();
    }
    buttonText.innerText = "Go to Fullscreen";
  } else {
    // Enter fullscreen if not in fullscreen
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      // Firefox
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      // Chrome, Safari, Opera
      document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      // IE/Edge
      document.documentElement.msRequestFullscreen();
    }
    buttonText.innerText = "Exit Fullscreen";
  }
}

// Add event listener for the button click to toggle fullscreen
fullScreenButton.addEventListener("click", toggleFullScreen);

// Function to open the dialog
function openDialog() {
  const dialog = document.getElementById("idle-timer-dialog");
  dialog.showModal(); // Open the dialog
}

// Function to close the dialog when the button is clicked
function closeDialog() {
  const dialog = document.getElementById("idle-timer-dialog");
  dialog.close(); // Close the dialog
}

/*==============================================================
       Get Poll Ready
============================================================= */
$("#booth-start-btn").on("click", function (e) {
  e.preventDefault();

  var $this = $(this);
  var pollID = $this.data("poll-id");
  var voterEmail = $this.data("voter-email");
  var hostID = $this.data("host-id");
  var sessionID = $this.data("session-id");

  $.ajax({
    url: "controllers/get-poll-booth",
    type: "POST",
    async: true,
    data: {
      preparePollBooth: true,
      pollID: pollID,
      voterEmail: voterEmail,
      hostID: hostID,
      sessionID: sessionID,
    },
    beforeSend: function () {
      // Disable the button and show a loading spinner
      $this
        .html(
          "<span><i class='spinner-grow spinner-grow-sm'></i> Starting poll, please wait...</span>"
        )
        .prop("disabled", true);
    },
    success: function (response) {
      // Assuming success, switch visibility of phase panels
      document.getElementById("phaseOnePanel").style.display = "none"; // Hide phase one panel
      document.getElementById("phaseTwoPanel").style.display = "block"; // Show phase two panel
      $("#show-booth").html(response).show();
    },
    error: function () {
      // Re-enable the button and change text back on error
      $this.text("Continue").prop("disabled", false);

      // Get modal elements correctly
      var modalDialogue = document.getElementById("errorAlertModal");
      var modalHeader =
        document.getElementsByClassName("modal-header-title")[0]; // Access the first modal header element
      var modalMessage = document.getElementsByClassName(
        "modal-alert-message"
      )[0]; // Access the first modal message element
      var controlButton = document.getElementById("modal-control-button");

      // Show the modal with the error message
      modalDialogue.showModal();

      // Set the modal header and message content
      modalHeader.innerText = "Oops! Something went wrong";
      modalMessage.innerText = "Poll could not be processed. Please try again.";

      // Set the modal control button to retry the operation
      controlButton.onclick = function () {
        // Retry the AJAX function when the control button is clicked
        $("#booth-start-btn").trigger("click");
        modalDialogue.close(); // Close the modal after clicking the retry button
      };
    },
  });
});

/*==============================================================
       Hashing function using SHA-256
============================================================= */
async function hashPassword(password) {
  const encoder = new TextEncoder();
  const data = encoder.encode(password);
  const hashBuffer = await crypto.subtle.digest("SHA-256", data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray
    .map((b) => b.toString(16).padStart(2, "0"))
    .join("");
  return hashHex;
}
