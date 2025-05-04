<?php
session_start();
include("globalFunctions.php");
//::||Get Candidate with pagination and other parameters(search_query, pageLimit, etc.) from the candidates Table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pageLimit'], $_POST['query']) && !empty($_POST['pageLimit'])) {

  // Function to get candidates with pagination and other parameters
  function getCandidatesWithParams($conn)
  {
    $search_query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $limit = isset($_POST['pageLimit']) ? (int)$_POST['pageLimit'] : 10;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'c.regDate';
    $elections = isset($_POST['elections']) ? $_POST['elections'] : [];
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';

    // Pagination
    $offset = ($page - 1) * $limit;

    // Sort options
    $sort_columns = [
      'name_asc' => 'c.sname ASC, c.fname ASC, c.oname ASC',
      'name_desc' => 'c.sname DESC, c.fname DESC, c.oname DESC',
      'email_asc' => 'c.email ASC',
      'email_desc' => 'c.email DESC',
      'position_asc' => 'c.position ASC',
      'position_desc' => 'c.position DESC',
      'date_asc' => 'c.regDate ASC',
      'date_desc' => 'c.regDate DESC',
      'title_asc' => 'p.title ASC',
      'title_desc' => 'p.title DESC',
    ];

    $order_by = isset($sort_columns[$sort_by]) ? $sort_columns[$sort_by] : 'c.regDate DESC';

    // Initial query
    $query = "SELECT SQL_CALC_FOUND_ROWS c.candidateID, c.hostID, c.pollID, c.sname, c.fname, c.oname, 
              c.gender, c.email, c.phone, c.address, c.imagePath, c.position, c.status, c.manifesto, 
              c.regDate, c.modifiedDate, p.title AS electionTitle , pt.name AS positionName
              FROM candidates c 
              LEFT JOIN polls p ON c.pollID = p.pollID 
              LEFT JOIN positions pt ON c.position = pt.positionID 
              WHERE c.hostID=?";
    $params = [$hostID];
    $types = 's';

    // Search query filter
    if (!empty($search_query)) {
      $query .= " AND (c.sname LIKE ? OR c.fname LIKE ? OR c.oname LIKE ? OR c.email LIKE ?)";
      $search_query = '%' . $conn->real_escape_string($search_query) . '%';
      $params = array_merge($params, [$search_query, $search_query, $search_query, $search_query]);
      $types .= 'ssss';
    }

    // Election filter
    if (!empty($elections)) {
      $placeholders = implode(',', array_fill(0, count($elections), '?'));
      $query .= " AND c.pollID IN ($placeholders)";
      foreach ($elections as $election) {
        $params[] = $election;
        $types .= 's';
      }
    }

    // Status filter
    if ($status === 'active') {
      $query .= " AND c.status = 'active'";
    } elseif ($status === 'inactive') {
      $query .= " AND c.status = 'inactive'";
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
    $candidates = [];
    while ($row = $result->fetch_assoc()) {
      $candidates[] = $row;
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
    echo json_encode(['candidates' => $candidates, 'pagination' => $pagination]);
  }

  // Call the function
  getCandidatesWithParams($conn);

  exit();
}
//:::Get Candidate with pagination and other parameters(search_query, pageLimit, etc.) from the candidates Table

//::: Add New Candidate
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prepareNewCandidate'], $_POST['candidateEmail'])) {

  header('content-Type: application/json');
  $hostID = $_SESSION['hostID'];
  $pollID = mysqli_real_escape_string($conn, $_POST['candidateElection']);
  $sname = mysqli_real_escape_string($conn, $_POST['candidateSname']);
  $fname = mysqli_real_escape_string($conn, $_POST['candidateFname']);
  $oname = mysqli_real_escape_string($conn, $_POST['candidateOname'] ?? '');
  $gender = mysqli_real_escape_string($conn, $_POST['candidateGender']);
  $email = mysqli_real_escape_string($conn, $_POST['candidateEmail']);
  $phone = mysqli_real_escape_string($conn, $_POST['candidatePhone']);
  $address = mysqli_real_escape_string($conn, $_POST['candidateAddress']);
  $position = mysqli_real_escape_string($conn, $_POST['candidatePosition']);
  $status = mysqli_real_escape_string($conn, $_POST['candidateStatus']);
  $manifesto = mysqli_real_escape_string($conn, $_POST['candidateManifesto'] ?? '');
  $candidateID = "CD" . strtoupper(generateRandomAlphaNumericStrings(10));

  // Check if the candidate already exists in the database
  $checkDuplicateQuery = "SELECT * FROM candidates WHERE email = ?";
  $stmt = $conn->prepare($checkDuplicateQuery);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $checkDuplicate = $result->fetch_assoc();
  $stmt->close();

  if (!empty($checkDuplicate)) {
    $status = 'warning';
    $header = 'Duplicate Entry!';
    $message = 'Candidate with this email already exists';
    $responseStatus = 'warning';
  } else {
    // Handle file upload for candidate image
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    $customFileName = $email;
    $candidateImage = handleFileUpload($_FILES['candidateImages'], 'resources/candidate_images', $allowedFormats, $customFileName);

    if (!$candidateImage) {
      echo json_encode(["status" => "error", "message" => "Error uploading candidate Image. Please try again."]);
      exit();
    }

    // Proceed with inserting the candidate if no duplicates are found and image uploaded successfully
    $insertQuery = "INSERT INTO candidates (`candidateID`,`hostID`, `pollID`, `sname`, `fname`, `oname`, `gender`, `email`, `phone`, `address`, `imagePath`, `position`, `status`, `manifesto`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
      die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the insert query
    $stmt->bind_param("ssssssssssssss", $candidateID, $hostID, $pollID, $sname, $fname, $oname, $gender, $email, $phone, $address, $candidateImage, $position, $status, $manifesto);

    // Execute the insertion
    if ($stmt->execute()) {
      $status = 'success';
      $header = 'Successful!';
      $message = 'Candidate has been added successfully';
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
//::: Add New Candidate

//::: Verify Candidate Email Entry Before Submitting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidateEmailEntryVer'])) {
  if (isset($_POST['candidateEmail']) && !empty($_POST['candidateEmail'])) {
    $email = $_POST['candidateEmail'];

    // Check if the candidate already exists in the database
    $checkDuplicateQuery = "SELECT * FROM candidates WHERE email = ?";
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
//::: Verify Candidate Email Entry Before Submitting

//::: Get Candidate Information for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['getCandidateEdit'], $_POST['candidateID']) && $_POST['getCandidateEdit'] == true) {
  $candidateID = mysqli_real_escape_string($conn, $_POST['candidateID']);
  $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';
  $candidate = getCandidateByID($conn, $candidateID, $hostID);

  if (!empty($candidate)) {
?>
    <div class="modal-header">
      <h5 class="modal-title" id="candidateModifyLabel">Modify <b><?= ucfirst($candidate['sname']) . " " . ucfirst($candidate['fname']); ?></b></h5>
    </div>
    <form id="editCandidateForm" class="needs-validation" novalidate>
      <div class="modal-body">
        <div class="row mt-3">

          <!-- Candidate Inputs -->
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-body py-3">
                  <div class="alert alert-primary" role="alert">
                    This candidate was added
                    <a href="javascript:void(0);" class="alert-link">
                      <?= structureTimestamp($candidate['regDate']); ?>
                    </a>
                    <?= !empty($candidate['modifiedDate']) && $candidate['modifiedDate'] != $candidate['regDate'] ? ' and was last modified ' . '<a href="javascript:void(0);" class="alert-link">' . structureTimestamp($candidate['modifiedDate']) . '.</a>' : ' and has not been modified yet.'; ?>
                  </div>
                  <div class="row mt-3">

                    <!--Edit Candidate Inputs-->
                    <div class="col-lg-8 col-md-12 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-body py-5">
                            <div class="row">

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <input type="hidden" name="candidateID" value="<?= $candidate['candidateID']; ?>">
                                  <label class="col-12" for="editCandidateElection">Polls<span class="text-danger">*</span><a href="polls" class="text-primary" style="float:right">+ Add New</a>
                                    <select class="form-control editModal-select" id="editCandidateElection" name="editCandidateElection" required="">
                                      <option value="" selected readonly>Select an election</option>
                                      <?php
                                      $polls = getPolls($conn, $_SESSION['hostID']);
                                      foreach ($polls as $poll) {
                                      ?>
                                        <option value="<?= $poll['pollID']; ?>" <?= $poll['pollID'] == $candidate['pollID'] ? 'selected' : ''; ?>><?= $poll['title']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidatePosition">Vying Position<span class="text-danger">*</span><a href="offices" class="text-primary" style="float:right">+ Add New</a>
                                    <select class="form-control editModal-select" id="editCandidatePosition" name="editCandidatePosition" required="">
                                      <option value="">Select Position</option>
                                      <?php
                                      $positions = getPositionsByHostID($conn, $hostID);;
                                      foreach ($positions as $position) {
                                      ?>
                                        <option value="<?= $position['positionID']; ?>" <?= $position['positionID'] == $candidate['position'] ? 'selected' : ''; ?>><?= htmlspecialchars($position['name']); ?></option>
                                      <?php } ?>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateStatus">Status<span class="text-danger">*</span>
                                    <select class="form-control editModal-select" type="text" name="editCandidateStatus" id="editCandidateStatus" required="">
                                      <option value="active" <?= $candidate['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                      <option value="inactive" <?= $candidate['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateGender">Gender<span class="text-danger">*</span>
                                    <select class="form-control editModal-select" type="text" name="editCandidateGender" id="editCandidateGender" required="">
                                      <option value="male" <?= $candidate['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                      <option value="female" <?= $candidate['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateSname">Surname<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="editCandidateSname" id="editCandidateSname" placeholder="Enter Candidate Surname" value="<?= $candidate['sname']; ?>" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateFname">First Name<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="editCandidateFname" id="editCandidateFname" placeholder="Enter Candidate First Name" value="<?= $candidate['fname']; ?>" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateOname">Other Names
                                    <input class="form-control" type="text" name="editCandidateOname" id="editCandidateOname" placeholder="Enter Candidate Other Names" value="<?= $candidate['oname']; ?>" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateEmail">Email Address<span class="text-danger">*</span>
                                    <input class="form-control" type="email" name="editCandidateEmail" id="editCandidateEmail" placeholder="Enter Candidate Email Address" value="<?= $candidate['email']; ?>" readonly required="" />
                                    <span id="email-feedback" class="text-danger" style="font-size:12px"></span>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidatePhone">Phone<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="editCandidatePhone" id="editCandidatePhone" placeholder="Enter Candidate Phone Number" value="<?= $candidate['phone']; ?>" required="" />
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-md-6 col-sm-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateAddress">Candidate Resident Address<span class="text-danger">*</span>
                                    <textarea class="form-control" placeholder="Enter Candidate Resident Address" name="editCandidateAddress" id="editCandidateAddress" required=""><?= $candidate['address']; ?></textarea>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-12">
                                <div class="input-group">
                                  <label class="col-12" for="editCandidateManifesto">Candidate Manifesto
                                    <textarea class="form-control" rows="5" maxlength="250" placeholder="Enter Candidate Manifesto" name="editCandidateManifesto" id="editCandidateManifesto"><?= $candidate['manifesto']; ?></textarea>
                                  </label>
                                </div>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--Edit Candidate Inputs-->


                    <!--Candidate Image-->
                    <div class="col-lg-4 col-md-12 mt-3">
                      <div class="card">
                        <div class="card-content">
                          <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Candidate Image Preview</h4>
                          </div>
                          <div class="card-body py-5">
                            <center class="col-12" style="margin: 0px auto;">
                              <img src="<?= !empty($candidate['imagePath']) && file_exists("../" . $candidate['imagePath']) ? $candidate['imagePath'] : 'images/no-preview.jpeg'; ?>" style="width:280px;height:280px" id="editCandidateImagePreview" />
                              <div>&nbsp;</div>
                              <label for="editCandidateImages" class="file-upload btn btn-primary btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Select Candidate Image<input id="editCandidateImages" name="editCandidateImages" type="file" />
                              </label>
                            </center>

                          </div>
                        </div>
                      </div>
                    </div>
                    <!--Candidate Image-->
                  </div>

                </div>
              </div>
            </div>
          </div>
          <!-- Candidate Inputs -->

        </div>
      </div>
      <div class="modal-footer">
        <span id="editCandidateMsg"></span>
        <button type="submit" class="btn btn-primary" id="saveEditCandidateBtn">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>

    <script>
      $(document).ready(function() {
        //To allow the select2 input work as expected
        $('.editModal-select').select2({
          dropdownParent: $('#editCandidateForm'),
        });

      });

      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<
      //Helper Function to preview uploaded candidate Image
      document.getElementById('editCandidateImages').addEventListener('change', function(event) {
        var output = document.getElementById('editCandidateImagePreview');
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

      // Fetch Elections Positions
      $("#editCandidateElection").on("change", function() {
        var pollID = $(this).val();
        if (pollID) {
          $.ajax({
            url: "controllers/get-selections",
            method: "POST",
            data: {
              pollID: pollID,
            },
            success: function(response) {
              var positionSelect = $("#editCandidatePosition");
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
          $("#editCandidatePosition").empty();
          $("#editCandidatePosition").append('<option value="" disabled selected>Select Candidate Position</option>');
        }
      });
      //::|| >>>>>>>>>>>>>::: 01: HELPER FUNCTIONS<<<<<<<<<<<<<<<<<<


      //::|| >>>>>>>>>>>>>::: 02: CANDIDATES FUNCTIONS<<<<<<<<<<<<<<<<<<<
      //::: Function to edit candidate
      $("#editCandidateForm").submit(function(e) {
        e.preventDefault();
        var candidateForm = new FormData(this); // Simplified FormData initialization
        var formID = "editCandidateForm";

        // Show error message if any of the form input is invalid
        if (!validateInput(formID)) {
          swal(
            "Required Fields",
            "Please fill out all required fields before submitting.",
            "error"
          );
          return; // Stop further execution if validation fails
        }

        candidateForm.append("updateCandidateRequest", true);
        swal({
            title: "Are you sure to continue?",
            text: "You are about updating the candidate information.",
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
              url: "controllers/get-candidates",
              type: "POST",
              async: true,
              processData: false,
              contentType: false,
              data: candidateForm,
              beforeSend: function() {
                $("#saveEditCandidateBtn").prop("disabled", true);
                $("#saveEditCandidateBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
              },
              success: function(response) {
                $("#saveEditCandidateBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                var status = response.status;
                var message = response.message;
                var header = response.header;

                if (status !== "error" && status !== "warning") {
                  loadCandidates(candidatesCurrentPageNo); // Reload candidates after successful submission
                  $("#editCandidateImages").val(""); // Reset Image input after submission
                  // $("#editCandidateForm")[0].reset(); // Reset form after submission
                  // $("#editCategoryID").val("").trigger("change"); //Reset Category Select2 dropdown
                  // $("#editSubCategoryID").val("").trigger("change"); // Reset Sub Category Select2 dropdown
                  //$(imageContainers).remove(); //Remove Candidate image preview container
                  // updateFileInput(); //Restore Image input to default

                  $("#saveEditCandidateBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                }
                swal(header, message, status); // Display response message
              },
              error: function() {
                $("#saveEditCandidateBtn").html("Save Changes").prop("disabled", false); // Re-enable button after request failure
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          });
      });
      //::|| >>>>>>>>>>>>>::: 02: CANDIDATES FUNCTIONS<<<<<<<<<<<<<<<<<<
    </script>

<?php
  }

  exit();
}
//::: Get Candidate Information for Edit

//::: Update Candidate information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateCandidateRequest'], $_POST['candidateID']) && $_POST['updateCandidateRequest'] == "true") {

  header("Content-Type: application/json");

  // Sanitize input fields
  $candidateID = mysqli_real_escape_string($conn, $_POST['candidateID']);
  $pollID = mysqli_real_escape_string($conn, $_POST['editCandidateElection']);
  $sname = mysqli_real_escape_string($conn, $_POST['editCandidateSname']);
  $fname = mysqli_real_escape_string($conn, $_POST['editCandidateFname']);
  $oname = mysqli_real_escape_string($conn, $_POST['editCandidateOname'] ?? '');
  $gender = mysqli_real_escape_string($conn, $_POST['editCandidateGender']);
  $email = mysqli_real_escape_string($conn, $_POST['editCandidateEmail']);
  $phone = mysqli_real_escape_string($conn, $_POST['editCandidatePhone']);
  $address = mysqli_real_escape_string($conn, $_POST['editCandidateAddress']);
  $position = mysqli_real_escape_string($conn, $_POST['editCandidatePosition']);
  $status = mysqli_real_escape_string($conn, $_POST['editCandidateStatus']);
  $manifesto = mysqli_real_escape_string($conn, $_POST['editCandidateManifesto'] ?? '');

  // Ensure required fields are not empty
  if (empty($candidateID) || empty($pollID) || empty($sname) || empty($fname) || empty($gender) || empty($email) || empty($phone) || empty($address) || empty($position) || empty($status)) {
    echo json_encode([
      'header' => 'Missing Fields',
      'message' => 'All fields marked with * are required.',
      'status' => 'error'
    ]);
    exit();
  }

  // Retrieve the current candidate data
  $currentData = getCandidateByID($conn, $candidateID, $_SESSION['hostID']);
  if (!$currentData) {
    echo json_encode([
      'header' => 'Not Found',
      'message' => 'Candidate not found.',
      'status' => 'error'
    ]);
    exit();
  }

  // Compare each field to check if anything has changed
  $fieldsToUpdate = [];
  $params = [];

  // Array of field names and their corresponding variables
  $fieldMappings = [
    'pollID' => $pollID,
    'sname' => $sname,
    'fname' => $fname,
    'oname' => $oname,
    'gender' => $gender,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'position' => $position,
    'status' => $status,
    'manifesto' => $manifesto
  ];

  foreach ($fieldMappings as $column => $newValue) {
    if ($newValue !== $currentData[$column]) {
      $fieldsToUpdate[] = "$column = ?";
      $params[] = $newValue; // Add the new value to the params array
    }
  }

  // Check if there are new images
  $hasNewImages = !empty($_FILES['editCandidateImages']) && $_FILES['editCandidateImages']['size'] > 0;
  if ($hasNewImages) {
    // Remove the previous file if it exists
    if (!empty($currentData['imagePath']) && file_exists("../" . $currentData['imagePath'])) {
      unlink("../" . $currentData['imagePath']);
    }

    // Handle the new file upload
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    $customFileName = $email;
    $candidateImage = handleFileUpload($_FILES['editCandidateImages'], 'resources/candidate_images', $allowedFormats, $customFileName);
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

  // Update the candidate if there are changes
  if (!empty($fieldsToUpdate)) {
    $updateQuery = "UPDATE candidates SET " . implode(", ", $fieldsToUpdate) . ", `modifiedDate`=now() WHERE candidateID = ? ";
    $params[] = $candidateID;

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if (!$stmt->execute()) {
      echo json_encode([
        'header' => 'Update Failed',
        'message' => 'Error updating candidate: ' . $stmt->error,
        'status' => 'error'
      ]);
      exit();
    }
    $stmt->close();
  }

  // Handle new image if uploaded
  if ($hasNewImages) {
    // Update the main imagePath in candidates table
    $updateMainImageQuery = "UPDATE candidates SET imagePath = ? WHERE candidateID = ?";
    $stmtMainImage = $conn->prepare($updateMainImageQuery);
    $stmtMainImage->bind_param("ss", $candidateImage, $candidateID);
    $stmtMainImage->execute();
    $stmtMainImage->close();
  }

  echo json_encode([
    'status' => 'success',
    'header' => 'Update Successful!',
    'message' => 'Candidate updated successfully.'
  ]);
  exit();
}
//::: Update Candidate information

//:: Delete Candidate 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidateID'], $_POST["deleteCandidateRequest"]) && $_POST["deleteCandidateRequest"] == true) {
  // Check this tables where the deleteID exist to avoid system crash for other modules ::: 'TableName' => 'Column where deleteID is used'
  $referenceChecks = ['votes' => 'candidateID'];
  $table = "candidates";
  $uniqueColumn = "candidateID";
  $deleteID = mysqli_real_escape_string($conn, $_POST['candidateID']);
  $deleteResponse = deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Delete Candidate

//:: Force Delete Candidate :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidateID'], $_POST["forceDeleteCandidateRequest"]) && $_POST["forceDeleteCandidateRequest"] == true) {
  // Reference tables where the deleteID exists to avoid system crash in other modules
  $referenceChecks = ['votes' => 'candidateID'];

  $table = "candidates"; // Main table where the candidate is stored
  $uniqueColumn = "candidateID"; // Column that uniquely identifies the candidate
  $deleteID = mysqli_real_escape_string($conn, $_POST['candidateID']); // Sanitizing the candidateID input

  // Force delete from all tables
  $deleteResponse = forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  // Return JSON response
  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Force Delete Candidate :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
?>