<?php
session_start();
include("globalFunctions.php");
//::||Get Position with pagination and other parameters(search_query, pageLimit, etc.) from the positions Table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pageLimit'], $_POST['query']) && !empty($_POST['pageLimit'])) {

  // Function to get positions with pagination and other parameters
  function getPositionsWithParams($conn)
  {
    $search_query = isset($_POST['query']) ? trim($_POST['query']) : '';
    $limit = isset($_POST['pageLimit']) ? (int)$_POST['pageLimit'] : 10;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'p.regDate';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';

    // Pagination
    $offset = ($page - 1) * $limit;

    // Sort options
    $sort_columns = [
      'name_asc' => 'p.name ASC',
      'name_desc' => 'p.name DESC',
      'abbr_asc' => 'p.abbr ASC',
      'abbr_desc' => 'p.abbr DESC',
      'date_asc' => 'p.regDate ASC',
      'date_desc' => 'p.regDate DESC',
    ];

    $order_by = isset($sort_columns[$sort_by]) ? $sort_columns[$sort_by] : 'p.regDate DESC';

    // Initial query
    $query = "SELECT SQL_CALC_FOUND_ROWS p.positionID, p.hostID, p.name, p.abbr, 
              p.status, p.regDate, p.modifiedDate
              FROM positions p
              WHERE p.hostID=?";
    $params = [$hostID];
    $types = 's';

    // Search query filter
    if (!empty($search_query)) {
      $query .= " AND (p.name LIKE ? OR p.abbr LIKE ?)";
      $search_query = '%' . $conn->real_escape_string($search_query) . '%';
      $params = array_merge($params, [$search_query, $search_query]);
      $types .= 'ss';
    }

    // Status filter
    if ($status === 'active') {
      $query .= " AND p.status = 'active'";
    } elseif ($status === 'inactive') {
      $query .= " AND p.status = 'inactive'";
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
    $positions = [];
    while ($row = $result->fetch_assoc()) {
      $positions[] = $row;
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
    echo json_encode(['positions' => $positions, 'pagination' => $pagination]);
  }

  // Call the function
  getPositionsWithParams($conn);

  exit();
}
//:::Get Position with pagination and other parameters(search_query, pageLimit, etc.) from the positions Table

//::: Add New Position
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prepareNewPosition'], $_POST['positionName'], $_POST['positionAbbr'], $_POST['positionStatus']) && $_POST['prepareNewPosition'] == true) {

  header('Content-Type: application/json');
  $hostID = $_SESSION['hostID'];
  $name = mysqli_real_escape_string($conn, $_POST['positionName']);
  $abbr = mysqli_real_escape_string($conn, $_POST['positionAbbr']);
  $status = mysqli_real_escape_string($conn, $_POST['positionStatus']);
  $positionID = "SOF-" . strtoupper(generateRandomAlphaNumericStrings(8));

  // Check if the position already exists in the database
  $checkDuplicateQuery = "SELECT * FROM positions WHERE name = ? AND hostID = ?";
  $stmt = $conn->prepare($checkDuplicateQuery);
  $stmt->bind_param("ss", $name, $hostID);
  $stmt->execute();
  $result = $stmt->get_result();
  $checkDuplicate = $result->fetch_assoc();
  $stmt->close();

  if (!empty($checkDuplicate)) {
    $status = 'warning';
    $header = 'Duplicate Entry!';
    $message = 'Position with this name already exists';
    $responseStatus = 'warning';
  } else {

    // Proceed with inserting the position if no duplicates are found and image uploaded successfully
    $insertQuery = "INSERT INTO positions (`positionID`, `hostID`, `name`, `abbr`, `status`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt === false) {
      die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the insert query
    $stmt->bind_param("sssss", $positionID, $hostID, $name, $abbr, $status);

    // Execute the insertion
    if ($stmt->execute()) {
      $status = 'success';
      $header = 'Successful!';
      $message = 'Position has been added successfully';
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
//::: Add New Position

//::: Get Position Information for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['getPositionEdit'], $_POST['positionID']) && $_POST['getPositionEdit'] == true) {
  $positionID = mysqli_real_escape_string($conn, $_POST['positionID']);
  $hostID = isset($_SESSION['hostID']) ? $_SESSION['hostID'] : '';
  $position = getPositionByID($conn, $positionID, $hostID);

  if (!empty($position)) {
?>
    <div class="modal-header">
      <h5 class="modal-title" id="positionModifyLabel">Modify <b><?= ucfirst($position['name']); ?></b></h5>
    </div>
    <form id="editPositionForm" class="needs-validation" novalidate>
      <div class="modal-body">
        <div class="row mt-3">

          <!-- Position Inputs -->
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-body py-3">
                  <div class="alert alert-primary" role="alert">
                    This position was added
                    <a href="javascript:void(0);" class="alert-link">
                      <?= structureTimestamp($position['regDate']); ?>
                    </a>
                    <?= !empty($position['modifiedDate']) && $position['modifiedDate'] != $position['regDate'] ? ' and was last modified ' . '<a href="javascript:void(0);" class="alert-link">' . structureTimestamp($position['modifiedDate']) . '.</a>' : ' and has not been modified yet.'; ?>
                  </div>
                  <div class="row mt-3">

                    <!--Edit Position Inputs-->
                    <div class="row">

                      <div class="form-group col-sm-6">
                        <div class="input-group">
                          <input type="hidden" name="positionID" value="<?= $position['positionID']; ?>" />
                          <label class="col-12" for="editPositionStatus">Status
                            <select class="form-control editModal-select" type="text" name="editPositionStatus" id="editPositionStatus" required="">
                              <option value="active" <?= $position['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                              <option value="inactive" <?= $position['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                          </label>
                        </div>
                      </div>

                      <div class="form-group col-sm-6">
                        <div class="input-group">
                          <label class="col-12" for="editPositionName">Name
                            <input class="form-control" type="text" name="editPositionName" id="editPositionName" placeholder="Enter Position Name" value="<?= $position['name']; ?>" required="" />
                          </label>
                        </div>
                      </div>

                      <div class="form-group col-sm-6">
                        <div class="input-group">
                          <label class="col-12" for="editPositionAbbr">Abbreviation
                            <input class="form-control" type="text" name="editPositionAbbr" id="editPositionAbbr" placeholder="Enter Position Abbreviation" value="<?= $position['abbr']; ?>" required="" />
                          </label>
                        </div>
                      </div>

                    </div>
                    <!--Edit Position Inputs-->
                  </div>

                </div>
              </div>
            </div>
          </div>
          <!-- Position Inputs -->

        </div>
      </div>
      <div class="modal-footer">
        <span id="editPositionMsg"></span>
        <button type="submit" class="btn btn-primary" id="saveEditPositionBtn">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>

    <script>
      $(document).ready(function() {
        //To allow the select2 input work as expected
        $('.editModal-select').select2({
          dropdownParent: $('#editPositionForm'),
        });

      });


      //::|| >>>>>>>>>>>>>::: 02: POSITIONS FUNCTIONS<<<<<<<<<<<<<<<<<<<
      //::: Function to edit position
      $("#editPositionForm").submit(function(e) {
        e.preventDefault();
        var positionForm = new FormData(this); // Simplified FormData initialization
        var formID = "editPositionForm";

        // Show error message if any of the form input is invalid
        if (!validateInput(formID)) {
          swal(
            "Required Fields",
            "Please fill out all required fields before submitting.",
            "error"
          );
          return; // Stop further execution if validation fails
        }

        positionForm.append("updatePositionRequest", true);
        swal({
            title: "Are you sure to continue?",
            text: "You are about updating the position information.",
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
              url: "controllers/get-positions",
              type: "POST",
              async: true,
              processData: false,
              contentType: false,
              data: positionForm,
              beforeSend: function() {
                $("#saveEditPositionBtn").prop("disabled", true);
                $("#saveEditPositionBtn").html("<i class='fa fa-spinner fa-spin'></i> Processing...").show();
              },
              success: function(response) {
                $("#saveEditPositionBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                var status = response.status;
                var message = response.message;
                var header = response.header;

                if (status !== "error" && status !== "warning") {
                  loadPositions(positionsCurrentPageNo); // Reload positions after successful submission
                  $("#editPositionImages").val(""); // Reset Image input after submission
                  // $("#editPositionForm")[0].reset(); // Reset form after submission
                  // $("#editCategoryID").val("").trigger("change"); //Reset Category Select2 dropdown
                  // $("#editSubCategoryID").val("").trigger("change"); // Reset Sub Category Select2 dropdown
                  //$(imageContainers).remove(); //Remove Position image preview container
                  // updateFileInput(); //Restore Image input to default

                  $("#saveEditPositionBtn").html("Save Changes").prop("disabled", false); // Disable button after successful submission
                }
                swal(header, message, status); // Display response message
              },
              error: function() {
                $("#saveEditPositionBtn").html("Save Changes").prop("disabled", false); // Re-enable button after request failure
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          });
      });
      //::|| >>>>>>>>>>>>>::: 02: POSITIONS FUNCTIONS<<<<<<<<<<<<<<<<<<
    </script>

<?php
  }

  exit();
}
//::: Get Position Information for Edit

//::: Update Position information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatePositionRequest'], $_POST['positionID']) && $_POST['updatePositionRequest'] == "true") {

  header("Content-Type: application/json");

  // Sanitize input fields
  $positionID = mysqli_real_escape_string($conn, $_POST['positionID']);
  $name = mysqli_real_escape_string($conn, $_POST['editPositionName']);
  $abbr = mysqli_real_escape_string($conn, $_POST['editPositionAbbr']);
  $status = mysqli_real_escape_string($conn, $_POST['editPositionStatus']);

  // Ensure required fields are not empty
  if (empty($positionID) || empty($name) || empty($abbr) || empty($status)) {
    echo json_encode([
      'header' => 'Missing Fields',
      'message' => 'All fields marked with * are required.',
      'status' => 'error'
    ]);
    exit();
  }

  // Retrieve the current position data
  $currentData = getPositionByID($conn, $positionID, $_SESSION['hostID']);
  if (!$currentData) {
    echo json_encode([
      'header' => 'Not Found',
      'message' => 'Position not found.',
      'status' => 'error'
    ]);
    exit();
  }

  // Compare each field to check if anything has changed
  $fieldsToUpdate = [];
  $params = [];

  // Array of field names and their corresponding variables
  $fieldMappings = [
    'name' => $name,
    'abbr' => $abbr,
    'status' => $status
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

  // Update the position if there are changes
  if (!empty($fieldsToUpdate)) {
    $updateQuery = "UPDATE positions SET " . implode(", ", $fieldsToUpdate) . ", `modifiedDate`=now() WHERE positionID = ? ";
    $params[] = $positionID;

    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if (!$stmt->execute()) {
      echo json_encode([
        'header' => 'Update Failed',
        'message' => 'Error updating position: ' . $stmt->error,
        'status' => 'error'
      ]);
      exit();
    }
    $stmt->close();
  }

  echo json_encode([
    'status' => 'success',
    'header' => 'Update Successful!',
    'message' => 'Position updated successfully.'
  ]);
  exit();
}
//::: Update Position information

//:: Delete Position 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['positionID'], $_POST["deletePositionRequest"]) && $_POST["deletePositionRequest"] == true) {
  // Check this tables where the deleteID exist to avoid system crash for other modules ::: 'TableName' => 'Column where deleteID is used'
  $referenceChecks = ['votes' => 'positionID'];
  $table = "positions";
  $uniqueColumn = "positionID";
  $deleteID = mysqli_real_escape_string($conn, $_POST['positionID']);
  $deleteResponse = deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Delete Position

//:: Force Delete Position :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['positionID'], $_POST["forceDeletePositionRequest"]) && $_POST["forceDeletePositionRequest"] == true) {
  // Reference tables where the deleteID exists to avoid system crash in other modules
  $referenceChecks = ['votes' => 'positionID'];

  $table = "positions"; // Main table where the position is stored
  $uniqueColumn = "positionID"; // Column that uniquely identifies the position
  $deleteID = mysqli_real_escape_string($conn, $_POST['positionID']); // Sanitizing the positionID input

  // Force delete from all tables
  $deleteResponse = forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  // Return JSON response
  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Force Delete Position :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
?>