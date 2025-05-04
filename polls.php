<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "polls";
$_SESSION["adminPreviousPage"] = $page;
// $pageAccess =  "manage polls";
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
              <h4 class="mb-0">Polls</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="staff-config">Polls</a></li>
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
            <!-- <div class="card-header  justify-content-between align-items-center">
            </div> -->
            <div class=" card-header justify-content-between align-items-center">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewPoll" style="float:right"> + Add New Poll</button>
            </div>
            <div class="card-body">
              <div class="tablesaw-bar tablesaw-mode-columntoggle">

                <div class="row">
                  <!-- Search Input -->
                  <div class="col-md-6 mb-3">
                    <label for="pollSearchEntry">Search:</label>
                    <input type="text" id="pollSearchEntry" class="form-control" placeholder="Search polls...">
                  </div>

                  <!-- Date Range Filter -->
                  <div class="col-md-6 mb-3">
                    <label for="">Filter By Poll Date and Time Range:</label>
                    <button id="clearDateRange" class="btn btn-info p-1 ml-5 pull-right">Clear Date Filter</button>
                    <div class="d-flex align-items-end">
                      <input type="text" id="filterStartDate" class="form-control me-2" placeholder="Start Date and Time">
                      <input type="text" id="filterEndDate" class="form-control" placeholder="End Date and Time">
                    </div>
                  </div>

                  <!-- Per Page -->
                  <div class="col-md-4 mb-3">
                    <label for="pollPageLimit">Per Page:</label>
                    <select class="perpage orderby filterSelect" id="pollPageLimit">
                      <option value="10">10 Per Page</option>
                      <option value="50">50 Per Page</option>
                      <option value="100">100 Per Page</option>
                      <option value="500">500 Per Page</option>
                    </select>
                  </div>

                  <!-- Sort By Poll Status -->
                  <div class="col-md-4 mb-3">
                    <label for="status">Poll Status</label>
                    <select id="status" class="form-control">
                      <option value="">All</option>
                      <option value="active">Active</option>
                      <option value="upcoming">Upcoming</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>

                  <!-- Sort By Dropdown -->
                  <div class="col-md-4 mb-3">
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy" class="form-control">
                      <option value="date_desc" selected>Date (Newest First)</option>
                      <option value="date_asc">Date (Oldest First)</option>
                      <option value="title_asc">Title (A-Z)</option>
                      <option value="title_desc">Title (Z-A)</option>
                    </select>
                  </div>
                </div>


              </div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Poll ID</th>
                      <th class="text-center">Title</th>
                      <th class="text-center">Description</th>
                      <th class="text-center">Visibility</th>
                      <th class="text-center">Start Date</th>
                      <th class="text-center">End Date</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Created At</th>
                      <th class="text-center">Results</th>
                      <th class="text-center">Options</th>
                    </tr>
                  </thead>
                  <tbody id="displayPolls">
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


          <!-- Modal Section to Add New Poll -->
          <div class="modal fade bd-example-modal-lg" id="addNewPoll" tabindex="-1" role="dialog" aria-labelledby="addNewPollLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addNewPollLabel">Add Poll</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form id="addNewPollForm" class="needs-validation" novalidate>
                  <div class="modal-body">
                    <div class="row mt-3">

                      <!-- Poll Inputs -->
                      <div class="col-12 mt-3">
                        <div class="card">
                          <div class="card-content">
                            <div class="card-body py-5">
                              <!-- Poll Inputs -->
                              <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-info-tab">
                                <div class="row mt-3">
                                  <!-- Poll Title -->
                                  <div class="form-group col-sm-6">
                                    <label for="pollTitle">Poll Title<span class="text-danger">*</span>
                                      <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Enter the title of the poll."></i>
                                    </label>
                                    <input type="text" class="form-control" id="pollTitle" name="pollTitle" placeholder="Enter Poll Title" required>
                                  </div>

                                  <!-- Poll Type -->
                                  <div class="form-group col-sm-6">
                                    <label for="pollType">Poll Visibility<span class="text-danger">*</span>
                                      <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Private: Only registered voters can vote. Public: Anyone with the poll link can vote."></i>
                                    </label>
                                    <select class="form-control modal-select" id="pollVisibility" name="pollVisibility" required>
                                      <option value="" disabled selected>Select Poll Visibility</option>
                                      <option value="private">private</option>
                                      <option value="public">public</option>
                                    </select>
                                  </div>

                                  <!-- Start Date -->
                                  <div class="form-group col-sm-6">
                                    <label for="startDate">Start Date<span class="text-danger">*</span>
                                      <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Select the start date and time for the poll."></i>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="startDate" name="startDate" required>
                                  </div>

                                  <!-- End Date -->
                                  <div class="form-group col-sm-6">
                                    <label for="endDate">End Date<span class="text-danger">*</span>
                                      <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Select the end date and time for the poll."></i>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="endDate" name="endDate" required>
                                  </div>

                                  <!-- Poll Description -->
                                  <div class="form-group col-sm-12">
                                    <label for="pollDescription">Poll Description<span class="text-danger">*</span>
                                      <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Provide a detailed description of the poll."></i>
                                    </label>
                                    <textarea class="form-control" id="pollDescription" name="pollDescription" placeholder="Enter Poll Description" required></textarea>
                                  </div>

                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Poll Inputs -->
                    </div>

                  </div>
                  <div class="modal-footer">
                    <span id="addNewPollMsg"></span>
                    <button type="submit" class="btn btn-primary" id="saveNewPollBtn">Save Poll</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- Modal Section to Add New Poll -->


          <!-- Modal Section for modify Poll Starts -->
          <div class="modal fade bd-example-modal-lg" id="pollsEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">

                <div class="modal-body" id="displayPollsInputs"></div>
              </div>
            </div>
          </div>
          <!-- Modal Section for modify Poll Starts -->

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
    loadPolls(pollsCurrentPageNo); // Display available system users
    $('.modal-select').select2({
      dropdownParent: $('#editPollForm')
    });
  });


  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<
  // helper function to make sure selection date does not select from behind
  const now = new Date();
  const formattedNow = now.toISOString().slice(0, 16);
  document.getElementById("startDate").min = formattedNow;
  document.getElementById("endDate").min = formattedNow;
  // helper function to make sure selection date does not select from behind

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

  //Initiate the flat picker for the filter date range inputs
  flatpickr("#filterStartDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    allowInput: false,
    position: "auto",
    onReady: function(selectedDates, dateStr, instance) {
      instance.input.classList.add("form-control");
    }
  });

  flatpickr("#filterEndDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    allowInput: false,
    position: "auto",
    onReady: function(selectedDates, dateStr, instance) {
      instance.input.classList.add("form-control");
    }
  });

  // Clear buttons functionality
  $("#clearDateRange").on("click", function() {
    $("#filterStartDate, #filterEndDate").val(""); // Clears the Flatpickr input
    loadPolls(pollsCurrentPageNo);
  });

  //To allow the select2 input work as expected
  $('#addNewPoll').on('shown.bs.modal', function() {
    $('.modal-select').select2({
      dropdownParent: $('#addNewPoll'),
    });
  });

  // Price formatter for the desired locale and formatting options
  var formatter = new Intl.NumberFormat("en-US", {
    // style: "currency",
    // currency: "NGN",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
  //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


  //::|| >>>>>>>>>>>>>::: 02: POLLS FUNCTIONS<<<<<<<<<<<<<<<<<<
  var pollsCurrentPageNo =
    localStorage.getItem("pollsCurrentPageNo") > 0 ?
    localStorage.getItem("pollsCurrentPageNo") :
    1; // Load the current page from localStorage if it exists
  var pollsLoaded = false;

  //::: Load polls function
  function loadPolls(page = 1, query = "") {
    var pollPageLimit = $("#pollPageLimit").val();
    var sortBy = $("#sortBy").val();
    var status = $("#status").val();

    // Get date range values
    var startDate = $("#filterStartDate").val();
    var endDate = $("#filterEndDate").val();

    $.ajax({
      url: "controllers/get-polls",
      method: "POST",
      async: true,
      data: {
        query: query,
        page: page,
        pageLimit: pollPageLimit,
        sort_by: sortBy,
        status: status,
        startDate: startDate,
        endDate: endDate
      },
      beforeSend: function() {
        if (!pollsLoaded) {
          $("#displayPolls").html("").show();
          showPollSkeletonLoader();
        }
      },
      success: function(response) {
        pollsLoaded = true;
        if (response && response.polls.length > 0) {
          var pollHtml = generatePollHtml(response.polls);
          var paginationHtml = generatePaginationHtml(response.pagination);
          var totalData = response.pagination.total_results;
          var startResult = response.pagination.start_result;
          var endResult = response.pagination.end_result;
          var dataListingInfo = `Showing ${startResult} - ${endResult} of ${totalData} results`;

          setTimeout(function() {
            $("#displayPolls").html(pollHtml).show();
            $("#paginationControls").html(paginationHtml);
            $("#dataListingInfo").html(dataListingInfo);
            $(".skeleton-loader").remove(); // Remove skeleton loader
          }, 1000);
        } else {
          $("#displayPolls")
            .html(`
            <div class="empty-content">
              <tr>
                  <td colspan="10" class="text-center">
                    No polls found!
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
          loadPolls(pollsCurrentPageNo); // Retry loading
        }, 5000);
      },
    });
  }

  // Generate HTML for displaying polls
  function generatePollHtml(polls) {
    var html = "";
    polls.forEach(function(poll) {
      var unitPrice = parseFloat(poll.unitPrice);
      html += `
      <tr class="poll-entry">
        <td class="text-center">${poll.pollID ? htmlspecialchars(poll.pollID) : '<span class="text-danger">Not Available</span>'}</td>
        <td>${poll.title ? htmlspecialchars(poll.title) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${poll.description ? htmlspecialchars(poll.description) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${poll.visibility ? htmlspecialchars(toTitleCase(poll.visibility)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${poll.startDate ? htmlspecialchars(formatDate(poll.startDate)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">${poll.endDate ? htmlspecialchars(formatDate(poll.endDate)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">
          ${poll.pollStatus ? 
            `<span class="badge ${poll.pollStatus === 'active' ? ' badge-primary ' : (poll.pollStatus === 'upcoming' ? ' badge-warning ' : ' badge-secondary ')}">
              ${htmlspecialchars(capitalizeFirstLetter(poll.pollStatus))}
            </span>` 
            : '<span class="text-danger">Not Available</span>'}
        </td>
        <td class="text-center">${poll.createdAt ? htmlspecialchars(formatDate(poll.createdAt)) : '<span class="text-danger">Not Available</span>'}</td>
        <td class="text-center">
            <a href="live-charts?id=${htmlspecialchars(poll.pollID)}" target="_blank" class="poll-result btn btn-info btn-sm mt-3">
            <i class="fas fa-chart-bar"></i>
            Live Charts
            </a>
            <a href="results?id=${htmlspecialchars(poll.pollID)}" target="_blank" class="poll-result btn btn-warning btn-sm mt-3">
            <i class="fas fa-chart-bar"></i>
            Results
            </a>
        </td>
        <td class="text-center">
          <button type="button" data-toggle="modal" data-target="#pollsEditModal" onClick="loadPollEditModal(this);" class="edit-poll btn btn-primary btn-sm mt-3" data-value="${htmlspecialchars(poll.pollID)}">
            Edit
          </button>
          <button type="button" class="delete-poll btn btn-danger btn-sm mt-3" onClick="deletePoll(this);" data-value="${htmlspecialchars(poll.pollID)}">
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
        <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="${currentPage - 1}">
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
            <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="1">
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
            <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="${i}">
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
          <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="${totalPages}">
            ${totalPages}
          </a>
        </li>`;
      }
    }

    // Next Button
    if (currentPage < totalPages) {
      html += `
      <li class="page-item">
        <a class="page-link poll-page-link" href="javascript:void(0);" data-page_number="${currentPage + 1}">
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
  $(document).on("click", ".poll-page-link", function() {
    var page = $(this).data("page_number");
    var query = $("#pollSearchEntry").val();
    pollsLoaded = false;

    $("html, body").animate({
      scrollTop: $("#sortBy").offset().top
    }, 500);
    loadPolls(page, query);
    pollsCurrentPageNo = page;

    localStorage.setItem("pollsCurrentPageNo", pollsCurrentPageNo); // Store page in localStorage
  });

  var typingTimer;
  var doneTypingInterval = 1000;
  // Poll Search Listener
  $("#pollSearchEntry").on("keyup change paste", function() {
    pollsLoaded = false;
    var query = $("#pollSearchEntry").val();
    clearTimeout(typingTimer);

    typingTimer = setTimeout(function() {
      loadPolls(1, query); // Start search from page 1
    }, doneTypingInterval);
  });

  // Page Limit Listener
  $("#pollPageLimit").on("change", function() {
    pollsLoaded = false;
    loadPolls(1); // Refresh polls list from page 1
    localStorage.setItem("pollsCurrentPageNo", 1); // Reset to page 1
  });

  // Poll Status Listener
  $("#status").on("change", function() {
    pollsLoaded = false;
    loadPolls(1); // Refresh polls list from page 1
    localStorage.setItem("pollsCurrentPageNo", 1); // Reset to page 1
  });

  // Sort By Listener
  $("#sortBy").on("change", function() {
    pollsLoaded = false;
    loadPolls(1); // Refresh polls list from page 1
    localStorage.setItem("pollsCurrentPageNo", 1); // Reset to page 1
  });

  // Date Range Filter Listener
  $("#filterStartDate, #filterEndDate").on("change", function() {
    pollsLoaded = false;
    loadPolls(1); // Reload polls on date range change
  });

  // Skeleton Loader
  function showPollSkeletonLoader() {
    var skeletonHtml = `
    <tr class="poll-entry">
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
    </tr> `;
    for (var i = 0; i < 10; i++) {
      $("#displayPolls").append(skeletonHtml);
    }
  }

  //::: Function to add new poll
  $("#addNewPollForm").submit(function(e) {
    e.preventDefault();
    var pollForm = new FormData(this); // Simplified FormData initialization
    var formID = "addNewPollForm";

    // Show error message if any of the form input is invalid
    if (!validateInput(formID)) {
      swal(
        "Required Fields",
        "Please fill out all required fields before submitting.",
        "error"
      );
      return; // Stop further execution if validation fails
    }

    pollForm.append("prepareNewPoll", true);
    swal({
        title: "Are you sure to continue?",
        text: "You are about adding a new poll to the administrative portal.",
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
          url: "controllers/get-polls",
          type: "POST",
          async: true,
          processData: false,
          contentType: false,
          data: pollForm,
          beforeSend: function() {
            $("#saveNewPollBtn").prop("disabled", true);
            $("#saveNewPollBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
          },
          success: function(response) {
            $("#saveNewPollBtn").html("Save Poll").prop("disabled", false); // Disable button after successful submission
            var status = response.status;
            var message = response.message;
            var header = response.header;
            swal(header, message, status); // Display response message
            if (status !== "error" && status !== "warning") {
              loadPolls(1);
              $("#addNewPollForm")[0].reset(); // Reset form after submission
              $("#saveNewPollBtn").html("Save Poll").prop("disabled", false); // Disable button after successful submission
            }
          },
          error: function() {
            $("#saveNewPollBtn").html("Save Poll").prop("disabled", false); // Re-enable button after request failure
            swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
          },
        });
      });
  });

  //::: Function to load poll edit table
  function loadPollEditModal(button) {
    var pollID = $(button).data("value");
    $.ajax({
      url: 'controllers/get-polls',
      method: 'POST',
      async: true,
      data: {
        getPollEdit: true,
        pollID: pollID
      },
      beforeSend: function(pollsResponse) {
        $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
        $('#displayPollsInputs').html("").show();
        modalSkeletonLoader();
      },
      success: function(pollsResponse) {
        $("#pollsEditModal").modal('show');
        $(button).html('Edit').show();
        setTimeout(function() {
          $('#displayPollsInputs').html(pollsResponse).show();
          $('.modal-skeleton-loader').remove(); // Remove skeleton loader
        }, 1000);
      },
      error: function(pollsResponse) {
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
      $('#displayPollsInputs').append(skeletonHtml);
    }
  }

  //::: Function to delete poll
  function deletePoll(button) {
    var pollID = $(button).data("value");
    swal({
        title: "Are you sure to continue?",
        text: "You are about to delete a poll from the administrative portal. This action is irreversible and will permanently remove the poll and its associated data from the system.",
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
          url: 'controllers/get-polls',
          method: 'POST',
          async: true,
          data: {
            deletePollRequest: true,
            pollID: pollID
          },
          beforeSend: function(pollsResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(pollsResponse) {
            if (pollsResponse.status === true) {
              loadPolls(pollsCurrentPageNo);
              swal("Record Deleted", pollsResponse.message, "success");
            } else {
              swal("Action Blocked", pollsResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(pollsResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }

  //::: Function to Force delete poll even when record is available in other table
  function forceDeletePoll(button) {
    var pollID = $(button).data("value");
    swal({
        title: "Warning: Irreversible Action!",
        text: "You are about to permanently delete a poll from the administrative portal. This will remove all associated records tied to the poll across multiple modules in the system. Once deleted, this action **CANNOT** be undone and **ALL** related data will be lost forever. Proceed with extreme caution.",
        icon: 'warning',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-primary',
        confirmButtonText: 'Yes, Delete Poll!',
        cancelButtonText: 'No, Cancel!',
        closeOnConfirm: false,
        closeOnCancel: true
      },
      function() {
        $.ajax({
          url: 'controllers/get-polls',
          method: 'POST',
          async: true,
          data: {
            forceDeletePollRequest: true,
            pollID: pollID
          },
          beforeSend: function(pollsResponse) {
            $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
            $(button).prop("disabled", true);
          },
          success: function(pollsResponse) {
            if (pollsResponse.status === true) {
              loadPolls(pollsCurrentPageNo);
              swal("Record Deleted", pollsResponse.message, "success");
            } else {
              swal("Action Blocked", pollsResponse.message, "warning");
              $(button).html("Delete").show();
              $(button).prop("disabled", false);
            }
          },
          error: function(pollsResponse) {
            $(button).prop("disabled", false);
            $(button).html("Delete").show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      });
  }
  //::|| >>>>>>>>>>>>>::: 02: POLLS FUNCTIONS<<<<<<<<<<<<<<<<<<
</script>