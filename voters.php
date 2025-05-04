<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "voters";
$_SESSION["adminPreviousPage"] = $page;
// $pageAccess =  "manage voters";
// $failedAccessRedirect = "./dashboard";
?>

<!DOCTYPE html>
<html lang="en">

<!-- START: Head-->
<?php include("inc/headTag.php"); ?>
<!-- END Head-->
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
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0">Voters</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="staff-config">Voters</a></li>
              <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->
      <div class="row">
        <div class="col-12 mt-3">
          <div class="card">
            <!-- <div class="card-header  justify-content-between align-items-center"></div> -->
            <div class=" card-header justify-content-between align-items-center">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewVoter" style="float:right"> + Add New Voter</button>
            </div>
            <div class="card-body">
              <div class="tablesaw-bar tablesaw-mode-columntoggle">

                <div class="row">
                  <!-- Search Input -->
                  <div class="col-md-12 mb-3">
                    <label for="voterSearchEntry">Search:</label>
                    <input type="text" id="voterSearchEntry" class="form-control" placeholder="Search voters...">
                  </div>

                  <!-- Per Page -->
                  <div class="col-md-3 mb-3">
                    <label for="voterPageLimit">Per Page:</label>
                    <select class="perpage orderby filterSelect" id="voterPageLimit">
                      <option value="10">10 Per Page</option>
                      <option value="50">50 Per Page</option>
                      <option value="100">100 Per Page</option>
                      <option value="500">500 Per Page</option>
                    </select>
                  </div>

                  <!-- Election Multiple Select -->
                  <div class="col-md-3 mb-3">
                    <label for="filterElections">Filter By Elections:</label>
                    <select id="filterElections" class="form-control" placeholder="Please select Elections to filter" multiple>
                      <?php
                      $polls = getPolls($conn, $_SESSION['hostID']);
                      foreach ($polls as $poll) {
                      ?>
                        <option value="<?= $poll['pollID']; ?>"><?= $poll['title']; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <!-- Sort By Voter Status -->
                  <div class="col-md-3 mb-3">
                    <label for="status">Voter Status</label>
                    <select id="status" class="form-control">
                      <option value="">All</option>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>

                  <!-- Sort By Dropdown -->
                  <div class="col-md-3 mb-3">
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy" class="form-control">
                      <option value="date_desc" selected>Date (Newest First)</option>
                      <option value="date_asc">Date (Oldest First)</option>
                      <option value="title_asc">Election Title (A-Z)</option>
                      <option value="title_desc">Election Title (Z-A)</option>
                      <option value="name_asc">Name (A-Z)</option>
                      <option value="name_desc">Name (Z-A)</option>
                      <option value="email_asc">Email (A-Z)</option>
                      <option value="email_desc">Email (Z-A)</option>
                    </select>
                  </div>
                </div>

              </div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Photo</th>
                      <th class="text-center">Email</th>
                      <th class="text-center">Voter ID</th>
                      <th class="text-center">Surname</th>
                      <th class="text-center">First Name</th>
                      <th class="text-center">Other Names</th>
                      <th class="text-center">Phone</th>
                      <th class="text-center">Gender</th>
                      <th class="text-center">Registration Date</th>
                      <th class="text-center">Vote Date</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Options</th>
                    </tr>
                  </thead>
                  <tbody id="displayVoters">
                    <!-- Dynamic Content -->
                  </tbody>
                </table>
              </div>


              <!--::: Display Data Listing Info  -->
              <div id="dataListingInfo"></div>

              <!--  ::: Users Pagination Controls -->
              <nav class="">
                <ul class='page-numbers' id="paginationControls">
                </ul>
              </nav>
              <!--  ::: Users Pagination Controls -->
            </div>
          </div>


          <!-- Modal Section to Add New Voter -->
          <div class="modal fade bd-example-modal-xl" id="addNewVoter" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="myLargeModalLabel10">Add New Voter</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="add-voter-tab" data-toggle="tab" href="#add-voter" role="tab" aria-controls="add-voter" aria-selected="true">
                        <i class="fa fa-plus"></i> Add Voter
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="upload-voter-tab" data-toggle="tab" href="#upload-voter" role="tab" aria-controls="upload-voter" aria-selected="true">
                        <i class="fa fa-upload"></i> Upload Voters CSV FIle
                      </a>
                    </li>
                  </ul>

                  <div class="tab-content" id="votersTabContent">

                    <!-- ::01 Add new Voter Tab -->
                    <div class="tab-pane fade show active" id="add-voter" role="tabpanel" aria-labelledby="add-voter-tab">
                      <form id="addNewVoterForm" novalidate>
                        <div class="row mt-3">
                          <div class="alert alert-primary text-center" style="margin: 0px auto;"><em>NB:</em> You can only add voters to private polls. Kindly send the public poll links/QR out for engagement.</div>
                          <!--New Voter Inputs-->
                          <div class="col-lg-8 col-md-12 mt-3">
                            <div class="card">
                              <div class="card-content">
                                <div class="card-body py-5">
                                  <div class="row">

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterPoll">Private Polls<span class="text-danger">*</span>
                                          <select class="form-control modal-select" id="voterPoll" name="voterPoll" required="">
                                            <option value="" selected readonly>Select a poll</option>
                                            <?php
                                            $polls = getPolls($conn, $_SESSION['hostID']);
                                            foreach ($polls as $poll) {
                                              if ($poll['visibility'] == "private"):
                                            ?>
                                                <option value="<?= $poll['pollID']; ?>"><?= $poll['title']; ?></option>
                                            <?php
                                              endif;
                                            } ?>
                                          </select>
                                        </label>
                                      </div>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterEmail">Email Address<span class="text-danger">*</span>
                                          <input class="form-control" type="email" name="voterEmail" id="voterEmail" placeholder="Enter Voter Email Address" required="" />
                                          <span id="email-feedback" class="text-danger" style="font-size:12px"></span>
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterStatus">Status<span class="text-danger">*</span>
                                          <select class="form-control modal-select" type="text" name="voterStatus" id="voterStatus" required="">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                          </select>
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterSname">Surname<span class="text-danger">*</span>
                                          <input class="form-control" type="text" name="voterSname" id="voterSname" placeholder="Enter Voter Surname" required="" />
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterFname">First Name<span class="text-danger">*</span>
                                          <input class="form-control" type="text" name="voterFname" id="voterFname" placeholder="Enter Voter First Name" required="" />
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterOname">Other Names
                                          <input class="form-control" type="text" name="voterOname" id="voterOname" placeholder="Enter Voter Other Names" />
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterPhone">Phone
                                          <input class="form-control" type="text" name="voterPhone" id="voterPhone" placeholder="Enter Voter Phone Number" />
                                        </label>
                                      </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                      <div class="input-group">
                                        <label class="col-12" for="voterGender">Gender<span class="text-danger">*</span>
                                          <select class="form-control modal-select" type="text" name="voterGender" id="voterGender" required="">
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                          </select>
                                        </label>
                                      </div>
                                    </div>

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!--New Voter Inputs-->


                          <!--Voter Image-->
                          <div class="col-lg-4 col-md-12 mt-3">
                            <div class="card">
                              <div class="card-content">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                  <h4 class="card-title">Voter Image Preview</h4>
                                </div>
                                <div class="card-body py-5">
                                  <center class="col-12" style="margin: 0px auto;">
                                    <img src="images/no-preview.jpeg" style="width:15rem;height:15rem" id="newVoterImagePreview" />
                                    <div>&nbsp;</div>
                                    <label for="voterImages" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Voter Image<input id="voterImages" name="voterImages" type="file" />
                                    </label>
                                  </center>

                                </div>
                              </div>
                            </div>
                          </div>
                          <!--Voter Image-->
                        </div>
                        <div class="modal-footer mt-2">
                          <center style="margin: 0px auto;">
                            <span id="addNewVoterMsg"></span>
                          </center>
                          <button type="submit" class="btn btn-primary" id="saveNewVoter">Save Voter</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </form>
                    </div>

                    <!-- ::02  Upload Voters Tab -->
                    <div class="tab-pane fade" id="upload-voter" role="tabpanel" aria-labelledby="upload-voter-tab">
                      <form id="uploadVotersForm" novalidate>
                        <div class="row mt-3 d-block mx-auto">
                          <div class="col-12">
                            <div class="card">
                              <div class="card-content">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                  <h4 class="card-title">Upload Voters CSV File</h4>
                                </div>
                                <div class="card-body">
                                  <div class="form-group col-md-6 col-sm-12 mx-auto">
                                    <div class="input-group">
                                      <label class="col-12" for="votersPollID">Select A Poll To Upload Voters<span class="text-danger">*</span><a href="polls" class="text-primary" style="float:right">+ Add New</a>
                                        <select class="form-control modal-select" id="votersPollID" name="votersPollID" required="">
                                          <option value="" selected readonly>Select an election</option>
                                          <?php
                                          $polls = getPolls($conn, $_SESSION['hostID']);
                                          foreach ($polls as $poll) {
                                            if ($poll['visibility'] == "private"):
                                          ?>
                                              <option value="<?= $poll['pollID']; ?>"><?= $poll['title']; ?></option>
                                          <?php
                                            endif;
                                          } ?>
                                        </select>
                                      </label>
                                      <i class="text-danger" style="font-size: x-small;">
                                        Kindly note that only <em>private</em> polls are shown in the select option. If you can't find a poll you created, ensure the visibility is set to private and try again.
                                      </i>

                                    </div>
                                  </div>
                                  <div class="alert-info p-4 m-5 text-center">
                                    <label for="">
                                      <h4><i class="fa fa-upload"></i></h4>
                                      Select A CSV File for upload. The CSV file should contain only the email addresses of the voters to be uploaded.
                                    </label>
                                    <div class="form-group col-sm-12 mt-2">
                                      <label for="votersEmailCSV" class="file-upload btn btn-info btn-xl px-4 rounded-pill shadow">
                                        <i class="fa fa-file mr-2"></i>Select Voters CSV File (<b>Emails Only</b>)
                                        <input class="form-control" id="votersEmailCSV" name="votersEmailCSV" accept=".csv" type="file" required>
                                      </label>
                                      <div>&nbsp;</div>
                                      <span id="file-selected" class="text-primary" style="display:none;font-size:1rem;"><i class="fa fa-check"></i> A CSV FIle Selected</span>
                                    </div>

                                    <script>
                                      document.getElementById('votersEmailCSV').addEventListener('change', function() {
                                        var fileSelected = document.getElementById('file-selected');
                                        if (this.files.length > 0) {
                                          fileSelected.style.display = 'inline';
                                        } else {
                                          fileSelected.style.display = 'none';
                                        }
                                      });
                                    </script>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer mt-2">
                          <button type="submit" class="btn btn-primary" id="uploadVoterFile"><i class="fa fa-upload mr-2"></i>Upload Voters</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </form>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal Section to Add New Voter -->


          <!-- Modal Section for modify Voter Starts -->
          <div class="modal fade bd-example-modal-xl" id="votersEditModal" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body" id="displayVotersInputs"></div>
              </div>
            </div>
          </div>
          <!-- Modal Section for modify Voter Starts -->

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
  <a href="javascript:void(0);" class="scrollup text-center">
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
  <script src="dist/js/flatpickr.js"></script>
  <script src="dist/js/select2.script.js"></script>
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <!-- END: Page Script JS-->
  <!-- END: Template JS-->

  <!-- START: APP JS-->
  <script src="dist/js/app.js"></script>
  <!-- END: APP JS-->

</body>
<!-- END: Body-->

</html>
<script>
  $(document).ready(function() {
    loadVoters(votersCurrentPageNo); // Display available system users

    //To allow the select2 input work as expected
    $('.modal-select').select2({
      dropdownParent: $('#addNewVoter'),
    });

  });

  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<
  // Helper function to escape HTML special characters
  function htmlspecialchars(str) {
    if (typeof str !== 'string') {
      return str;
    }
    return str.replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // Helper function to capitalize the letters (Capitalize)
  function capitalize(str) {
    return str.replace(/\w\S*/g, function(txt) {
      return txt.toUpperCase();
    });
  }

  // Helper function to capitalize the first letter of each word (Title Case)
  function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt) {
      return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
  }

  // Helper function to capitalize the first letter only
  function capitalizeFirstLetter(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
  }

  // Helper function to format date
  function formatDate(dateString) {
    var options = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: 'numeric',
      hour12: true // This will add AM/PM to the time
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
  }

  //Helper function to validate form
  function validateInput(formID) {
    var form = document.getElementById(formID);
    // console.log(form);
    if (!form) {
      console.error("Form element with ID " + formID + " not found");
      return false;
    }

    var inputs = form.querySelectorAll(
      "input[required], select[required], textarea[required]"
    );

    let isValid = true;
    inputs.forEach(function(input) {
      if (input.value.trim() === "") {
        isValid = false;
        input.classList.add("is-invalid"); // Highlight invalid inputs
      } else {
        input.classList.remove("is-invalid");
      }
    });

    return isValid;
  }


  // Price formatter for the desired locale and formatting options
  var formatter = new Intl.NumberFormat("en-US", {
    // style: "currency",
    // currency: "NGN",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  //Helper Function to preview uploaded voter Image
  document.getElementById('voterImages').addEventListener('change', function(event) {
    var output = document.getElementById('newVoterImagePreview');
    if (event.target.files.length > 0) {
      var reader = new FileReader();
      reader.onload = function() {
        output.src = reader.result;
        output.style.display = 'block';
      };
      reader.readAsDataURL(event.target.files[0]);
    } else {
      output.style.display = 'none';
      output.src = 'images/no-preview.jpeg';
    }
  });


  // Verify Voter's Registration Email for duplicates
  // $("#voterEmail").on("input paste", function() {
  //   clearTimeout(typingTimer); // Clear the previous timer

  //   // Set a new timer to trigger the verification function after typing stops
  //   typingTimer = setTimeout(function() {
  //     verifyVoterEntryEmail();
  //   }, 1000);
  // });

  function verifyVoterEntryEmail() {
    var email = $("#voterEmail").val();
    $.ajax({
      type: "POST",
      url: "controllers/get-voters",
      async: true,
      data: {
        voterEmailEntryVer: 1,
        voterEmail: email,
      },
      success: function(response) {
        var status = response.status;
        var message = response.message;

        if (status === true) {
          $("#email-feedback").html(message).show();
          $("#saveNewVoter").hide();
          isEmailValid = false;
        } else {
          $("#email-feedback").hide();
          $("#saveNewVoter").show();
          isEmailValid = true;
        }
      },
      error: function() {
        swal(
          "Connection Failed",
          "Error in connectivity, please check your internet connection and try again.",
          "error"
        );
      },
    });
  }

  /* Functions for voter images Preview and Upload */


  // Fetch Elections Positions
  $("#voterElection").on("change", function() {
    var pollID = $(this).val();
    if (pollID) {
      $.ajax({
        url: "controllers/get-selections",
        method: "POST",
        data: {
          pollID: pollID,
        },
        success: function(response) {
          var positionSelect = $("#voterPosition");
          positionSelect.empty();
          positionSelect.append('<option value="" disabled selected>Select Voter Position</option>');
          response.forEach(function(position) {
            positionSelect.append('<option value="' + position.positionID + '">' + position.name + '</option>');
          });
        },
        error: function() {
          swal("Error", "Failed to fetch Voter Position. Please try again.", "error");
        }
      });
    } else {
      $("#voterPosition").empty();
      $("#voterPosition").append('<option value="" disabled selected>Select Voter Position</option>');
    }
  });
  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


  //::|| >>>>>>>>>>>>>::: 02: VOTERS FUNCTIONS<<<<<<<<<<<<<<<<<<
  var votersCurrentPageNo =
    localStorage.getItem("votersCurrentPageNo") > 0 ?
    localStorage.getItem("votersCurrentPageNo") :
    1; // Load the current page from localStorage if it exists
  var votersLoaded = false;

  //::: Load voters function
  function loadVoters(page = 1, query = "") {
    var voterPageLimit = $("#voterPageLimit").val();
    var sortBy = $("#sortBy").val();
    var status = $("#status").val();

    // Pass filtered elections
    var selectedElections = [];
    $("#filterElections option:selected").each(function() {
      selectedElections.push($(this).val());
    });

    $.ajax({
      url: "controllers/get-voters",
      method: "POST",
      async: true,
      data: {
        query: query,
        page: page,
        pageLimit: voterPageLimit,
        sort_by: sortBy,
        status: status,
        elections: selectedElections
      },
      beforeSend: function() {
        if (!votersLoaded) {
          $("#displayVoters").html("").show();
          showVoterSkeletonLoader();
        }
      },
      success: function(response) {
        votersLoaded = true;
        if (response && response.voters.length > 0) {
          var voterHtml = generateVoterHtml(response.voters);
          var paginationHtml = generatePaginationHtml(response.pagination);
          var totalData = response.pagination.total_results;
          var startResult = response.pagination.start_result;
          var endResult = response.pagination.end_result;
          var dataListingInfo = `Showing ${startResult} - ${endResult} of ${totalData} results`;

          setTimeout(function() {
            $("#displayVoters").html(voterHtml).show();
            $("#paginationControls").html(paginationHtml);
            $("#dataListingInfo").html(dataListingInfo);
            $(".skeleton-loader").remove(); // Remove skeleton loader
          }, 1000);
        } else {
          $("#displayVoters")
            .html(`
            <div class="empty-content">
              <tr>
                  <td colspan="12" class="text-center">
                    No voters found!
                  </td>
                </tr>
              </div>`)
            .show();
          $("#paginationControls").html(""); // Clear pagination controls
          $("#dataListingInfo").html("No Records Found").show();
          $(".skeleton-loader").remove(); // Remove skeleton loader
        }
      },
      error: function() {
        setTimeout(function() {
          loadVoters(votersCurrentPageNo); // Retry loading
        }, 5000);
      },
    });
  }

  // Generate HTML for displaying voters
  function generateVoterHtml(voters) {
    var html = "";
    voters.forEach(function(voter) {
      html += `
      <tr class="voter-entry">
        <td class="text-center"><img src="${voter.imagePath ? htmlspecialchars(voter.imagePath) : 'images/no-preview.png'}" alt="${voter.imagePath ? htmlspecialchars(voter.fname) : 'No Image'}" style="width: 50px; height: 50px;" onerror="this.onerror=null;this.src='images/no-preview.jpeg';"></td>
        <td class="text-center">${voter.voterEmail ? htmlspecialchars(voter.voterEmail) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.voterID ? htmlspecialchars(voter.voterID) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.sname ? htmlspecialchars(voter.sname) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.fname ? htmlspecialchars(voter.fname) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.oname ? htmlspecialchars(voter.oname) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.phone ? htmlspecialchars(voter.phone) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.gender ? htmlspecialchars(toTitleCase(voter.gender)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.regDate ? formatDate(voter.regDate) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.voteDate ? formatDate(voter.voteDate) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${voter.status ? (voter.status == "active" ? htmlspecialchars(toTitleCase(voter.status)) : '<span class="text-danger">Inactive</span>') : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">
        ${voter.voterID ? `
          <button type="button" data-toggle="modal" data-target="#votersEditModal" onClick="loadVoterEditModal(this);" class="edit-voter btn btn-primary btn-sm mt-3" data-value="${htmlspecialchars(voter.voterID)}">
            Edit
          </button>
          <button type="button" class="delete-voter btn btn-danger btn-sm mt-3" onClick="deleteVoter(this);" data-value="${htmlspecialchars(voter.voterEmail)}">
            Delete
          </button>
          ` :
          `<button type="button" class="delete-voter btn btn-danger btn-sm mt-3" onClick="forceDeleteVoter(this);" data-value="${htmlspecialchars(voter.voterEmail)}">
            Delete
          </button>`
          }
        </td>
      </tr>`;
    });
    return html;
  }

  // Generate pagination HTML
  function generatePaginationHtml(pagination) {
    var html = '<ul class="pagination justify-content-center">';
    var currentPage = pagination.current_page;
    var totalPages = pagination.total_pages;
    var maxVisiblePages = 5; // Maximum number of visible page links

    // Previous Button
    if (currentPage > 1) {
      html += ` <
          li class = "page-item" >
          <
          a class = "page-link voter-page-link"
        href = "javascript:void(0);"
        data - page_number = "${currentPage - 1}" >
          Previous <
          /a> <
          /li>`;
    } else {
      html += `
      <li class="page-item disabled">
        <span class="page-link">Previous</span>
      </li>`;
    }

    // Page Numbers
    if (totalPages <= maxVisiblePages) {
      for (var i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
          html += `
          <li class="page-item active">
            <span class="page-link">${i}</span>
          </li>`;
        } else {
          html += `
          <li class="page-item">
            <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="${i}">
              ${i}
            </a>
          </li>`;
        }
      }
    } else {
      var startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
      var endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

      if (startPage > 1) {
        html += `
        <li class="page-item">
          <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="1">
            1
          </a>
        </li>`;
        if (startPage > 2) {
          html += `
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>`;
        }
      }

      for (var i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
          html += `
          <li class="page-item active">
            <span class="page-link">${i}</span>
          </li>`;
        } else {
          html += `
          <li class="page-item">
            <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="${i}">
              ${i}
            </a>
          </li>`;
        }
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          html += `
          <li class="page-item disabled">
            <span class="page-link">...</span>
          </li>`;
        }
        html += `
        <li class="page-item">
          <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="${totalPages}">
            ${totalPages}
          </a>
        </li>`;
      }
    }

    // Next Button
    if (currentPage < totalPages) {
      html += `
      <li class="page-item">
        <a class="page-link voter-page-link" href="javascript:void(0);" data-page_number="${currentPage + 1}">
          Next
        </a>
      </li>`;
    } else {
      html += `
      <li class="page-item disabled">
        <span class="page-link">Next</span>
      </li>`;
    }

    html += '</ul>';
    return html;
  }

  // Pagination Javascript Listener
  $(document).on("click", ".voter-page-link", function() {
    var page = $(this).data("page_number");
    var query = $("#voterSearchEntry").val();
    votersLoaded = false;

    $("html, body").animate({
      scrollTop: $("#sortBy").offset().top
    }, 500);
    loadVoters(page, query);
    votersCurrentPageNo = page;

    localStorage.setItem("votersCurrentPageNo", votersCurrentPageNo); // Store page in localStorage
  });

  var typingTimer;
  var doneTypingInterval = 1000;
  // Voter Search Listener
  $("#voterSearchEntry").on("keyup change paste", function() {
    votersLoaded = false;
    var query = $("#voterSearchEntry").val();
    clearTimeout(typingTimer);

    typingTimer = setTimeout(function() {
      loadVoters(1, query); // Start search from page 1
    }, doneTypingInterval);
  });

  // Page Limit Listener
  $("#voterPageLimit").on("change", function() {
    votersLoaded = false;
    loadVoters(1); // Refresh voters list from page 1
    localStorage.setItem("votersCurrentPageNo", 1); // Reset to page 1
  });

  // Voter Status Listener
  $("#status").on("change", function() {
    votersLoaded = false;
    loadVoters(1); // Refresh voters list from page 1
    localStorage.setItem("votersCurrentPageNo", 1); // Reset to page 1
  });

  // Sort By Listener
  $("#sortBy").on("change", function() {
    votersLoaded = false;
    loadVoters(1); // Refresh voters list from page 1
    localStorage.setItem("votersCurrentPageNo", 1); // Reset to page 1
  });

  // Categories Filter Listener
  $("#filterElections").on("change", function() {
    votersLoaded = false;
    loadVoters(1); // Reload voter on filter change
  });

  // Skeleton Loader
  function showVoterSkeletonLoader() {
    var skeletonHtml = `
    <tr class="voter-entry">
      <td class="text-center">
        <div class="skeleton-loader" style="height: 50px; width: 50px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 150px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 80px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 80px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 80px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 60px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 60px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 60px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 60px;"></div>
      </td>
    </tr> `;
    for (var i = 0; i < 10; i++) {
      $("#displayVoters").append(skeletonHtml);
    }
  }

  //::: Function to add new voter
  $("#addNewVoterForm").submit(function(e) {
    e.preventDefault();
    var voterForm = new FormData(this); // Simplified FormData initialization
    var formID = "addNewVoterForm";
    // var imagePreview = $('#newVoterImagePreview');

    // Show error message if any of the form input is invalid
    if (!validateInput(formID)) {
      swal(
        "Required Fields",
        "Please fill out all required fields before submitting.",
        "error"
      );
      return; // Stop further execution if validation fails
    }

    voterForm.append("prepareNewVoter", true);
    swal({
        title: "Are you sure to continue?",
        text: "You are about adding a new voter to the administrative portal.",
        icon: 'question',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        confirmButtonText: 'Continue!',
        cancelButtonText: 'Cancel!',
        closeOnConfirm: false,
        //closeOnCancel: false
      },
      function() {
        $.ajax({
          url: "controllers/get-voters",
          type: "POST",
          async: true,
          processData: false,
          contentType: false,
          data: voterForm,
          beforeSend: function() {
            $("#saveNewVoterBtn").prop("disabled", true);
            $("#saveNewVoterBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
          },
          success: function(response) {
            $("#saveNewVoterBtn").html("Save Voter").prop("disabled", false); // Disable button after successful submission
            var status = response.status;
            var message = response.message;
            var header = response.header;


            if (status == "success") {
              loadVoters(1);
              $("#addNewVoterForm")[0].reset(); // Reset form after submission
              swal(header, message, status); // Display response message
              // $("#voterElection").val("").trigger("change"); //Reset Category Select2 dropdown
              // $("#voterPosition").val("").trigger("change"); // Reset Sub Category Select2 dropdown
              document.getElementById('newVoterImagePreview').src = "images/no-preview.jpeg"; //Restore Image input to default

              $("#saveNewVoterBtn").html("Save Voter").prop("disabled", false); // Disable button after successful submission
            } else if (status == "warning") {
              swal(header, message, status); // Display response message
            } else {
              swal("Request Blocked", "Failed to retrieve data from server", "error");
            }
          },
          error: function() {
            $("#saveNewVoterBtn").html("Save Voter").prop("disabled", false); // Re-enable button after request failure
            swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
          },
        });
      });
  });

  //::: Function to upload voter csv file
  $("#uploadVotersForm").submit(function(e) {
    e.preventDefault();
    var voterForm = new FormData(this); // Simplified FormData initialization
    var formID = "uploadVotersForm";

    // Show error message if any of the form input is invalid
    if (!validateInput(formID)) {
      swal(
        "Required Fields",
        "Please fill out all required fields before submitting.",
        "error"
      );
      return; // Stop further execution if validation fails
    }

    voterForm.append("uploadVotersCSVRequest", true);
    swal({
        title: "Are you sure to continue?",
        text: "You are about to upload a CSV file containing voters.",
        icon: 'question',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        confirmButtonText: 'Continue!',
        cancelButtonText: 'Cancel!',
        closeOnConfirm: false,
        //closeOnCancel: false
      },
      function() {
        $.ajax({
          url: "controllers/get-voters",
          type: "POST",
          async: true,
          processData: false,
          contentType: false,
          data: voterForm,
          beforeSend: function() {
            $("#uploadVoterFile").prop("disabled", true);
            $("#uploadVoterFile").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
          },
          success: function(response) {
            $("#uploadVoterFile").html("<i class='fa fa-upload mr-2'></i>Upload Voters").prop("disabled", false); // Disable button after successful submission
            var status = response.status;
            var message = response.message;
            var header = response.header;

            if (status !== "error" && status !== "warning") {
              swal(header, message, status); // Display response message
              loadVoters(1);
              $("#uploadVotersForm")[0].reset(); // Reset form after submission
              document.getElementById('file-selected').style.display = 'none';
              document.getElementById('votersEmailCSV').value = "";
              $("#uploadVoterFile").html("<i class='fa fa-upload mr-2'></i>Upload Voters").prop("disabled", false); // Disable button after successful submission
            } else if (status == "warning") {

              swal(header, message, status); // Display response message
            } else {
              swal("Request Blocked", "Failed to retrieve data from server", "error");
            }
          },
          error: function() {
            $("#uploadVoterFile").html("<i class='fa fa-upload mr-2'></i>Upload Voters").prop("disabled", false); // Re-enable button after request failure
            swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
          },
        });
      });
  });

  //::: Function to load voter edit table
  function loadVoterEditModal(button) {
    var voterID = $(button).data("value");
    $.ajax({
      url: 'controllers/get-voters',
      method: 'POST',
      async: true,
      data: {
        getVoterEdit: true,
        voterID: voterID
      },
      beforeSend: function(votersResponse) {
        $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
        $('#displayVotersInputs').html("").show();
        modalSkeletonLoader();
      },
      success: function(votersResponse) {
        $("#votersEditModal").modal('show');
        $(button).html('Edit').show();
        setTimeout(function() {
          $('#displayVotersInputs').html(votersResponse).show();
          $('.modal-skeleton-loader').remove(); // Remove skeleton loader
        }, 1000);
      },
      error: function(votersResponse) {
        $(button).html('Edit').show();
        swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
      }
    });
  }

  // Skeleton Loader
  function modalSkeletonLoader() {
    var skeletonHtml = `
            <div  class='modal-skeleton-loader'> </div>
        `;

    for (var i = 0; i < 1; i++) {
      $('#displayVotersInputs').append(skeletonHtml);
    }
  }

  //::: Function to delete voter
  function deleteVoter(button) {
    var voterID = $(button).data("value");
    swal({
        title: "Are you sure to continue?",
        text: "You are about to delete a voter from the administrative portal. This action is irreversible and will permanently remove the voter and its associated data from the system.",
        icon: 'question',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-success',
        confirmButtonText: 'Delete!',
        cancelButtonText: 'Cancel!',
        closeOnConfirm: false,
        //closeOnCancel: false
      },
      function() {
        $.ajax({
          url: 'controllers/get-voters',
          method: 'POST',
          async: true,
          data: {
            deleteVoterRequest: true,
            voterEmail: voterID
          },
          beforeSend: function(votersResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(votersResponse) {
            if (votersResponse.status === true) {
              loadVoters(votersCurrentPageNo);
              swal("Record Deleted", votersResponse.message, "success");
            } else {
              swal("Action Blocked", votersResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(votersResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }

  //::: Function to Force delete voter even when record is available in other table
  function forceDeleteVoter(button) {
    var voterID = $(button).data("value");
    swal({
        title: "Warning: Irreversible Action!",
        text: "You are about to permanently delete a voter from the administrative portal. This will remove all associated records tied to the voter across multiple modules in the system. Once deleted, this action **CANNOT** be undone and **ALL** related data will be lost forever. Proceed with extreme caution.",
        icon: 'warning',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-primary',
        confirmButtonText: 'Yes, Delete Voter!',
        cancelButtonText: 'No, Cancel!',
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function() {
        $.ajax({
          url: 'controllers/get-voters',
          method: 'POST',
          async: true,
          data: {
            forceDeleteVoterRequest: true,
            voterEmail: voterID
          },
          beforeSend: function(votersResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(votersResponse) {
            if (votersResponse.status === true) {
              loadVoters(votersCurrentPageNo);
              swal("Record Deleted", votersResponse.message, "success");
            } else {
              swal("Action Blocked", votersResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(votersResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }
  //::|| >>>>>>>>>>>>>::: 02: VOTERS FUNCTIONS<<<<<<<<<<<<<<<<<<
</script>