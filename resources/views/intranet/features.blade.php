@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('management') }}">Management</a></li>
    @if ($sheet->assessment()->first()->assessmenttype_id==1)
        <li class="breadcrumb-item"><a href="{{ route('assessments.technical') }}">{{ $sheet->assessment()->first()->assessmenttype()->first()->title }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('assessments.economical') }}">{{ $sheet->assessment()->first()->assessmenttype()->first()->title }}</a></li>
    @endif
    <li class="breadcrumb-item active"><a href="{{ route('sheets.index',['id' => $sheet->assessment()->first()->id])}}">{{ $sheet->assessment()->first()->title }}</a></li>
    <li class="breadcrumb-item active">{{ $sheet->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Sheet features are divided into Categories and Subcategories. You can admin (add new, edit or delete) this categories and subcategories.</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-9">
        <ul class="nav nav-tabs d-inline-flex">
          @foreach($sheet->cats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $cat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('features.index',['id' => $sheet->id,'cat' => $item_cat->id]) }}">{{ strtoupper($item_cat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right">
        <a href="#" class="btn btn-primary btn-sm fa fa-file-o" title="New category" data-toggle="modal" data-target="#cat-add-item"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit category" data-toggle="modal" data-target="#cat-edit-item"></a>
        @if (count($sheet->cats()->get())>1)
        <a href="#" class="btn btn-primary btn-sm fa fa-trash mr-1" title="Delete category" data-toggle="modal" data-target="#cat-del-item"></a>
        @endif
    </div>
</div>
<!--Subcategories -->
<div class="row mb-1">
    <div class="col-9">
        <ul class="nav nav-tabs d-inline-flex">
          @foreach($cat->subcats()->get() as $item_subcat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_subcat->id == $subcat->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('features.index',['id' => $sheet->id,'cat' => $cat->id,'subcat'=>$item_subcat->id]) }}">{{ strtoupper($item_subcat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right">
        <a href="#" class="btn btn-primary btn-sm fa fa-file-o" title="New subcategory" data-toggle="modal" data-target="#subcat-add-item"></a>
        <a href="#" class="btn btn-primary btn-sm fa fa-pencil" title="Edit subcategory" data-toggle="modal" data-target="#subcat-edit-item"></a>
        @if (count($cat->subcats()->get())>1)
        <a href="#" id="subcat_btn_delete" class="btn btn-primary btn-sm fa fa-trash mr-1" title="Delete subcategory" data-toggle="modal" data-target="#subcat-del-item"></a>
        @endif
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Features are the final attributes. You can create new Features, edit or delete and the changes will be automatically propagated to all the related benches. Also, you can group features under a root feature of type Yes/No.</small></td>
          <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-cube mr-1" title="New feature" data-toggle="modal" data-target="#feature-item" onclick="adminFeature(0,0,0,1,'','',1,0,0,0)"></a><a href="#" class="btn btn-primary btn-sm fa fa-cubes mr-1" title="New group" data-toggle="modal" data-target="#group-item" onclick="adminGroup()"></a></td>
        </tr>
</table>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th></th>
            <th>FEATURES</th>
            <th>TYPE</th>
            <th>UNIT</th>
            <th><i class="fa fa-fw fa-upload" title="Importable"></i></th>
            <th><i class="fa fa-fw fa-search" title="Advance search"></i></th>
            <th><i class="fa fa-fw fa-star-half-o" title="Rating tool"></i></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php ($subcat_delete_btn = true)
        @foreach($subcat->questions()->get() as $question)
            @foreach($question->features()->get() as $item_feature)
                <!-- V2 (RATING TOOL) Check if this feature is relationed to any techrequest (=used in Rating Tool)-->
                @php ($rating_tool_relationed = @isset ($item_feature->techrequests()->first()->id))
                @if ($rating_tool_relationed) @php ($subcat_delete_btn = false) @endif
                <tr @if (($question->questiontype_id==1) || ($question->questiontype_id==2 && $loop->first))
                      id="question_{{$question->id}}" data-title="{{ $item_feature->title }}"
                      class="bg-light"
                    @endif>
                    <td>
                    @if ($question->questiontype_id==1)
                        <i class="fa fa-fw fa-cube"></i>
                    @endif
                    @if ($question->questiontype_id==2 && $loop->first)
                        <i class="fa fa-fw fa-cubes"></i>
                    @endif
                    </td>
                    <td style="cursor: pointer;" data-toggle="modal" 
                      @if ($question->questiontype_id==2 && $loop->first) 
                        data-target="#group-item" 
                        onclick="adminGroup({{$item_feature->id}})"
                      @else
                        data-target="#feature-item" 
                        onclick="adminFeature(0,{{$item_feature->id}},{{ $item_feature->responsetype_id }},{{ $item_feature->unit_id }},'{{ $item_feature->brand_name }}','{{ $item_feature->brand_value }}',{{ $item_feature->brand_value_unit }},{{$item_feature->rating_req}}, {{$item_feature->rating_crit}}, {{$item_feature->rating_func}})"
                      @endif><span id="title_{{$item_feature->id}}" data-importable="{{$item_feature->importable}}" data-parameter="{{$item_feature->parameter}}" class="small">({{ $item_feature->id }})-{{ $item_feature->title }}</span>
                      @if ($item_feature->help)
                        <br/><small class="text-info"><span id="help_{{$item_feature->id}}">{{ $item_feature->help }}</span></small>
                        @endif
                    </td>
                    <td class="small">{{ $item_feature->responsetype()->first()->title }}</td>
                    <td class="small">
                        @if ($item_feature->responsetype()->first()->id==3)
                            {{ $item_feature->unit()->first()->title }}
                        @endif
                        @if ($item_feature->responsetype()->first()->id==5)
                          <table class="table-bordered table-sm" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                              <tr>
                                <th class="col-5"><small>{{ $item_feature->brand_name }}</small></th>
                                <th class="col-5"><small>{{ $item_feature->brand_value }}</small></th>
                                <th class="col-2"><small>{{ $item_feature->brand_value_unit()->first()->title }}</small></th>
                              </tr>
                            </thead>
                          </table>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item_feature->importable==1)
                            <i class="fa fa-fw fa-check-square-o">
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item_feature->parameter==1)
                            <i class="fa fa-fw fa-check-square-o">
                        @endif
                    </td>
                    <td class="text-center">
                        <!-- V2 (RATING TOOL) Now check if this feature is relationed to any techrequest (=used in Rating Tool):
                        @if ($item_feature->rating_req==1)
                            <i class="fa fa-fw fa-check-square-o">
                        @endif
                        -->
                        @if ($rating_tool_relationed)
                            <i class="fa fa-fw fa-check-square-o">
                        @endif
                    </td>
                    <td class="text-right">
                        @if ($question->questiontype_id==2 && $loop->first)
                        <a href="#" class="btn btn-primary btn-sm fa fa-plus-circle" title="Add feature to this group" data-toggle="modal" data-target="#feature-item" onclick="adminFeature({{$question->id}},0,0,1,'','',1,0,0,0)"></a>
                        <!-- V2 Rating Tool: Only delete option if not relationed with techrequest on Rating Tool -->
                        @if ($rating_tool_relationed)
                            <i class="btn btn-danger btn-sm fa fa-trash" title="Relating to Rating Tool">
                        @else
                            <a href="#" class="btn btn-primary btn-sm fa fa-trash-o" title="Delete group (and all features)" data-toggle="modal" data-target="#feature-delete-item" onclick="deleteFeature({{$item_feature->id}})"></a>
                        @endif
                        @else
                            <!-- V2 Rating Tool: Only delete option if not relationed with techrequest on Rating Tool -->
                            @if ($rating_tool_relationed)
                                <i class="btn btn-danger btn-sm fa fa-trash" title="Relating to Rating Tool">
                            @else
                                <a href="#" class="btn btn-primary btn-sm fa fa-trash" title="Delete feature" data-toggle="modal" data-target="#feature-delete-item" onclick="deleteFeature({{$item_feature->id}})"></a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<!-- Create Category-->
<div class="modal fade" id="cat-add-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New category on {{ $sheet->title }} sheet</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('cats.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title">Category title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="subcat_title">Subcategory default title:</label>
                        <input type="text" name="subcat_title" class="form-control" value="GENERAL" data-error="Please enter valid title." required />
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

<!-- Edit Category-->
<div class="modal fade" id="cat-edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit category on {{ $sheet->title }} sheet</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('cats.update',['cat'=>$cat->id]) }}" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="sheet_id" value="{{ $sheet->id }}" />
                    <input type="hidden" name="subcat_id" value="{{ $subcat->id}}" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Category title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." value="{{ $cat->title }}" required />
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
<!-- Delete Category-->
<div class="modal fade" id="cat-del-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete category</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Delete {{ $cat->title }} categorie on {{ $sheet->title }} sheet?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('cats.destroy',['cat'=>$cat->id]) }}" method="POST">
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

<!-- Create subcategory-->
<div class="modal fade" id="subcat-add-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New subcategory on {{ $sheet->title }} / {{ $cat->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('subcats.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="sheet_id" value="{{ $sheet->id }}" />
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title">Subcategory title:</label>
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

<!-- Edit subcategory-->
<div class="modal fade" id="subcat-edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit subcategory on {{ $sheet->title }} / {{ $cat->title }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('subcats.update',['subcat'=>$subcat->id]) }}" method="POST">
                    <input type="hidden" name="_method" value="PUT" />
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="title">Subcategory title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." value="{{ $subcat->title }}" required />
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
<!-- Delete subcategory-->
<div class="modal fade" id="subcat-del-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete subcategory</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title">Delete {{ $subcat->title }} subcategory on {{ $cat->title }}?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('subcats.destroy',['subcat'=>$subcat->id]) }}" method="POST">
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
<!-- Add / Edit group -->
<div class="modal fade" id="group-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="group_title_label"></h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $sheet->title }} / {{ $cat->title }} / {{ $subcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_group" method="POST">
                    <input type="hidden" id="id" name="id"/>
                    <input type="hidden" id="questiontype_id" name="questiontype_id" value="2"/>
                    {{ csrf_field() }}
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="title">Group title:</label>
                        <input type="text" name="title" id="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="help">Help (addtional info):</label>
                        <input type="text" id="help" name="help" class="form-control" data-error="Please enter valid help text." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success" id="button"></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add / Edit feature -->
<div class="modal fade" id="feature-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="feature_title_label"></h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $sheet->title }} / {{ $cat->title }} / {{ $subcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_feature" method="POST">
                    <input type="hidden" id="id" name="id"/>
                    <input type="hidden" id="questiontype_id" name="questiontype_id" value="1"/>
                    {{ csrf_field() }}
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}" />
                    <input type="hidden" id="question_id" name="question_id"/>
                    <p class="control-label bg-light border border-secondary rounded p-1" id="group_title_label"></p>
                    <div class="form-group">
                        <label class="control-label" for="title"><strong>Feature title:</strong></label>
                        <input type="text" name="title" id="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="help"><strong>Help (addtional info):</strong></label>
                        <input type="text" id="help" name="help" class="form-control" data-error="Please enter valid help text." />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group border rounded p-1">
                        <label class="control-label"><strong>Is importable and/or is it used in advanced searches?</strong></label>
                        <div class="row">
                            <div class="col-5 text-center">
                                <input type="checkbox" class="mr-3" id="importable" name="importable" value="1"/>Importable
                            </div>
                            <div class="col-7 text-center">
                                <input type="checkbox" class="mr-3" id="parameter" name="parameter" value="1"/>Advanced searches
                            </div>
                        </div>
                    </div>
                    <div class="form-group border rounded p-1">
                        <div class="row">
                            <div class="col-6">
                                <label class="control-label" for="responsetype_id"><strong>Feature response type?</strong></label>
                            </div>
                            <div class="col-6">
                                <select class="form-control" id="responsetype_id" name="responsetype_id" onchange="changeOnResponseType()">
                                    @foreach ($responsetypes as $responsetype)
                                        <option value="{{ $responsetype->id }}">{{ $responsetype->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group mt-1" id="brand">
                            <div class="row">
                                <div class="col-6">
                                    <label class="control-label" for="unittype_id">Item:</label>
                                </div>
                                <div class="col-6">
                                    <label class="control-label" for="brand_name">Number / Value:</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" id="brand_name" name="brand_name" class="form-control" data-error="Please enter valid brand name" />
                                </div>
                                <div class="col-6">
                                    <input type="text" id="brand_value" name="brand_value" class="form-control" data-error="Please enter valid brand name" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-1" id="unit">
                            <div class="row">
                                <div class="col-6  text-right">
                                    <label class="control-label" for="unit_id" id="unit_label"></label>
                                </div>
                                <div class="col-6">
                                    <select class="form-control selectpicker" id="unit_id" name="unit_id">
                                        <option disabled selected value="0">Select unit</option>
                                        <option value="1"><small>None</small></option>
                                        @foreach ($unittypes as $unittype)
                                            <optgroup label="{{ $unittype->title }}">
                                                @foreach ($unittype->units()->get() as $unit)
                                                @if($unit->id>1)
                                                    <option value="{{ $unit->id }}"><small>{{ $unit->title }}</small></option>
                                                @endif
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- V2 (RATING TOOL) Not necessary:
                    <div class="form-group border rounded p-1" id="rating_required">
                        <div class="row">
                            <div class="col-6">
                                <label class="control-label" for="rating_req"><strong>Required for Rating Tool?</strong></label>
                            </div>
                            <div class="col-3">
                                <input type="radio" class="text-center mr-2" id="rating_req_1" name="rating_req" value="1" onclick="changeOnRatingReq(1,0,0)" />Yes
                            </div>
                            <div class="col-3">
                                <input type="radio" class="text-center mr-2" id="rating_req_0" name="rating_req" value="0" onclick="changeOnRatingReq(0,0,0)"/>No
                            </div>
                        </div>
                        <div class="row mt-1" id="rating_criticality">
                            <div class="col-1"></div>
                            <div class="col-5">
                                <label class="control-label" for="rating_req">Requirement criticality?</label>
                            </div>
                            <div class="col-6">
                                <select class="form-control selectpicker" id="rating_crit" name="rating_crit">
                                    <option disabled selected value="0">Select option</option>
                                    <option value="2">Primary</option>
                                    <option value="3">Secondary</option>
                                    <option value="4">Tertiary</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-1" id="rating_function">
                            <div class="col-1"></div>
                            <div class="col-5">
                                <label class="control-label" for="rating_func">Rating function?</label>
                            </div>
                            <div class="col-6">
                                <select class="form-control selectpicker" id="rating_func" name="rating_func">
                                    <option disabled selected value="0">Select option</option>
                                    <option value="2">Binary</option>
                                    <option value="3">Scale</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    -->
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success" id="button"></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete feature -->
<div class="modal fade" id="feature-delete-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete feature:</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="title"></h5>
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
@if ($subcat_delete_btn==false)
    <script type="text/javascript">
        $("#subcat_btn_delete").removeAttr('data-target');
        $("#subcat_btn_delete").removeAttr('data-toggle');
        $("#subcat_btn_delete").removeAttr('class');
        $("#subcat_btn_delete").attr('class','btn btn-danger btn-sm fa fa-trash mr-1');
    </script>
@endif
<script type="text/javascript">
    function changeOnResponseType(unit_id,brand_name,brand_value,brand_value_unit,rating_req,rating_crit,rating_func){
        $("#feature-item #unit").attr('style','display:none;');
        $("#feature-item #unit_id").val(0);
        $("#feature-item #unit #unit_id").removeAttr('required');

        $("#feature-item #brand").attr('style','display:none;');
        //V2 (RATING TOOL) Not necessary:
        //$("#feature-item #rating_required").attr('style','display:none;');
        //$("#feature-item #rating_required #rating_criticality").attr('style','display:none;');
        //$("#feature-item #rating_required #rating_function").attr('style','display:none;');
        
        featuretype=parseInt($("#feature-item #responsetype_id").val());
        switch (featuretype) {
            case 2:
                //V2 (RATING TOOL) Not necessary:
                //$("#feature-item #rating_required").removeAttr('style');
                //changeOnRatingReq(rating_req,rating_crit,rating_func);
                break;
            case 3:
                $("#feature-item #unit_label").text("Unit:");
                $("#feature-item #unit").removeAttr('style');
                $("#feature-item #unit #unit_id").attr('required','required');
                if (unit_id>0)
                    $("#feature-item #unit_id").val(unit_id);
                //V2 (RATING TOOL) Not necessary:
                //$("#feature-item #rating_required").removeAttr('style');
                //changeOnRatingReq(rating_req,rating_crit,rating_func);
                break;
            case 5:
                $("#feature-item #brand").removeAttr('style');
                $("#feature-item #unit_label").text("Brand Number / Value unit:");
                $("#feature-item #unit").removeAttr('style');
                $("#feature-item #brand_name").val(brand_name);
                $("#feature-item #brand_value").val(brand_value);
                $("#feature-item #unit_id").val(brand_value_unit);
                break;
            default:
                $("#feature-item #unit_id").val(1);
                break;
        }
        
    }
    /* V2 (RATING TOOL) Not necessary:
    function changeOnRatingReq(rating_req,rating_crit,rating_func){
        if (rating_req=="1") {
            $("#feature-item #rating_req_1").prop('checked', true);
            $("#feature-item #rating_criticality").removeAttr('style');
            $("#feature-item #rating_crit").val(rating_crit);
            $("#feature-item #rating_crit").attr('required','required');
            $("#feature-item #rating_function").removeAttr('style');
            $("#feature-item #rating_func").val(rating_func);
            $("#feature-item #rating_func").attr('required','required');
        } else {
            $("#feature-item #rating_req_0").prop('checked', true);
            $("#feature-item #rating_criticality").attr('style','display:none;');
            $("#feature-item #rating_function").attr('style','display:none;');
            $("#feature-item #rating_crit").removeAttr('required');
            $("#feature-item #rating_func").removeAttr('required');
        }
    }
    */
    function adminFeature(question_id,item_id,responsetype_id,unit_id,brand_name,brand_value,brand_value_init,rating_req,rating_crit,rating_func){
        $("#feature-item #form_feature #_method").remove();
        $("#feature-item #group_title_label").attr('style','display:none;');
        $("#feature-item #question_id").val(question_id);
        $("#feature-item #importable").removeAttr('checked');
        $("#feature-item #parameter").removeAttr('checked');
        $("#feature-item #rating_req_0").prop('checked', false);
        $("#feature-item #rating_req_1").prop('checked', false);
        if (item_id>0) {
            //Edit item
            $("#feature-item #form_feature #button").text("Update");
            $("#feature-item #form_feature").attr("action","{{ url('features') }}/"+item_id);
            $("#feature-item #form_feature").append("<input type='hidden' name='_method' id='_method' value='PUT' />");
            $("#feature-item #id").val(item_id);
            $("#feature-item #feature_title_label").text('Edit feature on:');
            $("#feature-item #title").val($("#title_"+item_id).text());
            $("#feature-item #help").val($("#help_"+item_id).text());
            if ($("#title_"+item_id).data("importable")==1)
                $("#feature-item #importable").attr('checked','checked');
            if ($("#title_"+item_id).data("parameter")==1)
                $("#feature-item #parameter").attr('checked','checked');
            $("#feature-item #responsetype_id").val(responsetype_id);
            changeOnResponseType(unit_id,brand_name,brand_value,brand_value_init,rating_req,rating_crit,rating_func);
        } else {
            //Add item, check if single or group feature:
            if (question_id>0) {
              $("#feature-item #feature_title_label").text('New group feature on:');
              $("#feature-item #group_title_label").text('GROUP: '+$("#question_"+question_id).data("title"));
              $("#feature-item #group_title_label").removeAttr('style');
            } else {
              $("#feature-item #feature_title_label").text('New single feature on:');
            }
            $("#feature-item #form_feature #button").text("Add");
            $("#feature-item #form_feature").attr("action","{{ route('features.store') }}");
            $("#feature-item #id").val(0);
            $("#feature-item #title").val('');
            $("#feature-item #help").val('');
            $("#feature-item #responsetype_id").val(1);
            $("#feature-item #unit").attr('style','display:none;');
            $("#feature-item #brand").attr('style','display:none;');
            //V2 (RATING TOOL) Not necessary:
            //$("#feature-item #rating_required").attr('style','display:none;');
            //$("#feature-item #rating_required #rating_criticality").attr('style','display:none;');
            //$("#feature-item #rating_required #rating_function").attr('style','display:none;');
        }
    }
    function editFeature(item_id,responsetype_id,unittype_id,unit_id){
        $("#feature-edit-item #form_edit").attr("action","{{ url('features') }}/"+item_id);
        $("#feature-edit-item #responsetype_id").val(responsetype_id);
        $("#feature-edit-item #unittype_id").val(unittype_id);
        $("#feature-edit-item #help").val($("#help_"+item_id).text());
    }
    function deleteFeature(item_id){
        $("#feature-delete-item #form_delete").attr("action","{{ url('features') }}/"+item_id);
        $("#feature-delete-item #title").text($("#title_"+item_id).text());
    }
    function adminGroup(item_id){
      $("#group-item #form_group #_method").remove();
      $("#group-item #title").val('');
      $("#group-item #help").val('');
        if (item_id>0) {
          //Edit item
          $("#group-item #form_group #button").text("Update");
          $("#group-item #form_group").attr("action","{{ url('features') }}/"+item_id);
          $("#group-item #form_group").append("<input type='hidden' name='_method' id='_method' value='PUT' />");
          $("#group-item #id").val(item_id);
          $("#group-item #group_title_label").text('Edit group on:');
          $("#group-item #title").val($("#title_"+item_id).text());
          $("#group-item #help").val($("#help_"+item_id).text());
      } else {
          //Add item
          $("#group-item #form_group #button").text("Add");
          $("#group-item #form_group").attr("action","{{ route('features.store') }}");
          $("#group-item #id").val(0);
          $("#group-item #group_title_label").text('New group on:');
      }
    }
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "targets": [0,1,2,3,4,5,6,7]
    },
    {
        "width": "1%", 
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
        "width": "30%", 
        "targets": 3
    },
    {
        "width": "1%", 
        "targets": 4
    },
    {
        "width": "1%", 
        "targets": 5
    },
    {
        "width": "1%", 
        "targets": 6
    },
    {
        "width": "8%", 
        "targets": 7
    }],
    "bSort" : false,
    "dom": 'rt<"bottom"p>',
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@stop