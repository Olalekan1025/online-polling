<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "positions";
$_SESSION["adminPreviousPage"] = $page;
// $pageAccess =  "manage positions";
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
              <h4 class="mb-0">Positions</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="staff-config">Positions</a></li>
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
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewPosition" style="float:right"> + Add New Position</button>
            </div>
            <div class="card-body">
              <div class="tablesaw-bar tablesaw-mode-columntoggle">

                <div class="row">
                  <!-- Search Input -->
                  <div class="col-md-12 mb-3">
                    <label for="positionSearchEntry">Search:</label>
                    <input type="text" id="positionSearchEntry" class="form-control" placeholder="Search positions...">
                  </div>

                  <!-- Per Page -->
                  <div class="col-md-4 mb-3">
                    <label for="positionPageLimit">Per Page:</label>
                    <select class="perpage orderby filterSelect" id="positionPageLimit">
                      <option value="10">10 Per Page</option>
                      <option value="50">50 Per Page</option>
                      <option value="100">100 Per Page</option>
                      <option value="500">500 Per Page</option>
                    </select>
                  </div>

                  <!-- Sort By Position Status -->
                  <div class="col-md-4 mb-3">
                    <label for="status">Position Status</label>
                    <select id="status" class="form-control">
                      <option value="">All</option>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>

                  <!-- Sort By Dropdown -->
                  <div class="col-md-4 mb-3">
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy" class="form-control">
                      <option value="date_desc" selected>Date (Newest First)</option>
                      <option value="date_asc">Date (Oldest First)</option>
                      <option value="name_asc">Name (A-Z)</option>
                      <option value="name_desc">Name (Z-A)</option>
                      <option value="abbr_asc">Abbreviation (A-Z)</option>
                      <option value="abbr_desc">Abbreviation (Z-A)</option>
                      <option value="status_asc">Status (A-Z)</option>
                      <option value="status_desc">Status (Z-A)</option>
                    </select>
                  </div>
                </div>

              </div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Name</th>
                      <th class="text-center">Abbreviation</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Registration Date</th>
                      <th class="text-center">Options</th>
                    </tr>
                  </thead>
                  <tbody id="displayPositions">
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


          <!-- Modal Section to Add New Position -->
          <div class="modal fade bd-example-modal-lg" id="addNewPosition" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="myLargeModalLabel10">Add New Position</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form id="addNewPositionForm" novalidate>
                  <div class="modal-body">
                    <div class="row mt-3">

                      <!--New Position Inputs-->
                      <div class="col-12 mt-3">
                        <div class="card">
                          <div class="card-content">
                            <div class="card-body py-5">
                              <div class="row">

                                <div class="form-group col-sm-6">
                                  <div class="input-group">
                                    <label class="col-12" for="positionStatus">Status
                                      <select class="form-control modal-select" type="text" name="positionStatus" id="positionStatus" required="">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                      </select>
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-sm-6">
                                  <div class="input-group">
                                    <label class="col-12" for="positionName">Name
                                      <input class="form-control" type="text" name="positionName" id="positionName" placeholder="Enter Position Name" required="" />
                                    </label>
                                  </div>
                                </div>

                                <div class="form-group col-sm-6">
                                  <div class="input-group">
                                    <label class="col-12" for="positionAbbr">Abbreviation
                                      <input class="form-control" type="text" name="positionAbbr" id="positionAbbr" placeholder="Enter Position Abbreviation" required="" />
                                    </label>
                                  </div>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!--New Position Inputs-->
                    </div>

                  </div>
                  <div class="modal-footer">
                    <center style="margin: 0px auto;">
                      <span id="addNewPositionMsg"></span>
                    </center>
                    <button type="submit" class="btn btn-primary" id="saveNewPosition">Save Position</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- Modal Section to Add New Position -->


          <!-- Modal Section for modify Position Starts -->
          <div class="modal fade bd-example-modal-lg" id="positionsEditModal" tabindex="-1" role="dialog" aria-labelledby="addNewProductLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <div class="modal-body" id="displayPositionsInputs"></div>
              </div>
            </div>
          </div>
          <!-- Modal Section for modify Position Starts -->

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
    loadPositions(positionsCurrentPageNo); // Display available system users

    //To allow the select2 input work as expected
    $('.modal-select').select2({
      dropdownParent: $('#addNewPositionForm'),
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
  /* Functions for position images Preview and Upload */
  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


  //::|| >>>>>>>>>>>>>::: 02: POSITIONS FUNCTIONS<<<<<<<<<<<<<<<<<<
  var positionsCurrentPageNo =
    localStorage.getItem("positionsCurrentPageNo") > 0 ?
    localStorage.getItem("positionsCurrentPageNo") :
    1; // Load the current page from localStorage if it exists
  var positionsLoaded = false;
  //::: Load positions function
  function loadPositions(page = 1, query = "") {
    var positionPageLimit = $("#positionPageLimit").val();
    var sortBy = $("#sortBy").val();
    var status = $("#status").val();

    $.ajax({
      url: "controllers/get-positions",
      method: "POST",
      async: true,
      data: {
        query: query,
        page: page,
        pageLimit: positionPageLimit,
        sort_by: sortBy,
        status: status
      },
      beforeSend: function() {
        if (!positionsLoaded) {
          $("#displayPositions").html("").show();
          showPositionSkeletonLoader();
        }
      },
      success: function(response) {
        positionsLoaded = true;
        if (response && response.positions.length > 0) {
          var positionHtml = generatePositionHtml(response.positions);
          var paginationHtml = generatePaginationHtml(response.pagination);
          var totalData = response.pagination.total_results;
          var startResult = response.pagination.start_result;
          var endResult = response.pagination.end_result;
          var dataListingInfo = `Showing ${startResult} - ${endResult} of ${totalData} results`;

          setTimeout(function() {
            $("#displayPositions").html(positionHtml).show();
            $("#paginationControls").html(paginationHtml);
            $("#dataListingInfo").html(dataListingInfo);
            $(".skeleton-loader").remove(); // Remove skeleton loader
          }, 1000);
        } else {
          $("#displayPositions")
            .html(`
            <div class="empty-content">
              <tr>
                  <td colspan="5" class="text-center">
                    No positions found!
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
          loadPositions(positionsCurrentPageNo); // Retry loading
        }, 5000);
      },
    });
  }

  // Generate HTML for displaying positions
  function generatePositionHtml(positions) {
    var html = "";
    positions.forEach(function(position) {
      html += `
      <tr class="position-entry">
        <td class="text-center">${position.name ? htmlspecialchars(position.name) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${position.abbr ? htmlspecialchars(position.abbr) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${position.status ? (position.status=="active" ? htmlspecialchars(toTitleCase(position.status)) : '<span class="text-danger">Inactive</span>') : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${position.regDate ? formatDate(position.regDate) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">
          <button type="button" data-toggle="modal" data-target="#positionsEditModal" onClick="loadPositionEditModal(this);" class="edit-position btn btn-primary btn-sm mt-3" data-value="${htmlspecialchars(position.positionID)}">
            Edit
          </button>
          <button type="button" class="delete-position btn btn-danger btn-sm mt-3" onClick="deletePosition(this);" data-value="${htmlspecialchars(position.positionID)}">
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
        <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="${currentPage - 1}">
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
            <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="1">
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
            <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="${totalPages}">
            ${totalPages}
          </a>
        </li>`;
      }
    }

    // Next Button
    if (currentPage < totalPages) {
      html += `
      <li class="page-item">
        <a class="page-link position-page-link" href="javascript:void(0);" data-page_number="${currentPage + 1}">
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
  $(document).on("click", ".position-page-link", function() {
    var page = $(this).data("page_number");
    var query = $("#positionSearchEntry").val();
    positionsLoaded = false;

    $("html, body").animate({
      scrollTop: $("#sortBy").offset().top
    }, 500);
    loadPositions(page, query);
    positionsCurrentPageNo = page;

    localStorage.setItem("positionsCurrentPageNo", positionsCurrentPageNo); // Store page in localStorage
  });

  var typingTimer;
  var doneTypingInterval = 1000;
  // Position Search Listener
  $("#positionSearchEntry").on("keyup change paste", function() {
    positionsLoaded = false;
    var query = $("#positionSearchEntry").val();
    clearTimeout(typingTimer);

    typingTimer = setTimeout(function() {
      loadPositions(1, query); // Start search from page 1
    }, doneTypingInterval);
  });

  // Page Limit Listener
  $("#positionPageLimit").on("change", function() {
    positionsLoaded = false;
    loadPositions(1); // Refresh positions list from page 1
    localStorage.setItem("positionsCurrentPageNo", 1); // Reset to page 1
  });

  // Position Status Listener
  $("#status").on("change", function() {
    positionsLoaded = false;
    loadPositions(1); // Refresh positions list from page 1
    localStorage.setItem("positionsCurrentPageNo", 1); // Reset to page 1
  });

  // Sort By Listener
  $("#sortBy").on("change", function() {
    positionsLoaded = false;
    loadPositions(1); // Refresh positions list from page 1
    localStorage.setItem("positionsCurrentPageNo", 1); // Reset to page 1
  });

  // Skeleton Loader
  function showPositionSkeletonLoader() {
    var skeletonHtml = `
    <tr class="position-entry">
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
      <td class="text-center">
        <div class="skeleton-loader" style="height: 20px; width: 100px;"></div>
      </td>
    </tr> `;
    for (var i = 0; i < 10; i++) {
      $("#displayPositions").append(skeletonHtml);
    }
  }

  //::: Function to add new position
  $("#addNewPositionForm").submit(function(e) {
    e.preventDefault();
    var positionForm = new FormData(this); // Simplified FormData initialization
    var formID = "addNewPositionForm";
    // var imagePreview = $('#newPositionImagePreview');

    // Show error message if any of the form input is invalid
    if (!validateInput(formID)) {
      swal(
        "Required Fields",
        "Please fill out all required fields before submitting.",
        "error"
      );
      return; // Stop further execution if validation fails
    }

    positionForm.append("prepareNewPosition", true);
    swal({
        title: "Are you sure to continue?",
        text: "You are about adding a new position to the administrative portal.",
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
          url: "controllers/get-positions",
          type: "POST",
          async: true,
          processData: false,
          contentType: false,
          data: positionForm,
          beforeSend: function() {
            $("#saveNewPositionBtn").prop("disabled", true);
            $("#saveNewPositionBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
          },
          success: function(response) {
            $("#saveNewPositionBtn").html("Save Position").prop("disabled", false); // Disable button after successful submission
            var status = response.status;
            var message = response.message;
            var header = response.header;
            swal(header, message, status); // Display response message
            if (status !== "error" && status !== "warning") {
              loadPositions(1);
              $("#addNewPositionForm")[0].reset(); // Reset form after submission

              // $("#positionElection").val("").trigger("change"); //Reset Category Select2 dropdown
              // $("#positionPosition").val("").trigger("change"); // Reset Sub Category Select2 dropdown
              $("#newPositionImagePreview").src = "images/no-preview.jpg;"; //Restore Image input to default

              $("#saveNewPositionBtn").html("Save Position").prop("disabled", false); // Disable button after successful submission
            } else {
              swal("Request Blocked", "Failed to retrieve data from server", "error");
            }
          },
          error: function() {
            $("#saveNewPositionBtn").html("Save Position").prop("disabled", false); // Re-enable button after request failure
            swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
          },
        });
      });
  });

  //::: Function to load position edit table
  function loadPositionEditModal(button) {
    var positionID = $(button).data("value");
    $.ajax({
      url: 'controllers/get-positions',
      method: 'POST',
      async: true,
      data: {
        getPositionEdit: true,
        positionID: positionID
      },
      beforeSend: function(positionsResponse) {
        $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
        $('#displayPositionsInputs').html("").show();
        modalSkeletonLoader();
      },
      success: function(positionsResponse) {
        $("#positionsEditModal").modal('show');
        $(button).html('Edit').show();
        setTimeout(function() {
          $('#displayPositionsInputs').html(positionsResponse).show();
          $('.modal-skeleton-loader').remove(); // Remove skeleton loader
        }, 1000);
      },
      error: function(positionsResponse) {
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
      $('#displayPositionsInputs').append(skeletonHtml);
    }
  }

  //::: Function to delete position
  function deletePosition(button) {
    var positionID = $(button).data("value");
    swal({
        title: "Are you sure to continue?",
        text: "You are about to delete a position from the administrative portal. This action is irreversible and will permanently remove the position and its associated data from the system.",
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
          url: 'controllers/get-positions',
          method: 'POST',
          async: true,
          data: {
            deletePositionRequest: true,
            positionID: positionID
          },
          beforeSend: function(positionsResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(positionsResponse) {
            if (positionsResponse.status === true) {
              loadPositions(positionsCurrentPageNo);
              swal("Record Deleted", positionsResponse.message, "success");
            } else {
              swal("Action Blocked", positionsResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(positionsResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }

  //::: Function to Force delete position even when record is available in other table
  function forceDeletePosition(button) {
    var positionID = $(button).data("value");
    swal({
        title: "Warning: Irreversible Action!",
        text: "You are about to permanently delete a position from the administrative portal. This will remove all associated records tied to the position across multiple modules in the system. Once deleted, this action **CANNOT** be undone and **ALL** related data will be lost forever. Proceed with extreme caution.",
        icon: 'warning',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-primary',
        confirmButtonText: 'Yes, Delete Position!',
        cancelButtonText: 'No, Cancel!',
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function() {
        $.ajax({
          url: 'controllers/get-positions',
          method: 'POST',
          async: true,
          data: {
            forceDeletePositionRequest: true,
            positionID: positionID
          },
          beforeSend: function(positionsResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(positionsResponse) {
            if (positionsResponse.status === true) {
              loadPositions(positionsCurrentPageNo);
              swal("Record Deleted", positionsResponse.message, "success");
            } else {
              swal("Action Blocked", positionsResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(positionsResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }
  //::|| >>>>>>>>>>>>>::: 02: POSITIONS FUNCTIONS<<<<<<<<<<<<<<<<<<
</script>