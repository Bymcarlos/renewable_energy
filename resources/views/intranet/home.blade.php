@extends('layouts.intranet')

@section('content')
@if (isset($msg))
	<div class="text-danger">{{$msg}}</div>
@endif
<!-- Icon Cards-->
  <div class="row">
    <div class="col h3">FACILITIES DATABASE</div>
  </div>
  <div class="row">
    @if(Auth::user()->hasAnyRole(['admin','editor']))
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">BENCHES</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.index') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-info o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-handshake-o"></i>
          </div>
          <div class="mr-5">PARTNERS</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('partners.scopes') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @endif
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-warning o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-area-chart"></i>
          </div>
          <div class="mr-5">REPORTS</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('reports') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @if(Auth::user()->hasRole('admin'))
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-success o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-database"></i>
          </div>
          <div class="mr-5">MANAGEMENT</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('management') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @endif
  </div>
  <div class="row">
    <div class="col h3 mt-2">RATING TOOL</div>
  </div>
  <div class="row">
    @if(Auth::user()->hasRole('admin'))
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white o-hidden h-100" style="background: MediumOrchid;">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-tachometer"></i>
          </div>
          <div class="mr-5">Rating Tools</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('ratingtools') }}">
          <span class="float-left">View Details</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @endif
  </div>
@endsection