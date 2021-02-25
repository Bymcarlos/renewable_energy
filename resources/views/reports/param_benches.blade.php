@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item active">Search by parameters</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Create a ficticial bench and define the desired values of the features, to compare with real benches.</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new bench" data-toggle="modal" data-target="#add-bench"></a></td>
        </tr>
</table>

<!-- List Benches -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>BENCH SEARCH</th>
            <th>COMMENTS</th>
            <th>AREA</th>
            <th>COMPONENT</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($benches as $bench)
        @php ($component = $bench->areaComponent()->first()->component()->first())
        <tr>
            <td class="small" id="bench_{{ $bench->id}}" data-title="{{ $bench->title }}" title="{{ $bench->comments }}">{{ $bench->title }}</td>
            <td class="small">{{$bench->comments}}</td>
            <td class="small">
                @foreach ($component->areas()->get() as $area)
                    {{ $loop->first ? '' : '-' }}
                    {{ $area->title }}
                @endforeach
            </td>
            <td class="small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
            <td class="text-right">
                <a href="{{ route('benches.reports.parameters.bench',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-tasks" title="Set parameter values"></a>
                <a href="{{ route('benches.reports.parameters.show',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-area-chart" title="Show report"></a>
            </td>
            <td class="text-right">
                <a href="#" class="btn btn-primary btn-sm fa fa-edit" data-toggle="modal" data-target="#edit-bench" onclick="editBench({{ $bench->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" data-toggle="modal" data-target="#delete-bench" onclick="deleteBench({{ $bench->id}} )"></a>
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
                <h4 class="modal-title" id="modal_title">New search by parameters bench</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('benches.reports.parameters.store') }}" method="POST">
                    <input type="hidden" name="bench_id" id="bench_id" value="0"/>
                    <input type="hidden" name="entity" id="entity" value="0"/>
                    <input type="hidden" name="area" id="area" value="0"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Search bench title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Area:</small></label>
                        <select class="form-control" id="area_id" name="area_id" onchange="loadComponents(0)" required>
                            <option disabled selected value="0">Select area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->title }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="component_id">Component:</label>
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
                <h4 class="modal-title" id="myModalLabel">Edit search by parameters bench</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" id="id" name="id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Search bench title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
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
                <h4 class="modal-title" id="myModalLabel">Confirm delete search by parameters bench:</h4>
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
    function editBench(bench_id) {
        var route = "{{ route('benches.reports.parameters.update',['bench'=>':id']) }}".replace(':id', bench_id);
        $("#edit-bench #form_edit").attr("action",route);
        $("#edit-bench #id").val(bench_id);
        $("#edit-bench #title").val($("#bench_"+bench_id).data("title"));
        $("#edit-bench #comments").text($("#bench_"+bench_id).attr("title"));
    }
    function deleteBench(bench_id) {
        var route = "{{ route('benches.reports.parameters.destroy',['bench'=>':id']) }}".replace(':id', bench_id);
        $("#delete-bench #form_delete").attr("action",route);
        $("#delete-bench #title").text($("#bench_"+bench_id).data("title"));
    }
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,4,5]
    },
    {
        "width": "14%", 
        "targets": 0
    },
    {
        "width": "40%", 
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
        "width": "8%", 
        "targets": 4
    },
    {
        "width": "8%", 
        "targets": 5
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection