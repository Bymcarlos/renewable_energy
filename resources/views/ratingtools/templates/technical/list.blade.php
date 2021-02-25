@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('techsheets.areas') }}">Technical - Areas</a></li>
    <li class="breadcrumb-item active">{{$area->title}} templates</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%" class="small">Technical template contains the technical capabilities (requirements) upon which the laboratory will be evaluated.</td>
        	<td width="14%" class="text-right">@if (count($inputsheets)>0)<a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new technical template" data-toggle="modal" data-target="#add-template"></a>@else<a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new technical template" data-toggle="modal" data-target="#inputsheets-warning"></a>@endif</td>
        </tr>
</table>

<!-- Templates list-->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>TECHNICAL TEMPLATE</th>
            <th>DESCRIPTION</th>
            <th>REQUESTS FROM TS TEMPLATE</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($techsheets as $techsheet)
        @php ($inputsheet = $techsheet->inputsheet()->first())
        <tr style="cursor: pointer;">
            <td id="title_{{$techsheet->id}}" class="small" onclick="window.location.href='{{ route('techrequests.index',['id' => $techsheet->id])}}'">{{$techsheet->title}}</td>
            <td id="description_{{$techsheet->id}}" class="small">{{$techsheet->description}}</td>
            <td class="small">{{$inputsheet->title}}</td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cog" title="Criticalities weights" data-toggle="modal" data-target="#criticalities-weights" onclick="criticalitiesWeights({{$techsheet->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-file-text ml-1" title="New template like this" data-toggle="modal" data-target="#add-template-from" onclick="newInputFrom({{$techsheet->id}},{{$inputsheet->id}},'{{$inputsheet->title}}')"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit template" data-toggle="modal" data-target="#edit-template" onclick="editTemplate({{$techsheet->id}})"></a>@if ($techsheet->ratings()->count()>0)<a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="Existing ratings relating to this technical template"></a>
            @else<a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove template" data-toggle="modal" data-target="#delete-template" onclick="deleteTemplate({{$techsheet->id}})"></a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Criticalities weights for a technical template -->
<div class="modal fade" id="criticalities-weights" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Criticalities weights</h4>
            </div>
            <div class="modal-body">
                <form id="form_weights" data-toggle="validator" method="post">
                    {{ csrf_field() }}
                    <div class="form-group mt-2">
                        <label class="control-label" id="title"></label>
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-3">
                                <label class="control-label font-weight-bold" for="">Criticality:</label>
                            </div>
                            <div class="col-3">
                                <label class="control-label font-weight-bold" for="">Weight:</label>
                            </div>
                            <div class="col-4">
                                <label class="control-label" for=""></label>
                            </div>
                        </div>
                        <div id="items">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="button" id="btn_weights" class="btn crud-submit btn-success" onclick="criticalitiesWeightsValidate()">update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- No inputsheets availables in this area -->
<div class="modal fade" id="inputsheets-warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Technical template</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="title">No input from TS templates availables in this area. It's necessary to create an input from TS template for this area, before create a technical template.</h5>
            </div>
            <div class="modal-body">
                <form id="form_delete" method="GET" action="{{route('inputsheets.index',['area_id'=>$area->id])}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Show input templates</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Create Template Modal -->
<div class="modal fade" id="add-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Template</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('techsheets.store') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    <div class="form-group">
                        <label class="control-label" for="title">Template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area">Area:</label>
                        <select class="form-control" id="area" disabled>
                            <option selected>{{$area->title}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="inputsheet_id">Input data template:</label>
                        <select class="form-control" id="inputsheet_id" name="inputsheet_id" required>
                            <option disabled selected value="0">Select input data template</option>
                            @foreach ($inputsheets as $inputsheet)
                                <option value="{{$inputsheet->id}}">{{$inputsheet->title}}</option>
                            @endforeach
                        </select>
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
<!-- Create Template from other -->
<div class="modal fade" id="add-template-from" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New template like:</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" method="POST" action="{{ route('techsheets.store') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    <input type="hidden" id="inputsheet_id" name="inputsheet_id"/>
                    <input type="hidden" id="techsheet_id" name="techsheet_id"/>
                    <div class="form-group">
                        <label class="control-label" for="title">Origin template:</label>
                        <input type="text" id="title-origin" class="form-control" disabled="true" readonly="true" />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">New template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area">Area:</label>
                        <select class="form-control" disabled>
                            <option selected>{{$area->title}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="inputsheet">Input data template:</label>
                        <select class="form-control" id="inputsheet" disabled="true">
                        </select>
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
<!-- Edit Item Modal -->
<div class="modal fade" id="edit-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit technical template</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Technical template title:</label>
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
                <h4 class="modal-title" id="myModalLabel">Confirm delete technical template:</h4>
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
@endsection
@section('js_custom')
<script type="text/javascript">
function editTemplate(item_id){
    $("#edit-template #form_edit").attr("action","{{ url('techsheets') }}/"+item_id);
    $("#edit-template #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-template #form_edit #description").text($("#description_"+item_id).text());
}
function deleteTemplate(item_id){
    $("#delete-template #form_delete").attr("action","{{ url('techsheets') }}/"+item_id);
    $("#delete-template #title").text($("#title_"+item_id).text());
}
function newInputFrom(techsheet_id,inputsheet_id,inputsheet_title){
    $("#add-template-from #form_add #techsheet_id").val(techsheet_id);
    $("#add-template-from #form_add #inputsheet_id").val(inputsheet_id);
    $("#add-template-from #form_add #inputsheet").empty();
    $("#add-template-from #form_add #inputsheet").append("<option>"+inputsheet_title+"</option>");
    $("#add-template-from #form_add #title-origin").val($("#title_"+techsheet_id).text());
    $("#add-template-from #form_add #description").text($("#description_"+techsheet_id).text());
}
function criticalitiesWeights(item_id) {
    $("#criticalities-weights #form_weights #title").text($("#title_"+item_id).text());
    $("#criticalities-weights #form_weights").attr('action','{{url('techsheet/criticalities/weights')}}/'+item_id);
    $.ajax({
        type: 'GET',
        url: "{{ url('techsheet/criticalities/weights') }}/"+item_id,
        success: function(data){
            var weights_sum=0;
            $("#criticalities-weights #form_weights #items").empty();
            $.each(data, function(index, item) {
                weights_sum+=item.weight;
                $("#criticalities-weights #form_weights #items").append("<div class='row'><div class='col-2'></div><div class='col-3'><label class='control-label'>"+item.label+"</label></div><div class='col-4'><input type='number' id='weight_"+index+"' name='weight_"+index+"' class='form-control text-center' min='0' max='1000' value='"+item.weight+"' required onchange='criticalitiesWeightsSum()'/></div><div class='col-3'></div></div>");
            });
            $("#criticalities-weights #form_weights #items").append("<div class='row'><div class='col-2'></div><div class='col-3'><label class='control-label'>TOTAL:</label></div><div class='col-4'><input type='number' id='weight_sum' class='form-control text-center' value='"+weights_sum+"' readonly='true' disabled/></div><div class='col-3'></div></div>");
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+err.Message);
        }
    });
}
function criticalitiesWeightsSum() {
    var weights_sum=0;
    for (i=1;i<=3;i++) {
        weights_sum = weights_sum + parseInt($("#criticalities-weights #form_weights #items #weight_"+i).val());
    }
    $("#criticalities-weights #form_weights #items #weight_sum").val(weights_sum);
    if (weights_sum==1000) {
        $("#criticalities-weights #form_weights #items #weight_sum").attr('class','form-control text-center bg-success');
        $("#criticalities-weights #form_weights #btn_weights").attr('class','btn crud-submit btn-success');
    } else {
        $("#criticalities-weights #form_weights #items #weight_sum").attr('class','form-control text-center bg-warning');
        $("#criticalities-weights #form_weights #btn_weights").attr('class','btn crud-submit btn-warning');
    }
    return weights_sum;
}
function criticalitiesWeightsValidate() {
    if (criticalitiesWeightsSum()==1000) {
        $("#criticalities-weights #form_weights").submit();
    } else {
        alert("Weight sum must be 1000");
    }
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2,3]
    },
    {
        "width": "25%", 
        "targets": 0
    },
    {
        "width": "30%", 
        "targets": 1
    },
    {
        "width": "25%", 
        "targets": 2
    },
    {
        "width": "20%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection