<?php
session_start();

if (!isset($_SESSION['hostID']) && !isset($_SESSION['hostEmail']) && !isset($_SESSION['portalAccess'])) {
	header("location:./");
	$_SESSION["logMsg"] = "Please login to continue";
}

$page = "dashboard";
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
							<h4 class="mb-0"><?= ucfirst((isset($userData['fname']) && !empty($userData['fname'])) ? "Welcome Back, $userData[fname]" : "Polling Dashboard") ?></h4>
							<p>Manage and monitor polls and voting activities</p>
						</div>
						<ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
							<?= date("D, d-M-Y"); ?>
						</ol>
					</div>
				</div>
			</div>
			<!-- END: Breadcrumbs-->

			<div class="row ">
				<div class="col-12 mt-3">
					<div class="jumbotron text-primary">
						<h1 class="display-4"><b>Online Polling & Voting System</b></h1>
						<p class="lead">© 2025 Copyright University of Roehampton Polling & Voting System | Presented by <strong>Ridwan Olalekan Oguntola</strong>.</p>
					</div>
				</div>
			</div>

			<!-- START: Poll Overview Cards-->
			<div class="row">
				<!-- Active Polls -->
				<div class="col-12 col-sm-6 col-xl-3 mt-3">
					<div class="card bg-primary text-white">
						<div class="card-body">
							<div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
								<i class="fa fa-poll-h icons card-liner-icon mt-2"></i>
								<div class='card-liner-content'>
									<?php
									$getPolls = getPolls($conn, $_SESSION['hostID']);
									$activeOrCompletedPolls = array_filter($getPolls, function ($poll) use ($conn) {
										$pollID = $poll['pollID'];
										$pollStatus = getPollStatus($conn, $pollID);

										return $pollStatus == 'active' || $pollStatus == 'completed';
									});
									?>
									<h2 class="card-liner-title"><?= number_format(count($activeOrCompletedPolls)); ?></h2>
									<h6 class="card-liner-subtitle">Active/Completed Polls</h6>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Voting Participants -->
				<div class="col-12 col-sm-6 col-xl-3 mt-3">
					<div class="card bg-info text-white">
						<div class="card-body">
							<div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
								<i class="fa fa-users icons card-liner-icon mt-2"></i>
								<div class='card-liner-content'>
									<?php
									$getVotes = getVotesByHostID($conn, $_SESSION['hostID']);
									$numParticipants = !empty($getVotes) ? count(array_unique(array_column($getVotes, 'voterEmail'))) : 0;
									?>
									<h2 class="card-liner-title"><?= number_format($numParticipants); ?></h2>
									<h6 class="card-liner-subtitle">Voting Participants</h6>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Total Votes -->
				<div class="col-12 col-sm-6 col-xl-3 mt-3">
					<div class="card bg-secondary text-white">
						<div class="card-body">
							<div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
								<i class="fas fa-vote-yea card-liner-icon mt-2"></i>
								<div class='card-liner-content'>
									<h2 class="card-liner-title"><?= number_format(count($getVotes)); ?></h2>
									<h6 class="card-liner-subtitle">Total Votes</h6>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Upcoming Polls -->
				<div class="col-12 col-sm-6 col-xl-3 mt-3">
					<div class="card bg-warning text-white">
						<div class="card-body">
							<div class='d-flex px-0 px-lg-2 py-2 align-self-center'>
								<i class="fa fa-calendar-check icons card-liner-icon mt-2"></i>
								<div class='card-liner-content'>
									<?php
									$upcomingPolls = array_filter($getPolls, function ($poll) use ($conn) {
										$pollID = $poll['pollID'];
										$pollStatus = getPollStatus($conn, $pollID);
										return $pollStatus == 'upcoming';
									});
									?>
									<h2 class="card-liner-title"><?= number_format(count($upcomingPolls)); ?></h2>
									<h6 class="card-liner-subtitle">Upcoming Polls</h6>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END: Poll Overview Cards-->

			<!-- START: Recent Poll Activity -->
			<div class="row">
				<div class="col-12 col-lg-6 mt-3">
					<div class="card">
						<div class="card-header  justify-content-between align-items-center">
							<h6 class="card-title">Recent Poll Activity</h6>
						</div>
						<div class="card-body table-responsive p-0" style="height: 24rem;overflow: auto;">
							<table class="table  mb-0">
								<thead>
									<tr>
										<th>Poll ID</th>
										<th>Poll Title</th>
										<th>Participants</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$getPolls = getPolls($conn, $_SESSION['hostID']);
									if (!empty($getPolls)) {
										foreach ($getPolls as $poll) {
											$pollStatus = getPollStatus($conn, $poll['pollID']);
									?>
											<tr>
												<td><?= $poll['pollID'] ?></td>
												<td><?= $poll['title'] ?></td>
												<td><?= number_format(count(array_filter($getVotes, function ($vote) use ($poll) {
															return $vote['pollID'] == $poll['pollID'];
														}))) ?>
												</td>
												<td><span class="badge <?= $pollStatus == 'active' ? 'badge-primary' : ($pollStatus == 'upcoming' ? 'badge-warning' : 'badge-secondary'); ?>"><?= ucfirst($pollStatus); ?></span></td>
											</tr>
										<?php }
									} else { ?>
										<tr>
											<td colspan="4" class="text-center">No Recent Poll Activity</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- :::Poll Result Summary -->
				<div class="col-md-12 col-lg-6 mt-3">
					<?php
					function getMostRecentPollWithVotes($conn, $hostID)
					{
						$getPolls = getPolls($conn, $hostID);
						foreach ($getPolls as $poll) {
							$pollID = $poll['pollID'];
							$totalVotes = getTotalVotesForPoll($conn, $pollID, $hostID);
							if ($totalVotes > 0) {
								return $pollID;
							}
						}
						return null;
					}
					$recentPollID = getMostRecentPollWithVotes($conn, $_SESSION['hostID']);
					$pollInfo = getPollByID($conn, $recentPollID);
					$adminInfo = getHostInfo($conn, $_SESSION["hostID"]);
					?>
					<div class="card border-top-0">
						<div class="card-header  justify-content-between align-items-center">
							<h6 class="card-title">Leading Candidates for the Latest Poll <?= !empty($pollInfo) ? '<span class="text-primary">(' . ucfirst($pollInfo['title']) . ')</span>' : ''; ?> </h6>
						</div>
						<div class="card-content border-top border-primary border-w-5">
							<div class="card-body p-0">
								<!-- Load Data Table -->
								<div class="card-body" id="displayLeadingCandidates" style="height:15rem; overflow:auto"></div>
								<!-- Load Data Table -->

								<div class="d-flex outline-badge-primary border-0">
									<div class="w-50 text-center p-3 border-right"><a href="./live-charts?id=<?= $recentPollID; ?>" target="_blank" class="font-weight-bold">Live Chart <i class="fas fa-chart-line"></i></a></div>
									<div class="w-50 text-center p-3">
										<a href="./results?id=<?= $recentPollID; ?>" target="_blank" class="text-danger font-weight-bold">Poll Result <span class="fas fa-chart-bar"></span></a>
									</div>
								</div>
								<div class="d-flex justify-content-around p-3 border-bottom border-primary">
									<div class="text-center">
										<a href="<?= !empty($adminInfo['facebookLink']) ? htmlspecialchars($adminInfo['facebookLink']) : 'https://www.facebook.com'; ?>" target="_blank" style="color: #3b5998; text-decoration: none;">
											<i class="fab fa-facebook-f" style="font-size:2.5rem"></i><br>
											<span>Facebook</span>
										</a>
									</div>
									<div class="text-center">
										<a href="<?= !empty($adminInfo['linkedinLink']) ? htmlspecialchars($adminInfo['linkedinLink']) : 'https://www.linkedin.com'; ?>" target="_blank" style="color: #0077b5; text-decoration: none;">
											<i class="fab fa-linkedin-in" style="font-size:2.5rem"></i><br>
											<span>LinkedIn</span>
										</a>
									</div>
									<div class="text-center">
										<a href="<?= !empty($adminInfo['twitterLink']) ? htmlspecialchars($adminInfo['twitterLink']) : 'https://www.twitter.com'; ?>" target="_blank" style="color: #1da1f2; text-decoration: none;">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 2.5rem; height: 2.5rem;">
												<path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
											</svg><br>
											<span>X App</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END: Recent Poll Activity -->

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