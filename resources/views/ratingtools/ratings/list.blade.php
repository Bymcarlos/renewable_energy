@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.areas') }}">Ratings - Areas</a></li>
    <li class="breadcrumb-item active">{{$area->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%" class="small">A Rating evaluates testing facilities from a systematic technical, timing and economic point of view. Click on a rating to see procedure details.</td>
          <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new rating" data-toggle="modal" data-target="#add-rating"></a></td>
        </tr>
</table>

<!-- List Rating Templates -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th class="small font-weight-bold">RATING</th>
            <th class="small font-weight-bold">ASSOCIATED TEMPLATES</th>
            <th class="small font-weight-bold text-center">SELECTED BENCHES</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($ratings as $rating)
        @php ($benches_selected = false) 
        @if (isset($rating_benches_count[$rating->id]) && $rating_benches_count[$rating->id]->benches>0) 
            @php ($benches_selected = true)
        @endif
        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('ratings.show',['rating_id' => $rating->id])}}'">
            <td id="title_{{$rating->id}}" class="small">{{$rating->title}}</td>
            <td class="small">
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-tachometer"></i>&nbsp;{{$rating->techsheet()->first()->title}}</div></div>
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-clock-o"></i>&nbsp;{{$rating->timesheet()->first()->title}}</div></div>
                <div class="row"><div class="col text-left"><i class="fa fa-fw fa-euro"></i>&nbsp;{{$rating->economicsheet()->first()->title}}</div></div>
            </td>
            @if ($benches_selected)
            <td class="small text-center">
                @foreach($rating->ratingbenches()->get() as $ratingbench)
                    @php ($bench = $ratingbench->bench()->first())
                    <div class="row">
                        <div class="col text-left"><img src="{{ asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png') }}" title="{{ $bench->title }}" width="14"/>&nbsp;<span class="small">{{$bench->title}}</span></div>
                    </div>
                @endforeach
            </td>
            @else
            <td class="small text-center">No benches selected yet</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Create Rating Modal -->
<div class="modal fade" id="add-rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Rating</h4>
            </div>
            <div class="modal-body">
                <form id="form_rating" data-toggle="validator" method="post" action="{{route('ratings.store')}}">
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Rating title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <input type="text" name="description" class="form-control" data-error="Please enter valid abreviation." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Area:</label>
                        <select class="form-control" disabled="true">
                            <option>{{$area->title}}</option>
                        </select>
                    </div>
                    <div class="form-group" id="techsheet">
                        <label class="control-label" for="techsheet_id">Technical template:</label>
                        <select class="form-control" id="techsheet_id" name="techsheet_id" required="true">
                            <option selected value="" disabled>Select technical template</option>
                            @foreach($techsheets as $techsheet)
                            <option value="{{$techsheet->id}}">{{$techsheet->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="timesheet">
                        <label class="control-label" for="timesheet_id">Timing template:</label>
                        <select class="form-control" id="timesheet_id" name="timesheet_id" required="true">
                            <option selected value="" disabled>Select timing template</option>
                            @foreach($timesheets as $timesheet)
                            <option value="{{$timesheet->id}}">{{$timesheet->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="economicsheet">
                        <label class="control-label" for="economicsheet_id">Economics template:</label>
                        <select class="form-control" id="economicsheet_id" name="economicsheet_id" required="true">
                            <option selected value="" disabled>Select economics template</option>
                            @foreach($economicsheets as $economicsheet)
                            <option value="{{$economicsheet->id}}">{{$economicsheet->title}}</option>
                            @endforeach
                        </select>
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
function editRating(rating_id) {
    $("#edit-rating #form_edit").attr('action','{{url('ratings')}}/'+rating_id);
    $("#edit-rating #form_edit #title").val($("#title_"+rating_id).text());
}
function deleteRating(rating_id) {
    $("#delete-rating #form_delete").attr('action','{{url('ratings')}}/'+rating_id);
    $("#delete-rating #title").text($("#title_"+rating_id).text());
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
        "width": "30%", 
        "targets": 1
    },
    {
        "width": "40%", 
        "targets": 2
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection