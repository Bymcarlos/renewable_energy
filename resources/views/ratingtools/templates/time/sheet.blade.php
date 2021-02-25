@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('timesheets.index') }}">Timing</a></li>
    <li class="breadcrumb-item active">{{$timesheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Timing template allows to assess different laboratories by availability, flexibility and productivity times.</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($timesheet->timecats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $timecat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('timerequests.index',['timesheet' => $timesheet->id,'timecat' => $item_cat->id]) }}">{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
<!--Subcategories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($timecat->timesubcats()->get() as $item_subcat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_subcat->id == $timesubcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('timerequests.index',['timesheet' => $timesheet->id,'timecat' => $timecat->id,'timesubcat' => $item_subcat->id]) }}">{{ $item_subcat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%"><small>Time related rating ... </small></td>
      <td width="14%" class="text-right">@if ($timesubcat->administrable)<a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New phase" data-toggle="modal" data-target="#timerequest-add"></a>@endif</td>
    </tr>
</table>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>ORDER</th>
            <th>PHASE</th>
            <th>TYPE</th>
            <th>UNIT</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($timesubcat->timerequests()->orderby('ordering','asc')->get() as $timerequest)
        @php ($last_order=$timerequest->ordering)
        <tr>
            <td id="ordering_{{$timerequest->id}}"><small>{{$timerequest->ordering}}</small></td>
            <td id="title_{{$timerequest->id}}"><small>{{$timerequest->title}}</small></td>
            <td><small>Numeric</small></td>
            <td><small>{{$timerequest->label}}</small></td>
            <td class="text-right">
            @if ($timerequest->settable>0)
                @php ($bt = "btn-primary")
                @if ($timerequest->state<0) @php ($bt = "btn-warning") @endif
                <a href="#" class="btn {{$bt}} btn-sm fa fa-cog" title="Settings" data-toggle="modal" data-target="#settings" onclick="loadSettings({{$timerequest->id}},{{$timerequest->settable}})"></a>
            @else
                <a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit time request" data-toggle="modal" data-target="#timerequest-edit" onclick="editRequest({{$timerequest->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Delete time request" data-toggle="modal" data-target="#timerequest-delete" onclick="deleteRequest({{$timerequest->id}})"></a>
            @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="timerequest-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create phase</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('timerequests.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}" />
                    <input type="hidden" name="timecat_id" value="{{ $timecat->id }}" />
                    <input type="hidden" name="timesubcat_id" value="{{ $timesubcat->id }}" />
                    <div class="form-group mt-2">
                        <div class="row mt-2">
                            <div class="col-2">
                                <label class="control-label" for="order"><strong>Order:</strong></label>
                            </div>
                            <div class="col-3">
                                <input type="text" pattern="[0-9]+" id="ordering" name="ordering" min="1" class="form-control" data-error="Please enter valid order" placeholder="{{$last_order+1}}"/>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title"><strong>Phase title:</strong></label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title" required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="control-label" for="responsetype_id"><strong>Response type:</strong></label>
                            </div>
                            <div class="col-7">
                                <select class="form-control" id="responsetype_id" disabled>
                                    <option selected>Numeric</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="control-label" for="unit_id"><strong>Unit:</strong></label>
                            </div>
                            <div class="col-7">
                                <select class="form-control" id="unit_id" disabled>
                                    <optgroup label="Time">
                                        <option selected>weeks</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Availability settings -->
<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Settings</h4>
            </div>
            <div class="modal-body">
                <form id="form_settings" data-toggle="validator" method="post">
                    <input type="hidden" name="timesheet_id" value="{{$timesheet->id}}"/>
                    <input type="hidden" name="timecat_id" value="{{$timecat->id}}"/>
                    <input type="hidden" name="timesubcat_id" value="{{$timesubcat->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group mt-2">
                        <label class="control-label" id="title"></label>
                        <div class="row">
                            <div class="col-2"></div>
                            <div class="col-3">
                                <label class="control-label" for="">Percent (%):</label>
                            </div>
                            <div class="col-3">
                                <label class="control-label" for="">Max value:</label>
                            </div>
                            <div class="col-4">
                                <label class="control-label" for=""></label>
                            </div>
                        </div>
                        <div id="items">
                        </div>
                        
                    </div>
                    <div class="form-group mt-2">
                        <button id="btn_settings_update" type="button" class="btn crud-submit btn-success" onclick="settingsValidate()">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Item Modal -->
<div class="modal fade" id="timerequest-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create phase</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" name="timesheet_id" value="{{ $timesheet->id }}" />
                    <input type="hidden" name="timecat_id" value="{{ $timecat->id }}" />
                    <input type="hidden" name="timesubcat_id" value="{{ $timesubcat->id }}" />
                    <div class="form-group mt-2">
                        <div class="row mt-2">
                            <div class="col-2">
                                <label class="control-label" for="order"><strong>Order:</strong></label>
                            </div>
                            <div class="col-3">
                                <input type="text" pattern="[0-9]+" id="ordering" name="ordering" min="1" class="form-control" data-error="Please enter valid order" placeholder="{{$last_order+1}}"/>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title"><strong>Phase title:</strong></label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title" required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="control-label" for="responsetype_id"><strong>Response type:</strong></label>
                            </div>
                            <div class="col-7">
                                <select class="form-control" id="responsetype_id" disabled>
                                    <option selected>Numeric</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="control-label" for="unit_id"><strong>Unit:</strong></label>
                            </div>
                            <div class="col-7">
                                <select class="form-control" id="unit_id" disabled>
                                    <optgroup label="Time">
                                        <option selected>weeks</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Inputrequest -->
<div class="modal fade" id="timerequest-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $timecat->title }} / {{ $timesubcat->title}}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="DELETE"/>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <h5 class="modal-content" id="title"></h5>
                    </div>
                    <div class="form-group mt-2">
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
function editRequest(timerequest_id){
    $("#timerequest-edit #form_edit").attr("action","{{ url('timerequests') }}/"+timerequest_id);
    $("#timerequest-edit #form_edit #title").val($("#title_"+timerequest_id).text());
    $("#timerequest-edit #form_edit #ordering").val($("#ordering_"+timerequest_id).text());
}
function deleteRequest(timerequest_id){
    $("#timerequest-delete #form_delete").attr("action","{{ url('timerequests') }}/"+timerequest_id);
    $("#timerequest-delete #form_delete #title").text($("#title_"+timerequest_id).text());
}
function loadSettings(timerequest_id,settable_type){
    //settable_type->1: Settings editables
    //settable_type->2: Settings read only
    $("#settings #form_settings #title").text($("#title_"+timerequest_id).text());
    if (settable_type==1) {
        $("#settings #form_settings").attr('action','{{url('timerequestsetts')}}/'+timerequest_id);
        $("#settings #form_settings #btn_settings_update").removeAttr('style');
    } else {
        $("#settings #form_settings #btn_settings_update").attr('style','display:none');
    }
    
    $.ajax({
        type: 'GET',
        url: "{{ url('timerequestsetts') }}/"+timerequest_id,
        success: function(data){
            $("#settings #form_settings #items").empty();
            $.each(data, function(index, item) {
                $("#settings #form_settings #items").append("<div class='row'><div class='col-2'></div><div class='col-3'><label class='control-label'>"+item.percent+"%</label></div><div class='col-3'><input type='number' min='0' id='sett_value_"+item.id+"' name='value_"+item.id+"' class='form-control text-center' value='"+item.value+"' required /></div><div class='col-4'><label class='control-label'>"+item.label+"</label></div></div>");
                if (settable_type==2) {
                    $("#settings #form_settings #items #sett_value_"+item.id).attr('disabled','true');
                }
            });
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+err.Message);
        }
    });
}
function settingsValidate() {
    var null_values = 0;
    $("input[id^='sett_value_']").each(function() {
        if ($(this).val().length === 0) null_values++;
    });
    if (null_values>0) {
        alert("Please set all values");
    } else {
        $("#settings #form_settings").submit();
    }
}
//Datatable:
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [0,1,2,3,4]
    },
    {
        "width": "1%", 
        "targets": 0
    },
    {
        "width": "69%", 
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
        "width": "10%", 
        "targets": 4
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection