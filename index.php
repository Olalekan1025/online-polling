<?php
session_start();
if (isset($_SESSION['hostID']) && isset($_SESSION['hostEmail']) && isset($_SESSION['portalAccess'])) {
  header("location:./dashboard");
}
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
        <h2 class="text-3xl font-bold mb-6 text-center">Sign in</h2>
        <a href="javascript:void(0);" onclick="handlePanelSwitch('registration');"
          class="text-green-700 hover:underline text-sm block text-center mb-6">
          or create an account
        </a>
        <form class="space-y-6  needs-validation" id="userLoginForm" novalidate>
          <div>
            <label class="block text-sm text-gray-700 mb-2">Email</label>
            <input type="email" placeholder="Enter your email address"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" name="userLoginID"
              id="loginEmail" required>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-2">Password</label>
            <input type="password" placeholder="Enter your password"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" name="userPassword"
              id="loginPassword" required>
          </div>
          <div class="flex items-center mt-4">
            <input type="checkbox" class="mr-2" id="showHide" toggle="#loginPassword">
            <label for="showHide" class="text-sm text-gray-700">Show Password</label>
          </div>
          <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg text-sm hover:bg-green-700"
            id="loginBtn">
            Sign in
          </button>
          <p align="center" style="text-align:center" id="loginMsg"></p><!--Log message-->
          <!-- <div class="mt-6 space-y-4">
            <button class="w-full bg-white border border-gray-300 text-black py-3 rounded-lg text-sm hover:bg-gray-100">
              <i class="fab fa-google mr-2"></i>Sign in with Google
            </button>
            <button class="w-full bg-white border border-gray-300 text-black py-3 rounded-lg text-sm hover:bg-gray-100">
              <i class="fab fa-apple mr-2"></i>Sign in with Apple
            </button>
          </div> -->
          <a href="javascript:void(0);" onclick="handlePanelSwitch('forgot-password');"
            class="text-green-700 hover:underline text-sm block text-center mt-6">Forgotten your password?</a>
        </form>
      </div>

      <!-- Registration Panel -->
      <div id="registration-panel" class="hidden needs-validation">
        <h2 class="text-3xl font-bold mb-6 text-center">Sign up</h2>
        <a href="javascript:void(0);" onclick="handlePanelSwitch('login');"
          class="text-green-700 hover:underline text-sm block text-center mb-6">
          Already have an account?
        </a>
        <form class="space-y-6 needs-validation" id="registrationForm" novalidate>
          <div>
            <label class="block text-sm text-gray-700 mb-2" for="type">Sign up as</label>
            <select class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" name="type" required>
              <option value="" disabled selected>Select type</option>
              <option value="individual">Individual</option>
              <option value="organization">Organization</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-2" for="username">Name</label>
            <input type="text" placeholder="Username or Company Name"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="username" name="username" required>
            <span id="username-feedback" style="font-size:12px; color:red"></span>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-2" for="email">Email</label>
            <input type="email" placeholder="Your Email Address"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="email" name="email" required>
            <span id="email-feedback" style="font-size:12px; color:red"></span>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-2" for="password">Password</label>
            <input type="password" placeholder="Create Password"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="password" name="password" required>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-2" for="confirmPassword">Confirm Password</label>
            <input type="password" placeholder="Confirm Password"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="confirmPassword" name="confirmPassword" required>
            <span id="password-feedback" style="font-size:12px; color:red"></span>
          </div>
          <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg text-sm hover:bg-green-700" id="regBtn">
            Sign up
          </button>
        </form>
      </div>

      <!-- Forgot Password Panel -->
      <div id="forgot-password-panel" class="hidden needs-validation">
        <h2 class="text-3xl font-bold mb-6 text-center">Forgot Password</h2>
        <a href="javascript:void(0);" onclick="handlePanelSwitch('login');"
          class="text-green-700 hover:underline text-sm block text-center mb-6">
          Back to Login
        </a>
        <form class="space-y-6 needs-validation" id="resetPasswordForm" novalidate>
          <div>
            <label class="block text-sm text-gray-700 mb-2">Email</label>
            <input type="email" placeholder="Enter your email address"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="resetPasswordEmail" name="resetPasswordEmail" required autocomplete="off">
          </div>
          <button type="submit" id="passwordResetBtn" class="w-full bg-green-600 text-white py-3 rounded-lg text-sm hover:bg-green-700">
            Send Reset Link
          </button>
        </form>
      </div>

      <!-- Validate One Time Password-->
      <div id="verification-panel" class="hidden">
        <h2 class="text-3xl font-bold mb-6 text-center">Verify OTP</h2>
        <a href="javascript:void(0);" onclick="handlePanelSwitch('login');"
          class="text-green-700 hover:underline text-sm block text-center mb-6">
          Back to Login
        </a>
        <form class="space-y-6  needs-validation" id="emailVerificationForm" novalidate>
          <div>
            <label class="block text-sm text-gray-700 mb-2">Verification Code</label>
            <input type="text" placeholder="Enter six digit code"
              class="w-full p-4 bg-gray-200 rounded-lg border border-gray-300 text-sm" id="validationCode" name="validationCode" maxlength="6" required>
            <input type="hidden" name="verificationEmail" id="verificationEmail" />
          </div>
          <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg text-sm hover:bg-green-700" id="emailVerificationBtn">
            Submit
          </button>
          <span id="emailVerificationMsg"></span>
        </form>
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
  <script src="js/index.js"></script>
</body>

</html>