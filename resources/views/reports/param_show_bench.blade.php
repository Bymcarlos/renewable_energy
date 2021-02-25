@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.parameters') }}">Search by parameters</a></li>
    <li class="breadcrumb-item active">{{ $bench->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Benches are the ... You can acces to the technical sheet, economical sheet and occupation.</small></td>
        	<td width="14%" class="text-right"></td>
        </tr>
</table>
{{-- Get the right Technical Sheet id (from Tech Assessment / Technical capabilities not required group) for this bench (It depends on the component) --}}
@php ($tech_cap = $bench->areaComponent()->first()->component()->first()->sheet_id)
<div class="card-body">
    <p class="text-white bg-primary pl-2">SEARCH BY PARAMETERS BENCH DATA (Title-Comments-Area-Component):</p>
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td width="20%">{{ $bench->title }}</td>
                    <td width="40%">{{ $bench->comments }}</td>
                    <td width="10%">{{ $bench->areaComponent()->first()->area()->first()->title }}</td>
                    <td width="25%">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                    <td width="5%"><a href="{{ route('benches.reports.parameters.show.export.excel',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export to Excel" data-title="Export to Excel"></a></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Assessment list -->
    <p class="text-white bg-primary pl-2">ASSESSMENTS: (Click to define values of the parameterizable features)</p>
    @foreach($assessments as $assessment)
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="col">{{ $assessment->title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                @foreach($assessment->sheets()->get() as $sheet)
                                    @if ($sheet->required == 1 || $sheet->id == $tech_cap)
                                    <tr>
                                        <td class="col"><a href="{{ route('benches.reports.parameters.show.features',['bench'=>$bench->id,'sheet'=>$sheet->id]) }}">{{ $sheet->title }}</a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</div>
@endsection