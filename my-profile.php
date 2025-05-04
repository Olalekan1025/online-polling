<?php
session_start();

if (!isset($_SESSION['hostID']) && !isset($_SESSION['hostEmail']) && !isset($_SESSION['portalAccess'])) {
  header("location:./");
  $_SESSION["logMsg"] = "Please login to continue";
}

$page = "my-profile";
?>
<!DOCTYPE html>
<html lang="en-US">

<!-- START: Head-->
<?php include("inc/headTag.php"); ?>
<!-- END Head-->

<!-- START: Body-->

<!-- START: Body-->

<body id="main-container" class="default compact-menu">
  <!-- START: Header-->
  <?php include("inc/header.php"); ?>
  <!-- END: Header-->

  <!-- START: Main Menu-->
  <?php include("inc/sidebar.php"); ?>
  <!-- END: Main Menu-->

  <!-- START: Main Content-->
  <main>
    <div class="container-fluid site-width">
      <!-- START: Breadcrumbs-->
      <div class="row ">
        <div class="col-12  align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto"><span class="h4 my-auto">User Profile</span></div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item">Home</li>
              <li class="breadcrumb-item">User</li>
              <li class="breadcrumb-item active"><a href="#">Profile</a></li>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->
      <div class="row">
        <div class="col-12 mt-3">
          <div class="position-relative">
            <div class="background-image-maker py-5" style="background-image: url(&quot;dist/images/portfolio13.jpg&quot;);"></div>
            <div class="holder-image">
              <img src="images/roehampton-london.jpg" alt="" class="img-fluid d-none">
              <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5);"></div>
            </div>
            <div class="position-relative px-3 py-5">
              <div class="media d-md-flex d-block">
                <a href="#"><img src="images/no-profile.png" width="100" alt="" class="img-fluid rounded-circle"></a>
                <div class="media-body z-index-1">
                  <div class="pl-4">
                    <h1 class="display-4 text-uppercase text-white mb-0">
                      <?php

                      $hostID = $_SESSION["hostID"];
                      $adminInfo = getHostInfo($conn, $hostID);
                      echo !empty($adminInfo['fname']) ? $adminInfo['fname'] . " " . $adminInfo['lname'] : "No Name";

                      ?></h1>
                    <h6 class="text-uppercase text-white mb-0">Poll Host</h6>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="profile-menu mt-4 theme-background border  z-index-1 p-2">
            <div class="d-sm-flex">
              <div class="align-self-center">
                <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                  <li class="nav-item ml-0">
                    <a class="nav-link py-2 px-4 px-lg-4 active" data-toggle="tab" href="#info">Personal Information </a>
                  </li>
                  <!-- <li class="nav-item ml-0">
                    <a class="nav-link py-2 px-4 px-lg-4" data-toggle="tab" href="#activities">Activities </a>
                  </li>
                  <li class="nav-item ml-0 mb-2 mb-sm-0">
                    <a class="nav-link py-2 px-4 px-lg-4 " data-toggle="tab" href="#message"> Message</a>
                  </li> -->
                </ul>
              </div>
              <div class="align-self-center ml-auto text-center text-sm-right">
                <!-- <a href="#">
                  <i class="icon-social-dropbox h5"></i>
                </a>
                <a href="#">
                  <i class="icon-social-facebook h5"></i>
                </a>
                <a href="#">
                  <i class="icon-social-github h5"></i>
                </a>
                <a href="#">
                  <i class="icon-social-google h5"></i>
                </a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-xl-3">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="card-title">Personal Information</h4>
            </div>
            <div class="card-body p-0">

              <ul class="list-unstyled mb-0">
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Username:</strong> <?= ucfirst($adminInfo['username']); ?></p>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Full Name:</strong> <?= $adminInfo['fname'] . " " . $adminInfo['lname']; ?></p>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Email:</strong> <?= $adminInfo['email']; ?></p>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Phone:</strong> <?= !empty($adminInfo['phone']) ? $adminInfo['phone'] : "<span class='text-danger'>Not available</span>"; ?></p>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Account Type:</strong> <?= !empty($adminInfo['accountType']) ? htmlspecialchars($adminInfo['accountType']) : "<span class='text-danger'>Not available</span>"; ?></p>
                </li>
                <li class="border-bottom p-2">
                  <?php
                  if ($adminInfo['onlineStatus'] == 1) {
                    echo "<p class='mb-0'><strong>Status:</strong> <span style='color: green;'>Online</span></p>";
                  } else {
                    echo "<p class='mb-0'><strong>Status:</strong> <span style='color: red;'>Offline</span></p>";
                  }
                  ?>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Last IP Address:</strong> <?= $adminInfo['lastDeviceIP']; ?></p>
                </li>
                <li class="border-bottom p-2">
                  <p class="mb-0"><strong>Last Access:</strong> <?= structureTimestamp($adminInfo['lastAccess']); ?></p>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-xl-9">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="card-title">Edit Personal Information</h4>
            </div>
            <div class="card-body">
              <div class="form-group mb-0">
                <form id="systemUserEditForm">
                  <input type="hidden" value="<?= $adminInfo['hostID']; ?>" name="hostID" />
                  <input type="hidden" value="<?= $adminInfo['email']; ?>" name="editEmail" />

                  <div class="form-row">
                    <div class="form-group col-sm-6">
                      <label for="editFname">First Name<span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="editFname" name="editFname" placeholder="Enter First Name" value="<?= htmlspecialchars($adminInfo['fname']); ?>" required>
                    </div>

                    <div class="form-group col-sm-6">
                      <label for="editLname">Last Name<span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="editLname" name="editLname" placeholder="Enter Last Name" value="<?= htmlspecialchars($adminInfo['lname']); ?>" required>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-sm-6">
                      <label for="editOname">Other Name</label>
                      <input type="text" class="form-control" id="editOname" name="editOname" placeholder="Enter Other Name" value="<?= htmlspecialchars($adminInfo['oname']); ?>">
                    </div>

                    <div class="form-group col-sm-6">
                      <label for="editPhone">Phone</label>
                      <input type="text" class="form-control" id="editPhone" name="editPhone" placeholder="Enter Phone Number" value="<?= htmlspecialchars($adminInfo['phone']); ?>">
                    </div>
                  </div>

                  <div class="form-row">

                    <div class="form-group col-sm-6">
                      <label for="editGender">Gender<span class="text-danger">*</span></label>
                      <select class="form-control" id="editGender" name="editGender" required>
                        <option value="" disabled>Select Gender</option>
                        <option value="Male" <?= $adminInfo['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?= $adminInfo['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                      </select>
                    </div>
                    <div class="form-group  col-sm-6">
                      <label for="editLinkedinLink">LinkedIn Link</label>
                      <input type="text" class="form-control" id="editLinkedinLink" name="editLinkedinLink" placeholder="https://linkedin.com/in/username" value="<?= !empty($adminInfo['linkedinLink']) ? htmlspecialchars($adminInfo['linkedinLink']) : ''; ?>">
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group col-sm-6">
                      <label for="editFacebookLink">Facebook Link</label>
                      <input type="text" class="form-control" id="editFacebookLink" name="editFacebookLink" placeholder="https://facebook.com/username" value="<?= !empty($adminInfo['facebookLink']) ? htmlspecialchars($adminInfo['facebookLink']) : ''; ?>">
                    </div>

                    <div class="form-group col-sm-6">
                      <label for="editTwitterLink">X (Twitter) Link</label>
                      <input type="text" class="form-control" id="editTwitterLink" name="editTwitterLink" placeholder="https://x.com/username" value="<?= !empty($adminInfo['twitterLink']) ? htmlspecialchars($adminInfo['twitterLink']) : ''; ?>">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-sm-6">
                      <label for="oldPassword">Old Password <span class="text-danger">(Leave blank if no change in password)</span></label>
                      <input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="Enter Old Password Password" />
                      <span class="text-danger" id="old-password-feedback"> </span>
                    </div>
                    <div class="form-group col-sm-6">
                      <label for="editPassword">New Password </label>
                      <input type="password" class="form-control" id="editPassword" name="editPassword" placeholder="Enter Password" />
                    </div>
                  </div>

                  <ul class="list-inline mb-0 pt-3 bg-light p-3 border border-top-0 d-flex justify-content-between align-items-center">
                    <span id="updateSystemUserMsg"></span>
                    <li class="list-inline-item ml-auto"><button type="submit" id="updateProfileBtn" class="btn btn-primary btn-xs text-uppercase">Update</button></li>
                  </ul>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
      <!-- END: Card DATA-->
    </div>
  </main>
  <!-- END: Content-->



  <!-- START: Footer-->
  <footer>
    <?php include("inc/footer.php"); ?>
  </footer>
  <!-- END: Footer-->


  <!-- START: Back to top-->
  <a href="#" class="scrollup text-center">
    <i class="icon-arrow-up"></i>
  </a>
  <!-- END: Back to top-->

  <!-- START: Template JS-->
  <script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
  <script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
  <script src="dist/vendors/moment/moment.js"></script>
  <script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
  <!-- START: Page Vendor JS-->
  <script src="dist/vendors/select2/js/select2.full.min.js"></script>
  <script src="dist/js/select2.script.js"></script>
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <!-- END: Page Script JS-->
  <!-- END: Template JS-->

  <!-- START: APP JS-->
  <script src="dist/js/app.js"></script>
  <!-- END: APP JS-->

  <!-- END: APP JS-->

</body>
<!-- END: Body-->


<script type="text/javascript">
  $(document).ready(function() {
    $("#editPassword").hide();
  });

  var typingTimer; // Timer identifier
  var doneTypingInterval = 1000; // Time in milliseconds (1 second)

  // Verify Old Password
  $("#oldPassword").on("input paste", function() {
    if ($("#oldPassword").val() === "") {
      $("#updateProfileBtn").show();
      $("#old-password-feedback").hide();
    } else {
      clearTimeout(typingTimer); // Clear the previous timer

      // Set a new timer to trigger the verification function after typing stops
      typingTimer = setTimeout(confirmOldPassword, doneTypingInterval);
    }
  });

  // Verify Old Password Entry
  function confirmOldPassword() {
    var oldPassword = $("#oldPassword").val();
    $.ajax({
      type: "POST",
      url: "controllers/get-system-users",
      async: true,
      data: {
        confirmOldPassword: 1,
        oldPassword: oldPassword,
      },
      success: function(response) {
        var status = response.status;
        var message = response.message;

        if (status === true) {
          $("#old-password-feedback").html(message).show();
          $("#updateProfileBtn").hide();
          $("#editPassword").hide();
        } else {
          $("#updateProfileBtn").show();
          $("#old-password-feedback").hide();
          $("#editPassword").show();
        }
      },
      error: function() {
        Swal.fire("Connection Failed", "Error in connectivity, please check your internet connection and try again.", "error");
      }
    });
  }
  // Verify Old Password Entry

  //Update profile information
  $("#systemUserEditForm").submit(function(e) {
    e.preventDefault();
    var systemUserEditForm = new FormData(this);
    systemUserEditForm.append("updateSystemUserRequest", true);

    swal({
        title: "Are you sure you want to update your user profile?",
        text: "Updating this will reflect the changes across the portal.",
        icon: 'question',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        confirmButtonText: 'Yes, Update!',
        cancelButtonText: 'Cancel!',
        closeOnConfirm: false
      },
      function() {
        $.ajax({
          type: 'POST',
          url: 'controllers/get-system-users',
          async: true,
          processData: false,
          contentType: false,
          data: systemUserEditForm,
          beforeSend: function() {
            $("#editUpdateSystemUserBtn").html("<span class='fa fa-spin fa-spinner'></span> Please wait...").show();
          },
          success: function(response) {
            var status = response.status;
            var message = response.message;
            var responseStatus = response.responseStatus;
            var header = response.header;

            if (status === true) {
              $("#updateSystemUserMsg").html(message).css("color", "green").show();
              swal(header, message, responseStatus);
              $("oldPassword").val("");
              $("editPassword").val("");
            } else {
              swal(header, message, responseStatus);
            }
          },
          error: function() {
            $("#updateSystemUserMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          },
          complete: function() {
            setTimeout(function() {
              $("#updateSystemUserMsg").fadeOut(300);
            }, 3000);
            $("#editUpdateSystemUserBtn").html("Update System User").show();
          }
        });
      });
  });
  //Update profile information
</script>

</html>