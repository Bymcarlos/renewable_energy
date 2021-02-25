@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item active">Economics</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%" class="small">Economical template is sustained on two complementary economic assessments:
                <ul class="mb-0"><li><strong>Business case.</strong> Based on a test specification (TS), different laboratories are contacted to request a quotation for the test execution. Business case allows to assess the quoted test expenses among the different contacted laboratories.</li>
                    <li><strong>Alternative case.</strong> This case allows to assess laboratories under possible scenarios out of TS scope.</li></ul></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new economic template" data-toggle="modal" data-target="#add-template"></a></td>
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
        @foreach($economicsheets as $economicsheet)
        <tr style="cursor: pointer;">
            <td id="title_{{$economicsheet->id}}" onclick="window.location.href='{{ route('economicrequests.index',['economicsheet' => $economicsheet->id])}}'"><small>{{$economicsheet->title}}</small></td>
            <td id="description_{{$economicsheet->id}}"><small>{{$economicsheet->description}}</small></td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-text" title="New template like this" data-toggle="modal" data-target="#add-template-from" onclick="newInputFrom({{ $economicsheet->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit template" data-toggle="modal" data-target="#edit-template" onclick="editInput({{ $economicsheet->id }})"></a>
            @if ($economicsheet->ratings()->count()>0)
                <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="There are ratings using this template"></a>
            @else
                <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove template" data-toggle="modal" data-target="#delete-template" onclick="deleteTemplate({{$economicsheet->id}})"></a>
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
                <h4 class="modal-title" id="myModalLabel">Create economic template</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('economicsheets.store') }}">
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
                <form id="form_add" method="POST" action="{{ route('economicsheets.store') }}">
                    {{ csrf_field() }}
                    <input type="hidden" id="economicsheet_id" name="economicsheet_id"/>
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
                <h4 class="modal-title" id="myModalLabel">Edit economic template:</h4>
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
                <h4 class="modal-title" id="myModalLabel">Confirm delete economic template:</h4>
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
function newInputFrom(item_id){
    $("#add-template-from #form_add #economicsheet_id").val(item_id);
    $("#add-template-from #form_add #title-origin").val($("#title_"+item_id).text());
    $("#add-template-from #form_add #description").text($("#description_"+item_id).text());
}
function editInput(item_id){
    $("#edit-template #form_edit").attr("action","{{ url('economicsheets') }}/"+item_id);
    $("#edit-template #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-template #form_edit #description").text($("#description_"+item_id).text());
}
function deleteTemplate(item_id){
    $("#delete-template #form_delete").attr("action","{{ url('economicsheets') }}/"+item_id);
    $("#delete-template #title").text($("#title_"+item_id).text());
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2]
    },
    {
        "width": "35%", 
        "targets": 0
    },
    {
        "width": "50%", 
        "targets": 1
    },
    {
        "width": "15%", 
        "targets": 2
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection