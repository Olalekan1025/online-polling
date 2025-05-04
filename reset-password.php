<?php
session_start();
if (isset($_SESSION['hostID']) && isset($_SESSION['hostEmail']) && isset($_SESSION['portalAccess'])) {
  header("location:./dashboard");
}
$page = "reset-password";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roehampton University :: Online Polling &amp; Voting System</title>
  <link rel="icon" href="images/roe.png" type="icon/png">
  <script src="js/teilwind.js"></script>
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">

  <!-- <link rel="stylesheet" href="dist/bootstrap/css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
</head>

<body class="min-h-screen flex flex-col lg:flex-row">
  <!-- Left Section -->
  <div class="relative lg:w-2/3 hidden lg:block">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('images/roehampton-london.jpg');">
      <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    </div>
    <div class="relative z-10 text-white p-10 h-full flex flex-col justify-center">
      <h1 class="text-5xl font-extrabold leading-snug">
        Online Polling &amp; Voting System
      </h1>
      <p class="mt-6 max-w-xl text-xl font-medium text-justify">
        The Roehampton Online Polling and Voting System is a secure and efficient platform designed for university
        elections, surveys, and feedback collection. It allows students and staff to vote or participate in polls
        easily, ensuring privacy, transparency, and real-time results. The system is tailored for seamless, online
        engagement, making it ideal for campus-wide decision-making.
      </p>
      <p class="mt-7 max-w-xl text-sm font-small bg-green-800"
        style="border-radius: 8px; padding: 5px; text-align: center;">
        &copy; 2025 Copyright University of Roehampton Polling & Voting System | <strong>Presented by Ridwan Olalekan Oguntola</strong>
      </p>
    </div>
  </div>

  <!-- Right Section -->
  <div class="flex items-center justify-center lg:w-1/3 bg-white min-h-screen lg:min-h-0 p-6 lg:p-10 relative z-20">
    <div class="w-full max-w-md">
      <div class="flex items-center mb-6">
        <img src="images/roe.png" alt="Roehampton University" style="height: 6rem; margin: 0 auto">
      </div>

      <!-- Login Panel -->
      <div id="login-panel" class="active needs-validation">
        <?php
        include("controllers/globalFunctions.php");

        // Check if the required parameters are set and valid
        if (isset($_GET['dataQuery']) && $_GET['dataQuery'] == "user" && isset($_GET["isVerifyCode"]) && isset($_GET["userEmail"])) {
          // Validate and sanitize input data
          $dataQuery = mysqli_real_escape_string($conn, $_GET['dataQuery']);
          $isVerifyCode = mysqli_real_escape_string($conn, $_GET["isVerifyCode"]);
          $userEmail = mysqli_real_escape_string($conn, $_GET["userEmail"]);

          // Prepare and execute the SQL statement
          if ($dataQuery == "user") {
            $userID = $_GET["userEmail"];
            $hostInfo = getHostInfo($conn, $userID);
          }
          if (!empty($hostInfo)) {
        ?>
            <h2 class="text-3xl font-bold mb-6 text-center">Reset Password</h2>
            <a href="./" class="text-green-700 hover:underline text-sm block text-center mb-6">
              Return To Login
            </a>
            <form class="space-y-6  needs-validation" id="passwordResetForm" novalidate>
              <div>
                <label class="block text-sm text-gray-700 mb-2">New Password</label>
                <input type="password"
                  class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="password" name="password" placeholder="Enter new password" oninput="validatePassword('passwordResetForm')" required autocomplete="off">

                <input type="hidden" name="verificationCode" value="<?= (isset($_GET['isVerifyCode'])) ? $_GET['isVerifyCode'] : '' ?>" />
                <input type="hidden" name="redirectLink" value="<?= (isset($_GET['redirectLink'])) ? $_GET['redirectLink'] : '' ?>" />
                <input type="hidden" name="dataQuery" value="<?= (isset($_GET['dataQuery'])) ? $_GET['dataQuery'] : '' ?>" />
                <input type="hidden" name="userEmail" value="<?= (isset($_GET['userEmail'])) ? $_GET['userEmail'] : '' ?>" />
              </div>
              <div>
                <label class="block text-sm text-gray-700 mb-2">Confirm Password</label>
                <input type="password" class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password" oninput="validatePassword('passwordResetForm')" required autocomplete="off">
              </div>
              <div class="flex items-center mt-4">
                <input class="mr-2" id="showhide" toggle='#confirmPassword, #password' name="rememberme" type="checkbox" value="Show Password">
                <label for="showhide" class="text-sm text-gray-700">Show Password</label>
              </div>
              <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg text-sm hover:bg-green-700"
                id="resetPasswordBtn">
                Change Password
              </button>
              <p align="center" style="text-align:center" id="errorMessage"></p><!--Log message-->
              <p align="center" style="text-align:center" id="passwordResetMsg"></p><!--Log message-->
            </form>
          <?php }
        } else { ?>
          <h2 class="text-3xl font-bold mb-6 text-center text-red-600">Invalid or Broken Link</h2>
          <p class="text-center text-gray-700 mb-6">
            The link you followed is either invalid, expired, or broken. Please ensure you are using the correct link or request a new one.
          </p>
          <a href="./" class="text-green-700 hover:underline text-sm block text-center mb-6">
            Return To Login
          </a>
        <?php } ?>
      </div>


    </div>
  </div>

  <?php include("inc/footer-index.php"); ?>

  <!-- START: Template JS-->
  <script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
  <script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
  <!-- END: Template JS-->

  <!-- Scripts -->
  <!-- <script src="js/index.js"></script> -->
</body>
<script>
  //SHOW AND HIDE PASSWORD START
  $("#showhide").click(function() {
    //$(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });
  //SHOW AND HIDE PASSWORD END


  // Function to validate password and confirm password
  function validatePassword(formId) {
    var passwordInput = document.getElementById('password');
    var confirmPasswordInput = document.getElementById('confirmPassword');

    // Check if the password and confirm password inputs exist
    if (!passwordInput || !confirmPasswordInput) {
      console.error("Password inputs not found for form with ID: " + formId);
      return false;
    }

    var password = passwordInput.value;
    var confirmPassword = confirmPasswordInput.value;
    var passwordMatch = password !== "" && confirmPassword !== "" && password === confirmPassword;

    // Show error message if passwords do not match
    var errorMessage = document.getElementById('errorMessage');
    if (!passwordMatch) {
      errorMessage.innerText = "Passwords do not match!";
      confirmPasswordInput.setCustomValidity("Passwords do not match");
    } else {
      errorMessage.innerText = "";
      confirmPasswordInput.setCustomValidity("");
    }

    // Disable signup button if passwords do not match
    var signupButton = document.getElementById('resetPasswordBtn');
    if (signupButton) {
      signupButton.disabled = !passwordMatch;
    }

    return passwordMatch;
  }

  //Authorize Password Reset
  $("#passwordResetForm").submit(function(e) {
    e.preventDefault();

    var verificationForm = new FormData($("#passwordResetForm")[0]);

    verificationForm.append("authPasswordReset", "<?= (isset($_GET['isVerifyCode'])) ? $_GET['isVerifyCode'] : false; ?>");

    $.ajax({
      url: "controllers/get-password-reset",
      type: "POST",
      async: true,
      data: verificationForm,
      processData: false, // Important when sending FormData
      contentType: false, // Important when sending FormData
      beforeSend: function(passwordResetX) {
        $("#resetPasswordBtn").prop("disabled", true);
        $("#resetPasswordBtn").html("<em class='spinner-grow spinner-grow-sm'></em> Processing... ").show();
      },
      success: function(passwordResetX) {
        var status = passwordResetX.status;
        var message = passwordResetX.message;
        var redirectPage = passwordResetX.redirectPage;
        var header = passwordResetX.header;

        if (status === "success") {
          swal(header, message, status);
          setTimeout(function() {
            $("#passwordResetMsg").html(message).css("color", "#4A8717").show();
          }, 2000);
          setTimeout(function() {
            $("#resetPasswordBtn").prop("disabled", true);
            $("#resetPasswordBtn").html("<i class='spinner-border spinner-border-sm'></i> Redirecting...").css("color", "white").show();
            window.location.href = redirectPage;
          }, 3000);
          setTimeout(function() {
            $("#passwordResetMsg").hide();
            // $("#resetPasswordBtn").prop("disabled", false);
            $("#resetPasswordBtn").html("<i class='spinner-border spinner-border-sm'></i> Redirecting...").css("color", "white").show();
          }, 5000);
        } else if (status === "error") {
          setTimeout(function() {
            $("#resetPasswordBtn").prop("disabled", false);
            $("#resetPasswordBtn").html("Change Password").show();
            $("#passwordResetMsg").html(message).css("color", "red").show();
            swal(header, message, status);
          }, 2000);
        }
      },
      error: function(passwordResetX) {
        $("#resetPasswordBtn").html("Change Password").show();
        $("#resetPasswordBtn").prop("disabled", false);
        swal("Connection Error", "Connectivity Error, Check your internet and try again", "error");
      },
    });
  });
</script>

</html>