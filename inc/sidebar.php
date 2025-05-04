<div class="sidebar">
  <div class="site-width">
    <!-- START: Menu-->
    <ul id="side-menu" class="sidebar-menu">
      <!-- Administrative Panel Section -->
      <li class="dropdown <?php echo in_array($page, ["dashboard", "candidates", "positions",  "voters", "polls", "results", "accredited-voters", "live-charts", "settings"]) ? 'active' : ''; ?>">
        <a href="javascript:void();"><i class="icon-home mr-1"></i> Administrative Panel</a>
        <ul>

          <li <?= ($page == "dashboard") ? "class='active'" : ""; ?>>
            <a href="dashboard"><i class="icon-home"></i>Dashboard</a>
          </li>


          <li <?= ($page == "polls") ? "class='active'" : ""; ?>>
            <a href="polls"><i class="fas fa-poll"></i>Polls</a>
          </li>
          <li <?= ($page == "voters") ? "class='active'" : ""; ?>>
            <a href="voters"><i class="fas fa-users"></i>Voters</a>
          </li>

          <li <?= ($page == "candidates") ? "class='active'" : ""; ?>>
            <a href="candidates"><i class="fas fa-user"></i>Candidates</a>
          </li>

          <li <?= ($page == "positions") ? "class='active'" : ""; ?>>
            <a href="positions"><i class="fas fa-user-tie"></i>Positions</a>
          </li>

          <li <?= ($page == "results") ? "class='active'" : ""; ?>>
            <a href="results"><i class="fas fa-chart-bar"></i>Results</a>
          </li>
          <li <?= ($page == "live-charts") ? "class='active'" : ""; ?>>
            <a href="live-charts"><i class="fas fa-chart-line"></i>Live Charts</a>
          </li>
          <!-- Polling and Voting Submenu -->
          <!-- <li class="dropdown <?php //echo in_array($page, ["polls", "voters", "candidates",  "positions", "results", "live-charts"]) ? 'active' : ''; 
                                    ?>">
            <a href="javascript:void();"><i class="fas fa-vote-yea"></i> Polling & Voting</a>
            <ul class="sub-menu">


            </ul>
          </li> -->

          <!-- Settings -->
          <li <?= ($page == "my-profile") ? "class='active'" : ""; ?>>
            <a href="my-profile"><i class="fas fa-cogs"></i>Settings</a>
          </li>

          <li>
            <a class="text-danger" href="controllers/logout"><i class=" icon-logout mr-2 h6  mb-0"></i>Logout</a>
          </li>

          <!-- Polling and Voting Submenu -->
          <!-- <li class="dropdown <?php //echo in_array($page, ["polls", "voters", "candidates",  "positions", "results", "live-charts"]) ? 'active' : ''; 
                                    ?>">
            <a href="javascript:void();"><i class="fas fa-vote-yea"></i> Polling & Voting</a>
            <ul class="sub-menu">

              <li <?php if ($page == "polls") {
                    echo "class='active'";
                  } ?>>
                <a href="polls"><i class="fas fa-poll"></i> Polls</a>
              </li>

              <li <?php if ($page == "voters") {
                    echo "class='active'";
                  } ?>>
                <a href="voters"><i class="fas fa-users"></i> Voters</a>
              </li>

              <li <?php if ($page == "candidates") {
                    echo "class='active'";
                  } ?>>
                <a href="candidates"><i class="fas fa-user"></i> Candidates</a>
              </li>

              <li <?php if ($page == "positions") {
                    echo "class='active'";
                  } ?>>
                <a href="positions"><i class="fas fa-user"></i> Positions</a>
              </li>

              <li <?php if ($page == "results") {
                    echo "class='active'";
                  } ?>>
                <a href="results"><i class="fas fa-chart-bar"></i> Results</a>
              </li>

              <li <?php if ($page == "live-charts") {
                    echo "class='active'";
                  } ?>>
                <a href="live-charts"><i class="fas fa-chart-line"></i> Live Charts</a>
              </li>
            </ul>
          </li> -->
        </ul>
      </li>
    </ul>
    <!-- END: Menu-->

    <ol class="breadcrumb bg-transparent align-self-center m-0 p-0 ml-auto">
      <li class="breadcrumb-item"><a href="javascript:void();">Polling System</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </div>
</div>