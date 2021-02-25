@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active">Management</li>
</ol>
<!-- Icon Cards-->
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small><strong>DATABASE: </strong> You can admin entities, areas ....</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="row">
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Entities</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('entities.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-warning o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-list"></i>
          </div>
          <div class="mr-5">Areas</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('areas.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-success o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-bars"></i>
          </div>
          <div class="mr-5">Components</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('components.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-dark o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-male"></i>
          </div>
          <div class="mr-5">Staff</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('staff.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-info o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-briefcase"></i>
          </div>
          <div class="mr-5">SGRE Portfolio</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('platforms.index')}}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-danger o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-dashboard"></i>
          </div>
          <div class="mr-5">Unit type and units</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('unittypes.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small><strong>ASSESSMENTS: </strong> Admin technical and economical assessments ....</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="row">
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-info o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-tasks"></i>
          </div>
          <div class="mr-5">Technical</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('assessments.technical') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-secondary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-eur"></i>
          </div>
          <div class="mr-5">Economical</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('assessments.economical') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small><strong>PARTNERS: </strong> Admin scopes and partner sheet ....</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="row">
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-info o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-tasks"></i>
          </div>
          <div class="mr-5">Scopes</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('scopes.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-secondary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-eur"></i>
          </div>
          <div class="mr-5">Partner sheet</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('generalrequests.index',['generalsheet'=>1]) }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small><strong>SETTINGS: </strong> You can admin users ....</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="row">
    <div class="col-xl-2 col-sm-4 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-users"></i>
          </div>
          <div class="mr-5">Users</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('users.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
</div>
@endsection