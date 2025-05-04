<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "live-charts";
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
                            <h4 class="mb-0">Poll live Reports</h4>
                        </div>

                        <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
                            <li class="breadcrumb-item active"><a href="<?php echo $page; ?>"><?php echo ucfirst($page); ?></a></li>
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- END: Breadcrumbs-->

            <!-- START: Card Data-->
            <div class="card card-body col-12">
                <div class="row">
                    <div class="col-12 mt-3 d-flex flex-column">
                        <div class="card w-100">
                            <div class="card-body">
                                <?php if (!isset($_GET['id'])  || empty($_GET['id'])) { ?>
                                    <div class="d-flex flex-column align-items-center mb-3">
                                        <label for="pollIDInput" class="mb-2 "><b>Kindly Enter Poll ID</b></label>
                                        <input id="pollIDInput" class="form-control col-12 col-md-4 text-center mb-2" type="text" placeholder="Enter Poll ID" />
                                        <button onclick="getLiveStatisticsData();" class="btn btn-warning">Load Live Charts</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Load Data Table -->
                                    <div class="card-body" id="displayLiveStatisticsData"> </div>
                                    <!-- Load Data Table -->

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-6 mt-3">
                        <div class="card">

                            <div class="card overflow-hidden">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 h5 font-w-600">Leading Candidates By Position</h6>
                                </div>
                                <!-- Load Data Table -->
                                <div class="card-body" id="displayLeadingCandidates" style="height:30rem; overflow:auto"> </div>
                                <!-- Load Data Table -->

                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-12 mt-2">
                        <!-- Positions Selection Tabs -->
                        <div class="profile-menu mt-4 theme-background border z-index-1 p-2">
                            <div class="d-sm-flex">
                                <div class="align-self-center">
                                    <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                                        <?php
                                        $pollID = isset($_GET['id']) ? $_GET['id'] : (isset($_SESSION['pollID']) ? $_SESSION['pollID'] : null);
                                        $pollID = htmlspecialchars($pollID, ENT_QUOTES, 'UTF-8');
                                        $getPollInfo = getPollByID($conn, $pollID ?? '');
                                        $hostID = isset($getPollInfo['hostID']) ? $getPollInfo['hostID'] : '';

                                        // Fetch poll positions
                                        $pollPositions = getPositionsByHostID($conn, $hostID);
                                        $positionNum = 0;
                                        $hasValidPositions = false;

                                        if (empty($pollPositions)) { ?>
                                            <li class="nav-item ml-0">
                                                <span class="nav-link py-2 px-3 px-lg-4 text-muted">
                                                    No positions available for this poll.
                                                </span>
                                            </li>
                                            <?php } else {
                                            foreach ($pollPositions as $position) {
                                                $positionId = $position['positionID'];

                                                // Get candidates for the position
                                                $pollCandidates = getCandidatesForPosition($conn, $pollID, $hostID, $positionId);

                                                // Skip positions with no candidates
                                                if (empty($pollCandidates)) {
                                                    continue;
                                                }

                                                $hasValidPositions = true;
                                                $positionNum++;
                                            ?>
                                                <li class="nav-item ml-0" data-value="<?php echo $positionId; ?>" onClick="loadPositionLiveResult(this);" id="navItemLink">
                                                    <a class="nav-link py-2 px-3 px-lg-4 <?php echo (($positionNum == 1) ? 'active' : '') ?>" data-toggle="tab" id="position<?php echo $positionNum; ?>Tab" href="#position<?php echo $positionNum; ?>">
                                                        <i><?php echo $positionNum; ?>.</i> <?php echo ucfirst($position['name']); ?>
                                                    </a>
                                                </li>
                                            <?php }
                                            if (!$hasValidPositions) { ?>
                                                <li class="nav-item ml-0">
                                                    <span class="nav-link py-2 px-3 px-lg-4 text-muted">
                                                        No positions with candidates available for this poll.
                                                    </span>
                                                </li>
                                        <?php }
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Positions Selection Tabs -->
                        <div class="tab-content" style="min-height:30rem">
                            <?php
                            $positionNum = 0;
                            $hasValidPositions = false;

                            if (!empty($pollPositions)) {
                                foreach ($pollPositions as $position) {
                                    $positionId = $position['positionID'];
                                    $positionName = $position['name'];

                                    // Get candidates for the position
                                    $pollCandidates = getCandidatesForPosition($conn, $pollID, $hostID, $positionId);

                                    // Skip positions with no candidates
                                    if (empty($pollCandidates)) {
                                        continue;
                                    }

                                    $hasValidPositions = true;
                                    $positionNum++;
                            ?>
                                    <!-- Position Active Tab Starts -->
                                    <div id="position<?php echo $positionNum; ?>" class="tab-pane fade <?php echo (($positionNum == 1) ? 'show active' : '') ?>">
                                        <div class="card">
                                            <div class="card-header justify-content-between align-items-center">
                                                <h4 class="card-title">Candidates For <?php echo ucfirst($positionName); ?></h4>
                                            </div>
                                            <!-- Display Result Table Data -->
                                            <div class="card-body" id="displayPositionLiveResult<?php echo $positionId; ?>">
                                                <?php
                                                // Constructing data for display
                                                $candidatesData = [];
                                                foreach ($pollCandidates as $candidate) {
                                                    $candidateId = $candidate['candidateID'];
                                                    $initial = isset($candidate['sname']) ? substr($candidate['sname'], 0, 1) : '';
                                                    $abbreviatedName = $initial . '. ' . $candidate['fname'];

                                                    // Fetch votes for the candidate
                                                    $stmt = $conn->prepare("SELECT COUNT(*) as voteCount FROM votes WHERE candidateID = ? AND position = ? AND pollID = ? AND hostID = ?");
                                                    $stmt->bind_param("ssss", $candidateId, $positionId, $pollID, $hostID);
                                                    $stmt->execute();
                                                    $voteResult = $stmt->get_result();
                                                    $votesForCandidate = $voteResult->fetch_assoc()['voteCount'] ?? 0;

                                                    $candidatesData[] = ['name' => $abbreviatedName, 'votes' => $votesForCandidate];
                                                }

                                                // Display the data
                                                foreach ($candidatesData as $data) {
                                                    echo "<p>{$data['name']}: {$data['votes']} votes</p>";
                                                }
                                                ?>
                                            </div>
                                            <!-- Display Result Table Data -->
                                        </div>
                                    </div>
                                    <!-- Position Active Tab Ends -->
                                <?php
                                }
                            }

                            if (!$hasValidPositions) { ?>
                                <div class="alert alert-warning text-center">
                                    <strong>No positions with candidates available for this poll.</strong>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div> -->

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

    <!-- END: Template JS-->

    <!-- START: APP JS-->
    <script src="dist/js/app.js"></script>
    <!-- END: APP JS-->


    <script src="dist/vendors/lineprogressbar/jquery.lineProgressbar.js"></script>
    <script src="dist/vendors/lineprogressbar/jquery.barfiller.js"></script>


</body>
<!-- END: Body-->

<script>
    $(document).ready(function() {
        getLiveStatisticsData(); //Load Live Statistics Data Table at default

        var firstTab = $("#navItemLink");
        loadPositionLiveResult(firstTab); // Call function with the initial positionID
    });

    //Function to get data value from tab link >>> Starts
    function getPositionID(tab) {
        return $(tab).data("value");
    }
    //Function to get data value from tab link >>> ENds
    // Check if pollID is in session and set JavaScript variable accordingly
    var isPollIDInSession = <?= isset($_SESSION['pollID']) && !empty($_SESSION['pollID']) ? 'true' : 'false'; ?>;

    //Get Positions Live Results For all Candidates >>> Starts
    function loadPositionLiveResult(tab) {
        var positionID = getPositionID(tab);
        var pollID = "<?= isset($_GET['id']) && !empty($_GET['id'])  ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : ''; ?>";
        if (!pollID) {
            pollID = $("#pollIDInput").val();
            $.ajax({
                url: "controllers/get-live-charts",
                type: "POST",
                data: {
                    setPollIDSession: true,
                    pollID: pollID
                },
                success: function(liveResponse) {
                    if (isPollIDInSession == 'false' && liveResponse.status == "success") {
                        location.reload();
                        isPollIDInSession = 'true'; // Update the variable to reflect that pollID is now in session
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
        // console.log(positionID);
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getPositionResults: 1,
                positionID: positionID,
                pollID: pollID
            },
            success: function(olr) {
                $("#displayPositionLiveResult" + positionID).html(olr).show();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    var firstTab = $("#navItemLink");
                    loadPositionLiveResult(firstTab); // Call function with the initial positionID
                }, 5000);
            }
        });
    }
    //Get Positions Live Results For all Candidates >>> ENds

    //Get Leading Candidates Table >>> Starts
    function getLeadingCandidates() {
        var pollID = "<?= isset($_GET['id']) && !empty($_GET['id'])  ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : ''; ?>";
        if (!pollID) {
            pollID = $("#pollIDInput").val();
        }
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getLeadingCandidate: 1,
                pollID: pollID
            },
            success: function(lcads) {
                $("#displayLeadingCandidates").html(lcads).show();
            },
            complete: function() {
                var firstTab = $("#navItemLink");
                loadPositionLiveResult(firstTab);

            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    getLeadingCandidates();
                }, 5000);
            }
        });
    }
    //Get Leading Candidates Table >>> Ends

    //Get Live Statistics Data >>> Starts
    function getLiveStatisticsData() {
        var pollID = "<?= isset($_GET['id']) && !empty($_GET['id'])  ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : ''; ?>";
        if (!pollID) {
            pollID = $("#pollIDInput").val();
        }
        $.ajax({
            url: "controllers/get-live-charts",
            type: "POST",
            async: false,
            data: {
                getLiveStatisticData: 1,
                pollID: pollID
            },
            success: function(dlsd) {
                $("#displayLiveStatisticsData").html(dlsd).show();
            },
            complete: function() {
                getLeadingCandidates();

            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                setTimeout(function() {
                    getLiveStatisticsData();
                }, 5000);
            }
        });
    }

    setInterval(getLiveStatisticsData, 5000); // Call every 5000 milliseconds (10 seconds)
    //Get Live Statistics Data >>> Ends
</script>

<?php unset($_SESSION['pollID']) ?>

</html>