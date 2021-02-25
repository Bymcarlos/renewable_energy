@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('partners.scopes') }}">Partners scopes</a></li>
    <li class="breadcrumb-item active">{{$scope->title}} - Partners</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Partner data, press sheet button for more information</small></td>
          <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new partner" data-toggle="modal" data-target="#add-partner"></a></td>
        </tr>
</table>

<!-- List Scopes -->
<table class="table-bordered table-sm" id="dtRT_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>PARTNER</th>
            <th>DESCRIPTION</th>
            <th>NDA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($partners as $partner)
        <tr>
            <td class="small" id="title_{{$partner->id}}">{{$partner->title}}</td>
            <td class="small" id="description_{{$partner->id}}">{{$partner->description}}</td>
            <td class="small" id="nda_{{$partner->id}}">{{$partner->nda}}</td>
            <td class="text-right"><a href="{{route('partner.sheet',['partner_id'=>$partner->id,'sheet_id'=>1])}}" class="btn btn-primary btn-sm fa fa-file-text" title="Show partner sheet"></a><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit template" data-toggle="modal" data-target="#edit-partner" onclick="editPartner({{$partner->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove partner" data-toggle="modal" data-target="#delete-partner" onclick="deletePartner({{$partner->id}})"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Partner -->
<div class="modal fade" id="add-partner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create {{$scope->title}} Partner</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('partners.store') }}">
                    <input type="hidden" name="scope_id" value="{{$scope->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Partner title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Scope:</label>
                        <select class="form-control" disabled>
                            <option value="{{$scope->id}}" selected >{{$scope->title}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">NDA:</label>
                        <input type="date" name="nda" class="form-control" data-error="Please enter valid date." />
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
<!-- Edit Partner -->
<div class="modal fade" id="edit-partner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit partner</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="put"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Partner title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="area_id">Scope:</label>
                        <select class="form-control" disabled>
                            <option value="{{$scope->id}}" selected >{{$scope->title}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title">NDA:</label>
                        <input type="date" id="nda" name="nda" class="form-control" data-error="Please enter valid date." />
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
<!-- Delete Partner -->
<div class="modal fade" id="delete-partner" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete partner:</h4>
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
function editPartner(item_id){
    $("#edit-partner #form_edit").attr("action","{{ url('partners') }}/"+item_id);
    $("#edit-partner #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-partner #form_edit #nda").val($("#nda_"+item_id).text());
    $("#edit-partner #form_edit #description").text($("#description_"+item_id).text());
}
function deletePartner(item_id){
    $("#delete-partner #form_delete").attr("action","{{ url('partners') }}/"+item_id);
    $("#delete-partner #title").text($("#title_"+item_id).text());
}

$('#dtRT_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,3]
    },
    {
        "width": "30%", 
        "targets": 0
    },
    {
        "width": "40%", 
        "targets": 1
    },
    {
        "width": "15%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection