@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.areas') }}">Ratings - Areas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.index',['area_id'=>$rating->area_id]) }}">{{$rating->area()->first()->title}}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratings.show',['rating_id'=>$rating->id]) }}">{{$rating->title}}</a></li>
    <li class="breadcrumb-item active">Bench -{{$ratingbench->bench()->first()->title}}- Economics</li>
</ol>
<table class="table-sm mb-2 bg-light border" width="100%" cellspacing="0">
    <tr>
      <td class="small" width="50%" valign="top"><strong>Business case&nbsp;</strong>assesses the quotations submitted by laboratories regarding a specific TS.<br/>
        <ul>
            <li>CAPEX:&nbsp;Capital Expenditures.</li>
            <li>OPEX:&nbsp;Operating Expenses.</li>
            <li>TRANSPORT:&nbsp;Transport and others.</li>
            <li>OPPORTUNITY COST</li>
        </ul>
      </td>
      <td class="small" width="50%" valign="top"><strong>Alternative case&nbsp;</strong>considers additional costs which may be incurred in under relatively common eventualities of a test campaign, out of laboratory responsibility.<br/>
        <ul>
            <li>Delay, failure during test & test extensions.</li>
            <li>Cancellations.</li>
        </ul>
      </td>
    </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($ratingbench->rating()->first()->economicsheet()->first()->economiccats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $economiccat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('ratingeconomicrequests.index',['ratingbench' => $ratingbench->id,'economiccat'=>$item_cat->id]) }}">{{ $item_cat->title }}</a>
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
            <a class="nav-link {{ $class_active }}" href="{{ route('ratingeconomicrequests.index',['ratingbench' => $ratingbench->id,'economiccat'=>$economiccat->id,'economicsubcat'=>$item_subcat->id]) }}">{{ $item_subcat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">

    </div>
</div>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>ORDER</th>
            <th>CONCEPT</th>
            <th>VALUE</th>
            <th>TYPE (UNIT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($economicsubcat->economicrequests()->orderby('ordering','asc')->get() as $economicrequest)
        @php ($ratingeconomicrequest = $economicrequests[$economicrequest->id])
        <tr bgcolor="{{$statecolors[$ratingeconomicrequest->ratingrequeststate_id]}}">
            <td class="small">{{$economicrequest->ordering}}</td>
            <td class="small" style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editRatingeconomicRequestValue('{{$ratingeconomicrequest->id}}','{{$ratingeconomicrequest->value}}','{{$ratingeconomicrequest->ratingrequeststate_id}}')" id="title_{{$ratingeconomicrequest->id}}">{{$economicrequest->title}}@if (isset($economicrequest->help)) <br/><span class="text-info">{{ $economicrequest->help }}</span>@endif</td>
            <td class="small" title="{{$ratingeconomicrequest->ratingrequeststate()->first()->title}}">{{$ratingeconomicrequest->value}}</td>
            <td class="small" id="unit_{{$ratingeconomicrequest->id}}">Numeric ({{$economicrequest->unit()->first()->title}})</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Edit Item Value -->
<div class="modal fade" id="edit-value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Economic request value:</h4>
            </div>
            <div class="modal-body">
                <form id="form_value" data-toggle="validator" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="economiccat_id" value="{{$economiccat->id}}">
                    <input type="hidden" name="economicsubcat_id" value="{{$economicsubcat->id}}">
                    <div class="form-group">
                        <label class="control-label" for="value" id="title" name="title"></label>
                        <small class="text-info"><span id="help"></span></small>
                    </div>
                    <div class="form-group row">
                        <div class="col-4 text-center">
                            <label class="control-label small" for="value">Value:</label>
                        </div>
                        <div class="col-4 text-center">
                            <label class="control-label small" for="unit_id">Unit:</label>
                        </div>
                        <div class="col-4 text-center">
                            <label class="control-label small" for="estimated">State</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-4">
                            <input type="number" step="any" min="0" id="value" name="value" class="form-control small" data-error="Please enter valid value." required/>
                        </div>
                        <div class="col-4 text-center">
                            <select class="form-control small" id="unit_id" name="unit_id" disabled>
                            </select>
                        </div>
                        <div class="col-4 text-center">
                            <select class="form-control small" id="state" name="state" required>
                                @foreach ($ratingrequeststates as $ratingrequeststate)
                                <option value="{{$ratingrequeststate->id}}" @if ($ratingrequeststate->id==1) disabled @endif>{{$ratingrequeststate->title}}</option>
                                @endforeach
                            </select>
                        </div>
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
@endsection
@section('js_custom')
<script type="text/javascript">
function editRatingeconomicRequestValue(ratingeconomicrequest_id,ratingeconomicrequest_value,ratingrequeststate){
    $("#edit-value #form_value").attr("action","{{ url('ratingeconomicrequests') }}/"+ratingeconomicrequest_id);
    $("#edit-value #form_value #title").text($("#title_"+ratingeconomicrequest_id).text());
    $("#edit-value #form_value #value").val(ratingeconomicrequest_value);
    $("#edit-value #form_value #unit_id").empty();
    $("#edit-value #form_value #unit_id").append("<option selected>"+$("#unit_"+ratingeconomicrequest_id).text()+"</option")
    $("#edit-value #form_value #state").val(ratingrequeststate);
}

$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1,2,3]
    },
    {
        "width": "1%", 
        "targets": 0
    },
    {
        "width": "64%", 
        "targets": 1
    },
    {
        "width": "20%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    }],
    "dom": 'rt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection