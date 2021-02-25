@extends('layouts.intranet')

@section('content')
@if (isset($msg))
	<div class="text-danger">{{$msg}}</div>
@endif
<!-- Icon Cards-->
  <div class="row">
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Requests from TS</div>
          <div class="mr-5">Templates of several areas</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('inputsheets.areas') }}">
          <span class="float-left">View templates</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-danger o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Technical</div>
          <div class="mr-5">Templates of several areas</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('techsheets.areas') }}">
          <span class="float-left">View templates</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-secondary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Timing</div>
          <div class="mr-5">List of current timing templates</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('timesheets.index') }}">
          <span class="float-left">View templates</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
    <div class="col-xl-3 col-sm-3 mb-3">
      <div class="card text-white bg-secondary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Economics</div>
          <div class="mr-5">List of current economics templates</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('economicsheets.index') }}">
          <span class="float-left">View templates</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
@endsection