/*==============================================================
      Panel Switch Function From Login, to Registration Panel, to verification panel
 ============================================================= */
function handlePanelSwitch(panel) {
  const panels = [
    "login-panel",
    "registration-panel",
    "forgot-password-panel",
    "verification-panel",
  ];

  panels.forEach((id) => {
    const element = document.getElementById(id);
    if (id === panel + "-panel") {
      element.classList.remove("hidden");
      element.classList.add("active");
    } else {
      element.classList.remove("active");
      element.classList.add("hidden");
    }
  });
}
/*==============================================================
      Function to show and hide login password
============================================================= */
$("#showHide").on("click", function () {
  //$(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

/*==============================================================
          Function to Check form Validity
============================================================= */
//Function to check if required input is not empty before submitting form
function checkFormInputsNotEmpty(formID) {
  var form = document.getElementById(formID);
  //console.log(form);
  if (!form) {
    console.error("Form element with ID " + form + " not found");
    return false;
  }

  var inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );
  var isEmpty = false;

  inputs.forEach(function (input) {
    if (input.value.trim() === "") {
      isEmpty = true;
    }
  });

  return !isEmpty;
}

/*==============================================================
      Authorize User Login
============================================================= */
$("#userLoginForm").submit(function (e) {
  e.preventDefault();

  var formID = "userLoginForm";
  var loginForm = new FormData($("#userLoginForm")[0]);

  loginForm.append("authUserLogin", true);
  if (checkFormInputsNotEmpty(formID)) {
    $.ajax({
      url: "controllers/authLogin",
      type: "POST",
      async: true,
      data: loginForm,
      processData: false, // Important when sending FormData
      contentType: false, // Important when sending FormData
      beforeSend: function () {
        $("#loginBtn").prop("disabled", true);
        $("#loginBtn")
          .html(
            "<span><i class='spinner-grow spinner-grow-sm'></i> Verifying...</span>"
          )
          .show();
      },
      success: function (lgFx) {
        var status = lgFx.status;
        var header = lgFx.header;
        var message = lgFx.message;
        var redirectPage = lgFx.redirectPage;

        if (status === "success") {
          showAlert(message, status);
          setTimeout(function () {
            $("#loginMsg").html(message).css("color", "#4A8717").show();
          }, 2000);
          setTimeout(function () {
            $("#loginBtn")
              .html(
                "<i class='spinner-border spinner-border-sm'></i> Redirecting..."
              )
              .css("color", "white")
              .show();
            window.location.href = redirectPage;
          }, 3000);
        } else if (status === "error") {
          showAlert(message, status);
          setTimeout(function () {
            $("#loginBtn").prop("disabled", false);
            $("#loginBtn").html("Sign in").show();
            $("#loginMsg").html(message).css("color", "red").show();
          }, 2000);
        } else if (status === "warning") {
          showAlert(message, status);
          setTimeout(function () {
            $("#loginBtn").prop("disabled", false);
            $("#loginBtn").html("Sign in").show();
            $("#loginMsg").html(message).css("color", "red").show();
          }, 2000);
        }
      },
      error: function () {
        $("#loginBtn").html("Sign in").show();
        $("#loginBtn").prop("disabled", false);
        showAlert(
          "Connectivity Error, Check your internet and try again.",
          "error"
        );
      },
    });
  } else {
    showAlert(
      "The login credentials seem empty or incomplete. Please check your entry and try again.",
      "error"
    );
  }
});

/*==============================================================
          Function to register users
============================================================= */
$("#registrationForm").submit(function (e) {
  e.preventDefault();

  var formID = "registrationForm";
  var registrationFormId = new FormData($("#registrationForm")[0]);
  var password = $("#password").val();
  var confirmPassword = $("#confirmPassword").val();

  if (password !== confirmPassword) {
    showAlert("Passwords do not match", "error");
    $("#loginMsg")
      .html("Password does not match")
      .css("color", "#4A8717")
      .show();
  } else if (checkFormInputsNotEmpty(formID)) {
    registrationFormId.append("regUser", true);

    swal(
      {
        title: "Are you sure to continue?",
        text: "You are about processing your registration.",
        icon: "warning",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger",
        confirmButtonText: "Continue!",
        cancelButtonText: "Cancel!",
        closeOnConfirm: true,
        closeOnCancel: true,
      },
      function (isConfirm) {
        if (isConfirm) {
          $.ajax({
            url: "controllers/get-registration",
            type: "POST",
            async: true,
            data: registrationFormId,
            processData: false, // Important when sending FormData
            contentType: false, // Important when sending FormData
            beforeSend: function () {
              $("#regBtn")
                .html(
                  "<span><i class='spinner-grow spinner-grow-sm'></i> Registering... </span>"
                )
                .show();
              $("#regBtn").prop("disabled", true);
            },
            success: function (lgFx) {
              var status = lgFx.status;
              var message = lgFx.message;
              var header = lgFx.header;
              var feedbackResponse = lgFx.feedbackResponse;
              var logID = $("#email").val();

              showAlert(message, status);

              if (feedbackResponse === true) {
                $("#userLoginID").val(logID);
                $("#verificationEmail").val(logID);
                $("#registrationForm")[0].reset();
                handlePanelSwitch("verification");
              }
            },
            error: function () {
              $("#regBtn").html("Sign in").show();
              $("#regBtn").prop("disabled", false);
              showAlert(
                "Connectivity Error, Check your internet and try again.",
                "error"
              );
            },
          });
        }
      }
    );
  } else {
    showAlert("Required fields cannot be empty.", "error");
  }
});

/*==============================================================
       Function to show alert and Set logMsg timeout to hide
============================================================= */
setTimeout(function () {
  $("#loginMsg").fadeOut(520);
}, 6000);

// Modal alert container
function showAlert(message, status) {
  var alertMessage = `
      <div class="bg-white w-full max-w-2xl p-4 md:p-6 rounded-lg shadow-lg relative">
        <div class="absolute top-2 right-2 cursor-pointer text-gray-600 text-xl klb-notice-close">✖</div>
        <div class="text-center">
          <h2 class="font-bold text-lg md:text-xl text-${
            status === "success" ? "green" : "red"
          }-700">${status === "success" ? "Success" : "Error"}</h2>
          <p class="mt-2">${message}</p>
        </div>
      </div>
    `;

  $("#klb-notice-ajax").html(alertMessage).removeClass("hidden").fadeIn(300);

  $(".klb-notice-close").on("click", function () {
    $("#klb-notice-ajax").fadeOut(300, function () {
      $(this).addClass("hidden").html("");
    });
  });

  setTimeout(function () {
    $("#klb-notice-ajax").fadeOut(300, function () {
      $(this).addClass("hidden").html("");
    });
  }, 10000);
}
/*==============================================================
       Validate User Entry For Duplicates and password match
============================================================= */
var typingTimer;
var doneTypingInterval = 1000;
var isEmailValid = false;
var isUsernameValid = false;

// Verify Users's Registration Email for duplicates
$("#email").on("input paste", function () {
  clearTimeout(typingTimer); // Clear the previous timer

  // Set a new timer to trigger the verification function after typing stops
  typingTimer = setTimeout(function () {
    verifyUserSignUpEntryEmail();
  }, doneTypingInterval);
});

function verifyUserSignUpEntryEmail() {
  var email = $("#email").val();
  $.ajax({
    type: "POST",
    url: "controllers/get-registration",
    async: true,
    data: {
      userEmailEntryVer: 1,
      email: email,
    },
    success: function (response) {
      var status = response.status;
      var message = response.message;

      if (status === true) {
        $("#email-feedback").html(message).show();
        isEmailValid = false;
      } else {
        $("#email-feedback").hide();
        isEmailValid = true;
      }
      validateSignupButton();
    },
    error: function () {
      swal(
        "Connection Failed",
        "Error in connectivity, please check your internet connection and try again.",
        "error"
      );
    },
  });
}

// Verify User's Registration Username for duplicates
$("#username").on("input paste", function () {
  clearTimeout(typingTimer); // Clear the previous timer

  // Set a new timer to trigger the verification function after typing stops
  typingTimer = setTimeout(function () {
    verifyUserSignUpEntryUsername();
  }, doneTypingInterval);
});

function verifyUserSignUpEntryUsername() {
  var username = $("#username").val();
  $.ajax({
    type: "POST",
    url: "controllers/get-registration",
    async: true,
    data: {
      userUsernameEntryVer: 1,
      username: username,
    },
    success: function (response) {
      var status = response.status;
      var message = response.message;

      if (status === true) {
        $("#username-feedback").html(message).show();
        isUsernameValid = false;
      } else {
        $("#username-feedback").hide();
        isUsernameValid = true;
      }
      validateSignupButton();
    },
    error: function () {
      swal(
        "Connection Failed",
        "Error in connectivity, please check your internet connection and try again.",
        "error"
      );
    },
  });
}

// Password and Confirm Password validation logic
const passwordField = document.getElementById("password");
const confirmPasswordField = document.getElementById("confirmPassword");
const regBtn = document.getElementById("regBtn");

if (passwordField && confirmPasswordField) {
  passwordField.addEventListener("input", checkPasswordMatch);
  confirmPasswordField.addEventListener("input", checkPasswordMatch);
}

function checkPasswordMatch() {
  const password = passwordField.value.trim();
  const confirmPassword = confirmPasswordField.value.trim();

  if (password !== confirmPassword) {
    $("#password-feedback").html("Passwords do not match.").show();
    if (regBtn) regBtn.disabled = true;
    $(regBtn).hide();
  } else {
    $("#password-feedback").hide();
    validateSignupButton(); // Check other validations
  }
}

// Toggle Signup button based on email and username validity
function validateSignupButton() {
  const password = passwordField.value.trim();
  const confirmPassword = confirmPasswordField.value.trim();

  if (isEmailValid && isUsernameValid && password === confirmPassword) {
    if (regBtn) regBtn.disabled = false;
    $(regBtn).show();
  } else {
    if (regBtn) regBtn.disabled = true;
    $(regBtn).hide();
  }
}

// Validate Email
function validateEmail() {
  const emailInput = document.getElementById("email");
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const emailFeedback = $("#email-feedback");

  isEmailValid = emailRegex.test(emailInput.value.trim());
  if (!isEmailValid) {
    emailFeedback.html("Please enter a valid email address.").show();
  } else {
    emailFeedback.hide();
  }
  validateSignupButton(); // Re-check button visibility
}

// Validate Username
function validateUsername() {
  const usernameInput = document.getElementById("username");
  const usernameFeedback = $("#username-feedback");

  isUsernameValid = usernameInput.value.trim().length >= 3;
  if (!isUsernameValid) {
    usernameFeedback
      .html("Username must be at least 3 characters long.")
      .show();
  } else {
    usernameFeedback.hide();
  }
  validateSignupButton(); // Re-check button visibility
}

// Attach Event Listeners
if (passwordField && confirmPasswordField) {
  passwordField.addEventListener("input", checkPasswordMatch);
  confirmPasswordField.addEventListener("input", checkPasswordMatch);
}

const emailInput = document.getElementById("email");
if (emailInput) {
  emailInput.addEventListener("input", validateEmail);
}

const usernameInput = document.getElementById("username");
if (usernameInput) {
  usernameInput.addEventListener("input", validateUsername);
}
/*==============================================================
       Email Verification functions
============================================================= */
$("#validationCode").on("keyup keydown input paste", function () {
  var validationCode = $(this).val(); // Get the current value of the input field

  if (validationCode.length < 6) {
    $("#emailVerificationBtn").prop("disabled", true);
  } else {
    $("#emailVerificationBtn").prop("disabled", false);
  }
});

// Success and error handlers
$("#emailVerificationForm").submit(function (e) {
  e.preventDefault();

  var verificationForm = new FormData($("#emailVerificationForm")[0]);
  var verificationEmail = $("#verificationEmail").val();
  verificationForm.append("verificationEmail", verificationEmail);
  verificationForm.append("authEmailVerification", true);

  $.ajax({
    url: "controllers/get-verification",
    type: "POST",
    async: true,
    data: verificationForm,
    processData: false,
    contentType: false,
    beforeSend: function () {
      $("#emailVerificationBtn")
        .prop("disabled", true)
        .html(
          "<span><i class='spinner-grow spinner-grow-sm'></i> Verifying... </span>"
        );
    },
    success: function (emailVerificationX) {
      var status = emailVerificationX.status;
      var message = emailVerificationX.message;
      var header = emailVerificationX.header;

      // Create the alert message dynamically
      var alertMessage = `
        <div class="bg-white w-full max-w-xl p-4 md:p-6 rounded-lg shadow-lg relative">
          <div class="absolute top-2 right-2 cursor-pointer text-gray-600 text-xl klb-notice-close">✖</div>
          <div class="text-center">
            <h2 class="font-bold text-lg md:text-xl text-${
              status === "success" ? "green" : "red"
            }-700">${header}</h2>
            <p class="mt-2">${message}</p>
          </div>
        </div>
      `;

      // Show the alert modal
      $("#klb-notice-ajax")
        .html(alertMessage)
        .removeClass("hidden")
        .fadeIn(300);

      // Close button functionality
      $(".klb-notice-close").on("click", function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      });

      // Hide the modal automatically after 10 seconds
      setTimeout(function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      }, 10000);

      if (status === "success") {
        // swal(header, message, status);
        handlePanelSwitch("login");
      } else {
        // swal(header, message, status);
        $("#emailVerificationBtn").prop("disabled", false).html("Confirm Code");
      }
    },
    error: function () {
      $("#emailVerificationBtn").prop("disabled", false).html("Confirm Code");
      swal(
        "Connection Error",
        "Connectivity Error, Check your internet and try again",
        "error"
      );

      var alertMessage = `
        <div class="bg-white w-full max-w-2xl p-4 md:p-6 rounded-lg shadow-lg relative">
          <div class="absolute top-2 right-2 cursor-pointer text-gray-600 text-xl klb-notice-close">✖</div>
          <div class="text-center">
            <h2 class="font-bold text-red-700">Connection Error</h2>
            <p class="mt-2">Connectivity Error, Check your internet and try again</p>
          </div>
        </div>
      `;

      $("#klb-notice-ajax")
        .html(alertMessage)
        .removeClass("hidden")
        .fadeIn(300);

      $(".klb-notice-close").on("click", function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      });

      setTimeout(function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      }, 10000);
    },
  });
});

/*==============================================================
//Submit Password reset form >>>>||
============================================================= */
$("#resetPasswordForm").submit(function (e) {
  e.preventDefault();
  resetPasswordForm = new FormData($("#resetPasswordForm")[0]);
  resetPasswordForm.append("authResetPasswordEmail", true);
  resetPasswordForm.append("authType", "user");
  $.ajax({
    type: "POST",
    url: "controllers/get-password-reset",
    async: true,
    processData: false,
    contentType: false,
    data: resetPasswordForm,
    beforeSend: function () {
      $("#passwordResetBtn")
        .html("<em class='spinner-grow spinner-grow-sm'></em> Processing...")
        .show();
      $("#passwordResetBtn").prop("disabled", true);
    },
    success: function (passwordResetX) {
      var status = passwordResetX.status;
      var message = passwordResetX.message;
      var header = passwordResetX.header;
      var feedbackResponse = passwordResetX.feedbackResponse;

      swal(header, message, status);

      // if (status === "error" || status === "warning") {
      //   var backgroundStyle = "#dc2626";
      // } else {
      //   var backgroundStyle = "";
      // }

      // var verificationMessage = $(`
      // 			// <div class='woocommerce-message' role='alert' style='background-color:${backgroundStyle}'>
      // 			// <p>${message}</p>
      // 			// <div class="klb-notice-close"><i class="klb-icon-x"></i></div>
      // 			// <p><b>Login Notification</b></p></div>`);

      var verificationMessage = `
        <div class="bg-white w-full max-w-xl p-4 md:p-6 rounded-lg shadow-lg relative">
          <div class="absolute top-2 right-2 cursor-pointer text-gray-600 text-xl klb-notice-close">✖</div>
          <div class="text-center">
            <h2 class="font-bold text-lg md:text-xl text-${
              status === "success" ? "green" : "red"
            }-700">${header}</h2>
            <p class="mt-2">${message}</p>
          </div>
        </div>
      `;

      var feedbackResponse = passwordResetX.feedbackResponse;
      if (feedbackResponse === true) {
        $("#resetPasswordForm")[0].reset(); // Reset student registration form
      }

      $("#passwordResetBtn").html("Send Reset Link").show();
      $("#passwordResetBtn").prop("disabled", false);

      $("#passwordResetBtn").html("Send Reset Link").show();

      // Create the alert message dynamically
      $("#klb-notice-ajax")
        .html(verificationMessage)
        .removeClass("hidden")
        .fadeIn(300);

      $(".klb-notice-close").on("click", function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      });

      setTimeout(function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      }, 10000);

      // Set a timeout to fade out and remove the alert after 10 seconds
      // setTimeout(function () {
      //   verificationMessage.fadeOut(300, function () {
      //     $(this).remove();
      //   });
      // }, 10000);

      // // Close button click handler
      // $(".klb-notice-close").on("click", function () {
      //   $(this).closest(".woocommerce-message").remove();
      // });
    },
    error: function () {
      $("#passwordResetBtn").prop("disabled", false);
      $("#passwordResetBtn").html("Send Reset Link").show();
      $("#klb-notice-ajax")
        .html(
          `
            <div class="bg-white w-full max-w-xl p-4 md:p-6 rounded-lg shadow-lg relative">
              <div class="absolute top-2 right-2 cursor-pointer text-gray-600 text-xl klb-notice-close">✖</div>
              <div class="text-center">
                <h2 class="font-bold text-lg md:text-xl text-red-700"><p><b>Connection Failed</b></h2>
                <p class="mt-2">Error in connectivity, please check your internet connection and try again.</p>
              </div>
            </div>
            `
        )
        .removeClass("hidden")
        .fadeIn(300);

      setTimeout(function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      }, 5000);

      $(".klb-notice-close").on("click", function () {
        $("#klb-notice-ajax").fadeOut(300, function () {
          $(this).addClass("hidden").html("");
        });
      });
    },
  });
});
//Submit Password reset form >>>>||

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
