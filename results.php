<?php
session_start();
if (!isset($_SESSION['hostID']) || !isset($_SESSION['hostEmail']) || !isset($_SESSION['portalAccess'])) { //Correct condition to be adjusted in other projects
    header("location:./");
}

$page = "results";
$_SESSION["adminPreviousPage"] = $page;
// $pageAccess =  "manage candidates";
// $failedAccessRedirect = "./dashboard";

?>

<!DOCTYPE html>
<html lang="en">

<!-- START: Head-->
<?php include("inc/headTag.php");
?>
<!-- END Head-->
<!-- START: Body-->

<body id="main-container" class="default compact-menu">

    <!-- START: Header-->
    <?php include("inc/header.php");
    ?>
    <!-- END: Header-->

    <!-- START: Main Menu-->
    <?php include("inc/sidebar.php");
    ?>
    <!-- END: Main Menu-->

    <!-- START: Main Content-->
    <main>
        <div class="container-fluid site-width">
            <!-- START: Breadcrumbs-->
            <div class="row ">
                <div class="col-12  align-self-center">
                    <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
                        <div class="w-sm-100 mr-auto">
                            <h4 class="mb-0">Poll Results</h4>
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
            <div class="row">
                <!-- <div class="card card-body col-12">
                    <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div>
                </div> -->

                <div class="col-12 mt-3 d-flex flex-column">
                    <div class="card w-100" style="min-height: 100vh;">
                        <div class="card-body">
                            <?php if (!isset($_GET['id'])  || empty($_GET['id'])) { ?>
                                <div class="d-flex flex-column align-items-center mb-3">
                                    <label for="pollIDInput" class="mb-2 "><b>Kindly Enter Poll ID</b></label>
                                    <input id="pollIDInput" class="form-control col-12 col-md-4 text-center mb-2" type="text" placeholder="Enter Poll ID" />
                                    <button onclick="getPollResults();" class="btn btn-warning">Load Poll Result</button>
                                </div>
                            <?php } ?>
                            <div class="container" style="width: 100%;">
                                <div class="card-header mb-4 d-flex justify-content-between align-items-center">
                                    <h2 class="card-title" id="result-header">Poll Result</h2>
                                    <button class="btn btn-danger btn-sm" id="download-chart-btn" onclick="downloadCharts();">Download Result</button>
                                </div>
                                <div id="charts-container" class="row justify-content-center"> </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <!-- END: Card DATA-->
        </div>
    </main>
    <!-- END: Content-->



    <!-- START: Footer-->
    <footer>
        <?php include("inc/footer.php");
        ?>
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

    <!-- START: Page Vendor JS-->
    <script src="dist/vendors/raphael/raphael.min.js"></script>
    <script src="dist/vendors/morris/morris.min.js"></script>
    <!-- END: Page Vendor JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

</body>
<script>
    $(document).ready(function() {
        getPollResults();
    });

    //Function to get poll Results
    function getPollResults() {
        var primarycolor = getComputedStyle(document.body).getPropertyValue('--primarycolor');
        var pollID = "<?= isset($_GET['id']) && !empty($_GET['id'])  ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : ''; ?>";
        if (!pollID) {
            pollID = $("#pollIDInput").val();
        }

        // Use AJAX to fetch data for each position
        $.ajax({
            url: 'controllers/get-results',
            type: 'POST',
            dataType: 'json',
            data: {
                get_poll_result: true,
                pollID: pollID
            },
            success: function(data) {

                if (!data || typeof data !== 'object' || !Array.isArray(data) || data.length === 0) {
                    $("#download-chart-btn").css("display", "none"); // Hide the button when no data
                    $('#charts-container').html(`
                        <div class="card border-danger shadow-lg">
                            <div class="card-body alert alert-danger text-center">
                                <h5 class="card-title">Unable to Retrieve Results</h5>
                                <p class="card-text">The results could not be retrieved. Please ensure the Poll ID is valid or try again once the poll is closed.</p>
                            <a class='btn btn-danger btn-sm' href='./results'>Reload</a>
                            </div>
                        </div>
                    `);
                    return;
                } else {
                    $("#download-chart-btn").css("display", "flex"); // Show the button when there is data
                }

                if (data.message && data.message.length > 0) {
                    $("#download-chart-btn").css("display", "none");

                    $('#charts-container').html(`
                        <div class="card border-danger shadow-lg">
                            <div class="card-body alert alert-danger text-center">
                                <h5 class="card-title">Unable to Retrieve Results</h5>
                                <p class="card-text">The results could not be retrieved. Please ensure the Poll ID is valid or try again once the poll is closed.</p>
                            </div>
                        </div>
                    `);

                    return;
                }

                // Clear any existing error messages or previous content
                $('#charts-container').empty();

                // Iterate over each position's data
                $.each(data, function(index, positionData) {
                    // Generate a unique ID for the chart container
                    var chartId = 'chart-' + index;

                    // Create a div for the chart box with inline-block display
                    var chartBox = $('<div class="chart-box col-md-6"></div>');

                    // Create a div for the chart title within the chart box
                    var chartTitle = $('<div class="chart-title h2"><b>' + positionData.position + '</b></div>');

                    // Prepend the chart title before appending the chart container
                    chartBox.prepend(chartTitle);

                    // Create a chart container within the chart box
                    var chartContainer = $('<div id="' + chartId + '" class="chart"></div>').appendTo(chartBox);

                    // Append the chart box to the charts-container
                    $('#charts-container').append(chartBox);

                    // Generate the Poll Header
                    $("#result-header").html(positionData.poll_title + " Poll Results");

                    // Rendering Morris chart for the current position
                    if ($('#' + chartId).length > 0) {
                        Morris.Bar({
                            element: chartId,
                            data: positionData.candidates,
                            xkey: 'name',
                            ykeys: ['votes'],
                            labels: ['Votes'],
                            barColors: function(row, series, type) {
                                if (row.y > 0) {
                                    return '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6); // Random color
                                }
                                return '#ccc'; // Gray color for zero votes
                            },
                            barRatio: 0.4,
                            xLabelAngle: 35,
                            hideHover: 'auto',
                            ymin: 0,
                            // Custom label function to add a class to candidate names
                            xLabelMargin: 10,
                            barLabelFontFamily: 'Arial',
                            barLabelFontSize: 14,
                            barLabelAlign: 'center',
                            barLabelFormat: function(y, data) {
                                return '<div class="candidate-name">' + data.x + '</div>';
                            }
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error, xhr);
            }
        });
    }

    //Download Result charts
    function downloadCharts() {
        const chartsContainer = document.getElementById('charts-container');
        const pollTitle = document.getElementById("result-header").innerText;

        if (!chartsContainer || chartsContainer.children.length === 0) {
            alert("No charts available to download!");
            return;
        }

        // Use html2canvas to capture the charts-container as an image
        html2canvas(chartsContainer).then(canvas => {
            // Convert the canvas to a data URL
            const imageData = canvas.toDataURL("image/png");

            // Create a temporary link to trigger the download
            const link = document.createElement('a');
            link.href = imageData;
            link.download = pollTitle.replace(/\s+/g, '_') + '_charts.png'; // Specify the file name for download

            // Simulate a click event on the link to trigger the download
            link.click();
        }).catch(error => {
            console.error("Error capturing charts:", error);
            alert("An error occurred while trying to download the charts.");
        });
    }
</script>
<!-- END: Body-->

</html>