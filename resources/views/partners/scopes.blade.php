@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('partners.scopes') }}">Partners</a></li>
    <li class="breadcrumb-item active">Select scope</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Partners are organized in scopes, select scope to list the partners</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>

<!-- List Scopes -->
<table class="table-bordered table-sm" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>SCOPE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scopes as $scope)
        <tr>
            <td><a href="{{route('partners.index',['scope_id'=>$scope->id])}}">{{ $scope->title }}</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
