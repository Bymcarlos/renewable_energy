@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('inputsheets.areas') }}">Input from TS - Areas</a></li>
    <li class="breadcrumb-item active">{{$area->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Requests from TS template contain the most relevant information required by TS to perform the test.</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new input from TS template" data-toggle="modal" data-target="#add-template"></a></td>
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
        @foreach ($inputsheets as $inputsheet)
            <tr style="cursor: pointer;">
                <td id="title_{{$inputsheet->id}}" onclick="window.location.href='{{ route('inputrequests.index',['id' => $inputsheet->id ])}}'"><small>{{ $inputsheet->title}}</small></td>
                <td id="description_{{$inputsheet->id}}"><small>{{$inputsheet->description}}</small></td>
                <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-text" title="New template like this" data-toggle="modal" data-target="#add-template-from" onclick="newInputFrom({{ $inputsheet->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit template" data-toggle="modal" data-target="#edit-template" onclick="editInput({{ $inputsheet->id }})"></a>
                @if ($inputsheet->techsheets()->count()>0)
                    <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="There are technical templates using this template"></a>
                @else
                    <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove template" data-toggle="modal" data-target="#delete-template" onclick="deleteInput({{$inputsheet->id}})"></a>
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
                <h4 class="modal-title" id="myModalLabel">Create Template</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('inputsheets.store') }}">
                    <input type="hidden" name="area_id" value="{{$area->id}}" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Area:</label>
                        <select class="form-control" disabled>
                            <option selected disabled>{{$area->title}}</option>
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
<!-- New template like other -->
<div class="modal fade" id="add-template-from" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New template like:</h4>
            </div>
            <div class="modal-body">
                <form id="form_add" data-toggle="validator" method="POST" action="{{ route('inputsheets.store') }}">
                    <input type="hidden" id="inputsheet_id" name="inputsheet_id"/>
                    <input type="hidden" name="area_id" value="{{$area->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Origin template:</label>
                        <input type="text" id="title-origin" class="form-control" disabled="true" readonly="true" />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">New template title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required="true" />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Area:</label>
                        <select class="form-control" disabled>
                            <option selected disabled>{{$area->title}}</option>
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
<!-- Edit item modal -->
<div class="modal fade" id="edit-template" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit input from TS template:</h4>
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
                <h4 class="modal-title" id="myModalLabel">Confirm delete Sheet:</h4>
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
    $("#add-template-from #form_add #inputsheet_id").val(item_id);
    $("#add-template-from #form_add #title-origin").val($("#title_"+item_id).text());
    $("#add-template-from #form_add #description").text($("#description_"+item_id).text());
}
function editInput(item_id){
    $("#edit-template #form_edit").attr("action","{{ url('inputsheets') }}/"+item_id);
    $("#edit-template #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-template #form_edit #description").text($("#description_"+item_id).text());
}
function deleteInput(item_id){
    $("#delete-template #form_delete").attr("action","{{ url('inputsheets') }}/"+item_id);
    $("#delete-template #title").text($("#title_"+item_id).text());
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2]
    },
    {
        "width": "40%", 
        "targets": 0
    },
    {
        "width": "45%", 
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