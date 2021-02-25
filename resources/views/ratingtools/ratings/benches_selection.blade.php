@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.areas') }}">Ratings - Areas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.index',['area_id'=>$rating->area_id]) }}">{{$rating->area()->first()->title}}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.show',['rating_id'=>$rating->id]) }}">{{$rating->title}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('ratingbenches.index',['rating'=>$rating->id])}}">Benches selected</a></li>
    <li class="breadcrumb-item active">Benches selection</li>
</ol>
@if (!isset($component_id))
    @php ($component_id=0)
@endif
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="80%" class="small">Selection of the benches to include on the rating.<br/><strong>Note:</strong> % Available values are informative. This concept assesses the percentage of required features filled in DB among all rating tool templates.</td>
            <td width="18%" class="text-right"> 	
                <form id="form_filter_component" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="rating_id" value="{{ $rating->id }}" />
                    <select id="component_id" name="component_id" class="form-control" onchange="filterByComponent({{$rating->id}},this.value)">
                        <option value="0" @if ($component_id==0) selected @endif>All components</option>
                        @foreach($rating->area()->first()->components()->get() as $component)
                            <option value="{{$component->id}}" @if ($component_id==$component->id) selected @endif>{{$component->title}}</option>
                        @endforeach
                    </select>
                </form>
            </td>
            <td width="1%" class="text-right">
                <a href="{{route('ratingbenches.index',['rating'=>$rating->id])}}" class="btn btn-primary btn-sm" title="Bench selection finished">DONE</a></td>
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
            <th class="text-center">% AVAILABLE</th>
            <th class="text-center">SELECTED</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($benches as $bench_id => $bench_info)
        @php ($bench = $bench_info["bench"])
        @php ($component = $bench->areaComponent()->first()->component()->first())
        <tr>
            <td class="small" id="bench_13" data-title="AEWC_BLADE_01" title="">{{$bench->title}}</td>
            <td class="small">{{$bench->entity()->first()->title}}</td>
            <td class="text-center"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->country()->first()->title }}" width="24"/></td>
            <td class="small">
                @foreach ($component->areas()->get() as $area)
                    {{ $loop->first ? '' : '-' }}
                    {{ $area->title }}
                @endforeach
            </td>
            <td class="small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
            <td class="text-center"><small>{{number_format($bench_info["values"],0,',','.')}}%</small></td>
            <td class="text-center">
                @if (isset($ratingbenches[$bench->id])) 
                    @php ($ic=2) 
                    @php ($ratingbench=$ratingbenches[$bench->id]->id)
                @else 
                    @php ($ic=0)
                    @php ($ratingbench=0)
                @endif
                <a href="{{route('ratingbenchesselection.state',['rating'=>$rating->id,'bench'=>$bench->id,'component'=>$component_id,'ratingbench'=>$ratingbench])}}"><img src="{{ asset('icons/ic_status_'.$ic.'.png') }}"/></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
@section('js_custom')
<script type="text/javascript">
function filterByComponent(rating_id,component_id){
    $("#form_filter_component").attr("action","{{ url('ratingbenchesselection') }}/"+rating_id+"/"+component_id);
    $("#form_filter_component").submit();
}
/*
function changeBenchState(rating_id,bench_id,component_id){
    $("#form_bench_"+bench_id).attr("action","{{ url('ratingbenchesselection') }}/"+rating_id+"/"+bench_id+"/"+component_id);
    $("#form_bench_"+bench_id).submit();
}
*/

$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [5,6]
    },
    {
        "width": "20%", 
        "targets": 0
    },
    {
        "width": "20%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "10%", 
        "targets": 3
    },
    {
        "width": "20%", 
        "targets": 4
    },
    {
        "width": "10%", 
        "targets": 5
    },
    {
        "width": "10%", 
        "targets": 6
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection