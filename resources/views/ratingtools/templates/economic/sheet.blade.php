@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('economicsheets.index') }}">Economic</a></li>
    <li class="breadcrumb-item active">{{$economicsheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="98%" class="small">Economical template is sustained on two complementary economic assessments:
                <ul class="mb-0"><li><strong>Business case.</strong> Based on a test specification (TS), different laboratories are contacted to request a quotation for the test execution. Business case allows to assess the quoted test expenses among the different contacted laboratories.</li>
                    <li><strong>Alternative case.</strong> This case allows to assess laboratories under possible scenarios out of TS scope.</li></ul></td>
          <td width="1%" class="text-right"></td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($economicsheet->economiccats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $economiccat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('economicrequests.index',['economicsheet' => $economicsheet->id,'economiccat' => $item_cat->id]) }}">{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
<!--Subcategories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($economiccat->economicsubcats()->get() as $item_subcat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_subcat->id == $economicsubcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('economicrequests.index',['economicsheet' => $economicsheet->id,'economiccat' => $economiccat->id,'economicsubcat' => $item_subcat->id]) }}">{{ $item_subcat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
@php ($bg = "bg-light")
@php ($bg_column = "")
@if ($economicsubcat->weighted && $weight_sum!=1)
@php ($bg = "bg-warning")
@php ($bg_column = "bg-warning")
<table class="table-sm mb-2 {{$bg}}" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%" class="small">The requests in this category have a weight associated to get the rating score. The sum of these weights must be 1</td>
      <td width="14%" class="text-right">@if ($economicsubcat->administrable)<a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New phase" data-toggle="modal" data-target="#request-add"></a>@endif</td>
    </tr>
</table>
@else
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%" class="small">Economic requests are ...</td>
      <td width="14%" class="text-right">@if ($economicsubcat->administrable)<a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New phase" data-toggle="modal" data-target="#request-add"></a>@endif</td>
    </tr>
</table>
@endif
<!-- List Features -->
<table class="table-bordered table-sm table-hover" @if ($economicsubcat->weighted) id="dt_list_weighted" @else id="dt_list" @endif  width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>ORDER</th>
            <th>CONCEPT</th>
            <th class="text-center">UNIT</th>
            @if ($economicsubcat->weighted)
            <th class="text-center {{$bg_column}}">WEIGHT ({{$weight_sum}})</th>
            @endif
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($economicsubcat->economicrequests()->orderby('ordering','asc')->get() as $economicrequest)
        @php ($last_order=$economicrequest->ordering)
        <tr>
            <td class="small" id="ordering_{{$economicrequest->id}}">{{$economicrequest->ordering}}</td>
            <td class="small"><span id="title_{{$economicrequest->id}}">{{$economicrequest->title}}</span>@if (isset($economicrequest->help)) <br/><span id="help_{{$economicrequest->id}}" class="text-info">{{ $economicrequest->help }}</span>@endif</td>
            <td class="small text-center" id="unit_{{$economicrequest->id}}">{{$economicrequest->unit()->first()->title}}</td>
            @if ($economicsubcat->weighted)
            <td class="small text-center" id="weight_{{$economicrequest->id}}">{{$economicrequest->weight}}</td>
            @endif
            <td class="text-right">
                @if ($economicsubcat->weighted)
                    <a href="#" class="btn btn-primary btn-sm fa fa-cog" title="Settings" data-toggle="modal" data-target="#request-edit-weight" onclick="editWeight({{$economicrequest->id}})"></a>
                @endif
                <a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit economic request" data-toggle="modal" data-target="#request-edit" onclick="editRequest({{$economicrequest->id}})"></a><a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Delete economic request" data-toggle="modal" data-target="#request-delete" onclick="deleteRequest({{$economicrequest->id}})"></a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Create Item Modal -->
<div class="modal fade" id="request-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create economic request</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('economicrequests.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="economicsheet_id" value="{{ $economicsheet->id }}" />
                    <input type="hidden" name="economiccat_id" value="{{ $economiccat->id }}" />
                    <input type="hidden" name="economicsubcat_id" value="{{ $economicsubcat->id }}" />
                    <div class="form-group mt-2">
                        <div class="row mt-2">
                            <div class="col-2">
                                <label class="control-label" for="order"><strong>Order:</strong></label>
                            </div>
                            <div class="col-3">
                                <input type="number" id="ordering" name="ordering" min="1" class="form-control" data-error="Please enter valid order" placeholder="{{$last_order+1}}"/>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title"><strong>Request title:</strong> (Indicating unit: €, €/week, €/hour ...)</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title" required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-5">
                                <label class="control-label" for="responsetype_id"><strong>Response type:</strong></label>
                            </div>
                            <div class="col-4">
                                <select class="form-control" id="responsetype_id" disabled>
                                    <option selected>Numeric</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @if ($economicsubcat->weighted)
                    <div class="row mt-2">
                        <div class="col-5">
                            <label class="control-label" for="responsetype_id"><strong>Weight:</strong></label>
                        </div>
                        <div class="col-4">
                            <input type="number" step="any" min="0.1" id="weight" name="weight" class="form-control small text-center" value="0.1" required />
                        </div>
                    </div>
                    @endif
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="request-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit economic request</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator"method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" name="economicsheet_id" value="{{ $economicsheet->id }}" />
                    <input type="hidden" name="economiccat_id" value="{{ $economiccat->id }}" />
                    <input type="hidden" name="economicsubcat_id" value="{{ $economicsubcat->id }}" />
                    <div class="form-group mt-2">
                        <div class="row mt-2">
                            <div class="col-2">
                                <label class="control-label" for="order"><strong>Order:</strong></label>
                            </div>
                            <div class="col-3">
                                <input type="number" id="ordering" name="ordering" min="1" class="form-control" data-error="Please enter valid order" placeholder="{{$last_order+1}}"/>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="title"><strong>Request title:</strong></label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title" required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-3">
                                <label class="control-label" for="responsetype_id"><strong>Unit:</strong></label>
                            </div>
                            <div class="col-9">
                                <label class="control-label" for="responsetype_id"><strong>Help:</strong>&nbsp;(Extra info, like €, €/week, €/hour ...)</label>
                            </div>
                        </div>
                    </div> 
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-3">
                                <select class="form-control text-center" id="responsetype_id" disabled>
                                </select>
                            </div>
                            <div class="col-9">
                                <input type="text" id="help" name="help" class="form-control" data-error="Please enter valid text help" />
                            </div>
                        </div>
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
<!-- Settings button: Edit Weight -->
<div class="modal fade" id="request-edit-weight" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit request weight</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" name="economicsheet_id" value="{{ $economicsheet->id }}" />
                    <input type="hidden" name="economiccat_id" value="{{ $economiccat->id }}" />
                    <input type="hidden" name="economicsubcat_id" value="{{ $economicsubcat->id }}" />
                    <div>
                        <div class="row mt-2">
                            <div class="col-2">
                                <label class="control-label text-center" for="responsetype_id"><strong>Order:</strong></label>
                            </div>
                            <div class="col-7">
                                <label class="control-label" for="responsetype_id"><strong>Request:</strong></label>
                            </div>
                            <div class="col-3">
                                <label class="control-label" for="responsetype_id"><strong>Weight:</strong></label>
                            </div>
                        </div>
                    </div> 
                    <div id="select_type_unit">
                        <div class="row mt-2">
                            <div class="col-2">
                                <input type="text" id="ordering" class="form-control small" disabled/>
                            </div>
                            <div class="col-7">
                                <input type="text" id="title" class="form-control small" disabled/>
                            </div>
                            <div class="col-3">
                                <input type="number" step="any" min="0.1" id="weight" name="weight" class="form-control small" required />
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Update weight</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete request -->
<div class="modal fade" id="request-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete request on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $economiccat->title }} / {{ $economicsubcat->title}}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
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
function editRequest(economicrequest_id){
    $("#request-edit #form_edit").attr("action","{{ url('economicrequests') }}/"+economicrequest_id);
    $("#request-edit #form_edit #ordering").val($("#ordering_"+economicrequest_id).text());
    $("#request-edit #form_edit #title").val($("#title_"+economicrequest_id).text());
    $("#request-edit #form_edit #help").val($("#help_"+economicrequest_id).text());
    $("#request-edit #form_edit #ordering").val($("#ordering_"+economicrequest_id).text());
    $("#request-edit #form_edit #responsetype_id").empty();
    $("#request-edit #form_edit #responsetype_id").append("<option selected>"+$("#unit_"+economicrequest_id).text()+"</option>");
}
function deleteRequest(economicrequest_id){
    $("#request-delete #form_delete").attr("action","{{ url('economicrequests') }}/"+economicrequest_id);
    $("#request-delete #form_delete #title").text($("#title_"+economicrequest_id).text());
}
function editWeight(economicrequest_id){
    $("#request-edit-weight #form_edit").attr("action","{{ url('economicrequest/weight') }}/"+economicrequest_id);
    $("#request-edit-weight #form_edit #ordering").val($("#ordering_"+economicrequest_id).text());
    $("#request-edit-weight #form_edit #title").val($("#title_"+economicrequest_id).text());
    $("#request-edit-weight #form_edit #weight").val($("#weight_"+economicrequest_id).text());
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [2,3]
    },
    {
        "width": "1%", 
        "targets": 0
    },
    {
        "width": "74%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
$('#dt_list_weighted').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [2,3,4]
    },
    {
        "width": "1%", 
        "targets": 0
    },
    {
        "width": "58%", 
        "targets": 1
    },
    {
        "width": "10%", 
        "targets": 2
    },
    {
        "width": "16%", 
        "targets": 3
    },
    {
        "width": "15%", 
        "targets": 4
    }],
    "dom": 'fprt',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection