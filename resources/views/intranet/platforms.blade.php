@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item active">Platforms</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Platforms are the ... You can create new Platforms, edit or delete (only platforms that not have any products on a bench occupation sheet).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new platform" data-toggle="modal" data-target="#add-platform"></a></td>
        </tr>
</table>

<!-- List platforms -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>PLATFORM NAME</th>
            <th>PRODUCTS</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($platforms as $platform)
        <tr style="cursor: pointer;">
            <td onclick="window.location.href='{{ route('products.index',['id'=>$platform->id]) }}'" title="Show products">{{ $platform->title }}</td>
            <td onclick="window.location.href='{{ route('products.index',['id'=>$platform->id]) }}'" title="Show products">@if ($platform->products()->get())
                (
                @foreach ($platform->products()->get() as $product)
                    {{ $loop->first ? '' : ', ' }}
                    {{ $product->title }}
                @endforeach
                )
              @endif
            </td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" id="platform_{{$platform->id}}" data-title="{{ $platform->title }}" data-toggle="modal" data-target="#edit-platform" onclick="editPlatform({{ $platform->id }})" title="Edit platform"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" data-toggle="modal" data-target="#delete-platform" onclick="deletePlatform({{ $platform->id}} )" title="Delete platform"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-platform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Platform</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('platforms.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Platform name:</label>
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
<div class="modal fade" id="edit-platform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Platform</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Platform name:</label>
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
<div class="modal fade" id="delete-platform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Platform:</h4>
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
function editPlatform(item_id){
    $("#edit-platform #form_edit").attr("action","{{ url('platforms') }}/"+item_id);
    $("#edit-platform #title").val($("#platform_"+item_id).data("title"));
}
function deletePlatform(item_id){
    $("#delete-platform #form_delete").attr("action","{{ url('platforms') }}/"+item_id);
    $("#delete-platform #title").text($("#platform_"+item_id).data("title"));
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2]
    },
    {
        "width": "50%", 
        "targets": 0
    },
    {
        "width": "40%", 
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