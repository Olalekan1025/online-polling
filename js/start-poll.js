/*========================================================
    >>>HELPER FUNCTIONS
  ==========================================================*/
// Validate Email Address Entry
function isValidEmail(email) {
  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  return regex.test(email);
}

var typingTimer;
var doneTypingInterval = 1000;
var isEmailValid = false;
var isUsernameValid = false;

// Verify Users's Registration Email for duplicates
$("#email").on("input paste", function () {
  clearTimeout(typingTimer); // Clear the previous timer

  // Set a new timer to trigger the verification function after typing stops
  typingTimer = setTimeout(function () {
    // verifyUserSignUpEntryEmail();
  }, doneTypingInterval);
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

/*==============================================================
>>>Validate Voter's Email for Poll Registration
================================================================*/
let emailSent = false;

$("#validateVoterPollEmail").submit(function (e) {
  e.preventDefault();

  let email = $("#voterEmail").val();
  let otp = $("#voterEmailOTP").val();
  let pollID = $("#pollID").val();
  let firstName = $("#voterFname").val();
  let lastName = $("#voterLname").val();
  let gender = $("#voterGender").val();

  if (!emailSent) {
    // Step 1: Request OTP
    if (!isValidEmail(email)) {
      swal(
        "Invalid Email!",
        "Please enter a valid email and try again",
        "error"
      );
      return;
    }

    $.ajax({
      url: "controllers/get-start-poll",
      type: "POST",
      data: {
        validatePollEmailAddressRequest: "true",
        voterEmail: email,
      },
      beforeSend: function () {
        $("#verifyEmailBtn").prop("disabled", true).text("Sending OTP...");
      },
      success: function (response) {
        if (
          response.status === "success" &&
          response.message === "otp generated"
        ) {
          emailSent = true;
          showOTPPanel();
          startOTPTimeout();
        } else {
          swal("Error!", "Failed to send OTP. Try again.", "error");
        }
        $("#verifyEmailBtn").prop("disabled", false).text("Verify Email");
      },
      error: function () {
        console.error("Error sending OTP");
        swal(
          "Connection Failed",
          "Error sending OTP, please check your internet connection and try again",
          "error"
        );
        $("#verifyEmailBtn").prop("disabled", false).text("Verify Email");
      },
    });
  } else if (
    firstName !== "" &&
    lastName !== "" &&
    gender !== "" &&
    emailSent
  ) {
    $.ajax({
      url: "controllers/get-start-poll",
      type: "POST",
      data: {
        processStartPoll: "true",
        voterEmail: email,
        fname: firstName,
        lname: lastName,
        gender: gender,
        pollID: pollID,
      },
      beforeSend: function () {
        $("#startPoll").prop("disabled", true).text("Verifying...");
      },
      success: function (response) {
        var status = response.status;
        var header = response.header;
        var message = response.message;
        var redirectLink = response.redirectLink;

        if (status === "success") {
          swal(header, message, status);
          $("#startPoll").text("Redirecting...");
          setTimeout(function () {
            window.location.href = redirectLink;
          }, 2000);
        } else if (status === "error") {
          swal(header, message, "error");
          $("#startPoll").prop("disabled", false).text("Start Poll");
        }
      },
      error: function () {
        console.error("Error Starting Poll, Please try again.");
        swal(
          "Connection Failed",
          "Error Starting Poll, please check your internet connection and try again",
          "error"
        );
        $("#startPoll").prop("disabled", false).text("Start Poll");
      },
    });
  } else {
    // Step 2: Validate OTP
    if (!otp || otp.length !== 6 || isNaN(otp)) {
      swal("Invalid OTP!", "Please enter a valid 6-digit OTP.", "error");
      return;
    }

    $.ajax({
      url: "controllers/get-start-poll",
      type: "POST",
      data: {
        validatePollEmailAddressRequest: "true",
        voterEmail: email,
        voterEmailOTP: otp,
        pollID: pollID,
      },
      beforeSend: function () {
        $("#verifyOtpBtn").prop("disabled", true).text("Verifying...");
      },
      success: function (response) {
        var status = response.status;
        var header = response.header;
        var message = response.message;

        if (response.status === "eligible") {
          var voter = response.voterInfo;
          showCompleteForm();
          document.getElementById("voterEmailForDisplay").value = email;
          if (response.voterInfo !== null) {
            document.getElementById("voterFname").value = voter.fname;
            document.getElementById("voterLname").value = voter.sname;
            document.getElementById("voterGender").value = voter.gender;
          }
        } else if (status === "ineligible" || status === "error") {
          swal(header, message, "error");
          resetVerification();
        }
        $("#verifyOtpBtn").prop("disabled", false).text("Confirm OTP");
      },
      error: function () {
        console.error("Error verifying OTP");
        swal(
          "Connection Failed",
          "Error verifying OTP, please check your internet connection and try again",
          "error"
        );
        $("#verifyOtpBtn").prop("disabled", false).text("Confirm OTP");
      },
    });
  }
});

function showOTPPanel() {
  $("#verifyVoterEmail").hide();
  $("#verifyEmailLabel").hide();
  $("#verifyVoterEmailOtp").show();
  $("#verifyOTPLabel").show();
  $("#voterEmailOTP").val("").focus();
}

function startOTPTimeout() {
  setTimeout(() => {
    $("#verificationReset").show();
  }, 30000);
}

function showCompleteForm() {
  $("#verifyVoterEmailOtp").hide();
  $("#verifyOTPLabel").hide();
  $("#voterInfoForm").show();
  $("#voterInfoFormLabel").show();
}

function resetVerification() {
  emailSent = false;
  $("#verifyVoterEmail").show();
  $("#verifyEmailLabel").show();
  $("#verifyVoterEmailOtp").hide();
  $("#verifyOTPLabel").hide();
  $("#verificationReset").hide();
  $("#voterInfoForm").hide();
  $("#voterInfoFormLabel").hide();

  // Reset all Voter Values
  document.getElementById("voterEmail").value = "";
  document.getElementById("voterEmailForDisplay").value = "";
  document.getElementById("voterFname").value = "";
  document.getElementById("voterLname").value = "";
  document.getElementById("voterGender").value = "";
}

function isValidEmail(email) {
  let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}
