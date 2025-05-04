<?php
session_start();

if (!isset($_SESSION['hostID']) && !isset($_SESSION['hostEmail']) && !isset($_SESSION['portalAccess'])) {
	header("location:./");
	$_SESSION["logMsg"] = "Please login to continue";
}

$page = "contact";
?>
<!DOCTYPE html>
<html lang="en-US">

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
							<h4 class="mb-0">Contact us</h4>
						</div>

						<ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
							<li class="breadcrumb-item">Home</li>
							<li class="breadcrumb-item">Page</li>
							<li class="breadcrumb-item active"><a href="./contact">Contact us</a></li>
						</ol>
					</div>
				</div>
			</div>
			<!-- END: Breadcrumbs-->

			<!-- START: Card Data-->
			<div class="row">
				<div class="col-12  mt-3">
					<div class="card">
						<div class="card-body">
							<iframe class="w-100 height-350 border-0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2486.098932085383!2d-0.24952586934541404!3d51.456339998872494!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48760ee6c0c242eb%3A0x3a5665ce81752bbf!2sUniversity%20of%20Roehampton%20London!5e0!3m2!1sen!2sng!4v1744403741118!5m2!1sen!2sng" allowfullscreen=""></iframe>
						</div>
					</div>
				</div>

				<!-- <div class="col-12 col-md-6 mt-4">
					<div class="card">
						<div class="card-header  justify-content-between align-items-center">
							<h4 class="card-title">Feedback</h4>
						</div>
						<div class="card-body">
							<form>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Name:">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Email:">
								</div>
								<div class="form-group">
									<textarea class="form-control" placeholder="Message:"></textarea>
								</div>


								<a href="#" class="btn btn-primary btn-default">Submit</a>
							</form>
						</div>
					</div>
				</div> -->
				<div class="col-12 col-md-6 mt-4">
					<div class="card">
						<div class="card-header  justify-content-between align-items-center">
							<h4 class="card-title">Contact us</h4>
						</div>
						<div class="card-body">
							<?php

							$adminInfo = getHostInfo($conn, $_SESSION["hostID"]);
							?>
							<address>
								<strong>University of Roehampton</strong><br>
								Roehampton Ln, London SW15 5PH,<br>
								United Kingdom<br>
								<abbr title="Phone Number">P: </abbr> <?= !empty($adminInfo['phone']) ? $adminInfo['phone'] : ''; ?>
							</address>
							<address>
								<strong>Email</strong><br>
								<a href="mailto:#" class="redial-primary redial-font-weight-600"><?= !empty($adminInfo['email']) ? $adminInfo['email'] : ''; ?> </a>
							</address>
							<div class="text-left">

								<a href="#" class="btn btn-social btn-dropbox text-white mb-2">
									<i class="ion ion-social-dropbox"></i>
								</a>
								<a href="#" class="btn btn-social btn-facebook text-white mb-2">
									<i class="ion ion-social-facebook"></i>
								</a>
								<a href="#" class="btn btn-social btn-github text-white mb-2">
									<i class="ion ion-social-github"></i>
								</a>
								<a href="#" class="btn btn-social btn-google text-white mb-2">
									<i class="ion ion-social-google"></i>
								</a>
								<a href="#" class="btn btn-social btn-instagram text-white mb-2">
									<i class="ion ion-social-instagram"></i>
								</a>
								<a href="#" class="btn btn-social btn-linkedin text-white mb-2">
									<i class="ion ion-social-linkedin"></i>
								</a>
								<a href="#" class="btn btn-social btn-pinterest text-white mb-2">
									<i class="ion ion-social-pinterest"></i>
								</a>
								<a href="#" class="btn btn-social btn-tumblr text-white mb-2">
									<i class="ion ion-social-tumblr"></i>
								</a>
								<a href="#" class="btn btn-social btn-twitter text-white mb-2">
									<i class="ion ion-social-twitter"></i>
								</a>
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
	<?php include("inc/footer.php"); ?>
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
	<script src="dist/vendors/chartjs/Chart.min.js"></script>
	<script src="dist/vendors/starrr/starrr.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.canvaswrapper.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.colorhelpers.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.saturated.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.browser.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.drawSeries.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.uiConstants.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.legend.js"></script>
	<script src="dist/vendors/jquery-flot/jquery.flot.pie.js"></script>
	<script src="dist/vendors/chartjs/Chart.min.js"></script>
	<script src="dist/vendors/jquery-jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
	<script src="dist/vendors/jquery-jvectormap/jquery-jvectormap-world-mill.js"></script>
	<script src="dist/vendors/jquery-jvectormap/jquery-jvectormap-de-merc.js"></script>
	<script src="dist/vendors/jquery-jvectormap/jquery-jvectormap-us-aea.js"></script>
	<script src="dist/vendors/apexcharts/apexcharts.min.js"></script>
	<!-- END: Page Vendor JS-->

	<!-- START: Page JS-->
	<script src="dist/js/home.script.js"></script>
	<!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
<script>
	$(document).ready(function() {
		var loaded = false;
		getLeadingCandidates(loaded); // Initial call to load the leading candidates
	});

	//Get Leading Candidates Table >>> Starts
	function getLeadingCandidates(loaded) {
		var pollID = "<?= $recentPollID; ?>";
		$.ajax({
			url: "controllers/get-live-charts",
			type: "POST",
			async: true,
			data: {
				getLeadingCandidate: 1,
				pollID: pollID
			},
			beforeSend: function() {
				if (!loaded) {
					$("#displayLeadingCandidates").html("<div class='text-center text-primary'><i class='spinner-grow spinner-grow-sm'></i>Retrieving Data For Recent Poll...</div>").show();
				}
			},
			success: function(leadingCandidates) {
				$("#displayLeadingCandidates").html(leadingCandidates).show();
				loaded = true;
			},
			error: function(xhr, status, error) {
				alert("Connectivity Error! Please check your internet connection and try again!");
				setTimeout(function() {
					getLeadingCandidates(loaded = true); // Retry after 5 seconds
				}, 5000);
			}
		});
	}
	//Get Leading Candidates Table >>> Ends
	setInterval(function() {
		getLeadingCandidates(true); // Subsequent calls skip beforeSend
	}, 5000); // Call every 5000 milliseconds (5 seconds)
</script>