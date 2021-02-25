@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">{{$generalsheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>{{$generalsheet->title}} sections</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<!--Sections -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($generalsheet->sections()->get() as $item_section)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_section->id == $section->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('generalrequests.index',['generalsheet_id' => $generalsheet->id,'section_id' => $item_section->id]) }}">{{ $item_section->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
        <a href="#" class="btn btn-primary btn-sm fa fa-file-o" title="New section" data-toggle="modal" data-target="#section-add"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit section" data-toggle="modal" data-target="#section-edit"></a>
        @if (($generalsheet->sections()->count()>1) && ($section->generalrequests()->count()==0))
        <a href="#" class="btn btn-primary btn-sm fa fa-trash mr-1" title="Delete section" data-toggle="modal" data-target="#section-del"></a>
        @endif
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%"><small>Requests are the ... </small></td>
      <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New request" data-toggle="modal" data-target="#request-add"></a></td>
    </tr>
</table>
<!-- List Requests -->
<table class="table-bordered table-sm table-hover" id="dtRT_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>REQUEST</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($section->generalrequests()->get() as $generalrequest)
        <tr class="bg-light">
            <td class="small"><span id="title_{{$generalrequest->id}}">{{$generalrequest->title}}</span>
                @if ($generalrequest->help)
                    <br/><small class="text-success" id="help_{{$generalrequest->id}}">{{$generalrequest->help}}</small>
                @endif
            </td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit request" data-toggle="modal" data-target="#request-edit" onclick="editRequest({{$generalrequest->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Delete request" data-toggle="modal" data-target="#request-delete" onclick="deleteRequest({{$generalrequest->id}})"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Section -->
<div class="modal fade" id="section-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New section on {{ $generalsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('sections.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="generalsheet_id" value="{{ $generalsheet->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title">Section title:</label>
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
<!-- Edit Section -->
<div class="modal fade" id="section-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit section on {{ $generalsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('sections.update',['setion'=>$section->id]) }}" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="generalsheet_id" value="{{ $generalsheet->id }}" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Category title:</label>
                        <input type="text" name="title" class="form-control" value="{{$section->title}}" data-error="Please enter valid title." required />
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
<!-- Delete Section-->
<div class="modal fade" id="section-del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete category</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Delete {{ $section->title }} section on {{ $generalsheet->title }}?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('sections.destroy',['section'=>$section->id]) }}" method="POST">
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
<!-- Create Generalrequest -->
<div class="modal fade" id="request-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create request</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="post" action="{{ route('generalrequests.store') }}">
                    <input type="hidden" name="generalsheet_id" value="{{$generalsheet->id}}"/>
                    <input type="hidden" name="section_id" value="{{$section->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Request title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="help">Help (addtional info):</label>
                        <input type="text" id="help" name="help" class="form-control" data-error="Please enter valid help text." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Generalrequest -->
<div class="modal fade" id="request-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $generalsheet->title }} / {{ $section->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="generalsheet_id" value="{{$generalsheet->id}}"/>
                    <input type="hidden" name="section_id" value="{{$section->id}}"/>
                    <input type="hidden" name="_method" value="PUT"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Request title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="help">Help (addtional info):</label>
                        <input type="text" id="help" name="help" class="form-control" data-error="Please enter valid help text." />
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
<!-- Delete Generalrequest -->
<div class="modal fade" id="request-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $generalsheet->title }} / {{ $section->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="generalsheet_id" value="{{$generalsheet->id}}"/>
                    <input type="hidden" name="section_id" value="{{$section->id}}"/>
                    <input type="hidden" name="_method" value="DELETE"/>
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
@endsection
@section('js_custom')
<script type="text/javascript">
function editRequest(generalrequest_id){
    $("#request-edit #form_edit").attr("action","{{ url('generalrequests') }}/"+generalrequest_id);
    $("#request-edit #form_edit #title").val($("#title_"+generalrequest_id).text());
    $("#request-edit #form_edit #help").val($("#help_"+generalrequest_id).text());
    $("#request-edit #form_edit #help").val($("#help_"+generalrequest_id).text());
}
function deleteRequest(generalrequest_id){
    $("#request-delete #form_delete").attr("action","{{ url('generalrequests') }}/"+generalrequest_id);
    $("#request-delete #form_delete #title").text($("#title_"+generalrequest_id).text());
}
$('#dtRT_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1]
    },
    {
        "width": "90%", 
        "targets": 0
    },
    {
        "width": "10%", 
        "targets": 1
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection