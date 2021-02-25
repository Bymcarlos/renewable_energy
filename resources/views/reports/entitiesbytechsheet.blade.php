@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item active">Entities by technical sheet</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>You can filter ... </small></td>
        	<td width="14%" class="text-right">
                @if (count($items)>0)
                <a href="{{ route('benches.reports.entitiesbytechsheet.export.excel',['component'=>$component_id,'area'=>$area_id])}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export bench" data-title="Export to excel"></a>
                @endif
            </td>
        </tr>
</table>
<div class="card-body">
    <p class="text-white bg-primary pl-2">TECHNICAL SHEET (Select Area / Component):</p>
    <form data-toggle="validator" action="{{ route('benches.reports.entitiesbytechsheet.filter') }}" method="POST">
        {{ csrf_field() }}
        <table class="table-bordered table-sm" id="filter" width="100%" cellspacing="0">
            <thead class="bg-light">
                <tr>
                    <th>SELECT AREA</th>
                    <th>SELECT COMPONENT</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="30%">
                        <select class="form-control" id="area_id" name="area_id" onchange="loadComponents(0)">
                            @if ($area_id) 
                            <option disabled value="0">Select area</option>
                            @else
                            <option disabled selected value="0">Select area</option>
                            @endif
                            @foreach ($areas as $area)
                                @if ($area->id == $area_id)
                                    <option value="{{ $area->id }}" selected>{{ $area->title }}</option>
                                @else
                                    <option value="{{ $area->id }}">{{ $area->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td width="30%">
                        @if ($components)
                            <select class="form-control" id="component_id" name="component_id">
                                <option disabled selected value>Select component</option>
                                @foreach ($components as $component)
                                    @if ($component->id == $component_id)
                                    <option value="{{$component->id}}" selected>{{$component->title}}</option>
                                    @else
                                    <option value="{{$component->id}}">{{$component->title}}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="component_id" name="component_id" disabled>
                                <option disabled selected value>Select component</option>
                            </select>
                        @endif
                    </td>
                    <td width="20%" class="text-center">
                        <button type="submit" class="btn crud-submit btn-success">Apply filter</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    @if (count($items)>0)
        <p class="text-white bg-primary pl-2 mt-2">LIST OF BENCHES:</p>
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="col-2">BENCH</th>
                    <th class="col-2">ENTITY</th>
                    <th class="col-1">COUNTRY</th>
                    <th class="col-2">AREA</th>
                    <th class="col-3">COMPONENT</th>
                    <th class="col-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $bench)
                    <tr id="item_{{$bench->id}}">
                        <td class="col-2" id="title_{{$bench->id}}">{{ $bench->title }}</td>
                        <td class="col-2">{{ $bench->entity()->first()->title }}</td>
                        <td class="text-center"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->country()->first()->title }}" width="24"/></td>
                        <td class="col-2">
                            @php ($i=1)
                            @php ($component = $bench->areaComponent()->first()->component()->first())
                            @foreach ($component->areas()->get() as $area)
                                @if ($i>1) - @endif
                                {{ $area->title }}
                                @php ($i++)
                            @endforeach
                        </td>
                        <td class="col-3">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                        <td class="col-2 text-right">
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
    @endif
</div>
@endsection
@section('js_custom')
<script type="text/javascript">
    function loadComponents(component_id){
        $.ajax({
                type: 'POST',
                url: "{{ url('area/components') }}",
                data: { _token: "{{ csrf_token() }}", area_id: $("#area_id").val() },
                success: function(data){
                    //console.log(data);
                    $("#filter #component_id").empty();
                    $("#filter #component_id").append('<option disabled selected value>Select component</option>');
                    $.each(data, function(index, object){
                        $("#filter #component_id").append('<option value="'+object.id+'">'+object.title+'</option>');
                    });
                    if(component_id>0)
                        $("#filter #component_id").val(component_id);
                    $("#filter #component_id").removeAttr('disabled');
                },
                error: function (xhr, status, error) {
                    //var err = eval("(" + xhr.responseText + ")");
                    //console.log("error:"+err.Message);
                }
            });
    }
</script>
@endsection