@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.areas') }}">Ratings - Areas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.index',['area'=>$rating->area_id]) }}">{{$rating->area()->first()->title}}</a></li>
    <li class="breadcrumb-item">{{$rating->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light border" width="100%" cellspacing="0">
        <tr>
          <td colspan="3" class="small"><strong>Technical rating</strong> procedure consists in the next four phases:</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-tasks" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Set involved test types and define requirements criticality level in TBC</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-file-text-o" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Set input data from TS</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-table" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Select laboratories for the rating</td>
        </tr>
        <tr>
          <td colspan="3" class="small">Once these values area filled, technical results icon will be available: <i class="fa fa-fw fa-sm fa-tachometer" style="color:#007bff;"></i></td>
        </tr>
        <tr>
          <td colspan="3" class="small"><strong>Timing and economical rating</strong> procedures are analogue. They are based on the next three phases:</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-table" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Select contacted laboratories</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-clock-o" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Fill timing values from laboratories quotation (column SELECTED BENCHES - VALUES)</td>
        </tr>
        <tr>
            <td width="1%"></td>
            <td width="1%" class="small"><i class="fa fa-fw fa-sm fa-euro" style="color:#007bff;"></i></td>
            <td width="98%" class="small">Fill economical values from laboratories quotation (column SELECTED BENCHES - VALUES)</td>
        </tr>
        <tr>
          <td colspan="3" class="small">Once these values area filled, timing and economical results will be available (column RESULTS): <i class="fa fa-fw fa-sm fa-clock-o" style="color:#007bff;"></i>&nbsp;<i class="fa fa-fw fa-sm fa-euro" style="color:#007bff;"></i></td>
        </tr>
</table>

<div class="row border m-1 badge-info"><div class="col text-center"><strong>RATING:</strong>&nbsp;{{$rating->title}}</div></div>
<table class="table-bordered table-sm table-hover" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th class="small font-weight-bold">ASSOCIATED TEMPLATES</th>
            <th class="small font-weight-bold text-center">VALUES</th>
            <th class="small font-weight-bold text-center">BENCHES SELECTION</th>
            <th class="small font-weight-bold text-center">SELECTED BENCHES - VALUES</th>
            <th class="small font-weight-bold text-center">RESULTS</th>
            <th class="small font-weight-bold text-center">TOOLS</th>
        </tr>
    </thead>
    <tbody>
        @php ($color_tech="#007bff")
        @php ($title_tech="Criticality and test types")
        @php ($scores_tech = true)

        @if ($ratingtechcats_applicable_pending[$rating->id]>0) 
            @php ($color_tech="red") 
            @php ($title_tech="Pending to define applicable categories")
            @php ($scores_tech = false)
        @else
            @if (isset($rating_criticalities[$rating->id][4]) || isset($rating_criticalities[$rating->id][5]))
                @php ($color_tech="red") 
                @php ($title_tech="Criticalities undefined")
                @php ($scores_tech = false)
            @else
                @for($criticality_id=1; $criticality_id<=3; $criticality_id++)
                    @if (!isset($rating_criticalities[$rating->id][$criticality_id]))
                        @php ($color_tech="red") 
                        @php ($title_tech="Needed at least one requirement of each criticality")
                        @php ($scores_tech = false)
                        @exit
                    @endif
                @endfor
            @endif
        @endif

        @php ($color_input="#007bff")
        @php ($title_input="Requests from TS values")
        @if ($rating_inputrequests_pending[$rating->id]>0) 
            @php ($color_input="red") 
            @php ($title_input="Pending to set input values")
            @php ($scores_tech = false)
        @endif
        
        @php ($color_benches="red")
        @php ($title_benches="Select benches for the rating")
        @php ($benches_selected = false) 
        @if (isset($rating_benches_count[$rating->id]) && $rating_benches_count[$rating->id]->benches>0) 
            @php ($color_benches="#007bff")
            @php ($title_benches="Benches selected") 
            @php ($benches_selected = true)
        @endif
        <tr>
            <td class="small">
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-tachometer"></i>&nbsp;{{$rating->techsheet()->first()->title}}</div></div>
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-clock-o"></i>&nbsp;{{$rating->timesheet()->first()->title}}</div></div>
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-euro"></i>&nbsp;{{$rating->economicsheet()->first()->title}}</div></div>
            </td>
            <td class="small text-center">
                <a href="{{ route('ratingtechrequests.index',['rating'=>$rating->id])}}" title="{{$title_tech}}"><i class="fa fa-fw fa-lg fa-tasks" style="color:{{$color_tech}};"></i></a>
                <a href="{{ route('ratinginputrequests.index',['rating'=>$rating->id])}}" title="{{$title_input}}"><i class="fa fa-fw fa-lg fa-file-text-o" style="color:{{$color_input}};"></i></a>
            </td>
            <td class="small text-center"><a href="{{ route('ratingbenches.index',['rating'=>$rating->id])}}" title="Select benches for the rating"><i class="fa fa-fw fa-lg fa-table" style="color:{{$color_benches}};"></i></a></td>
            @if ($benches_selected)
                @php ($show_timing_score = true)
                @php ($show_economic_score = true)
                <td class="small text-center">
                    @foreach($rating->ratingbenches()->get() as $ratingbench)
                        @php ($bench = $ratingbench->bench()->first())
                        <div class="row">
                            <div class="col-7 text-left"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->title }}" width="14"/>&nbsp;<span class="small">{{substr($bench->title,0,20)}}</span></div>
                            <div class="col-5 text-center">
                            @if ($benches_time_request_undefined[$bench->id]->total<=0)
                                <a href="{{ route('ratingtimerequests.index',['ratingbench'=>$ratingbench->id])}}" title="{{$bench->title}}: Timing template values"><i class="fa fa-fw fa-lg fa-clock-o" style="color:#007bff;"></i></a>
                            @else
                                @php ($show_timing_score = false)
                                @php ($undefined_values = $benches_time_request_undefined[$bench->id]->total)
                                <a href="{{ route('ratingtimerequests.index',['ratingbench'=>$ratingbench->id])}}" title="{{$bench->title}}: Timing request values undefined: {{$undefined_values}}"><i class="fa fa-fw fa-lg fa-clock-o" style="color:red;"></i></a>
                            @endif
                            @if ($benches_economic_request_undefined[$bench->id]->total<=0)
                                <a href="{{ route('ratingeconomicrequests.index',['ratingbench'=>$ratingbench->id])}}" title="{{$ratingbench->bench()->first()->title}}: Economic template values"><i class="fa fa-fw fa-lg fa-euro" style="color:#007bff;"></i></a>
                            @else
                                @php ($show_economic_score = false)
                                @php ($undefined_values = $benches_economic_request_undefined[$bench->id]->total)
                                <a href="{{ route('ratingeconomicrequests.index',['ratingbench'=>$ratingbench->id])}}" title="{{$ratingbench->bench()->first()->title}}: Economic request values undefined: {{$undefined_values}}"><i class="fa fa-fw fa-lg fa-euro" style="color:red;"></i></a>
                            @endif
                            </div>
                        </div>
                    @endforeach
                </td>
                <td class="small text-center">
                    @if ($scores_tech)
                        <a href="{{ route('scores.technical',['rating'=>$rating->id])}}" title="Show technical rating"><i class="fa fa-fw fa-lg fa-tachometer" style="color:#007bff;"></i></a>
                    @else
                        <i class="fa fa-fw fa-lg fa-tachometer" style="color:red;" title="Complete availability setting in timing template and fill selected benches values"></i>
                    @endif
                    @if ($show_timing_score)
                        <a href="{{ route('scores.timing',['rating'=>$rating->id])}}" title="Show timing rating"><i class="fa fa-fw fa-lg fa-clock-o" style="color:#007bff;"></i></a>
                    @else
                        <i class="fa fa-fw fa-lg fa-clock-o" style="color:red;" title="Pending set time execution values for one or more benches"></i>
                    @endif
                    @if ($show_economic_score)
                        <a href="{{ route('scores.economics',['rating'=>$rating->id])}}" title="Show economics rating"><i class="fa fa-fw fa-lg fa-euro" style="color:#007bff;"></i></a>
                    @else
                        <i class="fa fa-fw fa-lg fa-euro" style="color:red;" title="Pending set cost values for one or more benches"></i>
                    @endif
                </td>
                <td class="small text-center">
                    <a href="#" data-toggle="modal" data-target="#store-rating" title="Export full rating to excel and store on server" onclick="storeRating({{$rating->id}})"><i class="fa fa-fw fa-lg fa-save" style="color:#007bff;"></i></a>
                    <a href="#" data-toggle="modal" data-target="#edit-rating" title="Edit rating title" onclick="editRating({{$rating->id}})"><i class="fa fa-fw fa-lg fa-pencil" style="color:#007bff;"></i></a><a href="#" data-toggle="modal" data-target="#delete-rating" title="Remove rating" onclick="deleteRating({{$rating->id}})"><i class="fa fa-fw fa-lg fa-trash" style="color:#007bff;"></i></a>
                </td>
            @else
                <td class="small text-center">No benches selected</td>
                <td class="small text-center"></td>
                <td class="small text-center"><a href="#" data-toggle="modal" data-target="#edit-rating" title="Edit rating title" onclick="editRating({{$rating->id}})"><i class="fa fa-fw fa-lg fa-pencil" style="color:#007bff;"></i></a><a href="#" data-toggle="modal" data-target="#delete-rating" title="Remove rating" onclick="deleteRating({{$rating->id}})"><i class="fa fa-fw fa-lg fa-trash" style="color:#007bff;"></i></a></td>
            @endif
        </tr>
    </tbody>
</table>

<!-- Edit rating -->
<div class="modal fade" id="edit-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit rating</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="PUT"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Rating title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
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
<!-- Delete Rating-->
<div class="modal fade" id="delete-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete rating</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-danger">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Export to Excel -->
<div class="modal fade" id="store-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create rating excel file</h4>
            </div>
            <div class="modal-body">
                <form id="form_store" data-toggle="validator" method="post" action="{{ route('scores.store.rating')}}">
                    <input type="hidden" id="rating_id" name="rating_id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" readonly="true" required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <input type="text" id="description" name="description" class="form-control" data-error="Please enter valid description text." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Create file</button>
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
function storeRating(rating_id) {
    $("#store-rating #form_store #rating_id").val(rating_id);
    $("#store-rating #form_store #title").val($("#title_"+rating_id).text());
}
function select_templates() {
    var areaID = $("#add-rating #form_rating #area_id").val();
    $("#add-rating #form_rating #techsheet_id").empty();
    $("#add-rating #form_rating #techsheet").removeAttr('style');
    $.ajax({
        type: 'POST',
        url: "{{ url('area/techsheets') }}",
        data: { _token: "{{ csrf_token() }}", area_id: areaID },
        success: function(data){
            //console.log(data);
            if (data.length > 0) {
                $("#add-rating #form_rating #techsheet_id").append('<option value="0" selected disabled>Select technical template</option>');
                $.each(data, function(index, item) {
                    $("#add-rating #form_rating #techsheet_id").append('<option value="'+item.id+'">'+item.title+'</option>');
                });
                $("#add-rating #form_rating #timesheet").removeAttr('style');
                $("#add-rating #form_rating #economicsheet").removeAttr('style');
            } else {
                $("#add-rating #form_rating #techsheet_id").append('<option value="0" selected disabled>Area without technical templates yet</option>');
                $("#add-rating #form_rating #timesheet").attr('style','display:none;');
                $("#add-rating #form_rating #economicsheet").attr('style','display:none;');
            }
        },
        error: function (xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log("error:"+err.Message);
        }
    });
}
function editRating(rating_id) {
    $("#edit-rating #form_edit").attr('action','{{url('ratings')}}/'+rating_id);
    $("#edit-rating #form_edit #title").val($("#title_"+rating_id).text());
}
function deleteRating(rating_id) {
    $("#delete-rating #form_delete").attr('action','{{url('ratings')}}/'+rating_id);
    $("#delete-rating #title").text($("#title_"+rating_id).text());
}
</script>
@endsection