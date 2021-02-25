@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('management') }}">Management</a></li>
    <li class="breadcrumb-item"><a href="{{ route('platforms.index') }}">Platforms</a></li>
    <li class="breadcrumb-item active">{{ $platform->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Products are the ... You can create new Products, edit or delete (only products that are not associated on the occupation of any bench).</small></td>
        	<td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new product" data-toggle="modal" data-target="#add-product"></a></td>
        </tr>
</table>

<!-- List Products -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>PRODUCT NAME</th>
            <th>DESCRIPTION</th>
            <th class="text-right"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($platform->products()->get() as $product)
        <tr>
            <td>{{ $product->title }}</td>
            <td>This product ...</td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" id="product_{{$product->id}}" data-title="{{ $product->title }}" data-toggle="modal" data-target="#edit-product" onclick="editProduct({{ $product->id }})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" data-toggle="modal" data-target="#delete-product" onclick="deleteProduct({{ $product->id}} )"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="add-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create Product</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('products.store') }}" method="POST">
                    <input type="hidden" name="platform_id" id="platform_id" class="form-control" value="{{$platform->id}}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Product name:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid name." required />
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
<div class="modal fade" id="edit-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Product</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Product name:</label>
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
<div class="modal fade" id="delete-product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete Product:</h4>
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
function editProduct(item_id){
    $("#edit-product #form_edit").attr("action","{{ url('products') }}/"+item_id);
    $("#edit-product #title").val($("#product_"+item_id).data("title"));
}
function deleteProduct(item_id){
    $("#delete-product #form_delete").attr("action","{{ url('products') }}/"+item_id);
    $("#delete-product #title").text($("#product_"+item_id).data("title"));
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