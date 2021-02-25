@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('inputsheets.index',['area_id'=>$inputsheet->area_id]) }}">Requests from TS</a></li>
    <li class="breadcrumb-item active">{{$inputsheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="99%" class="small">Requests from TS template contain the most relevant information required by TS to perform the test. The information which must be included in Requests from TS template might be divided in several categories:
            <ul class="mb-0"><li><strong>Test specimen information:</strong> main information about dimensions, weight, center of gravity, etc.</li>
            <li><strong>General test information:</strong> relevant main elements of the test laboratory, i.e: if covered test facility, if temperature control is available, etc.</li>
            <li><strong>Specific test sub type information:</strong> critical information concerning a test sub type to be performed, i.e.: maximum loads, max frequency allowed, etc. (Examples of test sub types: Static test, fatigue test , etc.)</li>
            </ul>
          </td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($inputsheet->inputcats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $inputcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('inputrequests.index',['inputsheet' => $inputsheet->id,'inputcat' => $item_cat->id]) }}">{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
        <a href="#" class="btn btn-primary btn-sm fa fa-file-o" title="New category" data-toggle="modal" data-target="#inputcat-add"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit category" data-toggle="modal" data-target="#inputcat-edit"></a>
        @if (count($inputsheet->inputcats()->get())>1 && $inputcat->inputrequests()->count()==0)
        <a href="#" class="btn btn-primary btn-sm fa fa-trash mr-1" title="Delete category" data-toggle="modal" data-target="#inputcat-del"></a>
        @endif
    </div>
</div>
<!-- List -->
<table class="table-bordered table-sm table-hover mt-1" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>REQUESTS</th>
            <th class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New request" data-toggle="modal" data-target="#inputrequest-add"></a></th>
        </tr>
    </thead>
    <tbody>
    @foreach($inputcat->inputrequests()->get() as $inputrequest)
        <tr>
            <td><span id="title_{{$inputrequest->id}}"><small>{{ $inputrequest->title }}</small></span>
                @if ($inputrequest->help)
                <br/><small class="text-info"><span id="help_{{$inputrequest->id}}">{{ $inputrequest->help }}</span></small>
                @endif
            </td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit input request" data-toggle="modal" data-target="#inputrequest-edit" onclick="editInputrequest({{$inputrequest->id}})"></a>
            @if ($inputrequest->techrequests()->count()>0)
                <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="Request relating to technical requirement"></a>
            @else
                <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Delete input request" data-toggle="modal" data-target="#inputrequest-delete" onclick="deleteInputrequest({{$inputrequest->id}})"></a>
            @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<!-- Create Inputcat -->
<div class="modal fade" id="inputcat-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New category on {{ $inputsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('inputcats.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="inputsheet_id" value="{{ $inputsheet->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title">Category title:</label>
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
<!-- Edit Inputcat -->
<div class="modal fade" id="inputcat-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit category on {{ $inputsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('inputcats.update',['inputcat'=>$inputcat->id]) }}" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="inputsheet_id" value="{{ $inputsheet->id }}" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Category title:</label>
                        <input type="text" name="title" class="form-control" value="{{$inputcat->title}}" data-error="Please enter valid title." required />
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
<!-- Delete Inputcat-->
<div class="modal fade" id="inputcat-del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete category</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Delete {{ $inputcat->title }} categorie on {{ $inputsheet->title }}?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('inputcats.destroy',['inputcat'=>$inputcat->id]) }}" method="POST">
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
<!-- Create Inputrequest -->
<div class="modal fade" id="inputrequest-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create request</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="post" action="{{ route('inputrequests.store') }}">
                    <input type="hidden" name="inputsheet_id" value="{{$inputsheet->id}}"/>
                    <input type="hidden" name="inputcat_id" value="{{$inputcat->id}}"/>
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
<!-- Edit Inputrequest -->
<div class="modal fade" id="inputrequest-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $inputsheet->title }} / {{ $inputcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="inputsheet_id" value="{{$inputsheet->id}}"/>
                    <input type="hidden" name="inputcat_id" value="{{$inputcat->id}}"/>
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
<!-- Delete Inputrequest -->
<div class="modal fade" id="inputrequest-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $inputsheet->title }} / {{ $inputcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="inputsheet_id" value="{{$inputsheet->id}}"/>
                    <input type="hidden" name="inputcat_id" value="{{$inputcat->id}}"/>
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
function editInputrequest(inputrequest_id){
    $("#inputrequest-edit #form_edit").attr("action","{{ url('inputrequests') }}/"+inputrequest_id);
    $("#inputrequest-edit #form_edit #title").val($("#title_"+inputrequest_id).text());
    $("#inputrequest-edit #form_edit #help").val($("#help_"+inputrequest_id).text());
}
function deleteInputrequest(inputrequest_id){
    $("#inputrequest-delete #form_delete").attr("action","{{ url('inputrequests') }}/"+inputrequest_id);
    $("#inputrequest-delete #form_delete #title").text($("#title_"+inputrequest_id).text());
}
$('#dt_list').DataTable({
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
    "bSort" : false,
    "dom": 'rt<"bottom"p>',
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection