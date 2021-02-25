@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">Timing</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Timing template allows to assess different laboratories by availability, flexibility and productivity times.</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new timing template" data-toggle="modal" data-target="#add-template"></a></td>
        </tr>
</table>

<!-- List Rating Templates -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>TEMPLATE</th>
            <th>DESCRIPTION</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($timesheets as $timesheet)
        <tr style="cursor: pointer;">
            <td class="samll" id="title_{{$timesheet->id}}" onclick="window.location.href='{{ route('timerequests.index',['timesheet' => $timesheet->id])}}'">{{$timesheet->title}}
                @if ($timesheets_state[$timesheet->id]->pending_review>0)
                <i class="fa fa-fw fa-exclamation-circle" title="Review time sheets Availability settings and update" style="color:Gold"></i>
                @endif
            </td>
            <td id="description_{{$timesheet->id}}" class="samll">{{$timesheet->description}}</td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cog" title="Rating score categories weight" data-toggle="modal" data-target="#categories-weights" onclick="categoriesWeights({{$timesheet->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-file-text ml-1" title="New template like this" data-toggle="modal" data-target="#add-template-from" onclick="newTemplateFrom({{ $timesheet->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit template" data-toggle="modal" data-target="#edit-template" onclick="editTemplate({{$timesheet->id}})"></a>
            @if ($timesheet->ratings()->count()>0)
            <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="There are ratings using this template"></a>
            @else
            <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove template" data-toggle="modal" data-target="#delete-template" onclick="deleteTemplate({{$timesheet->id}})"></a>
            @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create time template</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('timesheets.store') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
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
<!-- Create template from -->
<div class="modal fade" id="add-template-from" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New template like</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" method="POST" action="{{ route('timesheets.store') }}">
                    {{ csrf_field() }}
                    <input type="hidden" id="timesheet_id" name="timesheet_id"/>
                    <div class="form-group">
                        <label class="control-label" for="title">Origin template:</label>
                        <input type="text" id="title-origin" class="form-control" disabled="true" readonly="true" />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
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
<!-- Edit item modal -->
<div class="modal fade" id="edit-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit timing template:</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Template title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
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
<!-- Delete Item Modal -->
<div class="modal fade" id="delete-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete timing template:</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="title"></h5>
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
<!-- Timecats weights to calculate rating score -->
<div class="modal fade" id="categories-weights" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Rating score categories weight</h4>
            </div>
            <div class="modal-body">
                <form id="form_weights" data-toggle="validator" method="post">
                    {{ csrf_field() }}
                    <div class="form-group mt-2">
                        <label class="control-label" id="title"></label>
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-5">
                                <label class="control-label font-weight-bold" for="">Category:</label>
                            </div>
                            <div class="col-3">
                                <label class="control-label font-weight-bold" for="">Weight:</label>
                            </div>
                            <div class="col-1">
                                <label class="control-label" for=""></label>
                            </div>
                        </div>
                        <div id="items">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="button" id="btn_weights" class="btn crud-submit btn-success" onclick="categoriesWeightsValidate()">update</button>
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
function newTemplateFrom(item_id){
    $("#add-template-from #form_add #timesheet_id").val(item_id);
    $("#add-template-from #form_add #title-origin").val($("#title_"+item_id).text());
    $("#add-template-from #form_add #description").text($("#description_"+item_id).text());
}
function editTemplate(item_id){
    $("#edit-template #form_edit").attr("action","{{ url('timesheets') }}/"+item_id);
    $("#edit-template #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-template #form_edit #description").text($("#description_"+item_id).text());
}
function deleteTemplate(item_id){
    $("#delete-template #form_delete").attr("action","{{ url('timesheets') }}/"+item_id);
    $("#delete-template #title").text($("#title_"+item_id).text());
}
function categoriesWeights(item_id) {
    $("#categories-weights #form_weights #title").text($("#title_"+item_id).text());
    $("#categories-weights #form_weights").attr('action','{{url('timecats/weight')}}/'+item_id);
    $.ajax({
        type: 'GET',
        url: "{{ url('timecats/weight') }}/"+item_id,
        success: function(data){
            console.log(data);
            var weights_sum=0;
            $("#categories-weights #form_weights #items").empty();
            $.each(data, function(index, item) {
                weights_sum+=item.score_weight;
                $("#categories-weights #form_weights #items").append("<div class='row'><div class='col-2'></div><div class='col-5'><label class='control-label'>"+item.title+"</label></div><div class='col-3'><input type='number' id='weight_"+item.type+"' name='weight_"+item.id+"' class='form-control text-center' min='0' max='1000' value='"+item.score_weight+"' required onchange='categoriesWeightsSum()'/></div><div class='col-1'></div></div>");
            });
            $("#categories-weights #form_weights #items").append("<div class='row'><div class='col-2'></div><div class='col-5'><label class='control-label'>TOTAL:</label></div><div class='col-3'><input type='number' id='weight_sum' class='form-control text-center' value='"+weights_sum+"' readonly='true' disabled/></div><div class='col-1'></div></div>");
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+err.Message);
        }
    });
}
function categoriesWeightsSum() {
    var weights_sum=0;
    for (type=1;type<=3;type++) {
        weights_sum = weights_sum + parseInt($("#categories-weights #form_weights #items #weight_"+type).val());
    }
    $("#categories-weights #form_weights #items #weight_sum").val(weights_sum);
    if (weights_sum==100) {
        $("#categories-weights #form_weights #items #weight_sum").attr('class','form-control text-center bg-success');
        $("#categories-weights #form_weights #btn_weights").attr('class','btn crud-submit btn-success');
    } else {
        $("#categories-weights #form_weights #items #weight_sum").attr('class','form-control text-center bg-warning');
        $("#categories-weights #form_weights #btn_weights").attr('class','btn crud-submit btn-warning');
    }
    return weights_sum;
}
function categoriesWeightsValidate() {
    if (categoriesWeightsSum()==100) {
        $("#categories-weights #form_weights").submit();
    } else {
        alert("Weight sum must be 100");
    }
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2]
    },
    {
        "width": "30%", 
        "targets": 0
    },
    {
        "width": "50%", 
        "targets": 1
    },
    {
        "width": "20%", 
        "targets": 2
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection