@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">Unit types</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Unit types are the ... You can create new Unit types, edit (clicking on items) or delete (only unit types that are not associated to any feature).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new unit type" data-toggle="modal" data-target="#add-unittype"></a></td>
        </tr>
</table>

<!-- List Unit types -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>NAME</th>
            <th>EXAMPLES</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($unittypes as $unittype)
        <tr id="unittype_{{$unittype->id}}" style="cursor: pointer;">
            <td id="title_{{$unittype->id}}" data-title="{{ $unittype->title }}" data-toggle="modal" data-target="#edit-unittype" onclick="editUnittype({{ $unittype->id }})">{{ $unittype->title }}</td>
            <td data-toggle="modal" data-target="#edit-unittype" onclick="editUnittype({{ $unittype->id }})">@if ($unittype->units()->get())
                (
                @foreach ($unittype->units()->get() as $unit)
                    {{ $unit->title }}&nbsp;
                @endforeach
                )
              @endif
            </td>
            <td class="text-right"><a href="{{ route('units.index',['id'=>$unittype->id]) }}" class="btn btn-primary btn-sm fa fa-files-o mr-1"></a>
            <!-- TODO: Chech delete case (only if all units of this type, are not relating to any feature)
                <a href="#" class="btn btn-primary btn-sm fa fa-trash" data-toggle="modal" data-target="#delete-unittype" onclick="deleteUnittype({{ $unittype->id}} )"></a>
            -->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-unittype" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Unit type</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('unittypes.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Unit type:</label>
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
<div class="modal fade" id="edit-unittype" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Unit type</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Unit type:</label>
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
<div class="modal fade" id="delete-unittype" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Unit type:</h4>
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
function editUnittype(item_id){
    $("#edit-unittype #form_edit").attr("action","{{ url('unittypes') }}/"+item_id);
    $("#edit-unittype #title").val($("#title_"+item_id).data("title"));
}
function deleteUnittype(item_id){
    $("#delete-unittype #form_delete").attr("action","{{ url('unittypes') }}/"+item_id);
    $("#delete-unittype #title").text($("#title_"+item_id).data("title"));
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2]
    },
    {
        "width": "45%", 
        "targets": 0
    },
    {
        "width": "40%", 
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