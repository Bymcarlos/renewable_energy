@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active">Benches</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Benches are the ... You can acces to the technical sheet, economical sheet and occupation.</small></td>
        	<td width="14%" class="text-right"><a href="{{ route('benches.export.excel')}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export bench" data-title="Export to excel"></a><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new bench" data-toggle="modal" data-target="#add-bench" onclick="addBench(0,0,0,0,1)"></a></td>
        </tr>
</table>

<!-- List Benches -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>BENCH</th>
            <th class="text-center">NDA</th>
            <th>ENTITY</th>
            <th>CTR</th>
            <th>AREA</th>
            <th>COMPONENT</th>
            <th class="text-center">ST</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($benches as $bench)
        @php ($component = $bench->areaComponent()->first()->component()->first())
        <tr>
            <td class="small" id="bench_{{ $bench->id}}" data-title="{{ $bench->title }}" title="{{ strtoupper($bench->title) }}@if (isset($bench->comments)): {{ $bench->comments }} @endif">
            @if (strlen($bench->title)>30)
            {{substr($bench->title,0,30)}}...
            @else
                {{$bench->title}}
            @endif
            </td>
            <td class="text-center">
            @php ($icon = "ic_status_0.png")
            @php ($tms_today = strtotime(date("Y-m-d")))
            @php ($nda_expire="Unknown value")
            @if (isset($bench->features()->wherePivot('feature_id',1439)->first()->pivot->value))
                @php ($nda_expire = $bench->features()->wherePivot('feature_id',1439)->first()->pivot->value)
                @php ($tms_expire = strtotime($nda_expire))
                @if ($tms_expire>=$tms_today)
                    @php ($icon = "ic_status_1.png")
                    <!-- Limit date, 1 month to expire => yellow icon else green icon -->
                    @if ($tms_expire>($tms_today+2674800))
                        @php ($icon = "ic_status_2.png")
                    @endif
                @endif
            @endif
                <img src="{{ asset('icons') }}/{{$icon}}" title="{{$nda_expire}}"/></td>
            <td class="small">{{ $bench->entity()->first()->title }}</td>
            <td class="text-center"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->country()->first()->title }}" width="24"/></td>
            <td class="small">
                @foreach ($component->areas()->get() as $area)
                    {{ $loop->first ? '' : '-' }}
                    {{ $area->title }}
                @endforeach
            </td>
            <td class="small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
            <td class="text-center"><img src="{{ asset('icons/ic_status_'.$bench->status.'.png') }}"/></td>
            <td class="text-right">
                @if(Auth::user()->hasAnyRole(['admin','editor']))
                <a href="{{ route('bench.export',['bench'=>$bench->id])}}" class="btn btn-primary btn-sm fa fa-download" title="Export bench" data-title="Export"></a>
                <a href="#" class="btn btn-primary btn-sm fa fa-upload" data-toggle="modal" data-target="#modal-import" title="Import bench" onclick="importBench({{$bench->id}})"></a>
                @endif
            </td>
            <td class="text-right">
                @if(Auth::user()->hasAnyRole(['admin','editor']))
                <a href="{{ route('bench.assessments.technical',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-tasks" title="Technical sheet"></a>
                @endif
                @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('occupation.index',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-calendar" title="Occupation"></a>
                <a href="{{ route('bench.assessments.economical',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-euro" title="Economic sheet"></a>
                @endif
            </td>
            <td class="text-right">
                @if(Auth::user()->hasRole('admin'))
                <a href="#" class="btn btn-primary btn-sm fa fa-file-text" title="New bench like this" data-title="New from existing bench" data-toggle="modal" data-target="#add-bench" onclick="addBench({{ $bench->id }},'{{ $bench->entity()->first()->id }}','{{ $bench->areaComponent()->first()->area_id }}','{{ $bench->areaComponent()->first()->component_id }}',{{ $bench->country_id }})"></a>
                <a href="#" class="btn btn-primary btn-sm fa fa-edit" data-toggle="modal" data-target="#edit-bench" onclick="editBench({{ $bench->id }},{{ $bench->entity_id }},{{ $bench->country_id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" data-toggle="modal" data-target="#delete-bench" onclick="deleteBench({{ $bench->id}} )"></a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Bench modal -->
<div class="modal fade" id="add-bench" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">New Bench</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('benches.store') }}" method="POST">
                    <input type="hidden" name="bench_id" id="bench_id" value="0"/>
                    <input type="hidden" name="entity" id="entity" value="0"/>
                    <input type="hidden" name="area" id="area" value="0"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Bench name:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label class="control-label" for="entity_id">Entity:</label>
                            <select class="form-control" id="entity_id" name="entity_id" required>
                                <option disabled selected value="0">Select entity</option>
                            @foreach ($entities as $entity)
                                <option value="{{ $entity->id }}">{{ $entity->title }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="control-label" for="entity_id">Country:</label>
                            <select class="form-control" id="country_id" name="country_id" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->title }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Area: <small>(If not exist the Area, cancel and create it first)</small></label>
                        <select class="form-control" id="area_id" name="area_id" onchange="loadComponents(0)" required>
                            <option disabled selected value="0">Select area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->title }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="component_id">Component: <small>(If not exist the Component, cancel and create it first)</small></label>
                        <select class="form-control" id="component_id" name="component_id" required>
                            <option disabled selected value>Select component</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="comments">Comments:</label>
                        <textarea class="form-control" id="comments" name="comments"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Bench Modal -->
<div class="modal fade" id="edit-bench" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Bench</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" id="id" name="id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Bench name:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label class="control-label" for="entity_id">Entity:</label>
                            <select class="form-control" id="entity_id" name="entity_id" required>
                                <option disabled selected value="0">Select entity</option>
                            @foreach ($entities as $entity)
                                <option value="{{ $entity->id }}">{{ $entity->title }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="control-label" for="country_id">Country:</label>
                            <select class="form-control" id="country_id" name="country_id" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->title }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="comments">Comments:</label>
                        <textarea class="form-control" id="comments" name="comments"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Bench Modal -->
<div class="modal fade" id="delete-bench" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Bench:</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal import bench -->
<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Import bench features</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_import" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="value">Select file to import:</label>
                        <input type="file" id="import_file" name="import_file" class="form-control" data-error="Select excel file"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Import</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js_custom')
<script type="text/javascript">
function loadComponents(component_id){
    $.ajax({
            type: 'POST',
            url: "{{ url('area/components') }}",
            data: { _token: "{{ csrf_token() }}", area_id: $("#add-bench #area_id").val() },
            success: function(data){
                //console.log(data);
                $("#add-bench #component_id").empty();
                $("#add-bench #component_id").append('<option disabled selected value>Select component</option>');
                $.each(data, function(index, object){
                    $("#add-bench #component_id").append('<option value="'+object.id+'">'+object.title+'</option>');
                });
                if(component_id>0)
                    $("#add-bench #component_id").val(component_id);
                $("#add-bench #component_id").removeAttr('disabled');
            },
            error: function (xhr, status, error) {
                //var err = eval("(" + xhr.responseText + ")");
                //console.log("error:"+err.Message);
            }
        });
}
function addBench(item_id,entity_id,area_id,component_id,country_id) {
    $("#add-bench #bench_id").val(item_id);
    $("#add-bench #entity_id").val(entity_id);
    $("#add-bench #country_id").val(country_id);
    $("#add-bench #entity").val(entity_id);
    $("#add-bench #area_id").val(area_id);
    $("#add-bench #area").val(area_id);
    if (item_id==0) {  
        $("#add-bench #modal_title").text("Create new Bench");
        $("#add-bench #entity_id").removeAttr('disabled');
        $("#add-bench #area_id").removeAttr('disabled');
        $("#add-bench #component_id").empty();
        $("#add-bench #component_id").append('<option disabled selected value>Select component</option>');
        $("#add-bench #component_id").attr('disabled', 'disabled');
    } else {
        $("#add-bench #modal_title").text("Create new Bench from '"+$("#title_"+item_id).text()+"'");
        $("#add-bench #comments").text($("#title_"+item_id).attr("title"));
        $("#add-bench #entity_id").attr('disabled', 'disabled');
        $("#add-bench #area_id").attr('disabled', 'disabled');
        loadComponents(component_id);
    }
}
function editBench(bench_id,entity_id,country_id) {
    var route = "{{ route('benches.update',['bench'=>':id']) }}".replace(':id', bench_id);
    $("#edit-bench #form_edit").attr("action",route);
    $("#edit-bench #id").val(bench_id);
    $("#edit-bench #title").val($("#bench_"+bench_id).data("title"));
    $("#edit-bench #comments").text($("#bench_"+bench_id).attr("title"));
    $("#edit-bench #country_id").val(country_id);
    $("#edit-bench #entity_id").val(entity_id);
}
function deleteBench(bench_id) {
    var route = "{{ route('benches.destroy',['bench'=>':id']) }}".replace(':id', bench_id);
    $("#delete-bench #form_delete").attr("action",route);
    $("#delete-bench #title").text($("#bench_"+bench_id).data("title"));
}
function importBench(bench_id) {
    var route = "{{ route('bench.import',['bench'=>':id']) }}".replace(':id', bench_id);
    $("#modal-import #form_import").attr("action",route);
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [5,6,7,8,9]
    },
    {
        "width": "16%", 
        "targets": 0
    },
    {
        "width": "1%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "1%", 
        "targets": 3
    },
    {
        "width": "15%", 
        "targets": 4
    },
    {
        "width": "10%", 
        "targets": 5
    },
    {
        "width": "1%", 
        "targets": 6
    },
    {
        "width": "12%", 
        "targets": 7
    },
    {
        "width": "17%", 
        "targets": 8
    },
    {
        "width": "17%", 
        "targets": 9
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection