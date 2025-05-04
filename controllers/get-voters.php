<?php
session_start();
include("globalFunctions.php");
//::||Get Voter with pagination and other parameters(search_query, pageLimit, etc.) from the voters Table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pageLimit'], $_POST['query']) && !empty($_POST['pageLimit'])) {
  // Function to get voters with pagination and other parameters
  function getVotersWithParams($conn)
  {
    $search_query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $limit = isset($_POST['pageLimit']) ? (int)$_POST['pageLimit'] : 10;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'v.regDate';
    $elections = isset($_POST['elections']) ? $_POST['elections'] : [];
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';

    // Pagination
    $offset = ($page - 1) * $limit;

    // Sort options
    $sort_columns = [
      'name_asc' => 'v.sname ASC, v.fname ASC, v.oname ASC',
      'name_desc' => 'v.sname DESC, v.fname DESC, v.oname DESC',
      'email_asc' => 'v.email ASC',
      'email_desc' => 'v.email DESC',
      'date_asc' => 'v.regDate ASC',
      'date_desc' => 'v.regDate DESC',
      'title_asc' => 'p.title ASC',
      'title_desc' => 'p.title DESC',
    ];

    $order_by = isset($sort_columns[$sort_by]) ? $sort_columns[$sort_by] : 'v.regDate DESC';

    // Initial query
    $query = "SELECT SQL_CALC_FOUND_ROWS pv.voterEmail,v.voterID, v.sname, v.fname, v.oname, v.email, v.phone,
              v.imagePath, v.gender, v.regDate, v.voteDate, v.status, p.title AS electionTitle
              FROM poll_voters pv
              LEFT JOIN voters v ON v.email = pv.voterEmail AND v.hostID = pv.hostID
              LEFT JOIN polls p ON p.pollID = pv.pollID  AND v.hostID = pv.hostID
              WHERE pv.hostID=? AND (pv.registrationType = 'added' || pv.registrationType = 'uploaded') GROUP BY pv.voterEmail";
    $params = [$hostID];
    $types = 's';

    // Search query filter
    if (!empty($search_query)) {
      $query .= " AND (v.sname LIKE ? OR v.fname LIKE ? OR v.oname LIKE ? OR v.email LIKE ?)";
      $search_query = '%' . $conn->real_escape_string($search_query) . '%';
      $params = array_merge($params, [$search_query, $search_query, $search_query, $search_query]);
      $types .= 'ssss';
    }

    // Election filter
    if (!empty($elections)) {
      $placeholders = implode(',', array_fill(0, count($elections), '?'));
      $query .= " AND vs.pollID IN ($placeholders)";
      foreach ($elections as $election) {
        $params[] = $election;
        $types .= 's';
      }
    }

    // Status filter
    if ($status === 'active') {
      $query .= " AND v.status = 'active'";
    } elseif ($status === 'inactive') {
      $query .= " AND v.status = 'inactive'";
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
    $voters = [];
    while ($row = $result->fetch_assoc()) {
      $voters[] = $row;
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
    echo json_encode(['voters' => $voters, 'pagination' => $pagination]);
  }

  // Call the function
  getVotersWithParams($conn);

  exit();
}
//:::Get Voter with pagination and other parameters(search_query, pageLimit, etc.) from the voters Table

//::: Add New Voter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prepareNewVoter'], $_POST['voterEmail'])) {

  header('Content-Type: application/json');
  $hostID = $_SESSION['hostID'];
  $sname = mysqli_real_escape_string($conn, $_POST['voterSname']);
  $fname = mysqli_real_escape_string($conn, $_POST['voterFname']);
  $oname = mysqli_real_escape_string($conn, $_POST['voterOname'] ?? '');
  $gender = mysqli_real_escape_string($conn, $_POST['voterGender']);
  $email = mysqli_real_escape_string($conn, $_POST['voterEmail']);
  $phone = mysqli_real_escape_string($conn, $_POST['voterPhone']);
  $status = mysqli_real_escape_string($conn, $_POST['voterStatus']);
  $voterPoll = mysqli_real_escape_string($conn, $_POST['voterPoll']);
  $voterID = "VT" . generateNumericStrings(6) . strtoupper(generateRandomAlphaNumericStrings(3));;
  $regAgent = $_SESSION['hostEmail'];

  // Check if the voter already exists in the voters poll table > database
  $checkDuplicatePrivatePollQuery = "SELECT * FROM poll_voters WHERE voterEmail = ? AND pollID= ? AND hostID =?";
  $stmtPrivatePoll = $conn->prepare($checkDuplicatePrivatePollQuery);
  $stmtPrivatePoll->bind_param("sss", $email, $voterPoll, $hostID);
  $stmtPrivatePoll->execute();
  $privatePollResult = $stmtPrivatePoll->get_result();
  $checkPollVotersDuplicate = $privatePollResult->fetch_assoc();
  $stmtPrivatePoll->close();

  // Check if the voter already exists in the voter table > database
  $checkDuplicateQuery = "SELECT * FROM voters WHERE email = ?";
  $stmt = $conn->prepare($checkDuplicateQuery);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $checkVotersDuplicate = $result->fetch_assoc();
  $stmt->close();

  if (!empty($checkPollVotersDuplicate)) {
    $response = array('status' => 'warning', 'message' => 'Voter with this email already exists', 'responseStatus' => 'warning', 'header' => 'Duplicate Entry!');
    echo json_encode($response);
    exit();
  }

  if (empty($checkPollVotersDuplicate) && !empty($checkVotersDuplicate)) {

    // Proceed with insert the voter into poll_voters table
    $insertQuery = "INSERT INTO poll_voters (`hostID`, `pollID`, `voterEmail`, `registrationType`) VALUES (?, ?, ?,'added')";
    $stmt = $conn->prepare($insertQuery);
    // Bind parameters to the insert query 
    $stmt->bind_param("sss",  $hostID, $voterPoll, $email);

    if ($stmt->execute()) {
      $status = 'success';
      $header = 'Successful!';
      $message = 'Voter has been added successfully';
      $responseStatus = 'success';
    }
  } else {
    if (isset($_FILES['voterImages']) && $_FILES['voterImages']['size'] > 0) {

      // Handle file upload for voter image
      $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
      $customFileName = $email;
      $voterImage = handleFileUpload($_FILES['voterImages'], 'resources/voter_images', $allowedFormats, $customFileName);

      if (!$voterImage) {
        echo json_encode(["status" => "error", "message" => "Error uploading voter image. Please try again."]);
        exit();
      }
    } else {
      $voterImage = '';
    }

    // Proceed with inserting the voter if no duplicates are found and image uploaded successfully
    $insertQuery = "INSERT INTO voters (`voterID`, `hostID`, `sname`, `fname`, `oname`, `email`, `phone`, `imagePath`, `gender`, `regAgent`, `status`, `source`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'added')";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
      die("Error preparing statement: " . $conn->error);
    }
    // Bind parameters to the insert query 
    $stmt->bind_param("sssssssssss", $voterID, $hostID, $sname, $fname, $oname, $email, $phone, $voterImage, $gender, $regAgent, $status);

    // Proceed with insert the voter into poll_voters table
    $insertPrivateQuery = "INSERT INTO poll_voters (`hostID`, `pollID`, `voterEmail`, `registrationType`) VALUES (?, ?, ?, 'added')";
    $stmtPrivate = $conn->prepare($insertPrivateQuery);
    // Bind parameters to the insert query 
    $stmtPrivate->bind_param("sss",  $hostID, $voterPoll, $email);

    // Execute the insertion
    if ($stmt->execute() && $stmtPrivate->execute()) {
      $status = 'success';
      $header = 'Successful!';
      $message = 'Voter has been added successfully';
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

  echo json_encode($response);

  exit();
}
//::: Add New Voter

//::: Verify Voter Email Entry Before Submitting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voterEmailEntryVer'])) {
  if (isset($_POST['voterEmail']) && !empty($_POST['voterEmail'])) {
    $email = $_POST['voterEmail'];

    // Check if the voter already exists in the database
    $checkDuplicateQuery = "SELECT * FROM voters WHERE email = ?";
    $stmt = $conn->prepare($checkDuplicateQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkDuplicate = $result->fetch_assoc();
    $stmt->close();

    if (!empty($checkDuplicate)) {
      $response = array("status" => true, "message" => "Email already exists.");
    } else {
      $response = array("status" => false, "message" => "Email is unique.");
    }
  } else {
    $response = array("status" => false, "message" => "Email not provided.");
  }

  header("Content-Type: application/json");
  echo json_encode($response);
  exit();
}
//::: Verify Voter Email Entry Before Submitting

//::: Get Voter Information for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['getVoterEdit'], $_POST['voterID']) && $_POST['getVoterEdit'] == true) {
  $voterID = mysqli_real_escape_string($conn, $_POST['voterID']);
  $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';
  $voter = getVoterByID($conn, $voterID, $hostID);

  if (!empty($voter)) {
?>
    <div class="modal-header">
      <h5 class="modal-title" id="voterModifyLabel">Modify <b><?= ucfirst($voter['sname']) . " " . ucfirst($voter['fname']); ?></b></h5>
    </div>
    <form id="editVoterForm" class="needs-validation" novalidate>
      <div class="modal-body">
        <div class="row mt-3">

          <!--Edit Voter Inputs-->
          <div class="col-lg-8 col-md-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-body py-5">
                  <div class="row">

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterEmail">Email Address<span class="text-danger">*</span>
                          <input class="form-control" type="email" name="editVoterEmail" id="editVoterEmail" placeholder="Enter Voter Email Address" value="<?= $voter['email']; ?>" readonly required="" />
                          <span id="email-feedback" class="text-danger" style="font-size:12px"></span>
                        </label>
                        <input type="hidden" name="voterID" value="<?= $voter['voterID']; ?>" />
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterStatus">Status<span class="text-danger">*</span>
                          <select class="form-control editModal-select" type="text" name="editVoterStatus" id="editVoterStatus" required="">
                            <option value="active" <?= $voter['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?= $voter['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                          </select>
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterSname">Surname<span class="text-danger">*</span>
                          <input class="form-control" type="text" name="editVoterSname" id="editVoterSname" placeholder="Enter Voter Surname" value="<?= $voter['sname']; ?>" required="" />
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterFname">First Name<span class="text-danger">*</span>
                          <input class="form-control" type="text" name="editVoterFname" id="editVoterFname" placeholder="Enter Voter First Name" value="<?= $voter['fname']; ?>" required="" />
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterOname">Other Names
                          <input class="form-control" type="text" name="editVoterOname" id="editVoterOname" placeholder="Enter Voter Other Names" value="<?= $voter['oname']; ?>" />
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterPhone">Phone
                          <input class="form-control" type="text" name="editVoterPhone" id="editVoterPhone" placeholder="Enter Voter Phone Number" value="<?= $voter['phone']; ?>" />
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                      <div class="input-group">
                        <label class="col-12" for="editVoterGender">Gender<span class="text-danger">*</span>
                          <select class="form-control editModal-select" type="text" name="editVoterGender" id="editVoterGender" required="">
                            <option value="male" <?= $voter['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?= $voter['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                          </select>
                        </label>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--Edit Voter Inputs-->


          <!--Voter Image-->
          <div class="col-lg-4 col-md-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4 class="card-title">Voter Image Preview</h4>
                </div>
                <div class="card-body py-5">
                  <center class="col-12" style="margin: 0px auto;">
                    <img src="<?= !empty($voter['imagePath']) && file_exists("../" . $voter['imagePath']) ? $voter['imagePath'] : 'images/no-preview.jpeg'; ?>" style="width:15rem;height:15rem" id="editVoterImagePreview" />
                    <div>&nbsp;</div>
                    <label for="editVoterImages" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Voter Image<input id="editVoterImages" name="editVoterImages" type="file" />
                    </label>
                  </center>

                </div>
              </div>
            </div>
          </div>
          <!--Voter Image-->

        </div>
      </div>
      <div class="modal-footer">
        <span id="editVoterMsg"></span>
        <button type="submit" class="btn btn-primary" id="saveEditVoterBtn">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>

    <script>
      $(document).ready(function() {
        //To allow the select2 input work as expected
        $('.editModal-select').select2({
          dropdownParent: $('#editVoterForm'),
        });

      });

      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<
      //Helper Function to preview uploaded voter Image
      document.getElementById('editVoterImages').addEventListener('change', function(event) {
        var output = document.getElementById('editVoterImagePreview');
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
      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


      //::|| >>>>>>>>>>>>>::: 02: VOTERS FUNCTIONS<<<<<<<<<<<<<<<<<<<
      //::: Function to edit voter
      $("#editVoterForm").submit(function(e) {
        e.preventDefault();
        var voterForm = new FormData(this); // Simplified FormData initialization
        var formID = "editVoterForm";

        // Show error message if any of the form input is invalid
        if (!validateInput(formID)) {
          swal(
            "Required Fields",
            "Please fill out all required fields before submitting.",
            "error"
          );
          return; // Stop further execution if validation fails
        }

        voterForm.append("updateVoterRequest", true);
        swal({
            title: "Are you sure to continue?",
            text: "You are about updating the voter information.",
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
                $("#saveEditVoterBtn").prop("disabled", true);
                $("#saveEditVoterBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
              },
              success: function(response) {
                $("#saveEditVoterBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                var status = response.status;
                var message = response.message;
                var header = response.header;

                if (status !== "error" && status !== "warning") {
                  loadVoters(votersCurrentPageNo); // Reload voters after successful submission
                  $("#editVoterImages").val(""); // Reset Image input after submission
                  // $("#editVoterForm")[0].reset(); // Reset form after submission
                  // $("#editCategoryID").val("").trigger("change"); //Reset Category Select2 dropdown
                  // $("#editSubCategoryID").val("").trigger("change"); // Reset Sub Category Select2 dropdown
                  //$(imageContainers).remove(); //Remove Voter image preview container
                  // updateFileInput(); //Restore Image input to default

                  $("#saveEditVoterBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                }
                swal(header, message, status); // Display response message
              },
              error: function() {
                $("#saveEditVoterBtn").html("Save Changes").prop("disabled", false); // Re-enable button after request failure
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          });
      });
      //::|| >>>>>>>>>>>>>::: 02: VOTERS FUNCTIONS<<<<<<<<<<<<<<<<<<
    </script>

<?php
  }

  exit();
}
//::: Get Voter Information for Edit

// Handling CSV upload request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['votersEmailCSV'], $_POST['uploadVotersCSVRequest']) && $_POST['uploadVotersCSVRequest'] == "true") {
  header("Content-Type: application/json");

  // Get the pollID and hostID from the POST request
  $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';
  $pollID = mysqli_real_escape_string($conn, $_POST['votersPollID']);

  // Function to upload voters from CSV file
  function uploadVotersFromCSV($conn, $csvFilePath, $hostID, $pollID)
  {
    // Open the CSV file for reading
    if (($handle = fopen($csvFilePath, 'r')) !== false) {
      // Initialize counters
      $insertedCount = 0;
      $skippedCount = 0;

      // Prepare the queries
      $checkQuery = "SELECT COUNT(*) FROM poll_voters WHERE pollID = ? AND hostID = ? AND voterEmail = ?";
      $insertQuery = "INSERT INTO poll_voters (hostID, pollID, voterEmail, `registrationType`) VALUES (?, ?, ?, 'uploaded')";

      // Prepare the statements
      $checkStmt = $conn->prepare($checkQuery);
      $insertStmt = $conn->prepare($insertQuery);

      if (!$checkStmt || !$insertStmt) {
        return ['status' => 'error', 'message' => 'Failed to prepare SQL statements'];
      }

      // Loop through each row in the CSV
      while (($data = fgetcsv($handle)) !== false) {
        $voterEmail = $data[0]; // Assuming 'voterEmail' is the only column in the CSV

        // Check if the voter email already exists for the given pollID and hostID
        $checkStmt->bind_param('sss', $pollID, $hostID, $voterEmail);
        $checkStmt->execute();

        // Use get_result to fetch the result of the query properly
        $result = $checkStmt->get_result();
        $row = $result->fetch_row();
        $count = $row[0];

        // After checking the result, free the result set
        $result->free();

        if ($count == 0) {
          // If the email doesn't exist, insert it into the database
          $insertStmt->bind_param('sss', $hostID, $pollID, $voterEmail);
          $insertStmt->execute();
          $insertedCount++;
        } else {
          // If the email exists, skip it
          $skippedCount++;
        }
      }

      // Close the file and statements
      fclose($handle);
      $checkStmt->close();
      $insertStmt->close();

      // Return success message
      return [
        'status' => 'success',
        'header' => 'Voters CSV Upload Executed',
        'message' => "Upload complete. Inserted: $insertedCount, Skipped: $skippedCount"
      ];
    } else {
      return ['status' => 'error', 'header' => 'Uploaded Failed', 'message' => 'Unable to open the CSV file'];
    }
  }

  // Get the CSV file path from the uploaded file
  $csvFilePath = $_FILES['votersEmailCSV']['tmp_name'];

  // Call the uploadVotersFromCSV function
  $responseMessage = uploadVotersFromCSV($conn, $csvFilePath, $hostID, $pollID);

  // Return the response message as JSON
  echo json_encode($responseMessage);
  exit();
}


//::: Update Voter information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateVoterRequest'], $_POST['voterID']) && $_POST['updateVoterRequest'] == "true") {

  header("Content-Type: application/json");

  // Sanitize input fields
  $voterID = mysqli_real_escape_string($conn, $_POST['voterID']);
  $sname = mysqli_real_escape_string($conn, $_POST['editVoterSname']);
  $fname = mysqli_real_escape_string($conn, $_POST['editVoterFname']);
  $oname = mysqli_real_escape_string($conn, $_POST['editVoterOname'] ?? '');
  $gender = mysqli_real_escape_string($conn, $_POST['editVoterGender']);
  $email = mysqli_real_escape_string($conn, $_POST['editVoterEmail']);
  $phone = mysqli_real_escape_string($conn, $_POST['editVoterPhone']);
  $status = mysqli_real_escape_string($conn, $_POST['editVoterStatus']);

  // Ensure required fields are not empty
  if (empty($voterID) || empty($sname) || empty($fname) || empty($gender) || empty($email) || empty($phone) || empty($status)) {
    echo json_encode([
      'header' => 'Missing Fields',
      'message' => 'All fields marked with * are required.',
      'status' => 'error'
    ]);
    exit();
  }

  // Retrieve the current voter data
  $currentData = getVoterByID($conn, $voterID, $_SESSION['hostID']);
  if (!$currentData) {
    echo json_encode([
      'header' => 'Not Found',
      'message' => 'Voter not found.',
      'status' => 'error'
    ]);
    exit();
  }

  // Compare each field to check if anything has changed
  $fieldsToUpdate = [];
  $params = [];

  // Array of field names and their corresponding variables
  $fieldMappings = [
    'sname' => $sname,
    'fname' => $fname,
    'oname' => $oname,
    'gender' => $gender,
    'email' => $email,
    'phone' => $phone,
    'status' => $status
  ];

  foreach ($fieldMappings as $column => $newValue) {
    if ($newValue !== $currentData[$column]) {
      $fieldsToUpdate[] = "$column = ?";
      $params[] = $newValue; // Add the new value to the params array
    }
  }

  // Check if there are new images
  $hasNewImages = !empty($_FILES['editVoterImages']) && $_FILES['editVoterImages']['size'] > 0;
  if ($hasNewImages) {
    // Remove the previous file if it exists
    if (!empty($currentData['imagePath']) && file_exists("../" . $currentData['imagePath'])) {
      unlink("../" . $currentData['imagePath']);
    }

    // Handle the new file upload
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    $customFileName = $email;
    $voterImage = handleFileUpload($_FILES['editVoterImages'], 'resources/voter_images', $allowedFormats, $customFileName);
  }

  $hasChanges = !empty($fieldsToUpdate);

  if (!$hasChanges && !$hasNewImages) {
    echo json_encode([
      'header' => 'No Changes!',
      'message' => 'There are no updates to apply.',
      'status' => 'warning'
    ]);
    exit();
  }

  // Update the voter if there are changes
  if (!empty($fieldsToUpdate)) {
    $updateQuery = "UPDATE voters SET " . implode(", ", $fieldsToUpdate) . ", `modifiedDate`=now() WHERE voterID = ? ";
    $params[] = $voterID;

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if (!$stmt->execute()) {
      echo json_encode([
        'header' => 'Update Failed',
        'message' => 'Error updating voter: ' . $stmt->error,
        'status' => 'error'
      ]);
      exit();
    }
    $stmt->close();
  }

  // Handle new image if uploaded
  if ($hasNewImages) {
    // Update the main imagePath in voters table
    $updateMainImageQuery = "UPDATE voters SET imagePath = ? WHERE voterID = ?";
    $stmtMainImage = $conn->prepare($updateMainImageQuery);
    $stmtMainImage->bind_param("ss", $voterImage, $voterID);
    $stmtMainImage->execute();
    $stmtMainImage->close();
  }

  echo json_encode([
    'status' => 'success',
    'header' => 'Update Successful!',
    'message' => 'Voter updated successfully.'
  ]);
  exit();
}
//::: Update Voter information

//:: Delete Voter 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['voterEmail'], $_POST["deleteVoterRequest"]) && $_POST["deleteVoterRequest"] == true) {
  // Check this tables where the deleteID exist to avoid system crash for other modules ::: 'TableName' => 'Column where deleteID is used'
  $referenceChecks = ['votes' => 'voterEmail', 'poll_voters' => 'voterEmail', 'voters' => 'email'];
  $table = "voters";
  $uniqueColumn = "voterEmail";
  $deleteID = mysqli_real_escape_string($conn, $_POST['voterEmail']);
  $deleteResponse = deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Delete Voter

//:: Force Delete Voter :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['voterEmail'], $_POST["forceDeleteVoterRequest"]) && $_POST["forceDeleteVoterRequest"] == true) {
  // Reference tables where the deleteID exists to avoid system crash in other modules
  $referenceChecks = ['votes' => 'voterEmail', 'poll_voters' => 'voterEmail'];
  $table = "poll_voters"; // Main table where the voter is stored
  $uniqueColumn = "voterEmail"; // Column that uniquely identifies the voter
  $deleteID = mysqli_real_escape_string($conn, $_POST['voterEmail']); // Sanitizing the voterID input

  // Force delete from all tables
  $deleteResponse = forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  // Return JSON response
  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Force Delete Voter :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY
?>