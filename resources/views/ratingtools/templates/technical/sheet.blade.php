@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools') }}">Rating Tools</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ratingtools.templates') }}">Templates</a></li>
    <li class="breadcrumb-item"><a href="{{ route('techsheets.areas') }}">Technical - Areas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('techsheets.index',['area_id'=>$area->id]) }}">{{$area->title}} templates</a></li>
    <li class="breadcrumb-item active">{{$techsheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light border" width="100%" cellspacing="0">
    <tr >
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td class="small" width="99%">Technical template contains the technical capabilities (requirements) upon which the laboratory will be evaluated.
Requirements might be sorted in several categories. It is recommend to use the categories already defined in Request from TS template.</td>
    </tr>
    <tr>
      <td width="1%"></td>
      <td class="small" width="99%"><img src="{{ asset('icons/ic_applicable_fix.png') }}"width="14"/>&nbsp;Required: applicable to any test subtype<img src="{{ asset('icons/ic_applicable_ask.png') }}"width="14" class="ml-5"/>&nbsp;Specific: applicable only to an specific test subtype</td>
    </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($techsheet->techcats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $techcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('techrequests.index',['techsheet' => $techsheet->id,'techcat' => $item_cat->id]) }}">
            @if ($item_cat->applicable_id==1)
                <img src="{{ asset('icons/ic_applicable_fix.png') }}" title="Required: applicable to any test subtype" width="14"/>
            @else
                <img src="{{ asset('icons/ic_applicable_ask.png') }}" title="Specific: applicable only to an specific test subtype" width="14"/>
            @endif
            &nbsp;{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
        <a href="#" id="button_apply" class="btn btn-primary btn-sm fa fa-check-circle" title="Change applicable state" data-toggle="modal" data-target="#techcat-applicable"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-file-o" title="New category (set title)" data-toggle="modal" data-target="#techcat-add-new"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-file-text-o" title="New category (select input data category)" data-toggle="modal" data-target="#techcat-add-frominput"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit category" data-toggle="modal" data-target="#techcat-edit"></a>
        @if (count($techsheet->techcats()->get())>1 && $techcat->techrequests()->count()==0)
        <a href="#" class="btn btn-primary btn-sm fa fa-trash mr-1" title="Delete category" data-toggle="modal" data-target="#techcat-del"></a>
        @endif
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%"><small>Technical requirements are the ... </small></td>
      <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New requirement" data-toggle="modal" data-target="#techrequest-add" onclick="addTechrequest()"></a></td>
    </tr>
</table>
<!-- List -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr class="small font-weight-bold">
            <th></th>
            <th>REQUIREMENTS</th>
            <th>CRIT</th>
            <th>FUNC</th>
            <th>CRITERIA</th>
            <th>PARAMS</th>
            <th><strong>DB</strong> Feature</th>
            <th>TYPE</th>
            <th><strong>ID</strong> from TS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($techcat->techrequests()->orderby('ordering','asc')->get() as $techrequest)
            @php ($feature = $techrequest->feature()->first())
            @php ($criteriafunc = $techrequest->criteriafunc()->first())
            @php ($inputrequest=null)
            @php ($inputrequest_id=0)
            @if (isset($techrequest->inputrequest_id))
                @php ($inputrequest=$techrequest->inputrequest()->first())
                @php ($inputrequest_cat=$inputrequest->inputcat()->first())
                @php ($inputrequest_sheet=$inputrequest_cat->inputsheet()->first())
                @php ($inputrequest_id=$inputrequest->id)
            @endif
            <tr class="bg-light">
                <td class="small" id="ordering_{{$techrequest->id}}">{{$techrequest->ordering}}</td>
            @if (!isset($feature))
                    <td class="small" style="cursor: pointer;" data-toggle="modal" data-target="#techrequest-add" data-sheet="0" data-subcat="0" data-feature="0" data-criticality="{{$techrequest->criticality_id}}" data-criteriafunc="{{$techrequest->criteriafunc_id}}" data-inputrequest="{{$inputrequest_id}}" data-input_factor="0" data-input_value="0" data-input_rangex="0" data-input_rangey="0" onclick="editTechrequestNullFeature({{$techrequest->id}})" id="title_{{$techrequest->id}}">{{$techrequest->title}}
            @else
                @php ($type = $feature->responsetype()->first())
                @php ($unit = $feature->unit()->first())
                @php ($last_order=$techrequest->ordering)
                @php ($feature_subcat = $feature->question()->first()->subcat()->first())
                @php ($feature_cat = $feature_subcat->cat()->first())
                @php ($feature_sheet = $feature_cat->sheet()->first())
                    <td class="small" style="cursor: pointer;" data-toggle="modal" data-target="#techrequest-add" data-sheet="{{$feature->question()->first()->subcat()->first()->cat()->first()->sheet_id}}" data-subcat="{{$feature->question()->first()->subcat_id}}" data-feature="{{$feature->id}}" data-criticality="{{$techrequest->criticality_id}}" data-criteriafunc="{{$techrequest->criteriafunc_id}}" data-inputrequest="{{$inputrequest_id}}" data-input_factor="{{$techrequest->inputfactor}}" data-input_value="{{$techrequest->value}}" data-input_rangex="{{$techrequest->range_x}}" data-input_rangey="{{$techrequest->range_y}}" onclick="editTechrequest({{$techrequest->id}})" id="title_{{$techrequest->id}}">{{$techrequest->title}}
            @endif
                    @if (isset($techrequest->help)) <br/><span class="text-info" id="help_{{$techrequest->id}}">{{ $techrequest->help }}</span>@endif
                    </td>
                    <td class="text-center small" title="{{$techrequest->criticality()->first()->title}}"><img src="{{asset('icons/ic_criticality_'.$techrequest->criticality_id.'.png')}}" /></td>
                    <td class="text-center small" title="{{$techrequest->criteriafunc()->first()->criteria()->first()->title}}"><img src="{{asset('icons/ic_criteria_'.$techrequest->criteriafunc()->first()->criteria_id.'.png')}}" /></td>
                    <td class="small" title="{{$criteriafunc->description}}">{{$criteriafunc->title}}</td>
                    <td class="small">
                        @if ($criteriafunc->askinput)
                            @if (!isset($techrequest->inputrequest_id))
                            <div class="row">
                                <div class="col-5 small">Input:</div>
                                <div class="col-5 small">
                                    <i class="fa fa-fw fa-exclamation-circle" style="color: red" title="Pending set input from TS request"></i>
                                </div>
                            </div>
                            @endif
                        <div class="row">
                            <div class="col-5 small">Factor:</div>
                            <div class="col-5 small">
                                @if (isset($techrequest->inputfactor))
                                {{$techrequest->inputfactor}}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        @endif
                        @if ($criteriafunc->askvalue)
                        <div class="row">
                            <div class="col-5 small">Value:</div>
                            <div class="col-5 small">
                                @if (isset($techrequest->value))
                                    {{$techrequest->value}}
                                @else
                                    <i class="fa fa-fw fa-exclamation-circle" style="color: red" title="Pending set criteria value"></i>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if ($criteriafunc->askrange)
                        <div class="row">
                            <div class="col-5 small font-weight-bold">RangeX:</div>
                            <div class="col-5 small font-weight-bold">RangeY:</div>
                        </div>
                        <div class="row">
                            <div class="col-5 small text-center">
                                @if (isset($techrequest->range_x))
                                    {{$techrequest->range_x}}
                                @else
                                    <i class="fa fa-fw fa-exclamation-circle" style="color: red" title="Pending set Range X value"></i>
                                @endif
                            </div>
                            <div class="col-5 small">
                                @if (isset($techrequest->range_y))
                                    {{$techrequest->range_y}}
                                @else
                                    <i class="fa fa-fw fa-exclamation-circle" style="color: red" title="Pending set Range Y value"></i>
                                @endif
                            </div>
                        </div>
                        @endif
                    </td>
            @if (!isset($feature))
                    <td class="small" bgcolor="SandyBrown">Without feature!</td>
                    <td class="small"></td>
            @else
                    <td class="small" title="{{$feature_sheet->title}} / {{$feature_cat->title}} / {{$feature_subcat->title}}">{{$feature->title}}</td>
                    <td class="small">{{$type->title}} @if ($unit->id>1) ({{$unit->title}}) @endif</td>
            @endif
                    @if ($criteriafunc->askinput && $inputrequest_id==0)
                        <td class="small" bgcolor="SandyBrown">Input from TS required!</td>
                    @else
                        @if (isset($inputrequest))
                            <td class="small" title="{{$inputrequest_sheet->title}} / {{$inputrequest_cat->title}}">{{$techrequest->inputrequest()->first()->title}}</td>
                        @else
                            <td></td>
                        @endif
                    @endif
                    <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-trash" title="Delete requirement" data-toggle="modal" data-target="#techrequest-delete" onclick="deleteTechrequest({{$techrequest->id}})"></a></td>
            </tr>
        @endforeach
        @if(!isset($last_order)) @php ($last_order=0) @endif
    </tbody>
</table>
<!-- Create Techcat -->
<div class="modal fade" id="techcat-add-new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New category on {{ $techsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('techcats.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="techsheet_id" value="{{ $techsheet->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title_new_cat">New category title</label>
                    </div>
                    <div class="form-group">
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." placeholder="Category title" required="true" />
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
<!--  Create Techcat (select title from input categories) -->
<div class="modal fade" id="techcat-add-frominput" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New category on {{ $techsheet->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('techcats.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="techsheet_id" value="{{ $techsheet->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title_new_cat">Select input data category</label>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="title" required="true">
                            <option value="0" selected disabled>Select input categorie</option> 
                            @foreach($techsheet->inputsheet()->first()->inputcats()->get() as $inputcat)
                                <option value="{{$inputcat->title}}">{{$inputcat->title}}</option>
                            @endforeach
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
<!-- Edit Techcat -->
<div class="modal fade" id="techcat-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit category title</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('techcats.update',['cat_id'=>$techcat->id]) }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" name="techsheet_id" value="{{ $techsheet->id }}" />
                    <div class="form-group">
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." value="{{$techcat->title}}" placeholder="Category title" required="true"/>
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
<div class="modal fade" id="techcat-del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete category</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Delete {{ $techcat->title }} categorie on {{ $techsheet->title }}?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('techcats.destroy',['techcat'=>$techcat->id]) }}" method="POST">
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
<!-- Create tech request -->
<div class="modal fade" id="techrequest-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create requirement</h4>
            </div>
            <div class="modal-body">
                <form id="form_info" data-toggle="validator" method="post">
                    <input type="hidden" name="techsheet_id" value="{{$techsheet->id}}"/>
                    <input type="hidden" name="techcat_id" value="{{$techcat->id}}"/>
                    <input type="hidden" id="_method" name="_method"/>
                    {{ csrf_field() }}
                    <div class="form-group mt-2">
                        <div class="row mt-2">
                            <div class="col-2">
                                <input type="text" pattern="[0-9]+" id="ordering" name="ordering" min="1" class="form-control" data-error="Please enter valid order" placeholder="{{$last_order+1}}"/>
                            </div>
                            <div class="col-10">
                                <input type="text" id="title" name="title" class="form-control" placeholder="Requirement" required />
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-2">
                            <label class="control-label" for="title">Help:</label>
                        </div>
                        <div class="col-10">
                            <input type="text" name="help" class="form-control" data-error="Please enter valid help description." />
                        <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-2">
                            <label class="control-label" for="title"><strong>Feature:</strong></label>
                        </div>
                        <div class="col-5">
                            <select class="form-control" id="sheet_id" name="sheet_id" onchange="loadCatSubcats(this.value)" required>
                                <option value="0" selected disabled>Assessment/Sheet</option>
                                @foreach ($assessments as $assessment)
                                    <optgroup label="{{$assessment->title}}">
                                        @foreach ($assessment->sheets()->orderby('title','asc')->get() as $sheet)
                                        <option value="{{$sheet->id}}">{{$sheet->title}}</option>
                                        @endforeach
                                    </optgroup> 
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <div id="select_subcat" style="display: none;">
                            <select class="form-control" id="subcat_id" name="subcat_id" onchange="loadFeatures(this.value)" required>
                            </select>
                        </div>
                        </div>
                    </div>

                    <div class="form-group row" id="select_feature" style="display: none;">
                        <div class="col-2">
                            
                        </div>
                        <div class="col-10">
                            <select class="form-control" id="feature_id" name="feature_id" onchange="showCriticality()" required>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row" id="select_criticality" style="display: none">
                        <div class="col-4">
                            <label class="control-label" for="criticality_id"><strong>Criticality?</strong></label>
                        </div>
                        <div class="col-5">
                            <select class="form-control" id="criticality_id" name="criticality_id" onchange="showCriteria()" required>
                                <option selected value="0" disabled>Select criticality</option>
                                @foreach($criticalities as $criticality)
                                <option value="{{$criticality->id}}">{{$criticality->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="select_criteria" style="display: none">
                        <div class="col-4">
                            <label class="control-label" for="criteriafunc_id"><strong>Rating criteria?</strong></label>
                        </div>
                        <div class="col-8">
                            <select class="form-control" id="criteriafunc_id" name="criteriafunc_id" onchange="askCriteriaParams(this.value)" required>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="select_input_data" style="display: none">
                        <div class="row">
                            <div class="col-2">
                            </div>
                            <div class="col-10">
                                <select id="inputrequest_id" name="inputrequest_id" class="form-control">
                                    <option value="0" selected disabled>Select ID from TS</option>
                                    @foreach($techsheet->inputsheet()->first()->inputcats()->get() as $inputcat)
                                    <optgroup label="{{$inputcat->title}}">
                                        @foreach($inputcat->inputrequests()->get() as $inputrequest)
                                        <option value="{{$inputrequest->id}}">{{$inputrequest->title}}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="select_criteria_values" style="display: none">
                        <div class="row">
                            <div class="col-3">
                                <label class="control-label" for="inputfactor"><small>Factor*ID:</small></label>
                            </div>
                            <div class="col-2">
                                <label class="control-label" for="criteria_value"><small>Value:</small></label>
                            </div>
                            <div class="col-2">
                                <label class="control-label" for="criteria_range_x"><small>Range X:</small></label>
                            </div>
                            <div class="col-2">
                                <label class="control-label" for="criteria_range_y"><small>Range Y:</small></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <input type="text" id="inputfactor" name="inputfactor" class="form-control form-control-sm" data-error="Please enter valid factor." disabled/>
                            </div>
                            <div class="col-2">
                                <input type="text" id="criteria_value" name="criteria_value" class="form-control form-control-sm" data-error="Please enter valid criteria value." disabled />
                            </div>
                            <div class="col-2">
                                <input type="text" id="criteria_range_x" name="criteria_range_x" class="form-control form-control-sm" data-error="Please enter valid criteria range X." disabled/>
                            </div>
                            <div class="col-2">
                                <input type="text" id="criteria_range_y" name="criteria_range_y" class="form-control form-control-sm" data-error="Please enter valid criteria range Y." disabled/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" id="btn_action" class="btn crud-submit btn-success"></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete tech requirement -->
<div class="modal fade" id="techrequest-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete requirement on</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $techsheet->title }} / {{ $techcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST">
                    <input type="hidden" name="techsheet_id" value="{{$techsheet->id}}"/>
                    <input type="hidden" name="techcat_id" value="{{$techcat->id}}"/>
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
<!-- Change category applicable state -->
<div class="modal fade" id="techcat-applicable" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Change category applicable state</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $techcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST" action="{{route('techcat.applicable')}}">
                    <input type="hidden" name="techcat_id" value="{{$techcat->id}}"/>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <h5 class="modal-content" id="title">
                        @if ($techcat->applicable_id==1)
                            Change applicability from required to specific?
                        @else
                            Change applicability from specific to required?
                        @endif
                        </h5>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Ok</button>
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
function loadCatSubcats(sheet_id,subcat_id=0,feature_id=0,criticality_id=0,criteriafunc_id=0,inputrequest_id=0,techrequest_id=0){
    $.ajax({
            type: 'GET',
            url: "{{ url('assessment/sheet') }}/"+sheet_id,
            //data: { _token: "{{ csrf_token() }}", area_id: $("#add-template #area_id").val() },
            success: function(data){
                $("#techrequest-add #subcat_id").empty();
                $("#techrequest-add #subcat_id").append('<option value="0" selected disabled>Select cat/subcat</option>');
                $.each(data, function(index, sheet_cat) {
                    $("#techrequest-add #subcat_id").append('<optgroup label="'+sheet_cat.cat+'">');
                    $.each(sheet_cat.subcats, function(index, sheet_subcat) {
                        $("#techrequest-add #subcat_id").append('<option value="'+sheet_subcat.id+'">&nbsp;&nbsp;'+sheet_subcat.subcat+'</option>');
                    });
                    $("#techrequest-add #subcat_id").append('</optgroup>');
                });
                if(subcat_id>0) {
                    $("#techrequest-add #subcat_id").val(subcat_id);
                    loadFeatures(subcat_id,feature_id,criticality_id,criteriafunc_id,inputrequest_id,techrequest_id);
                }
                $("#techrequest-add #select_subcat").removeAttr('style');
            },
            error: function (xhr, status, error) {
                //var err = eval("(" + xhr.responseText + ")");
                //console.log("error:"+err.Message);
            }
        });
}
function loadFeatures(subcat_id,feature_id=0,criticality_id=0,criteriafunc_id=0,inputrequest_id=0,techrequest_id=0){
    $.ajax({
        type: 'GET',
        url: "{{ url('cat/subcat') }}/"+subcat_id,
        //data: { _token: "{{ csrf_token() }}", area_id: $("#add-template #area_id").val() },
        success: function(data){
            console.log(data);
            $("#techrequest-add #feature_id").empty();
            $("#techrequest-add #feature_id").append('<option value="0" selected disabled>Select feature</option>');
            $.each(data, function(index, feature) {
                $("#techrequest-add #feature_id").append('<option value="'+feature.id+'">'+feature.id+'-'+feature.title+' '+feature.unit+'</option>');
            });
            if(feature_id>0) {
                $("#techrequest-add #feature_id").val(feature_id);
                showCriticality(criticality_id,criteriafunc_id,inputrequest_id,techrequest_id);
            }
            $("#techrequest-add #select_feature").removeAttr('style');
        },
        error: function (xhr, status, error) {
            //var err = eval("(" + xhr.responseText + ")");
            //console.log("error:"+err.Message);
        }
    });
}
function resetQuestions(){
    $("#techrequest-add #select_input_data").attr('style','display:none;');
    $("#techrequest-add #inputrequest_id").val(0);
    $("#techrequest-add #select_criteria_values").attr('style','display:none;');
    $("#techrequest-add #inputrequest_id").removeAttr('required');
    $("#techrequest-add #inputfactor").val('');
    $("#techrequest-add #inputfactor").attr('disabled','true');
    $("#techrequest-add #criteria_value").val('');
    $("#techrequest-add #criteria_value").attr('disabled','true');
    $("#techrequest-add #criteria_range_x").val('');
    $("#techrequest-add #criteria_range_x").attr('disabled','true');
    $("#techrequest-add #criteria_range_y").val('');
    $("#techrequest-add #criteria_range_y").attr('disabled','true');
}
function showCriticality(criticality_id=0,criteriafunc_id=0,inputrequest_id=0,techrequest_id=0){
    $("#techrequest-add #select_criticality").removeAttr('style');
    $("#techrequest-add #criticality_id").val(criticality_id);
    $("#techrequest-add #select_criteria").attr('style','display:none;');
    $("#techrequest-add #criteriafunc_id").empty();
    resetQuestions();
    if (criteriafunc_id>0) showCriteria(criteriafunc_id,inputrequest_id,techrequest_id);

}
function showCriteria(criteria_id=0,inputrequest_id=0,techrequest_id=0){
    //$("#techrequest-add #select_criteria").attr('style','display:none;');
    $("#techrequest-add #criteriafunc_id").empty();
    resetQuestions();
    var featureID = $("#techrequest-add #feature_id").val();
    var criticalityID = $("#techrequest-add #criticality_id").val();
    $.ajax({
            type: 'POST',
            url: "{{ url('criteriafuncs') }}",
            data: { _token: "{{ csrf_token() }}", feature_id: featureID, criticality_id: criticalityID },
            success: function(data){
                console.log(data);
                $("#techrequest-add #criteriafunc_id").append('<option value="0" selected disabled>Select criteria</option>');
                $.each(data, function(index, item) {
                    $("#techrequest-add #criteriafunc_id").append('<optgroup label="'+item.criteria+'">');
                    $.each(item.criteriafuncs, function(index, criteriafunc) {
                        console.log(criteriafunc);
                        $("#techrequest-add #criteriafunc_id").append('<option value="'+criteriafunc.id+'" id="criteriafunc_'+criteriafunc.id+'" data-responsetype="'+criteriafunc.responsetype+'" data-askinput="'+criteriafunc.askinput+'" data-askvalue="'+criteriafunc.askvalue+'" data-askrange="'+criteriafunc.askrange+'">&nbsp;&nbsp;'+criteriafunc.title+'</option>');
                    });
                    $("#techrequest-add #criteriafunc_id").append('</optgroup>');
                });
                if(criteria_id>0) {
                    $("#techrequest-add #criteriafunc_id").val(criteria_id);
                    $("#techrequest-add #form_info #inputrequest_id").val(inputrequest_id);
                    askCriteriaParams(criteria_id,inputrequest_id,techrequest_id);
                }
                $("#techrequest-add #select_criteria").removeAttr('style');
            },
            error: function (xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log("error:"+err.Message);
            }
        });
}
function askCriteriaParams(criteriafunc_id,inputrequest_id=0,techrequest_id=0){
    var id = "criteriafunc_"+criteriafunc_id;
    var responsetype = $("#techrequest-add #criteriafunc_id #"+id).data("responsetype");
    var askinput = $("#techrequest-add #criteriafunc_id #"+id).data("askinput");
    var askvalue = $("#techrequest-add #criteriafunc_id #"+id).data("askvalue");
    var askrange = $("#techrequest-add #criteriafunc_id #"+id).data("askrange");
    if (askinput) {
        $("#techrequest-add #select_input_data").removeAttr('style');
        $("#techrequest-add #inputrequest_id").attr('required','true');
        if (responsetype==3) {
            $("#techrequest-add #inputfactor").removeAttr('disabled');
            $("#techrequest-add #inputfactor").val($('#title_'+techrequest_id).data('input_factor'));
        } else
            $("#techrequest-add #inputfactor").attr('disabled','true');
    } else {
        $("#techrequest-add #select_input_data").attr('style','display:none;');
        $("#techrequest-add #inputrequest_id").removeAttr('required');
        $("#techrequest-add #inputfactor").attr('disabled','true');
        $("#techrequest-add #inputfactor").val('');
    }
    if (askvalue) {
        $("#techrequest-add #criteria_value").removeAttr('disabled');
        $("#techrequest-add #criteria_value").attr('required','true');
        $("#techrequest-add #criteria_value").val($('#title_'+techrequest_id).data('input_value'));
    } else {
        $("#techrequest-add #criteria_value").removeAttr('required');
        $("#techrequest-add #criteria_value").attr('disabled','true');
        $("#techrequest-add #criteria_value").val('');
    }

    if (askrange) {
        $("#techrequest-add #criteria_range_x").removeAttr('disabled');
        $("#techrequest-add #criteria_range_y").removeAttr('disabled');
        $("#techrequest-add #criteria_range_x").attr('required','true');
        $("#techrequest-add #criteria_range_x").val($('#title_'+techrequest_id).data('input_rangex'));
        $("#techrequest-add #criteria_range_y").attr('required','true');
        $("#techrequest-add #criteria_range_y").val($('#title_'+techrequest_id).data('input_rangey'));
    } else {
        $("#techrequest-add #criteria_range_x").removeAttr('required');
        $("#techrequest-add #criteria_range_y").removeAttr('required');
        $("#techrequest-add #criteria_range_x").attr('disabled','true');
        $("#techrequest-add #criteria_range_y").attr('disabled','true');
        $("#techrequest-add #criteria_range_x").val('');
        $("#techrequest-add #criteria_range_y").val('');
    }
    $("#techrequest-add #select_criteria_values").removeAttr('style');
}
function addTechrequest() {
    $("#techrequest-add #form_info").attr("action","{{ route('techrequests.store') }}");
    $("#techrequest-add #form_info #title").val('');
    $("#techrequest-add #form_info #ordering").val('');

    $("#techrequest-add #form_info #sheet_id").val(0);
    $("#techrequest-add #form_info #subcat_id").empty();
    $("#techrequest-add #select_subcat").attr('style','display:none;');
    $("#techrequest-add #form_info #feature_id").empty();
    $("#techrequest-add #select_feature").attr('style','display:none;');

    $("#techrequest-add #select_criticality").attr('style','display:none;');
    $("#techrequest-add #criticality_id").val(0);
    $("#techrequest-add #select_criteria").attr('style','display:none;');
    $("#techrequest-add #criteriafunc_id").empty();
    resetQuestions();
    $("#techrequest-add #form_info #_method").val("post");
    $("#techrequest-add #btn_action").text('Add');
}
function editTechrequest(techrequest_id) {
    var sheet_id = $("#title_"+techrequest_id).data("sheet");
    var subcat_id = $("#title_"+techrequest_id).data("subcat");
    var feature_id = $("#title_"+techrequest_id).data("feature");
    var criticality_id = $("#title_"+techrequest_id).data("criticality");
    var criteriafunc_id = $("#title_"+techrequest_id).data("criteriafunc");
    var inputrequest_id = $("#title_"+techrequest_id).data("inputrequest");

    $("#techrequest-add #form_info").attr("action","{{ url('techrequests') }}/"+techrequest_id);
    $("#techrequest-add #form_info #_method").val("put");
    $("#techrequest-add #form_info #title").val($("#title_"+techrequest_id).text());
    $("#techrequest-add #form_info #help").val($("#help_"+techrequest_id).text());
    $("#techrequest-add #form_info #ordering").val($("#ordering_"+techrequest_id).text());
    $("#techrequest-add #form_info #sheet_id").val(sheet_id);
    loadCatSubcats(sheet_id,subcat_id,feature_id,criticality_id,criteriafunc_id,inputrequest_id,techrequest_id);
    $("#techrequest-add #btn_action").text('Update');
    
}
function deleteTechrequest(techrequest_id){
    $("#techrequest-delete #form_delete").attr("action","{{ url('techrequests') }}/"+techrequest_id);
    $("#techrequest-delete #form_delete #title").text($("#title_"+techrequest_id).text());
}

function editTechrequestNullFeature(techrequest_id) {
    var criticality_id = $("#title_"+techrequest_id).data("criticality");
    var criteriafunc_id = $("#title_"+techrequest_id).data("criteriafunc");
    var inputrequest_id = $("#title_"+techrequest_id).data("inputrequest");

    $("#techrequest-add #form_info #sheet_id").val(0);
    $("#techrequest-add #form_info #subcat_id").empty();
    $("#techrequest-add #select_subcat").attr('style','display:none;');
    $("#techrequest-add #form_info #feature_id").empty();
    $("#techrequest-add #select_feature").attr('style','display:none;');

    $("#techrequest-add #select_criticality").attr('style','display:none;');
    $("#techrequest-add #criticality_id").val(0);
    $("#techrequest-add #select_criteria").attr('style','display:none;');
    $("#techrequest-add #criteriafunc_id").empty();

    $("#techrequest-add #form_info").attr("action","{{ url('techrequests') }}/"+techrequest_id);
    $("#techrequest-add #form_info #_method").val("put");
    $("#techrequest-add #form_info #title").val($("#title_"+techrequest_id).text());
    $("#techrequest-add #form_info #help").val($("#help_"+techrequest_id).text());
    $("#techrequest-add #form_info #ordering").val($("#ordering_"+techrequest_id).text());
    resetQuestions();

    $("#techrequest-add #form_info #feature_id").removeAttr('onchange');
    $("#techrequest-add #form_info #feature_id").attr('onchange','showCriticality('+criticality_id+','+criteriafunc_id+','+inputrequest_id+')');
    $("#techrequest-add #btn_action").text('Update');
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [0,1,2,3,4,5,6,7,8,9]
    },
    {
        "width": "1%", 
        "targets": 0
    },
    {
        "width": "29%", 
        "targets": 1
    },
    {
        "width": "1%", 
        "targets": 2
    },
    {
        "width": "1%", 
        "targets": 3
    },
    {
        "width": "10%", 
        "targets": 4
    },
    {
        "width": "8%", 
        "targets": 5
    },
    {
        "width": "26%", 
        "targets": 6
    },
    {
        "width": "6%", 
        "targets": 7
    },
    {
        "width": "17%", 
        "targets": 8
    },
    {
        "width": "1%", 
        "targets": 9
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection