<?php
session_start();
include("globalFunctions.php");
//::||Get Polls with pagination and other parameters(search_query, pageLimit, etc.) from the Polls Table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pageLimit'], $_POST['query']) && !empty($_POST['pageLimit'])) {


  date_default_timezone_set("Africa/Lagos");
  // Function to get polls with pagination and other parameters
  function getPollsWithParams($conn)
  {
    $search_query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $limit = isset($_POST['pageLimit']) ? (int)$_POST['pageLimit'] : 10;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'p.createdAt';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $start_date = isset($_POST['startDate']) ? $_POST['startDate'] : '';
    $end_date = isset($_POST['endDate']) ? $_POST['endDate'] : '';
    $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';

    // Pagination
    $offset = ($page - 1) * $limit;

    // Sort options
    $sort_columns = [
      'title_asc' => 'p.title ASC',
      'title_desc' => 'p.title DESC',
      'date_asc' => 'p.createdAt ASC',
      'date_desc' => 'p.createdAt DESC',
    ];

    $order_by = isset($sort_columns[$sort_by]) ? $sort_columns[$sort_by] : 'p.createdAt DESC';

    // Initial query
    $query = "SELECT SQL_CALC_FOUND_ROWS p.pollID, p.hostID, p.title, p.description, p.startDate, p.endDate, 
          CASE 
          WHEN p.status = 'cancelled' THEN 'cancelled'
          WHEN NOW() < p.startDate THEN 'upcoming'
          WHEN NOW() BETWEEN p.startDate AND p.endDate THEN 'active'
          ELSE 'completed'
          END AS pollStatus, 
          p.visibility, p.createdAt 
          FROM polls p 
          WHERE p.hostID = ?";
    $params = [$hostID];
    $types = 's';

    // Search query filter
    if (!empty($search_query)) {
      $query .= " AND (p.pollID LIKE ? OR p.title LIKE ? )";
      $search_query = '%' . $conn->real_escape_string($search_query) . '%';
      $params[] = $search_query;
      $params[] = $search_query;
      $types .= 'ss';
    }

    // Status filter
    if (!empty($status)) {
      $query .= " HAVING pollStatus = ?";
      $params[] = $status;
      $types .= 's';
    }

    // Date range filter
    if (!empty($start_date) && !empty($end_date)) {
      $query .= " AND p.createdAt BETWEEN ? AND ?";
      $params[] = $start_date;
      $params[] = $end_date;
      $types .= 'ss';
    }

    // Sorting
    $query .= " ORDER BY $order_by";

    // Pagination
    $query .= " LIMIT ?, ?";
    $params[] = (int)$offset;
    $params[] = (int)$limit;
    $types .= 'ii';

    // Prepare statement
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    $polls = [];
    while ($row = $result->fetch_assoc()) {
      $polls[] = $row;
    }

    // Get total results count
    $stmt_total = $conn->query("SELECT FOUND_ROWS() as total");
    $total_data = $stmt_total->fetch_assoc()['total'];
    // Pagination metadata
    $pagination = [
      'current_page' => $page,
      'total_pages' => ceil($total_data / $limit),
      'page_limit' => $limit,
      'total_results' => $total_data,
      'start_result' => $offset + 1,
      'end_result' => min($offset + $limit, $total_data),
    ];

    // Output response
    header("Content-Type: application/json");
    echo json_encode(['polls' => $polls, 'pagination' => $pagination]);
  }

  // Call the function
  getPollsWithParams($conn);

  exit();
}
//:::Get Polls with pagination and other parameters(search_query, pageLimit, etc.) from the Polls Table

//::: Add New Poll
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pollTitle'], $_POST['startDate'], $_POST['endDate'], $_POST['pollVisibility'], $_POST['pollDescription'])) {
  $pollID = strtoupper(generateRandomAlphaNumericStrings(10));
  $hostID = $_SESSION['hostID'];
  $title = mysqli_real_escape_string($conn, $_POST['pollTitle']);
  $startDate = mysqli_real_escape_string($conn, $_POST['startDate']);
  $endDate = mysqli_real_escape_string($conn, $_POST['endDate']);
  $visibility = mysqli_real_escape_string($conn, $_POST['pollVisibility']);
  $description = mysqli_real_escape_string($conn, $_POST['pollDescription']);
  $link = getPreferences($conn)['siteURL'] . "/start-poll?poll_id=" . $pollID . "&token=" . generateRandomAlphaNumericStrings(15) . "&poll=" . str_replace(" ", "_", $title);


  // Check if the poll already exists in the database
  $checkDuplicateQuery = "SELECT * FROM polls WHERE title = ? AND hostID = ?";
  $stmt = $conn->prepare($checkDuplicateQuery);
  $stmt->bind_param("ss", $title, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $checkDuplicate = $result->fetch_assoc();
  $stmt->close();

  if (!empty($checkDuplicate)) {
    $status = 'warning';
    $header = 'Duplicate Entry!';
    $message = 'Poll with this title already exists';
    $responseStatus = 'warning';
  } else {
    // Proceed with inserting the poll if no duplicates are found
    $insertQuery = "INSERT INTO polls (`pollID`, `hostID`, `title`, `description`, `startDate`, `endDate`, `visibility`, `link`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
      die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the insert query
    $stmt->bind_param("ssssssss", $pollID, $hostID, $title, $description, $startDate, $endDate, $visibility, $link);

    // Execute the insertion
    if ($stmt->execute()) {
      $status = 'success';
      $header = 'Successful!';
      $message = 'Poll has been added successfully';
      $responseStatus = 'success';
    } else {
      $status = 'error';
      $header = 'Failed!';
      $message = 'An error occurred, try again: ' . $stmt->error;
      $responseStatus = 'error';
    }
    // Close the statement
    $stmt->close();
  }

  $response = array(
    'status' => $status,
    'message' => $message,
    'responseStatus' => $responseStatus,
    'header' => $header
  );

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}

//::: Get Poll Information for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['getPollEdit'], $_POST['pollID']) && $_POST['getPollEdit'] == true) {
  $pollID = mysqli_real_escape_string($conn, $_POST['pollID']);
  $poll = getPollByID($conn, $pollID);

  if (!empty($poll)) {
?>
    <div class="modal-header">
      <h5 class="modal-title" id="pollModifyLabel">Modify <b><?= ucfirst($poll['title']); ?></b> Poll</h5>
    </div>

    <form id="editPollForm" class="needs-validation" novalidate>
      <div class="alert alert-primary" role="alert">
        This poll was added
        <a href="javascript:void(0);" class="alert-link">
          <?= structureTimestamp($poll['createdAt']); ?>
        </a>
        <?= !empty($poll['updatedAt']) && $poll['updatedAt'] != $poll['createdAt'] ? ' and was last modified ' . '<a href="javascript:void(0);" class="alert-link">' . structureTimestamp($poll['updatedAt']) . '.</a>' : ' and has not been modified yet.'; ?>
      </div>
      <div class="modal-body">
        <div class="row mt-3">
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-body py-5">
                  <!-- Poll Selection Tabs-->
                  <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="poll-info-tab" data-toggle="tab" href="#poll-info" role="tab" aria-controls="poll-info" aria-selected="true">
                        <i class="fa fa-info-circle"></i> Modify Poll Information
                      </a>
                    </li>
                    <!-- <li class="nav-item">
                      <a class="nav-link" id="participants-info-tab" data-toggle="tab" href="#participants-info" role="tab" aria-controls="poll-info" aria-selected="true">
                        <i class="fa fa-users-cog"></i> Poll Participants
                      </a>
                    </li> -->
                    <?php if ($poll['visibility'] == "private"): ?>
                      <li class="nav-item">
                        <a class="nav-link" id="voters-info-tab" data-toggle="tab" href="#voters-info" role="tab" aria-controls="voters-info" aria-selected="false">
                          <i class="fa fa-users"></i> Registered Voters
                        </a>
                      </li>
                    <?php endif; ?>
                  </ul>

                  <div class="tab-content" id="candidateTabContent">
                    <!-- Poll Inputs -->
                    <div class="tab-pane fade show active" id="poll-info" role="tabpanel" aria-labelledby="poll-info-tab">
                      <div class="row mt-3">
                        <!-- Poll Title -->
                        <div class="form-group col-sm-6">
                          <label for="editTitle">Poll Title<span class="text-danger">*</span>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Enter the title of the poll."></i>
                          </label>
                          <input type="text" class="form-control" id="editTitle" name="editTitle" value="<?= htmlspecialchars($poll['title'] ?? ''); ?>" required>
                          <input type="hidden" name="pollID" id="pollID" value="<?= $poll['pollID']; ?>" />
                        </div>

                        <!-- Poll Visibility-->
                        <div class="form-group col-sm-6">
                          <label for="editVisibility">Poll Visibility<span class="text-danger">*</span>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Election: A formal group decision-making process. Poll: A survey to gauge opinions."></i>
                          </label>
                          <select class="form-control" id="editVisibility" name="editVisibility" required>
                            <option value="" disabled>Select Poll Visibility</option>
                            <option value="public" <?= $poll['visibility'] == 'public' ? 'selected' : ''; ?>>Public</option>
                            <option value="private" <?= $poll['visibility'] == 'private' ? 'selected' : ''; ?>>Private</option>
                          </select>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group col-sm-6">
                          <label for="editStartDate">Start Date<span class="text-danger">*</span>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Select the start date and time for the poll."></i>
                          </label>
                          <input type="datetime-local" class="form-control" id="editStartDate" name="editStartDate" value="<?= date('Y-m-d\TH:i', strtotime($poll['startDate'])); ?>" required>
                        </div>

                        <!-- End Date -->
                        <div class="form-group col-sm-6">
                          <label for="editEndDate">End Date<span class="text-danger">*</span>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Select the end date and time for the poll."></i>
                          </label>
                          <input type="datetime-local" class="form-control" id="editEndDate" name="editEndDate" value="<?= date('Y-m-d\TH:i', strtotime($poll['endDate'])); ?>" required>
                        </div>

                        <!-- Poll Description -->
                        <div class="form-group col-sm-12">
                          <label for="editDescription">Poll Description<span class="text-danger">*</span>
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Provide a detailed description of the poll."></i>
                          </label>
                          <textarea class="form-control" id="editDescription" name="editDescription" placeholder="Enter Poll Description" required><?= htmlspecialchars($poll['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Generated Link -->
                        <div class="form-group col-sm-12">
                          <label for="pollGeneratedLink">Poll Link</label> - <em class="text-danger">You may share link across all platforms</em>
                          <div class="input-group">
                            <input type="text" class="form-control" id="pollGeneratedLink" name="pollGeneratedLink" value="<?= empty($poll['link']) ? htmlspecialchars(getPreferences($conn)['siteURL'] . '/start-poll?poll_id=' . $poll['pollID'] . '&&token=' . generateRandomAlphaNumericStrings(15) . '&&poll=' . str_replace(' ', '_', $poll['title'])) : htmlspecialchars($poll['link']); ?>" readonly>

                            <div class="input-group-append">
                              <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn">Copy Link</button>
                            </div>
                          </div>
                        </div>

                        <div class="p-2 m-3 alert alert-info justify-content-center"><b>NB: </b>
                          <?php if ($poll['visibility'] == "private"): ?>
                            This poll is presently set to <b><?= ucfirst($poll['visibility']); ?></b> which implies that only the emails registered for this poll can participate.

                            <div class="form-group col-sm-12 mt-2">
                              <label for="votersEmailCSV" class="file-upload btn btn-info btn-lg px-4 rounded-pill shadow">
                                <i class="fa fa-upload mr-2"></i>Upload Voters CSV File (<b>Emails Only</b>)
                                <input class="form-control" id="votersEmailCSV" name="votersEmailCSV" accept="" type="file">
                              </label>
                            </div>

                          <?php else: ?>
                            This Poll is presently set to <b><?= ucfirst($poll['visibility']); ?></b> which implies that anyone having the above link can participate in the Poll.
                          <?php endif; ?>
                        </div>

                        <!-- Div to display QR code and download button -->
                        <div class="text-center" style="margin: 0 auto">
                          <div><img style="height:80px;width:80px;position:relative;z-index:999" id="qrCodeContainer" /></div>
                          <button class="btn btn-dark btn-sm mt-3" type="button" id="downloadPollQRBtn" style="display:none;" onclick="downloadQRCode()">Download QR Code</button>
                        </div>
                      </div>
                    </div>

                    <!-- Participants Information -->
                    <!-- <div class="tab-pane fade" id="participants-info" role="tabpanel" aria-labelledby="participants-info-tab">
                      <div class="row mt-3">
                        <div class="card col-12 ">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Participants</h4>
                          </div>
                          <div class="card-body">
                            <div class="table-responsive">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Email</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>this@mdo.com</td>
                                  </tr>
                                  <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>this@fat.com</td>
                                  </tr>
                                  <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>this@twitter.com</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> -->

                    <!-- Voters Information -->
                    <?php if ($poll['visibility'] == "private"): ?>
                      <div class="tab-pane fade" id="voters-info" role="tabpanel" aria-labelledby="voters-info-tab">
                        <div class="row mt-3">
                          <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                              <h4 class="card-title">Registered Voters</h4>
                            </div>
                            <div class="card-body">
                              <div class="table-responsive">
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">First</th>
                                      <th scope="col">Last</th>
                                      <th scope="col">Email</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <th scope="row">1</th>
                                      <td>Mark</td>
                                      <td>Otto</td>
                                      <td>this@mdo.com</td>
                                    </tr>
                                    <tr>
                                      <th scope="row">2</th>
                                      <td>Jacob</td>
                                      <td>Thornton</td>
                                      <td>this@fat.com</td>
                                    </tr>
                                    <tr>
                                      <th scope="row">3</th>
                                      <td>Larry</td>
                                      <td>the Bird</td>
                                      <td>this@twitter.com</td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <span id="editPollMsg"></span>
        <button type="submit" class="btn btn-primary" id="saveEditPollBtn">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>

    <script src="dist/js/qrious.js"></script>
    <script>
      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<
      // helper function to make sure selection date does not select from behind
      // const now = new Date();
      // const formattedNow = now.toISOString().slice(0, 16);
      document.getElementById("editStartDate").min = formattedNow;
      document.getElementById("editEndDate").min = formattedNow;
      // helper function to make sure selection date does not select from behind

      //Helper To allow the select2 input work as expected
      $(document).ready(function() {
        $('.modal-select').select2({
          dropdownParent: $('#editPollForm')
        });

        //Generate QR Code on Load
        generateQRCode();
      });

      //Helper function to copy Link address
      document.getElementById('copyLinkBtn').addEventListener('click', function() {
        var copyText = document.getElementById('pollGeneratedLink');
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        swal("Link copied to clipboard:", copyText.value, "success");
      });

      //Helper function to generate a QR code
      function generateQRCode() {
        let qr = window.qr = new QRious({
          element: document.getElementById('qrCodeContainer'), //Where the code should be displayed
          size: 200,
          background: 'white', // Set the background color here
          foreground: 'rgb(2, 2, 43) ', // Set the foreground color if needed
          level: 'Q', // Optional: Set the error correction level (L, M, Q, H)
          value: document.getElementById('pollGeneratedLink').value, //url where QR code navigates to
        });

        const qrImageDataUrl = qr.toDataURL(); // Generate the image URL (base64)

        // Set the QR image data as the source for the image element
        document.getElementById('qrCodeContainer').src = qrImageDataUrl;

        document.getElementById('downloadPollQRBtn').style.display = 'inline'; // Show the download button
      }

      // Helper function to download the generated QR code as an image
      function downloadQRCode() {
        const qrImage = document.getElementById('qrCodeContainer');
        const pollTitle = document.getElementById('editTitle').value;
        if (!qrImage.src) {
          alert("QR code has not been generated yet!");
          return;
        }

        // Create a temporary link to trigger the download
        const link = document.createElement('a');
        link.href = qrImage.src; // Use the QR code image source as the download URL
        link.download = pollTitle.replace(/\s+/g, '_') + '_QR_code.png'; // Specify the file name for download

        // Simulate a click event on the link to trigger the download
        link.click();
      }

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
      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


      //::|| >>>>>>>>>>>>>::: 02: POLLS FUNCTIONS<<<<<<<<<<<<<<<<<<<
      //::: Function to edit poll
      $("#editPollForm").submit(function(e) {
        e.preventDefault();
        var pollForm = new FormData(this); // Simplified FormData initialization
        var formID = "editPollForm";
        // var link = pollForm.get("pollGeneratedLink");

        // Show error message if any of the form input is invalid
        if (!validateInput(formID)) {
          swal(
            "Required Fields",
            "Please fill out all required fields before submitting.",
            "error"
          );
          return; // Stop further execution if validation fails
        }

        pollForm.append("updatePollRequest", true);
        // pollForm.append("pollGeneratedLink", await (link));

        swal({
            title: "Are you sure to continue?",
            text: "You are about updating the poll information.",
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
                $("#saveEditPollBtn").prop("disabled", true);
                $("#saveEditPollBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
              },
              success: function(response) {
                $("#saveEditPollBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                var status = response.status;
                var message = response.message;
                var header = response.header;

                if (status !== "error" && status !== "warning") {
                  loadPolls(pollsCurrentPageNo); // Reload polls after successful submission
                  refreshPollEditModal(); //Refresh Poll Edit modal
                  $("#saveEditPollBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                }
                swal(header, message, status); // Display response message
              },
              error: function() {
                $("#saveEditPollBtn").html("Save Changes").prop("disabled", false); // Re-enable button after request failure
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          });
      });

      function refreshPollEditModal() {
        var pollID = "<?= $pollID; ?>";
        $.ajax({
          url: 'controllers/get-polls',
          method: 'POST',
          async: false,
          data: {
            getPollEdit: true,
            pollID: pollID
          },
          success: function(pollsResponse) {
            $("#pollsEditModal").modal('show');
            setTimeout(function() {
              $('#displayPollsInputs').html(pollsResponse).show();
            }, 1000);
          },
          error: function(pollsResponse) {
            $(button).html('Edit').show();
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          }
        });
      }

      //::|| >>>>>>>>>>>>>::: 02: POLLS FUNCTIONS<<<<<<<<<<<<<<<<<<
    </script>

<?php
  }

  exit();
}
//::: Get Poll Information for Edit

//::: Update Poll information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatePollRequest'], $_POST['pollID']) && $_POST['updatePollRequest'] == "true") {

  header("Content-Type: application/json");

  $hostID = $_SESSION['hostID'];
  $pollID =   !empty($_POST['pollID']) ? mysqli_real_escape_string($conn, $_POST['pollID']) : '';
  $title =  !empty($_POST['editTitle']) ? mysqli_real_escape_string($conn, $_POST['editTitle']) : '';
  $startDate = !empty($_POST['editStartDate']) ? date('Y-m-d H:i:s', strtotime($_POST['editStartDate'])) : null;
  $endDate = !empty($_POST['editEndDate']) ? date('Y-m-d H:i:s', strtotime($_POST['editEndDate'])) : null;
  $visibility = !empty($_POST['editVisibility']) ? mysqli_real_escape_string($conn, $_POST['editVisibility']) : '';
  $description = !empty($_POST['editDescription']) ? mysqli_real_escape_string($conn, $_POST['editDescription']) : '';
  $pollLink = !empty($_POST['pollGeneratedLink']) ? mysqli_real_escape_string($conn, $_POST['pollGeneratedLink']) : '';

  // Ensure required fields are not empty
  if (empty($title) || empty($startDate) || empty($endDate) || empty($visibility) || empty($description)) {
    echo json_encode([
      'header' => 'Missing Fields',
      'message' => 'Poll Title, Type, Status, Description, Start Date, and End Date are required.',
      'status' => 'error'
    ]);
    exit();
  }

  // Retrieve the current poll data
  $currentData = getPollByID($conn, $pollID);
  if (!$currentData) {
    echo json_encode([
      'header' => 'Not Found',
      'message' => 'Poll not found.',
      'status' => 'error'
    ]);
    exit();
  }

  // Compare each field to check if anything has changed
  $fieldsToUpdate = [];
  $params = [];

  // Array of field names and their corresponding variables
  $fieldMappings = [
    'title' => $title,
    'description' => $description,
    'startDate' => $startDate,
    'endDate' => $endDate,
    'visibility' => $visibility,
    'link' => $pollLink
  ];

  foreach ($fieldMappings as $column => $newValue) {
    if ($newValue !== $currentData[$column]) {
      $fieldsToUpdate[] = "$column = ?";
      $params[] = $newValue; // Add the new value to the params array
    }
  }

  $hasChanges = !empty($fieldsToUpdate);

  if (!$hasChanges) {
    echo json_encode([
      'header' => 'No Changes!',
      'message' => 'There are no updates to apply.',
      'status' => 'warning'
    ]);
    exit();
  }

  // Update the poll if there are changes
  if (!empty($fieldsToUpdate)) {
    $updateQuery = "UPDATE polls SET " . implode(", ", $fieldsToUpdate) . ", `updatedAt`=now() WHERE `pollID` = ? AND `hostID` =? ";
    $params[] = $pollID;
    $params[] = $_SESSION['hostID'];

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if (!$stmt->execute()) {
      echo json_encode([
        'header' => 'Update Failed',
        'message' => 'Error updating poll: ' . $stmt->error,
        'status' => 'error'
      ]);
      exit();
    }
  }

  echo json_encode([
    'status' => 'success',
    'header' => 'Update Successful!',
    'message' => 'Poll updated successfully.'
  ]);

  $stmt->close();
  exit();
}
//::: Update Poll information

//:: Delete Poll 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pollID'], $_POST["deletePollRequest"]) && $_POST["deletePollRequest"] == true) {
  // Check this tables where the deleteID exist to avoid system crash for other modules ::: 'TableName' => 'Column where deleteID is used'
  $referenceChecks = ['votes' => 'pollID', 'candidates' => 'pollID', 'poll_voters' => 'pollID'];
  $table = "polls";
  $uniqueColumn = "pollID";
  $deleteID = mysqli_real_escape_string($conn, $_POST['pollID']);
  $deleteResponse = deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Delete Poll

//:: Force Delete Poll :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pollID'], $_POST["forceDeletePollRequest"]) && $_POST["forceDeletePollRequest"] == true) {
  // Reference tables where the deleteID exists to avoid system crash in other modules
  $referenceChecks = ['votes' => 'pollID', 'candidates' => 'pollID', 'poll_voters' => 'pollID'];

  $table = "polls"; // Main table where the poll is stored
  $uniqueColumn = "sku"; // Column that uniquely identifies the poll
  $deleteID = mysqli_real_escape_string($conn, $_POST['pollID']); // Sanitizing the pollID input

  // Force delete from all tables
  $deleteResponse = forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  // Return JSON response
  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Force Delete Poll :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
?>