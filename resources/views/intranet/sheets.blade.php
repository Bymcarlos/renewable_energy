@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('management') }}">Management</a></li>
    @if ($assessment->assessmenttype_id==1)
        <li class="breadcrumb-item"><a href="{{ route('assessments.technical') }}">{{ $assessment->assessmenttype()->first()->title }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('assessments.economical') }}">{{ $assessment->assessmenttype()->first()->title }}</a></li>
    @endif
    <li class="breadcrumb-item active">{{ $assessment->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Sheets are the ... You can create new Sheets, edit (clicking on items) or delete (only sheets that are not associated to any benches).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new sheet" data-toggle="modal" data-target="#add-sheet"></a></td>
        </tr>
</table>

<!-- List Sheets -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>SHEET</th>
            <th>TYPE</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($assessment->sheets()->get() as $sheet)
        <tr id="sheet_{{$sheet->id}}" style="cursor: pointer;">
            <td id="title_{{$sheet->id}}" data-title="{{ $sheet->title }}" data-abbrev="{{ $sheet->abbrev }}" data-required="{{$sheet->required}}" title="Show features" onclick="window.location.href='{{ route('features.index',['id' => $sheet->id]) }}'">{{ $sheet->title }}</td>
            <td title="Show features" onclick="window.location.href='{{ route('features.index',['id' => $sheet->id]) }}'">
                @if ($sheet->required==1) 
                    Required
                @else
                    Specific
                @endif
            </td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" data-toggle="modal" data-target="#edit-sheet" onclick="editSheet({{ $sheet->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove sheet" data-toggle="modal" data-target="#delete-sheet" onclick="deleteSheet({{ $sheet->id}} )"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-sheet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Sheet</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('sheets.store') }}" method="POST">
                    <input type="hidden" name="assessment_id" value="{{$assessment->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Sheet title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Abbreviation:</label>
                        <input type="text" name="abbrev" class="form-control" data-error="Please enter valid abreviation." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Sheet type:</label>
                        <select class="form-control" id="required" name="required">
                            <option value="1" selected>Required (for all benches)</option>
                            <option value="2">Specific (depends on component)</option>
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
<!-- Edit Item Modal -->
<div class="modal fade" id="edit-sheet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Sheet</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Sheet title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Abbreviation:</label>
                        <input type="text" id="abbrev" name="abbrev" class="form-control" data-error="Please enter valid abreviation." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Sheet type:</label>
                        <select class="form-control" id="required" name="required">
                            <option value="1">Required (for all benches)</option>
                            <option value="2">Specific (depends on component)</option>
                        </select>
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
<div class="modal fade" id="delete-sheet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
function editSheet(item_id){
    $("#edit-sheet #form_edit").attr("action","{{ url('sheets') }}/"+item_id);
    $("#edit-sheet #title").val($("#title_"+item_id).data("title"));
    $("#edit-sheet #abbrev").val($("#title_"+item_id).data("abbrev"));
    $("#edit-sheet #required").val($("#title_"+item_id).data("required"));
}
function deleteSheet(item_id){
    $("#delete-sheet #form_delete").attr("action","{{ url('sheets') }}/"+item_id);
    $("#delete-sheet #title").text($("#title_"+item_id).data("title"));
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