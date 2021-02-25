@extends('layouts.intranet')

@section('content')
@if (isset($msg))
	<div class="{{$msg_type}} text-center m-5">{{$msg}}</div>
@endif
<!-- Icon Cards-->
  <div class="row">
    <div class="col-xl-6 col-sm-6 mb-3">
      <div class="card text-white bg-primary o-hidden h-100">
        <div class="card-body">
          <div class="card-body-icon">
            <i class="fa fa-fw fa-table"></i>
          </div>
          <div class="mr-5">Templates</div>
          <div class="mr-5">List of templates: Requests from TS, Technical, Timing and Economics ratings</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('ratingtools.templates') }}">
          <span class="float-left">View templates</span>
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
          <div class="mr-5">Ratings</div>
          <div class="mr-5">Description ...</div>
        </div>
        <a class="card-footer text-white clearfix small z-1" href="{{ route('ratings.index') }}">
          <span class="float-left">View ratings</span>
          <span class="float-right">
            <i class="fa fa-angle-right"></i>
          </span>
        </a>
      </div>
    </div>
@endsection