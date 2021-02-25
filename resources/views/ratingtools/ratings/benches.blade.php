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
    <li class="breadcrumb-item active">Benches selected</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%" class="small">Select benches for the rating</td>
        	<td width="14%" class="text-right"><a href="{{route('ratingbenchesselection',['rating'=>$rating->id])}}" class="btn btn-primary btn-sm fa fa-table" title="Bench selection"></a></td>
        </tr>
</table>

<!-- List Benches -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>SELECTED BENCHES</th>
            <th>ENTITY</th>
            <th>CTR</th>
            <th>AREA</th>
            <th>COMPONENT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rating->ratingbenches()->get() as $ratingbench)
        @php ($bench = $ratingbench->bench()->first())
        @php ($component = $bench->areaComponent()->first()->component()->first())
        <tr>
            <td class="small">{{$bench->title}}</td>
            <td class="small">{{$bench->entity()->first()->title}}</td>
            <td class="text-center"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->country()->first()->title }}" width="24"/></td>
            <td class="small">
                @foreach ($component->areas()->get() as $area)
                    {{ $loop->first ? '' : '-' }}
                    {{ $area->title }}
                @endforeach</td>
            <td class="small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
@section('js_custom')
<script type="text/javascript">
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [4]
    },
    {
        "width": "30%", 
        "targets": 0
    },
    {
        "width": "15%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "20%", 
        "targets": 3
    },
    {
        "width": "25%", 
        "targets": 4
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection