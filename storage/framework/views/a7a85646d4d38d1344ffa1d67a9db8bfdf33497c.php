<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?php echo e(config('app.name', 'RENEWABLE ENERGY - ADMIN TOOL')); ?></title>
  <!-- Bootstrap core CSS-->
  <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css">
  <!-- Custom fonts for this template-->
  <link href="<?php echo e(asset('css/font-awesome.min.css')); ?>" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="<?php echo e(asset('css/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="<?php echo e(asset('css/sb-admin.css')); ?>" rel="stylesheet" type="text/css">
  <style type="text/css">
    .dataTables_filter {
        float: left;
    }
    .menu_section {
      color:white;
      font-size: 18px;
    }
  </style>
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="<?php echo e(url('home')); ?>"><?php echo e(config('app.name', 'RENEWABLE ENERGY - ADMIN TOOL')); ?></a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="mainMenu">
        <li class="nav-item" data-toggle="tooltip" data-placement="right">
            <span class="nav-link-text menu_section">&nbsp;DATABASE</span>
        </li>
        <?php if(Auth::user()->hasAnyRole(['admin','editor'])): ?>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Benches">
          <a class="nav-link" href="<?php echo e(route('benches.index')); ?>">
            <i class="fa fa-fw fa-table"></i>
            <span class="nav-link-text">Benches</span>
          </a>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Partners">
          <a class="nav-link" href="<?php echo e(route('partners.scopes')); ?>">
            <i class="fa fa-fw fa-handshake-o"></i>
            <span class="nav-link-text">Partners</span>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Reports">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseReports" data-parent="#mainMenu">
            <i class="fa fa-fw fa-area-chart"></i>
            <span class="nav-link-text">Reports</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseReports">
            <li>
              <a href="<?php echo e(route('benches.reports.index')); ?>">Benches</a>
            </li>
            <li>
              <a href="<?php echo e(route('benches.reports.entitiesbytechsheet')); ?>">Entities by technical Sheet</a>
            </li>
            <li>
              <a href="<?php echo e(route('benches.reports.parameters')); ?>">Search by parameters</a>
            </li>
            <?php if(Auth::user()->hasAnyRole(['admin'])): ?>
            <li>
              <a href="<?php echo e(route('benches.reports.occupationcomponent')); ?>">Occupation by component</a>
            </li>
            <li>
              <a href="<?php echo e(route('benches.reports.occupationentity')); ?>">Occupation by entity</a>
            </li>
            <?php endif; ?>
          </ul>
        </li>
        <!-- This section only for Admin users -->
        <?php if(Auth::user()->hasRole('admin')): ?>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Management">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseManagement" data-parent="#mainMenu">
            <i class="fa fa-fw fa-database"></i>
            <span class="nav-link-text">Management</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseManagement">
            <li>
                <a class="nav-link-collapse collapsed" data-toggle="collapse" href="#collap_database">Database</a>
                <ul class="sidenav-third-level collapse" id="collap_database">
                  <li>
                    <a href="<?php echo e(route('entities.index')); ?>">Entities</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('areas.index')); ?>">Areas</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('components.index')); ?>">Components</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('staff.index')); ?>">Staff</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('platforms.index')); ?>">SGRE Portfolio</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('unittypes.index')); ?>">Unit types and units</a>
                  </li>
                </ul>
            </li>
            <li>
                <a class="nav-link-collapse collapsed" data-toggle="collapse" href="#collap_assessments">Assessments</a>
                <ul class="sidenav-third-level collapse" id="collap_assessments">
                  <li>
                    <a href="<?php echo e(route('assessments.technical')); ?>">Technical</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('assessments.economical')); ?>">Economical</a>
                  </li>
                </ul>
            </li>
            <li>
                <a class="nav-link-collapse collapsed" data-toggle="collapse" href="#collap_partners">Partners</a>
                <ul class="sidenav-third-level collapse" id="collap_partners">
                  <li>
                    <a href="<?php echo e(route('scopes.index')); ?>">Scopes</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('generalrequests.index',['generalsheet'=>1])); ?>">Partner sheet</a>
                  </li>
                </ul>
            </li>
            <li>
                <a class="nav-link-collapse collapsed" data-toggle="collapse" href="#collap_settings">Settings</a>
                <ul class="sidenav-third-level collapse" id="collap_settings">
                  <li>
                    <a href="<?php echo e(route('users.index')); ?>">Users</a>
                  </li>
                </ul>
            </li>
          </ul>
        </li>
        <?php endif; ?>
        <!-- V2: Rating Tool -->
        <?php if(Auth::user()->hasAnyRole(['admin','editor'])): ?>
        <li class="nav-item mt-4" data-toggle="tooltip" data-placement="right">
            <span class="nav-link-text menu_section">&nbsp;RATING TOOL</span>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Rating Tools">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseRating" data-parent="#mainMenu">
            <i class="fa fa-fw fa-tachometer"></i>
            <span class="nav-link-text">Rating Tools</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseRating">
            <li>
              <a data-toggle="collapse" href="#collap_templates">Templates</a>
              <ul class="sidenav-third-level collapse" id="collap_templates">
                  <li>
                    <a href="<?php echo e(route('inputsheets.areas')); ?>">Requests from TS</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('techsheets.areas')); ?>">Technical</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('timesheets.index')); ?>">Timing</a>
                  </li>
                  <li>
                    <a href="<?php echo e(route('economicsheets.index')); ?>">Economics</a>
                  </li>
                </ul>
            </li>
            <li>
              <a href="<?php echo e(route('ratings.areas')); ?>">Ratings</a>
            </li>
            <li>
              <a href="<?php echo e(route('ratingreports.index')); ?>">Reports</a>
            </li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item mr-5">
          <img src="<?php echo e(asset('icons/logo_xl_inv.png')); ?>" width="200" />
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <i class="fa fa-fw fa-user-o"></i><?php echo e(Auth::user()->name); ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-fw fa-sign-out"></i>Logout</a>
        </li>
      </ul>
    </div>
  </nav>
  <div class="content-wrapper">
    <div class="container-fluid">
      <?php echo $__env->yieldContent('content'); ?>
    </div>
    <!-- /.container-fluid-->
    <!-- /.content-wrapper-->
    <footer class="sticky-footer">
      <div class="container">
        <div class="text-center">
          <small>Copyright © <a href="#" target="_blank">Renewable Energy</a> <?php echo e(date("Y")); ?></small>
        </div>
      </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><?php echo e(__('Logout')); ?></a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
    <script src="<?php echo e(asset('js/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?php echo e(asset('js/jquery-easing/jquery.easing.min.js')); ?>"></script>
    <!-- Page level plugin JavaScript-->
    <script src="<?php echo e(asset('js/Chart.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.dataTables.js')); ?>"></script>
    <script src="<?php echo e(asset('js/dataTables.bootstrap4.js')); ?>"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?php echo e(asset('js/sb-admin.min.js')); ?>"></script>
    <?php echo $__env->yieldContent('js_custom'); ?>
</body>
</html>