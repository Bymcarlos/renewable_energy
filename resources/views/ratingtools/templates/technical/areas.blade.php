@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">Technical - Areas</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%" class="small">Technical templates are sorted by area. Select an area to list related templates.</td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>

<!-- List Areas -->
<table class="table-bordered table-sm" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>AREA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($areas as $area)
        <tr id="area_{{$area->id}}">
            <td><a href="{{route('techsheets.index',['area_id'=>$area->id])}}">{{ $area->title }}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection