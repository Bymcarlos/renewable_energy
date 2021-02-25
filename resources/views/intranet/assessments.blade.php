@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">{{ $asstype->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Assessments are the ... You can create new Assessments, edit (clicking on items) or delete (only assessments that are not associated to any benches).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new assessment" data-toggle="modal" data-target="#add-assessment"></a></td>
        </tr>
</table>

<!-- List Assessments -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>ID</th>
            <th>ASSESSMENT</th>
            <th>DESCRIPTION</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($assessments as $assessment)
        <tr id="assessment_{{$assessment->id}}" style="cursor: pointer;">
            <td title="Show sheets" onclick="window.location.href='{{ route('sheets.index',['id' => $assessment->id])}}'">{{$assessment->id}}</td>
            <td id="title_{{$assessment->id}}" data-title="{{ $assessment->title }}" title="Show sheets" onclick="window.location.href='{{ route('sheets.index',['id' => $assessment->id])}}'">{{ $assessment->title }}</td>
            <td title="Show sheets" onclick="window.location.href='{{ route('sheets.index',['id' => $assessment->id])}}'"><small>In this assessment you can find sheets about ...</small></td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" data-toggle="modal" data-target="#edit-assessment" onclick="editAssessment({{ $assessment->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove assessment" data-toggle="modal" data-target="#delete-assessment" onclick="deleteAssessment({{ $assessment->id}} )"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-assessment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Assessment</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('assessments.store') }}" method="POST">
                    <input type="hidden" name="assessmenttype_id" value="{{$asstype->id}}" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Assessment title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
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
<div class="modal fade" id="edit-assessment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Assessment</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Assessment title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
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
<div class="modal fade" id="delete-assessment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Assessment:</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="title"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="id" name="id"/>
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
function loadDetail(){

}
function editAssessment(item_id){
    $("#edit-assessment #form_edit").attr("action","{{ url('assessments') }}/"+item_id);
    $("#edit-assessment #title").val($("#title_"+item_id).data("title"));
}
function deleteAssessment(item_id){
    $("#delete-assessment #form_delete").attr("action","{{ url('assessments') }}/"+item_id);
    $("#delete-assessment #title").text($("#title_"+item_id).data("title"));
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [2,3]
    },
    {
        "width": "30%", 
        "targets": 1
    },
    {
        "width": "60%", 
        "targets": 2
    },
    {
        "width": "8%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection