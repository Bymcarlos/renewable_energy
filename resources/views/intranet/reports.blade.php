@extends('layouts.intranet')

@section('content')
@if (isset($msg))
	<div class="text-danger">{{$msg}}</div>
@endif
<!-- Icon Cards-->
  <div class="row">
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Benches</div>
          <div class="mr-5">List of current benches with assessments and features</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.reports.index') }}">
          <span class="float-left">View Report</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-warning o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Entities by technical sheet</div>
          <div class="mr-5">Description ...</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.reports.entitiesbytechsheet') }}">
          <span class="float-left">View Report</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-secondary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Parameters</div>
          <div class="mr-5">Description ...</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.reports.parameters') }}">
          <span class="float-left">View Report</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @if(Auth::user()->hasAnyRole(['admin']))
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-success o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Occupation by technical sheet</div>
          <div class="mr-5">Description ...</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.reports.occupationcomponent') }}">
          <span class="float-left">View Report</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-success o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Occupation by entity</div>
          <div class="mr-5">Description ...</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('benches.reports.occupationentity') }}">
          <span class="float-left">View Report</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    @endif
@endsection