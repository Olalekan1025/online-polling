<?php
session_start();
include("globalFunctions.php");

//::||Get System Users with pagination and other parameters(search_query, pageLimit, etc.) from the Products Table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pageLimit'], $_POST['query']) && !empty($_POST['pageLimit'])) {

  // Function to get system users with pagination and other parameters
  function getAdminsWithParams($conn)
  {
    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
    $limit = isset($_POST['pageLimit']) ? (int)$_POST['pageLimit'] : 10;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'a.lname';
    $roles = isset($_POST['roles']) ? $_POST['roles'] : [];

    // Determine sort order based on sort_by value
    $sort_order = in_array($sort_by, ['name_asc', 'email_asc', 'role_asc']) ? 'ASC' : 'DESC';

    $sort_columns = [
      'name_asc' => 'a.lname',
      'name_desc' => 'a.lname',
      'email_asc' => 'a.email',
      'email_desc' => 'a.email',
      'role_asc' => 'a.role',
      'role_desc' => 'a.role',
    ];

    // Pagination
    $offset = ($page - 1) * $limit;

    // Initial query
    $query = "SELECT SQL_CALC_FOUND_ROWS r.roleName AS adminRole, a.hostID, a.fname, a.lname, a.email, a.role, a.status FROM users a LEFT JOIN matrix_roles r ON r.roleID = a.role WHERE 1=1";

    // Add search query conditions
    if (!empty($search_query)) {
      $search_query = '%' . $conn->real_escape_string($search_query) . '%';
      $query .= " AND (a.hostID LIKE ? OR a.fname LIKE ? OR a.lname LIKE ? OR a.email LIKE ? OR r.roleName LIKE ?)";
    }

    // Add roles filter
    if (!empty($roles)) {
      $roles_placeholders = implode(',', array_fill(0, count($roles), '?'));
      $query .= " AND r.roleName IN ($roles_placeholders)";
    }

    // Filter by status if 'active' or 'inactive' is selected
    if ($sort_by === 'active') {
      $query .= " AND a.status = 'active'";
    } elseif ($sort_by === 'inactive') {
      $query .= " AND a.status = 'inactive'";
    } else {
      // Apply sorting based on other columns
      if (array_key_exists($sort_by, $sort_columns)) {
        $query .= " ORDER BY " . $sort_columns[$sort_by] . " $sort_order";
      } else {
        $query .= " ORDER BY a.lname $sort_order"; // Default sorting
      }
    }

    // Pagination
    $query .= " LIMIT ?, ?";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $params = [];
    $types = '';

    if (!empty($search_query)) {
      $params = array_fill(0, 5, $search_query);
      $types .= 'sssss';
    }

    if (!empty($roles)) {
      foreach ($roles as $role) {
        $params[] = $role;
        $types .= 's';
      }
    }

    // Bind pagination params
    $params[] = $offset;
    $params[] = $limit;
    $types .= 'ii';

    if (!empty($types)) {
      $stmt->bind_param($types, ...$params);
    }

    // Execute query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    $users = [];
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }

    // Get total number of results
    $stmt_total = $conn->query("SELECT FOUND_ROWS() as total");
    $total_data = $stmt_total->fetch_assoc()['total'];

    // Calculate start and end result numbers
    $start_result = $offset + 1;
    $end_result = min($offset + $limit, $total_data);

    // Calculate total pages
    $total_pages = ceil($total_data / $limit);

    // Prepare pagination data
    $pagination = [
      'current_page' => $page,
      'total_pages' => $total_pages,
      'page_limit' => $limit,
      'total_results' => $total_data,
    ];

    // Prepare output array
    $output = [
      'users' => $users,
      'pagination' => $pagination,
      'total_data' => $total_data,
      'start_result' => $start_result,
      'end_result' => $end_result,
    ];

    // Set content type and echo JSON encoded output
    header("Content-Type: application/json");
    echo json_encode($output);
  }

  // Call the function
  getAdminsWithParams($conn);

  exit();
}
//:::Get System Users with pagination and other parameters(search_query, pageLimit, etc.) from the Products Table

//::: Add New System Admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prepareNewSystemUser'])) {

  // Capture form inputs (assuming they have been sanitized)
  $prefix = mysqli_real_escape_string($conn, $_POST['prefix']);
  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $oname = mysqli_real_escape_string($conn, $_POST['oname']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  // $dob = mysqli_real_escape_string($conn, $_POST['dob']);
  $hireDate = mysqli_real_escape_string($conn, $_POST['hireDate']);
  // $biography = mysqli_real_escape_string($conn, $_POST['biography']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
  $role = mysqli_real_escape_string($conn, $_POST['role']);
  $hostID = "ad" . generateNumericStrings(8);
  $hashVerificationCode = password_hash($hostID, PASSWORD_DEFAULT);

  // Check if the email already exists in the database
  $checkDuplicate = getHostInfo($conn, $email);

  if (!empty($checkDuplicate)) {
    $status = 'warning';
    $header = 'Duplicate Entry!';
    $message = 'User with this email Already Exist';
    $responseStatus = 'warning';
  } else {
    // Proceed with inserting the admin if no duplicates are found
    $insertQuery = " INSERT INTO users  (hostID, prefix, fname, lname, oname, gender, email, phone, hireDate, status, role,  verificationCode)  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
    $stmt = $conn->prepare($insertQuery);

    //If role is available assign role to user on the matrix
    if (!empty($role)) {
      assignRoleToUser($conn, $hostID, $role);
    }

    if ($stmt === false) {
      die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters to the insert query
    $stmt->bind_param("ssssssssssss", $hostID, $prefix, $fname, $lname, $oname, $gender, $email, $phone, $hireDate, $status, $role, $hashVerificationCode);

    // Execute the insertion
    if ($stmt->execute()) {

      $otherPlaceholderArray = [];
      //Send Verification Email
      $subject = "Password Reset";
      $emailTitle = "Notification - Password Reset";
      $template_file = "../../emailTemplates/adminResetPasswordEmail.php";
      $name =  $fname;
      $siteAddress = "https://poll.homdroid.com/";
      $customURL = $siteAddress . "reset-password?token=" . generateRandomAlphaNumericStrings(100) . "&&isVerifyCode=" . $hashVerificationCode . "&&dataQuery=admin&&userEmail=" . $email . "&&redirectLink=" . $siteAddress . "index";
      $customText = "";
      sendEmail($email, $subject, $emailTitle, $name, $siteAddress, $customURL, $customText, $template_file, $otherPlaceholderArray);

      // Take Activity Log
      $userId = $email;
      $eventType = "sentPasswordResetLink";
      $eventDescription = $email . " Admin Received A Password Reset Link  ";
      $status = "success";
      $errorMessage = null;
      $logActivity = insertLogEvent($conn, $userId, $eventType, $eventDescription, $status, $errorMessage);

      //Return Query Feedback
      $status = 'success';
      $header = 'Successful!';
      $message = 'System User Has Been Added Successfully';
      $responseStatus = 'success';
    } else {
      $status = 'error';
      $header = 'Failed!';
      $message = 'An error occurred, try again' . $stmt->error;
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
//::: Add New System Admin

//::: Get System Users Information for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['getSystemUsersEdit']) && $_POST['getSystemUsersEdit'] == true) {
  $hostID = mysqli_real_escape_string($conn, $_POST['hostID']);
  $systemUser = getHostInfo($conn, $hostID);
  if (!empty($systemUser)) {
?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Modify <?= ucfirst($systemUser['fname'])  . " " . ucfirst($systemUser['lname']); ?> Profile</h5>
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    </div>
    <form id="systemUserEditForm">
      <!--Department Inputs-->
      <div class="col-12 mt-3">
        <div class="card">
          <div class="card-content">
            <div class="card-body py-5">
              <div class="row mt-3">

                <!-- System User Inputs -->
                <input type="hidden" value="<?= $systemUser['hostID']; ?>" name="hostID" />

                <!-- Prefix -->
                <div class="form-group col-sm-6">
                  <label for="editPrefix">Prefix<span class="text-danger">*</span></label>
                  <select class="form-control" id="editPrefix" name="editPrefix" required>
                    <option value="" disabled>Select Prefix</option>
                    <option value="mr." <?= $systemUser['prefix'] == 'mr.' ? 'selected' : ''; ?>>Mr.</option>
                    <option value="mrs." <?= $systemUser['prefix'] == 'mrs.' ? 'selected' : ''; ?>>Mrs.</option>
                    <option value="miss." <?= $systemUser['prefix'] == 'miss.' ? 'selected' : ''; ?>>Miss</option>
                    <option value="dr." <?= $systemUser['prefix'] == 'dr.' ? 'selected' : ''; ?>>Dr.</option>
                    <option value="prof." <?= $systemUser['prefix'] == 'prof.' ? 'selected' : ''; ?>>Prof.</option>
                    <!-- Add more as needed -->
                  </select>
                </div>

                <!-- Gender -->
                <div class="form-group col-sm-6">
                  <label for="editGender">Gender<span class="text-danger">*</span></label>
                  <select class="form-control" id="editGender" name="editGender" required>
                    <option value="" disabled>Select Gender</option>
                    <option value="Male" <?= $systemUser['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?= $systemUser['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                  </select>
                </div>

                <!-- Status -->
                <div class="form-group col-sm-6">
                  <label for="editStatus">Status<span class="text-danger">*</span></label>
                  <select class="form-control" id="editStatus" name="editStatus" required>
                    <option value="" disabled>Select Status</option>
                    <option value="inactive" <?= $systemUser['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="active" <?= $systemUser['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                  </select>
                </div>

                <!-- Role -->
                <div class="form-group col-sm-6">
                  <label for="editRole">Role</label>
                  <select class="form-control" id="editRole" name="editRole">
                    <option value="" selected>Select User Role</option>
                    <?php
                    $roles = getRoles($conn);
                    foreach ($roles as $role) {
                      // Only list 'superAdmin' role if the current session role is 'superAdmin'
                      if ($role['roleName'] == 'superAdmin' && $_SESSION["adminRole"] != 'superAdmin') {
                        continue; // Skip 'superAdmin' if the current session role is not 'superAdmin'
                      }
                    ?>
                      <option value="<?= $role['roleID']; ?>" <?= $systemUser['role'] == $role['roleID'] ? 'selected' : ''; ?>>
                        <?= ucfirst($role['roleName']); ?>
                      </option>
                    <?php } ?>

                  </select>
                </div>

                <!-- Email -->
                <div class="form-group col-sm-6">
                  <label for="editEmail">Email<span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="editEmail" name="editEmail" placeholder="Enter Email" value="<?= htmlspecialchars($systemUser['email']); ?>" required>
                </div>

                <!-- Password -->
                <div class="form-group col-sm-6">
                  <label for="editPassword">Password</label>
                  <input type="password" class="form-control" id="editPassword" name="editPassword" placeholder="Enter Password" />
                </div>

                <!-- First Name -->
                <div class="form-group col-sm-6">
                  <label for="editFname">First Name<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editFname" name="editFname" placeholder="Enter First Name" value="<?= htmlspecialchars($systemUser['fname']); ?>" required>
                </div>

                <!-- Last Name -->
                <div class="form-group col-sm-6">
                  <label for="editLname">Last Name<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="editLname" name="editLname" placeholder="Enter Last Name" value="<?= htmlspecialchars($systemUser['lname']); ?>" required>
                </div>

                <!-- Other Name -->
                <div class="form-group col-sm-6">
                  <label for="editOname">Other Name</label>
                  <input type="text" class="form-control" id="editOname" name="editOname" placeholder="Enter Other Name" value="<?= htmlspecialchars($systemUser['oname']); ?>">
                </div>

                <!-- Phone -->
                <div class="form-group col-sm-6">
                  <label for="editPhone">Phone</label>
                  <input type="text" class="form-control" id="editPhone" name="editPhone" placeholder="Enter Phone Number" value="<?= htmlspecialchars($systemUser['phone']); ?>">
                </div>

                <!-- Date of Birth -->
                <div class="form-group col-sm-6">
                  <label for="editDob">Date of Birth</label>
                  <input type="date" class="form-control" id="editDob" name="editDob" value="<?= htmlspecialchars(!empty($systemUser['dob'])); ?>">
                </div>

                <!-- Hire Date -->
                <div class="form-group col-sm-6">
                  <label for="editHireDate">Hire Date</label>
                  <input type="date" class="form-control" id="editHireDate" name="editHireDate" value="<?= htmlspecialchars(!empty($systemUser['hireDate'])); ?>">
                </div>

                <!-- Salary -->
                <!-- <div class="form-group col-sm-6">
                            <label for="editSalary">Salary<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editSalary" name="editSalary" placeholder="Enter Salary" value="<? //= htmlspecialchars($systemUser['salary']); 
                                                                                                                                          ?>" required>
                        </div> -->

                <!-- Biography -->
                <div class="form-group col-sm-12">
                  <label for="editBiography">Biography</label>
                  <textarea class="form-control" id="editBiography" name="editBiography" placeholder="Enter Biography"><?= htmlspecialchars($systemUser['biography']); ?></textarea>
                </div>
              </div>
              <?php if ($_SESSION['adminRole'] == "superAdmin") { ?>
                <center><button type="button" class="btn btn-danger" onClick="forceDeleteSystemUser(this);" data-value="<?= htmlspecialchars($systemUser['hostID']); ?>">Force Delete Permanently From Portal </button></center>
              <?php } ?>
              <!-- System User Inputs -->
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <span id="updateSystemUserMsg"></span>
          <button type="submit" class="btn btn-primary" id="editUpdateSystemUserBtn">Update System User</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
    <script>
      $("#systemUserEditForm").submit(function(e) {
        e.preventDefault();
        var systemUserEditForm = new FormData(this);
        var systemUsersCurrentPageNo = localStorage.getItem("systemUsersCurrentPageNo") > 0 ? localStorage.getItem("systemUsersCurrentPageNo") : 1; // Load the current page from localStorage if it exists
        systemUserEditForm.append("updateSystemUserRequest", true);

        swal({
            title: "Are you sure you want to update this System User?",
            text: "Updating this user will reflect the changes across the portal.",
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
                  loadSystemUsers(systemUsersCurrentPageNo);
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
                $("#editUpdateSystemUserBtn").html("Update System User").show(); // Reset the button text
              }
            });
          });
      });
    </script>

<?php
  }

  exit();
}
//::: Get System Users Information for Edit

//::: Update System User information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateSystemUserRequest']) && $_POST['updateSystemUserRequest'] == true) {
  $hostID = mysqli_real_escape_string($conn, $_POST['hostID']);

  // Retrieve current user data
  $currentData = getHostInfo($conn, $hostID);

  // Retrieve and sanitize form inputs
  $fname = trim($_POST['editFname'] ?? '');
  $lname = trim($_POST['editLname'] ?? '');
  $oname = trim($_POST['editOname'] ?? '');
  $gender = trim($_POST['editGender'] ?? '');
  $email = trim($_POST['editEmail'] ?? '');
  $phone = trim($_POST['editPhone'] ?? '');
  $password = $_POST['editPassword'] ?? '';
  $editLinkedinLink = $_POST['editLinkedinLink'] ?? '';
  $editFacebookLink = $_POST['editFacebookLink'] ?? '';
  $editTwitterLink = $_POST['editTwitterLink'] ?? '';

  // Check for duplicate email or phone in the database
  $duplicateCheckQuery = "SELECT hostID FROM users WHERE (email = ? OR phone = ?) AND hostID != ?";
  $duplicateCheckStmt = $conn->prepare($duplicateCheckQuery);
  $duplicateCheckStmt->bind_param("sss", $email, $phone, $hostID);
  $duplicateCheckStmt->execute();
  $duplicateResult = $duplicateCheckStmt->get_result();
  $duplicateCheckStmt->close();

  if ($duplicateResult->num_rows > 0) {
    // Duplicate found
    $status = false;
    $header = 'Duplicate Entry!';
    $message = 'A user with this email or phone number already exists.';
    $responseStatus = 'warning';
  } else {
    // Check if any fields have changed
    $hasChanges = (
      $fname !== trim($currentData['fname']) || $phone !== trim($currentData['phone']) ||
      $lname !== trim($currentData['lname']) || $oname !== trim($currentData['oname']) ||
      $gender !== trim($currentData['gender']) || $email !== trim($currentData['email']) ||
      $gender !== trim($currentData['linkedinLink']) || $email !== trim($currentData['facebookLink']) ||
      $email !== trim($currentData['twitterLink']) ||
      (!empty($password) && !password_verify($password, $currentData['password']))
    );

    if (!$hasChanges) {
      $status = false;
      $header = 'No Changes!';
      $message = 'There is nothing to update.';
      $responseStatus = 'warning';
    } else {
      // Update the record
      $updateQuery = "UPDATE users SET  fname = ?, lname = ?, oname = ?, gender = ?, email = ?, phone = ?, linkedinLink =?, facebookLink = ?, twitterLink = ?, password = ? WHERE hostID = ?";
      $stmt = $conn->prepare($updateQuery);

      // Use the current password if not updated
      $passwordToSave = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $currentData['password'];

      $stmt->bind_param("sssssssssss", $fname, $lname, $oname, $gender, $email, $phone, $editLinkedinLink, $editFacebookLink, $editTwitterLink, $passwordToSave, $hostID);

      if ($stmt->execute()) {
        $status = true;
        $header = 'Successful!';
        $message = 'System User has been updated successfully.';

        // Assign role to user in the matrix
        // if (!empty($role)) {
        //   assignRoleToUser($conn, $hostID, $role);
        // }

        $responseStatus = 'success';
      } else {
        $status = false;
        $header = 'Failed!';
        $message = 'An error occurred, try again: ' . $stmt->error;
        $responseStatus = 'error';
      }

      $stmt->close();
    }
  }

  // Prepare and return response
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
//::: Update System User information


//::: Check Admin Profile Page Old Password Entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmOldPassword'])) {
  if (isset($_POST['oldPassword'])) {
    $passwordEntry = mysqli_real_escape_string($conn, $_POST['oldPassword']);
    $stmt = $conn->prepare("SELECT password FROM users WHERE hostID = ? ");
    $stmt->bind_param("s", $_SESSION['adminID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $getEntry = $result->fetch_array();
    $stmt->close();

    if (!password_verify($passwordEntry, $getEntry['password'])) {
      $response = array("status" => true, "message" => "Incorrect Password");
    } else {
      $response = array("status" => false, "message" => "Password Confirmed");
    }
  } else {
    $response = array("status" => false, "message" => "Password Not Provided");
  }
  header("Content-Type: application/json");
  echo json_encode($response);
  exit();
}
//::: Check Admin Profile Page Old Password Entry

//:: Delete System User 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hostID']) && $_POST["deleteSystemUserRequest"] == true) {
  // Check this tables where the deleteID exist to avoid system crash for other modules ::: 'TableName' => 'Column where deleteID is used'
  $referenceChecks = ['matrix_user_roles' => 'hostID'];
  $table = "users";
  $uniqueColumn = "hostID";
  $deleteID = mysqli_real_escape_string($conn, $_POST['hostID']);
  $deleteResponse = deleteFromTable($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  //$response = array("status" => true, "message" => $deleteResponse['message']);

  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Delete System User

//:: Force Delete System User :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hostID']) && $_POST["forceDeleteSystemUserRequest"] == true) {
  // Reference tables where the deleteID exists to avoid system crash in other modules
  $referenceChecks = ['matrix_user_roles' => 'hostID'];

  $table = "users"; // Main table where the user is stored
  $uniqueColumn = "hostID"; // Column that uniquely identifies the user
  $deleteID = mysqli_real_escape_string($conn, $_POST['hostID']); // Sanitizing the hostID input

  // Force delete from all tables
  $deleteResponse = forceDeleteFromTables($conn, $table, $uniqueColumn, $deleteID, $referenceChecks);

  // Return JSON response
  header("Content-Type: application/json");
  echo json_encode($deleteResponse);
  exit();
}
//:: Force Delete System User :::|||THIS FUNCTION IS STRICTLY FOR THE SUPER-ADMIN AND SHOULD BE USED CAREFULLY 
?>