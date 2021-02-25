@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingreports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">{{$area->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="99%" class="small">Report is a stored rating. It contains technical, timing and economical rating performed for an specific TS (test specification).</td>
        </tr>
</table>

<!-- List Rating Templates -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>TITLE</th>
            <th>DESCRIPTION</th>
            <th>AREA</th>
            <th>DATE</th>
            <th class="text-right"></th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ratingfiles as $key => $ratingfile)
        <tr>
            <td id="title_{{$ratingfile->id}}" class="small">{{$ratingfile->title}}</td>
            <td id="description_{{$ratingfile->id}}" class="small">{{$ratingfile->description}}</td>
            <td class="small">{{$area->title}}</td>
            <td class="small">{{$ratingfile->created_at}}</td>
            <td class="text-right"><a href="{{asset('files/ratingtool/'.$ratingfile->file)}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Download Excel file"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit title / description" data-toggle="modal" data-target="#ratingfile-edit" onclick="editRatingfile({{$ratingfile->id}})"></a>
                @if ($ratingfile->status==1)
                <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove rating file" data-toggle="modal" data-target="#ratingfile-delete" onclick="deleteRatingfile({{$ratingfile->id}})"></a>
                @else
                <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="Status confirmed, can not remove this rating file"></a>
                @endif
            </td>
            <td class="text-right">
                @if ($ratingfile->status==1)
                <a href="#" data-toggle="modal" data-target="#ratingfile-status" onclick="statusRatingfile({{$ratingfile->id}})"><img src="{{asset('icons/ic_status_1.png')}}" width="16" title="Pending to be confirmed"></a>
                @else
                <img src="{{asset('icons/ic_status_2.png')}}" width="16" title="Confirmed">
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Edit title  description Ratingfile -->
<div class="modal fade" id="ratingfile-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit rating file data</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="post">
                    <input type="hidden" name="_method" value="put"/>
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <input type="text" id="description" name="description" class="form-control" data-error="Please enter valid description text." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Update file</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Ratingfile -->
<div class="modal fade" id="ratingfile-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete rating file</h4>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="delete"/>
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
<!-- Change status Ratingfile -->
<div class="modal fade" id="ratingfile-status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm rating file status change</h4>
            </div>
            <div class="modal-body">
                <table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
                    <tr class="border">
                      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
                      <td width="99%"><small>Changing status, the rating file can not be deleted</small></td>
                    </tr>
                </table>
                <form id="form_status" data-toggle="validator" method="POST" action="{{route('ratingfile.status')}}">
                    {{ csrf_field() }}
                    <input type="hidden" id="id" name="id"/>
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    <div class="modal-body">
                        <h5 class="modal-content" id="title"></h5>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Change status</button>
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
function editRatingfile(ratingfile_id) {
    $("#ratingfile-edit #form_edit").attr("action","{{ url('ratingfiles') }}/"+ratingfile_id);
    $("#ratingfile-edit #form_edit #title").val($("#title_"+ratingfile_id).text());
    $("#ratingfile-edit #form_edit #description").val($("#description_"+ratingfile_id).text());
}
function deleteRatingfile(ratingfile_id) {
    $("#ratingfile-delete #form_delete").attr("action","{{ url('ratingfiles') }}/"+ratingfile_id);
    $("#ratingfile-delete #form_delete #title").text($("#title_"+ratingfile_id).text());
}
function statusRatingfile(ratingfile_id) {
    $("#ratingfile-status #form_status #id").val(ratingfile_id);
    $("#ratingfile-status #form_status #title").text($("#title_"+ratingfile_id).text());
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [0,1,2,3,4,5]
    },
    {
        "width": "20%", 
        "targets": 0
    },
    {
        "width": "35%", 
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
        "width": "14%", 
        "targets": 4
    },
    {
        "width": "1%", 
        "targets": 5
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection