  <?php
  include("controllers/globalFunctions.php");
  $userData = getHostInfo($conn, $_SESSION['hostID']);

  // Check for page Access
  if (!in_array($_SESSION['hostRole'], ["superAdmin", "admin"])) {
    header("Location: " . ($failedAccessRedirect ?? './'));
    exit();
  }
  //Check for page Access
  ?>
  <!-- START: Pre Loader-->
  <div class="se-pre-con">
    <img class="loader" src="images/roe.png" alt="Roehampton University">
  </div>
  <!-- END: Pre Loader-->

  <!-- START: Header-->
  <div id="header-fix" class="header fixed-top">
    <div class="site-width">
      <nav class="navbar navbar-expand-lg  p-0">
        <div class="navbar-header  h-100 h4 mb-0 align-self-center logo-bar text-left">
          <a href="./dashboard" class="horizontal-logo text-left">
            <img src="images/roe.png" style="width:40px; height:40px;" />
            <!-- <span class="h5 font-weight-bold align-self-center mb-0 ml-auto"><img src="images/roe.png" style="height: 50px;margin-left:-15px" alt=""></span> -->
          </a>
        </div>
        <div class="navbar-header h4 mb-0 text-center h-100 collapse-menu-bar">
          <a href="javascript:void(0);" class="sidebarCollapse" id="collapse"><i class="icon-menu"></i></a>
        </div>

        <form class="float-left d-none d-lg-block search-form">
          <!-- <div class="form-group mb-0 position-relative">
            <input type="text" class="form-control border-0 rounded bg-search pl-5" placeholder="Search anything...">
            <div class="btn-search position-absolute top-0">
              <a href="javascript:void(0);"><i class="h6 icon-magnifier"></i></a>
            </div>
            <a href="javascript:void(0);" class="position-absolute close-button mobilesearch d-lg-none" data-toggle="dropdown" aria-expanded="false"><i class="icon-close h5"></i>
            </a>

          </div> -->
        </form>
        <div class="navbar-right ml-auto h-100">
          <ul class="ml-auto p-0 m-0 list-unstyled d-flex top-icon h-100">
            <!-- <li class="d-inline-block align-self-center  d-block d-lg-none">
              <a href="javascript:void(0);" class="nav-link mobilesearch" data-toggle="dropdown" aria-expanded="false"><i class="icon-magnifier h4"></i>
              </a>
            </li> -->

            <!-- <li class="dropdown align-self-center">
                          <a href="javascript:void(0);" class="nav-link" data-toggle="dropdown" aria-expanded="false"><i class="icon-reload h4"></i>
                              <span class="badge badge-default"> <span class="ring">
                                  </span><span class="ring-point">
                                  </span> </span>
                          </a>
                          <ul class="dropdown-menu dropdown-menu-right border  py-0">
                              <li>
                                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                                      <div class="media">
                                          <img src="../dist/images/author.jpg" alt="" class="d-flex mr-3 img-fluid rounded-circle">
                                          <div class="media-body">
                                              <p class="mb-0">john</p>
                                              <span class="text-success">New user registered.</span>
                                          </div>
                                      </div>
                                  </a>
                              </li>
                              <li>
                                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                                      <div class="media">
                                          <img src="../dist/images/author2.jpg" alt="" class="d-flex mr-3 img-fluid rounded-circle">
                                          <div class="media-body">
                                              <p class="mb-0">Peter</p>
                                              <span class="text-success">Server #12 overloaded.</span>
                                          </div>
                                      </div>
                                  </a>
                              </li>
                              <li>
                                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                                      <div class="media">
                                          <img src="../dist/images/author3.jpg" alt="" class="d-flex mr-3 img-fluid rounded-circle">
                                          <div class="media-body">
                                              <p class="mb-0">Bill</p>
                                              <span class="text-danger">Application error.</span>
                                          </div>
                                      </div>
                                  </a>
                              </li>

                              <li><a class="dropdown-item text-center py-2" href="javascript:void(0);"> See All Tasks <i class="icon-arrow-right pl-2 small"></i></a></li>
                          </ul>

                      </li> -->
            <li class="dropdown align-self-center d-inline-block">
              <!-- <a href="javascript:void(0);" class="nav-link" data-toggle="dropdown" aria-expanded="false"><i class="icon-bell h4"></i>
                <span class="badge badge-default"> <span class="ring">
                  </span><span class="ring-point">
                  </span> </span>
              </a> -->
              <!-- <ul class="dropdown-menu dropdown-menu-right border   py-0">
                <li>
                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                    <div class="media">
                      <i class="d-flex mr-3 img-fluid rounded-circle w-50 fas fa-bullhorn"></i>
                      <div class="media-body">
                        <p class="mb-0 text-success">New Notification</p>
                        12 min ago
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                    <div class="media">
                      <i class="d-flex mr-3 img-fluid rounded-circle w-50 fas fa-bullhorn"></i>
                      <div class="media-body">
                        <p class="mb-0 text-success">New Notification</p>
                        15 min ago
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item px-2 py-2 border border-top-0 border-left-0 border-right-0" href="javascript:void(0);">
                    <div class="media">
                      <i class="d-flex mr-3 img-fluid rounded-circle w-50 fas fa-bullhorn"></i>
                      <div class="media-body">
                        <p class="mb-0 text-success">New Notification</p>
                        21 min ago
                      </div>
                    </div>
                  </a>
                </li>

                <li><a class="dropdown-item text-center py-2" href="javascript:void(0);"> Read All Notifications <i class="icon-arrow-right pl-2 small"></i></a></li>
              </ul> -->
            </li>
            <li class="dropdown user-profile align-self-center d-inline-block">
              <a href="javascript:void(0);" class="nav-link py-0" data-toggle="dropdown" aria-expanded="false">
                <div class="media">
                  <img src="images/user.png" alt="" class="d-flex img-fluid rounded-circle" width="40">
                </div>
              </a>

              <div class="dropdown-menu border dropdown-menu-right p-0">
                <a href="my-profile" class="dropdown-item px-2 align-self-center d-flex">
                  <span class="icon-settings mr-2 h6 mb-0"></span> Profile Settings</a>
                <div class="dropdown-divider"></div>
                <a href="contact" class="dropdown-item px-2 align-self-center d-flex">
                  <span class="icon-support mr-2 h6  mb-0"></span> Contact Support</a>
                <div class="dropdown-divider"></div>
                <a href="controllers/logout" class="dropdown-item px-2 text-danger align-self-center d-flex">
                  <span class="icon-logout mr-2 h6  mb-0"></span> Logout</a>
              </div>

            </li>

          </ul>
        </div>
      </nav>
    </div>
  </div>
  <!-- END: Header-->

  <!-- END: Main Menu-->

  <style>
    /* Skeleton Loader Styles */
    .skeleton-loader {
      animation: shimmer 1.5s infinite linear;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      width: 100%;
      height: 20px;
      margin: 0 15px;
      border-radius: 4px;
      display: inline-block;
    }

    .modal-skeleton-loader {
      animation: shimmer 1.5s infinite linear;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      width: 100%;
      height: 300px;
      border-radius: 4px;
      display: flex;
    }

    /* Skeleton for Table Rows */
    .table-skeleton-loader {
      width: 100%;
      margin: 15px 0;
    }

    .table-skeleton-loader tr {
      display: table-row;
      animation: shimmer 1.5s infinite linear;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
    }

    .table-skeleton-loader td {
      height: 20px;
      padding: 10px;
      border-radius: 4px;
    }

    /* Additional styling for better visual separation between cells */
    .skeleton-loader+.skeleton-loader,
    .table-skeleton-loader tr+.table-skeleton-loader tr {
      margin-top: 0;
    }

    @keyframes shimmer {
      0% {
        background-position: -200% 0;
      }

      100% {
        background-position: 200% 0;
      }
    }
  </style>