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
    <li class="breadcrumb-item active">Requests from TS</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Fill Requests values with TS information.</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($rating->techsheet()->first()->inputsheet()->first()->inputcats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $inputcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('ratinginputrequests.index',['rating' => $rating->id,'inputcat' => $item_cat->id]) }}">{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
    </div>
</div>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>REQUESTS</th>
            <th>SET VALUE</th>
            <th>TYPE (UNIT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inputcat->inputrequests()->get() as $inputrequest)
        @php ($ratinginputrequest = $inputrequests[$inputrequest->id])
        @php ($techrequest=$inputrequest->techrequests()->first())
        @php ($feature=null)
        @php ($responsetype=null)
        @php ($responsetype_id=0)
        @if (isset($techrequest))
            @php ($feature=$techrequest->feature()->first())
            @if (isset($feature))
                @php ($responsetype=$feature->responsetype()->first())
                @php ($responsetype_id=$responsetype->id)
                @php ($unit=$feature->unit()->first())
            @endif
        @endif
        <tr class="bg-light">
            @if (isset($techrequest))
                <td style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editRatingInputRequestValue('{{$ratinginputrequest->id}}','{{$ratinginputrequest->value}}','{{$responsetype_id}}')" id="title_{{$ratinginputrequest->id}}">
            @else
                <td>
            @endif
            <small>{{$inputrequest->title}}</small>
            @if ($inputrequest->help)
                <br/><small class="text-success">(<span id="help_{{$ratinginputrequest->id}}">{{$inputrequest->help}}</span>)</small>
            @endif</td>
            @if (isset($responsetype))
                @if (isset($ratinginputrequest->value))
                    <td><small>{{$ratinginputrequest->value}}</small><i class="fa fa-check-circle float-right" style="color:green;" title="Value relating to technical requirement"></td>
                @else
                    <td><i class="fa fa-exclamation-circle float-right" style="color:red;" title="Value required for technical requirement"></i></td>
                @endif
                <td class="small" id="unit_{{$ratinginputrequest->id}}">{{$responsetype->title}} @if ($responsetype->id==3) ({{$unit->title}}) @endif</td>
            @else
                <td></td>
                <td></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Edit Item Value -->
<div class="modal fade" id="edit-value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input request value:</h4>
            </div>
            <div class="modal-body">
                <form id="form_value" data-toggle="validator" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="inputcat_id" value="{{$inputcat->id}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <label class="control-label" for="value" id="title" name="title"></label>
                                <small class="text-info"><span id="help"></span></small>
                            </div>
                        </div>
                        <div class="row">
                            <div id="content" class="col-5">
                            </div>
                            <div id="unit_selection" class="col-5">
                                <select class="form-control" id="unit_id" disabled>
                                </select>
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
function editRatingInputRequestValue(ratinginputrequest_id,ratinginputrequest_value,responsetype_id){
    $("#edit-value #form_value").attr("action","{{ url('ratinginputrequests') }}/"+ratinginputrequest_id);
    $("#edit-value #form_value #title").text($("#title_"+ratinginputrequest_id).text());
    $("#edit-value #form_value #help").text($("#help_"+ratinginputrequest_id).text());
    $("#edit-value #form_value #content").empty();
    $("#edit-value #form_value #unit_id").empty();
    switch(responsetype_id) {
        case '2':
            $("#edit-value #form_value #content").append("<select class='form-control' id='value' name='value'><option value='Yes'>Yes</option><option value='No'>No</option></select>");
            $("#edit-value #form_value #content #value").val(ratinginputrequest_value);
            $("#edit-value #form_value #unit_id").append('<option selected>Yes/No</option>');
            break;
        case '3':
            unit = $("#unit_"+ratinginputrequest_id).text();
            $("#edit-value #form_value #content").append("<input type='number' step='any' class='form-control' name='value' value='"+ratinginputrequest_value+"'/>");
            $("#edit-value #form_value #unit_id").append('<option selected>'+unit+'</option>');
            break;
        case '4':
            $("#edit-value #form_value #content").append("<input type='date' class='form-control' name='value' value='"+ratinginputrequest_value+"'/>");
            $("#edit-value #form_value #unit_id").append('<option selected>Date</option>');
            break;
    };
    
}
$('#dt_list').DataTable({
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": [0,1,2]
        },
        {
            "width": "50%", 
            "targets": 0
        },
        {
            "width": "10%", 
            "targets": 1
        },
        {
            "width": "40%", 
            "targets": 2
        }],
        "bSort" : false,
        "dom": 'rt<"bottom"p>',
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
    });
</script>
@endsection