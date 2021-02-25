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
    <li class="breadcrumb-item active">Bench -{{$ratingbench->bench()->first()->title}}- Timing</li>
</ol>
<table class="table-sm mb-2 bg-light border" width="100%" cellspacing="0">
    <tr>
      <td class="small"><strong>Availability:&nbsp;</strong>This concept shows if the laboratory test rig is available at SGRE required start date.<br/>
        <strong>Test execution time:&nbsp;</strong>Number of weeks required of each testing phase.<br/>
        <strong>Flexibility:&nbsp;</strong>This concept assesses facilities resolution skills upon unexpected events based on whether SGRE is a priority customer, number of rigs, preservation period, incidents resolution proactivity, etc.
      </td>
    </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($ratingbench->rating()->first()->timesheet()->first()->timecats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $timecat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('ratingtimerequests.index',['ratingbench' => $ratingbench->id,'timecat'=>$item_cat->id]) }}">{{ $item_cat->title }}</a>
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
            <a class="nav-link {{ $class_active }}" href="{{ route('ratingtimerequests.index',['ratingbench' => $ratingbench->id,'timecat'=>$timecat->id,'timesubcat'=>$item_subcat->id]) }}">{{ $item_subcat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>ORDER</th>
            <th>PHASE</th>
            <th class="text-center">VALUE</th>
            <th class="text-center">TYPE (UNIT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($timesubcat->timerequests()->orderby('ordering','asc')->get() as $timerequest)
        @php ($ratingtimerequest = $timerequests[$timerequest->id])
        <tr bgcolor="{{$statecolors[$ratingtimerequest->ratingrequeststate_id]}}">
            <td class="small">{{$timerequest->ordering}}</td>
            <td style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" data-value="{{$ratingtimerequest->value}}" onclick="editRatingTimeRequestValue({{$ratingtimerequest->id}},{{$timerequest->id}},'{{$timerequest->label}}',{{$timerequest->settable}},{{$ratingtimerequest->ratingrequeststate_id}})" id="title_{{$ratingtimerequest->id}}" class="small">{{$timerequest->title}}</td>
            <td class="small text-right">{{number_format($ratingtimerequest->value, 2, ',', '.')}}</td>
            <td class="small text-right">Numeric ({{$timerequest->label}})</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Edit Item Value -->
<div class="modal fade" id="edit-value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit value of timing phase:</h4>
            </div>
            <div class="modal-body">
                <form id="form_value" data-toggle="validator" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="timecat_id" value="{{$timecat->id}}">
                    <input type="hidden" name="timesubcat_id" value="{{$timesubcat->id}}">
                    <div class="form-group">
                        <label class="control-label" for="value" id="title" name="title"></label>
                        <small class="text-info"><span id="help"></span></small>
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col-3">
                                <input type="number" step="any" id="value" name="value" class="form-control text-center" data-error="Please enter valid value." required/>
                            </div>
                            <div class="col-3">
                                <input id="label" type="text" class="form-control text-center" disabled/>
                            </div>
                            <div class="col-4">
                                <select class="form-control small" id="state" name="state" required>
                                    @foreach ($ratingrequeststates as $ratingrequeststate)
                                    <option value="{{$ratingrequeststate->id}}" @if ($ratingrequeststate->id==1) disabled @endif>{{$ratingrequeststate->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="settable_info" class="m-2 bg-light border">
                            <div class="row">
                                <div class="col-1"></div>
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
@endsection
@section('js_custom')
<script type="text/javascript">
function editRatingTimeRequestValue(ratingtimerequest_id,timerequest_id,timerequest_label,settable,ratingrequeststate){
    $("#edit-value #form_value").attr("action","{{ url('ratingtimerequests') }}/"+ratingtimerequest_id);
    $("#edit-value #form_value #title").text($("#title_"+ratingtimerequest_id).text());
    $("#edit-value #form_value #value").val($("#title_"+ratingtimerequest_id).data('value'));
    $("#edit-value #form_value #label").val(timerequest_label);
    $("#edit-value #form_value #state").val(ratingrequeststate);
    if (settable>0) {
        $.ajax({
            type: 'GET',
            url: "{{ url('timerequestsetts') }}/"+timerequest_id,
            success: function(data){
                console.log(data);
                $("#edit-value #form_value #settable_info #items").empty();
                $.each(data, function(index, item) {
                    $("#edit-value #form_value #settable_info #items").append("<div class='row'><div class='col-2'></div><div class='col-3'><label class='control-label'>"+item.percent+"%</label></div><div class='col-3'><input type='text' min='0' class='form-control text-center' value='"+item.value+"' disabled /></div><div class='col-4'><label class='control-label'>"+item.label+"</label></div></div>");
                });
                $("#edit-value #form_value #settable_info").removeAttr('style');
                if (settable==2) {
                    $("#edit-value #form_value #value").attr('min','0');
                    $("#edit-value #form_value #value").attr('max','100');
                    $("#edit-value #form_value #value").attr('step','20');
                }
            },
            error: function (xhr, status, error) {
                //var err = eval("(" + xhr.responseText + ")");
                //console.log("error:"+err.Message);
            }
        });
    } else {
        $("#edit-value #form_value #settable_info").attr('style','display:none');
    }
}
</script>
@endsection