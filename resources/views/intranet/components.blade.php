@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">Components</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Components are the ... You can create new Components, edit or delete (only components that are not associated to any benches).</small></td>
        	<td width="14%" class="text-right"><a href="{{ route('components.export') }}" class="btn btn-primary btn-sm fa fa-file-excel-o m-1" title="Export to Excel"></a><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new component" data-toggle="modal" data-target="#add-component"></a></td>
        </tr>
</table>
@php ($areas_list="")
@foreach($areas as $area)
    @if ($loop->first)
        @php ($areas_list=$area->id)
    @else
        @php ($areas_list.=','.$area->id)
    @endif
@endforeach
<!-- List Components -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th class="col-5">NAME</th>
            <th class="col-3">SHEET</th>
            <th class="col-3">AREAS</th>
            <th class="col-1 text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($components as $component)
        <tr id="component_{{$component->id}}">
            <td class="col-5" id="title_{{$component->id}}">{{ $component->title }}</td>
            <td class="col-3">
                <small class="text-dark"></small>&nbsp;<small class="text-secondary">{{ $component->sheet()->first()->title }}</small>
            </td>
            <td class="col-3"><small>
                @php ($i=1)
                @php ($areas_selected="")
                @foreach ($component->areas()->get() as $area)
                    @if ($i>1) - @endif
                    {{ $area->title }}
                    @if ($i==1)
                        @php ($areas_selected=$area->id)
                    @else
                        @php ($areas_selected.=','.$area->id)
                    @endif
                    @php ($i++)
                @endforeach
            </small>
            </td>
            <td class="col-1 text-right">
                <a href="#" class="btn btn-primary btn-sm fa fa-pencil" data-title="Edit" data-toggle="modal" data-target="#edit-component" onclick="editComponent({{ $component->id }},{{$component->sheet_id}},[{{$areas_list}}],[{{$areas_selected}}])"></a>
                <a href="#" class="btn btn-primary btn-sm fa fa-trash" data-title="Delete" data-toggle="modal" data-target="#delete-component" onclick="deleteComponent({{ $component->id }} )"></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Create component Modal -->
<div class="modal fade" id="add-component" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Component</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_add" action="{{ route('components.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="text" name="title" placeholder="Component title" class="form-control" data-error="Please enter valid title." required/>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Technical capabilites: <br/>(If not exist technical sheet, cancel and create it first)</label>
                        <select class="form-control" id="sheet_id" name="sheet_id" required>
                            <option disabled selected value>Select technical sheet</option>
                        @foreach ($sheets as $sheet)
                            <option value="{{ $sheet->id }}">{{ $sheet->title }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Related areas:</label>
                        <div class="table-responsive">
                            <table class="table" id="dataTable" width="100%" cellspacing="0">
                              <tbody>
                                @foreach($areas as $area)
                                <tr>
                                  <td class="col">
                                    <input type="checkbox" class="mr-3" id="area_{{ $area->id }}" name="area_{{ $area->id }}"/>{{ $area->title }}
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="add_submit" class="btn crud-submit btn-success" >Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit component Modal -->
<div class="modal fade" id="edit-component" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Component</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required/>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Technical capabilites: <br/>(If not exist technical sheet, cancel and create it first)</label>
                        <select class="form-control" id="sheet_id" name="sheet_id" required>
                            <option disabled selected value>Select technical sheet</option>
                        @foreach ($sheets as $sheet)
                            <option value="{{ $sheet->id }}">{{ $sheet->title }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">Related areas:</label>
                        <div class="table-responsive">
                            <table class="table" id="dataTable" width="100%" cellspacing="0">
                              <tbody id="areas">
                                @foreach($areas as $area)
                                <tr>
                                  <td class="col">
                                    <input type="checkbox" class="mr-3" id="area_{{ $area->id }}" name="area_{{ $area->id }}"/>{{ $area->title }}
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="add_submit" class="btn crud-submit btn-success" >Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete component -->
<div class="modal fade" id="delete-component" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Component:</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
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
function editComponent(component_id,sheet_id,areas,selected){
    var route = "{{ route('components.update',['component'=>':id']) }}".replace(':id', component_id);
    $("#edit-component #form_edit").attr("action",route);
    $("#edit-component #form_edit #title").val($("#title_"+component_id).text());
    $("#edit-component #form_edit #sheet_id").val(sheet_id);
    $("#edit-component #form_edit #sheet_id").change();
    for (i = 0; i < areas.length; i++) {
        $("#edit-component #form_edit #area_"+areas[i]).removeAttr('checked');
    }
    for (i = 0; i < selected.length; i++) {
        $("#edit-component #form_edit #area_"+selected[i]).attr('checked','checked');
    }
}
function deleteComponent(component_id){
    var route = "{{ route('components.destroy',['component'=>':id']) }}".replace(':id', component_id);
    $("#delete-component #form_delete").attr("action",route);
    $("#delete-component #title").text($("#title_"+component_id).text());
}
$('#dt_list').DataTable({
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": [3]
        },
        {
            "width": "35%", 
            "targets": 0
        },
        {
            "width": "30%", 
            "targets": 1
        },
        {
            "width": "27%", 
            "targets": 1
        },
        {
            "width": "8%", 
            "targets": 2
        }],
        "dom": 'fprt<"bottom"p>',
        "order": [[ 0, 'asc' ]],
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
    });
</script>
@endsection