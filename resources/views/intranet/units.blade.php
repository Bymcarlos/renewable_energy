@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active"><a href="{{ route('unittypes.index') }}">Unit types</a></li>
    <li class="breadcrumb-item active">{{$unittype->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Unit are the ... You can create new Units, edit (clicking on items) or delete (only unit units that are not associated to any feature).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new unit type" data-toggle="modal" data-target="#add-unit"></a></td>
        </tr>
</table>

<!-- List Unit types -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>NAME</th>
            <th>DESCRIPTION</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($unittype->units()->get() as $unit)
        <tr id="unittype_{{$unit->id}}" style="cursor: pointer;">
            <td id="title_{{$unit->id}}" data-title="{{ $unit->title }}" data-toggle="modal" data-target="#edit-unit" onclick="editUnit({{ $unit->id }})">{{ $unit->title }}</td>
            <td data-toggle="modal" data-target="#edit-unit" onclick="editUnit({{ $unit->id }})">web</td>
            <td class="text-right">
                @if ($unit->features()->count()>0 || $unit->featurebrandvalues()->count()>0 || $unit->economicrequests()->count()>0)
                <a href="#" class="btn btn-danger btn-sm fa fa-trash" title="There are features or rating tools requirements relating to this unit"></a>
                @else
                <a href="#" class="btn btn-primary btn-sm fa fa-trash" data-toggle="modal" data-target="#delete-unit" onclick="deleteUnit({{ $unit->id}} )"></a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Unit</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('units.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="unittype_id" value="{{$unittype->id}}"/>
                    <div class="form-group">
                        <label class="control-label" for="title">Unit:</label>
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
<div class="modal fade" id="edit-unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Unit</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Unit:</label>
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
<div class="modal fade" id="delete-unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Unit:</h4>
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
function editUnit(item_id){
    $("#edit-unit #form_edit").attr("action","{{ url('units') }}/"+item_id);
    $("#edit-unit #title").val($("#title_"+item_id).data("title"));
}
function deleteUnit(item_id){
    $("#delete-unit #form_delete").attr("action","{{ url('units') }}/"+item_id);
    $("#delete-unit #title").text($("#title_"+item_id).data("title"));
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