<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "candidates";
$_SESSION["adminPreviousPage"] = $page;
// $pageAccess =  "manage candidates";
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
              <h4 class="mb-0">Candidates</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="staff-config">Candidates</a></li>
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
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewCandidate" style="float:right"> + Add New Candidate</button>
            </div>
            <div class="card-body">
              <div class="tablesaw-bar tablesaw-mode-columntoggle">

                <div class="row">
                  <!-- Search Input -->
                  <div class="col-md-12 mb-3">
                    <label for="candidateSearchEntry">Search:</label>
                    <input type="text" id="candidateSearchEntry" class="form-control" placeholder="Search candidates...">
                  </div>

                  <!-- Per Page -->
                  <div class="col-md-3 mb-3">
                    <label for="candidatePageLimit">Per Page:</label>
                    <select class="perpage orderby filterSelect" id="candidatePageLimit">
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

                  <!-- Sort By Candidate Status -->
                  <div class="col-md-3 mb-3">
                    <label for="status">Candidate Status</label>
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
                      <option value="position_asc">Position (A-Z)</option>
                      <option value="position_desc">Position (Z-A)</option>
                    </select>
                  </div>
                </div>

              </div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Photo</th>
                      <th class="text-center">Candidate ID</th>
                      <th class="text-center">Name</th>
                      <th class="text-center">Email</th>
                      <th class="text-center">Gender</th>
                      <th class="text-center">Election Title</th>
                      <th class="text-center">Position</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Created At</th>
                      <th class="text-center">Options</th>
                    </tr>
                  </thead>
                  <tbody id="displayCandidates">
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


          <!-- Modal Section to Add New Candidate -->
          <div class="modal fade bd-example-modal-xl" id="addNewCandidate" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="myLargeModalLabel10">Add New Candidate</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form id="addNewCandidateForm" novalidate>
                  <div class="modal-body">
                    <div class="row mt-3">

                      <!--New Candidate Inputs-->
                      <div class="col-lg-8 col-md-12 mt-3">
                        <div class="card">
                          <div class="card-content">
                            <div class="card-body py-5">
                              <div class="row">

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateElection">Polls<span class="text-danger">*</span><a href="polls" class="text-primary" style="float:right">+ Add New</a>
                                      <select class="form-control modal-select" id="candidateElection" name="candidateElection" required="">
                                        <option value="" selected readonly>Select an election</option>
                                        <?php
                                        $polls = getPolls($conn, $_SESSION['hostID']);
                                        foreach ($polls as $poll) {
                                        ?>
                                          <option value="<?= $poll['pollID']; ?>"><?= $poll['title']; ?></option>
                                        <?php } ?>
                                      </select>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidatePosition">Vying Position<span class="text-danger">*</span><a href="offices" class="text-primary" style="float:right">+ Add New</a>
                                      <select class="form-control modal-select" id="candidatePosition" name="candidatePosition" required="">
                                        <option value="">Select a Position</option>

                                      </select>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateStatus">Status<span class="text-danger">*</span>
                                      <select class="form-control modal-select" type="text" name="candidateStatus" id="candidateStatus" required="">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                      </select>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateGender">Gender<span class="text-danger">*</span>
                                      <select class="form-control modal-select" type="text" name="candidateGender" id="candidateGender" required="">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                      </select>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateSname">Surname<span class="text-danger">*</span>
                                      <input class="form-control" type="text" name="candidateSname" id="candidateSname" placeholder="Enter Candidate Surname" required="" />
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateFname">First Name<span class="text-danger">*</span>
                                      <input class="form-control" type="text" name="candidateFname" id="candidateFname" placeholder="Enter Candidate First Name" required="" />
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateOname">Other Names
                                      <input class="form-control" type="text" name="candidateOname" id="candidateOname" placeholder="Enter Candidate Other Names" />
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateEmail">Email Address<span class="text-danger">*</span>
                                      <input class="form-control" type="email" name="candidateEmail" id="candidateEmail" placeholder="Enter Candidate Email Address" required="" />
                                      <span id="email-feedback" class="text-danger" style="font-size:12px"></span>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidatePhone">Phone<span class="text-danger">*</span>
                                      <input class="form-control" type="text" name="candidatePhone" id="candidatePhone" placeholder="Enter Candidate Phone Number" required="" />
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-md-6 col-sm-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateAddress">Candidate Resident Address<span class="text-danger">*</span>
                                      <textarea class="form-control" placeholder="Enter Candidate Resident Address" name="candidateAddress" id="candidateAddress" required=""></textarea>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-12">
                                  <div class="input-group">
                                    <label class="col-12" for="candidateManifesto">Candidate Manifesto
                                      <textarea class="form-control" rows="5" maxlength="250" placeholder="Enter Candidate Manifesto" name="candidateManifesto" id="candidateManifesto"></textarea>
                                    </label>
                                  </div>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!--New Candidate Inputs-->


                      <!--Candidate Image-->
                      <div class="col-lg-4 col-md-12 mt-3">
                        <div class="card">
                          <div class="card-content">
                            <div class="card-header d-flex justify-content-between align-items-center">
                              <h4 class="card-title">Candidate Image Preview</h4>
                            </div>
                            <div class="card-body py-5">
                              <center class="col-12" style="margin: 0px auto;">
                                <img src="images/no-preview.jpeg" style="width:280px;height:280px" id="newCandidateImagePreview" />
                                <div>&nbsp;</div>
                                <label for="candidateImages" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Candidate Image<input id="candidateImages" name="candidateImages" type="file" required />
                                </label>
                              </center>

                            </div>
                          </div>
                        </div>
                      </div>
                      <!--Candidate Image-->
                    </div>

                  </div>
                  <div class="modal-footer">
                    <center style="margin: 0px auto;">
                      <span id="addNewCandidateMsg"></span>
                    </center>
                    <button type="submit" class="btn btn-primary" id="saveNewCandidate">Save Candidate</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- Modal Section to Add New Candidate -->


          <!-- Modal Section for modify Candidate Starts -->
          <div class="modal fade bd-example-modal-xl" id="candidatesEditModal" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body" id="displayCandidatesInputs"></div>
              </div>
            </div>
          </div>
          <!-- Modal Section for modify Candidate Starts -->

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
    loadCandidates(candidatesCurrentPageNo); // Display available system users

    //To allow the select2 input work as expected
    $('.modal-select').select2({
      dropdownParent: $('#addNewCandidate'),
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

  //Helper Function to preview uploaded candidate Image
  document.getElementById('candidateImages').addEventListener('change', function(event) {
    var output = document.getElementById('newCandidateImagePreview');
    if (event.target.files.length > 0) {
      var reader = new FileReader();
      reader.onload = function() {
        output.src = reader.result;
        output.style.display = 'block';
      };
      reader.readAsDataURL(event.target.files[0]);
    } else {
      output.style.display = 'none';
      output.src = 'void(0);';
    }
  });


  // Verify Candidate's Registration Email for duplicates
  $("#candidateEmail").on("input paste", function() {
    clearTimeout(typingTimer); // Clear the previous timer

    // Set a new timer to trigger the verification function after typing stops
    typingTimer = setTimeout(function() {
      verifyCandidateEntryEmail();
    }, 1000);
  });

  function verifyCandidateEntryEmail() {
    var email = $("#candidateEmail").val();
    $.ajax({
      type: "POST",
      url: "controllers/get-candidates",
      async: true,
      data: {
        candidateEmailEntryVer: 1,
        candidateEmail: email,
      },
      success: function(response) {
        var status = response.status;
        var message = response.message;

        if (status === true) {
          $("#email-feedback").html(message).show();
          $("#saveNewCandidate").hide();
          isEmailValid = false;
        } else {
          $("#email-feedback").hide();
          $("#saveNewCandidate").show();
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

  /* Functions for candidate images Preview and Upload */


  // Fetch Elections Positions
  $("#candidateElection").on("change", function() {
    var pollID = $(this).val();
    if (pollID) {
      $.ajax({
        url: "controllers/get-selections",
        method: "POST",
        data: {
          pollID: pollID,
        },
        success: function(response) {
          var positionSelect = $("#candidatePosition");
          positionSelect.empty();
          positionSelect.append('<option value="" disabled selected>Select Candidate Position</option>');
          response.forEach(function(position) {
            positionSelect.append('<option value="' + position.positionID + '">' + position.name + '</option>');
          });
        },
        error: function() {
          swal("Error", "Failed to fetch Candidate Position. Please try again.", "error");
        }
      });
    } else {
      $("#candidatePosition").empty();
      $("#candidatePosition").append('<option value="" disabled selected>Select Candidate Position</option>');
    }
  });
  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


  //::|| >>>>>>>>>>>>>::: 02: CANDIDATES FUNCTIONS<<<<<<<<<<<<<<<<<<
  var candidatesCurrentPageNo =
    localStorage.getItem("candidatesCurrentPageNo") > 0 ?
    localStorage.getItem("candidatesCurrentPageNo") :
    1; // Load the current page from localStorage if it exists
  var candidatesLoaded = false;

  //::: Load candidates function
  function loadCandidates(page = 1, query = "") {
    var candidatePageLimit = $("#candidatePageLimit").val();
    var sortBy = $("#sortBy").val();
    var status = $("#status").val();

    // Pass filtered elections
    var selectedElections = [];
    $("#filterElections option:selected").each(function() {
      selectedElections.push($(this).val());
    });

    $.ajax({
      url: "controllers/get-candidates",
      method: "POST",
      async: true,
      data: {
        query: query,
        page: page,
        pageLimit: candidatePageLimit,
        sort_by: sortBy,
        status: status,
        elections: selectedElections
      },
      beforeSend: function() {
        if (!candidatesLoaded) {
          $("#displayCandidates").html("").show();
          showCandidateSkeletonLoader();
        }
      },
      success: function(response) {
        candidatesLoaded = true;
        if (response && response.candidates.length > 0) {
          var candidateHtml = generateCandidateHtml(response.candidates);
          var paginationHtml = generatePaginationHtml(response.pagination);
          var totalData = response.pagination.total_results;
          var startResult = response.pagination.start_result;
          var endResult = response.pagination.end_result;
          var dataListingInfo = `Showing ${startResult} - ${endResult} of ${totalData} results`;

          setTimeout(function() {
            $("#displayCandidates").html(candidateHtml).show();
            $("#paginationControls").html(paginationHtml);
            $("#dataListingInfo").html(dataListingInfo);
            $(".skeleton-loader").remove(); // Remove skeleton loader
          }, 1000);
        } else {
          $("#displayCandidates")
            .html(`
            <div class="empty-content">
              <tr>
                  <td colspan="10" class="text-center">
                    No candidates found!
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
          loadCandidates(candidatesCurrentPageNo); // Retry loading
        }, 5000);
      },
    });
  }

  // Generate HTML for displaying candidates
  function generateCandidateHtml(candidates) {
    var html = "";
    candidates.forEach(function(candidate) {
      var unitPrice = parseFloat(candidate.unitPrice);
      html += `
      <tr class="candidate-entry">
        <td class="text-center"><img src="${candidate.imagePath ? htmlspecialchars(candidate.imagePath) : 'images/no-preview.png'}" alt="${candidate.imagePath ? htmlspecialchars(candidate.fname) : 'No Image'}" style="width: 50px; height: 50px;" onerror="this.onerror=null;this.src='images/no-preview.png';"></td>
        <td class="text-center">${candidate.candidateID ? htmlspecialchars(candidate.candidateID) : '<span class="text-danger">Not Available</span>'}</td>
        <td>${candidate.fname ? htmlspecialchars(candidate.sname + " " + candidate.fname + " " + candidate.oname ) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.email ? htmlspecialchars(toTitleCase(candidate.email)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.gender ? htmlspecialchars(toTitleCase(candidate.gender)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.electionTitle ? candidate.electionTitle : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.positionName ? htmlspecialchars(candidate.positionName) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.status ? (candidate.status=="active" ? htmlspecialchars(toTitleCase(candidate.status)) : '<span class="text-danger">Inactive</span>') : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${candidate.regDate ? formatDate(candidate.regDate) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">
          <button type="button" data-toggle="modal" data-target="#candidatesEditModal" onClick="loadCandidateEditModal(this);" class="edit-candidate btn btn-primary btn-sm mt-3" data-value="${htmlspecialchars(candidate.candidateID)}">
            Edit
          </button>
          <button type="button" class="delete-candidate btn btn-danger btn-sm mt-3" onClick="deleteCandidate(this);" data-value="${htmlspecialchars(candidate.candidateID)}">
            Delete
          </button>
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
      html += `
      <li class="page-item">
        <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="${currentPage - 1}">
          Previous
        </a>
      </li>`;
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
            <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="1">
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
            <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="${totalPages}">
            ${totalPages}
          </a>
        </li>`;
      }
    }

    // Next Button
    if (currentPage < totalPages) {
      html += `
      <li class="page-item">
        <a class="page-link candidate-page-link" href="javascript:void(0);" data-page_number="${currentPage + 1}">
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
  $(document).on("click", ".candidate-page-link", function() {
    var page = $(this).data("page_number");
    var query = $("#candidateSearchEntry").val();
    candidatesLoaded = false;

    $("html, body").animate({
      scrollTop: $("#sortBy").offset().top
    }, 500);
    loadCandidates(page, query);
    candidatesCurrentPageNo = page;

    localStorage.setItem("candidatesCurrentPageNo", candidatesCurrentPageNo); // Store page in localStorage
  });

  var typingTimer;
  var doneTypingInterval = 1000;
  // Candidate Search Listener
  $("#candidateSearchEntry").on("keyup change paste", function() {
    candidatesLoaded = false;
    var query = $("#candidateSearchEntry").val();
    clearTimeout(typingTimer);

    typingTimer = setTimeout(function() {
      loadCandidates(1, query); // Start search from page 1
    }, doneTypingInterval);
  });

  // Page Limit Listener
  $("#candidatePageLimit").on("change", function() {
    candidatesLoaded = false;
    loadCandidates(1); // Refresh candidates list from page 1
    localStorage.setItem("candidatesCurrentPageNo", 1); // Reset to page 1
  });

  // Candidate Status Listener
  $("#status").on("change", function() {
    candidatesLoaded = false;
    loadCandidates(1); // Refresh candidates list from page 1
    localStorage.setItem("candidatesCurrentPageNo", 1); // Reset to page 1
  });

  // Sort By Listener
  $("#sortBy").on("change", function() {
    candidatesLoaded = false;
    loadCandidates(1); // Refresh candidates list from page 1
    localStorage.setItem("candidatesCurrentPageNo", 1); // Reset to page 1
  });

  // Categories Filter Listener
  $("#filterElections").on("change", function() {
    candidatesLoaded = false;
    loadCandidates(1); // Reload candidate on filter change
  });

  // Skeleton Loader
  function showCandidateSkeletonLoader() {
    var skeletonHtml = `
    <tr class="candidate-entry">
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
    </tr> `;
    for (var i = 0; i < 10; i++) {
      $("#displayCandidates").append(skeletonHtml);
    }
  }

  //::: Function to add new candidate
  $("#addNewCandidateForm").submit(function(e) {
    e.preventDefault();
    var candidateForm = new FormData(this); // Simplified FormData initialization
    var formID = "addNewCandidateForm";
    // var imagePreview = $('#newCandidateImagePreview');

    // Show error message if any of the form input is invalid
    if (!validateInput(formID)) {
      swal(
        "Required Fields",
        "Please fill out all required fields before submitting.",
        "error"
      );
      return; // Stop further execution if validation fails
    }

    var imagePreview = document.getElementById('newCandidateImagePreview');
    var imageContainers = imagePreview.querySelectorAll('div');

    candidateForm.append("prepareNewCandidate", true);
    swal({
        title: "Are you sure to continue?",
        text: "You are about adding a new candidate to the administrative portal.",
        icon: 'question',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        confirmButtonText: 'Continue!',
        cancelButtonText: 'Cancel!',
        closeOnConfirm: true,
        //closeOnCancel: false
      },
      function() {
        $.ajax({
          url: "controllers/get-candidates",
          type: "POST",
          async: true,
          processData: false,
          contentType: false,
          data: candidateForm,
          beforeSend: function() {
            $("#saveNewCandidateBtn").prop("disabled", true);
            $("#saveNewCandidateBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
          },
          success: function(response) {
            $("#saveNewCandidateBtn").html("Save Candidate").prop("disabled", false); // Disable button after successful submission
            var status = response.status;
            var message = response.message;
            var header = response.header;
            swal(header, message, status); // Display response message
            if (status !== "error" && status !== "warning") {
              loadCandidates(1);
              $("#addNewCandidateForm")[0].reset(); // Reset form after submission

              // $("#candidateElection").val("").trigger("change"); //Reset Category Select2 dropdown
              // $("#candidatePosition").val("").trigger("change"); // Reset Sub Category Select2 dropdown
              $(imagePreview).remove(); //Remove Candidate image preview container
              // $("#newCandidateImagePreview").src = "images/no-preview.jpg;"; //Restore Image input to default

              $("#saveNewCandidateBtn").html("Save Candidate").prop("disabled", false); // Disable button after successful submission
            } else {
              swal("Request Blocked", "Failed to retrieve data from server", "error");
            }
          },
          error: function() {
            $("#saveNewCandidateBtn").html("Save Candidate").prop("disabled", false); // Re-enable button after request failure
            swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
          },
        });
      });
  });

  //::: Function to load candidate edit table
  function loadCandidateEditModal(button) {
    var candidateID = $(button).data("value");
    $.ajax({
      url: 'controllers/get-candidates',
      method: 'POST',
      async: true,
      data: {
        getCandidateEdit: true,
        candidateID: candidateID
      },
      beforeSend: function(candidatesResponse) {
        $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
        $('#displayCandidatesInputs').html("").show();
        modalSkeletonLoader();
      },
      success: function(candidatesResponse) {
        $("#candidatesEditModal").modal('show');
        $(button).html('Edit').show();
        setTimeout(function() {
          $('#displayCandidatesInputs').html(candidatesResponse).show();
          $('.modal-skeleton-loader').remove(); // Remove skeleton loader
        }, 1000);
      },
      error: function(candidatesResponse) {
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
      $('#displayCandidatesInputs').append(skeletonHtml);
    }
  }

  //::: Function to delete candidate
  function deleteCandidate(button) {
    var candidateID = $(button).data("value");
    swal({
        title: "Are you sure to continue?",
        text: "You are about to delete a candidate from the administrative portal. This action is irreversible and will permanently remove the candidate and its associated data from the system.",
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
          url: 'controllers/get-candidates',
          method: 'POST',
          async: true,
          data: {
            deleteCandidateRequest: true,
            candidateID: candidateID
          },
          beforeSend: function(candidatesResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(candidatesResponse) {
            if (candidatesResponse.status === true) {
              loadCandidates(candidatesCurrentPageNo);
              swal("Record Deleted", candidatesResponse.message, "success");
            } else {
              swal("Action Blocked", candidatesResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(candidatesResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }

  //::: Function to Force delete candidate even when record is available in other table
  function forceDeleteCandidate(button) {
    var candidateID = $(button).data("value");
    swal({
        title: "Warning: Irreversible Action!",
        text: "You are about to permanently delete a candidate from the administrative portal. This will remove all associated records tied to the candidate across multiple modules in the system. Once deleted, this action **CANNOT** be undone and **ALL** related data will be lost forever. Proceed with extreme caution.",
        icon: 'warning',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-primary',
        confirmButtonText: 'Yes, Delete Candidate!',
        cancelButtonText: 'No, Cancel!',
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function() {
        $.ajax({
          url: 'controllers/get-candidates',
          method: 'POST',
          async: true,
          data: {
            forceDeleteCandidateRequest: true,
            candidateID: candidateID
          },
          beforeSend: function(candidatesResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(candidatesResponse) {
            if (candidatesResponse.status === true) {
              loadCandidates(candidatesCurrentPageNo);
              swal("Record Deleted", candidatesResponse.message, "success");
            } else {
              swal("Action Blocked", candidatesResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(candidatesResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }
  //::|| >>>>>>>>>>>>>::: 02: CANDIDATES FUNCTIONS<<<<<<<<<<<<<<<<<<
</script>