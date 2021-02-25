@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item active">Benches</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Benches are the ... You can acces to the technical sheet, economical sheet and occupation.</small></td>
        	<td width="14%" class="text-right">
            @if (count($benches)>0)
                <a href="{{ route('benches.export.excel')}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export bench" data-title="Export to excel"></a>
            @endif
            </td>
        </tr>
</table>

<!-- List Benches -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>BENCH</th>
            <th>ENTITY</th>
            <th>CTR</th>
            <th>AREA</th>
            <th>COMPONENT</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($benches as $bench)
        @php ($component = $bench->areaComponent()->first()->component()->first())
        <tr>
            <td class="small" id="bench_{{ $bench->id}}" data-title="{{ $bench->title }}" title="{{ $bench->comments }}">{{ $bench->title }}</td>
            <td class="small">{{ $bench->entity()->first()->title }}</td>
            <td class="text-center"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->country()->first()->title }}" width="24"/></td>
            <td class="small">
                @foreach ($component->areas()->get() as $area)
                    {{ $loop->first ? '' : '-' }}
                    {{ $area->title }}
                @endforeach
            </td>
            <td class="small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
            <td class="text-right">
                <a href="{{ route('bench.reports.assessments.technical',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-tasks" title="Technical sheet"></a>
                @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('bench.reports.occupation',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-calendar" title="Occupation"></a>
                <a href="{{ route('bench.reports.assessments.economical',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-euro" title="Economic sheet"></a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('js_custom')
<script type="text/javascript">
$('#dt_list').DataTable({
    "columnDefs": [{
        "searchable": false,
        "orderable": false,
        "targets": [5]
    },
    {
        "width": "20%", 
        "targets": 0
    },
    {
        "width": "15%", 
        "targets": 1
    },
    {
        "width": "5%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    },
    {
        "width": "30%", 
        "targets": 4
    },
    {
        "width": "15%", 
        "targets": 5
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection