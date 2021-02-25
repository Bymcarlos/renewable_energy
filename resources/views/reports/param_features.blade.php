@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.parameters') }}">Search by parameters</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.parameters.bench',['bench'=>$bench->id]) }}">{{ $bench->title }}</a></li>
    <li class="breadcrumb-item active"><span class="text-info">{{ $sheet->assessment()->first()->title }}</span> - {{ $sheet->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Sheet features ... </small></td>
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
            <a class="nav-link {{ $class_active }}" href="{{ route('benches.reports.parameters.features',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$item_cat->id]) }}">{{ strtoupper($item_cat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right"></div>
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
            <a class="nav-link {{ $class_active }}" href="{{ route('benches.reports.parameters.features',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$item_cat->id,'subcat'=>$item_subcat->id]) }}" >{{ strtoupper($item_subcat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right"></div>
</div>

<!--Features -->
<table class="table-bordered table-sm table-hover" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
      <tr>
        <th></th>
        <th class="col-5">Feature</th>
        <th class="col-1">Type</th>
        <th class="col-6">Value</th>
      </tr>
    </thead>
    <tbody>
        @foreach($subcat->questions()->get() as $question)
            @php ($showDetails = true)
            @php ($question_title="")
            @foreach($question->features()->get() as $item_feature)
                @if ($item_feature->parameter==0) @continue @endif
                @php ($item_files = $bench->featuresfiles()->wherePivot('feature_id',$item_feature->id)->get())
                @if ($item_feature->responsetype_id == 5)
                  @php ($item_brands = $bench->featuresbrands()->wherePivot('feature_id',$item_feature->id)->get())
                @endif
                <tr id="feature_{{$item_feature->id}}" data-title="{{ $item_feature->title }}" data-help="{{$item_feature->help}}" 
                @if ($question->questiontype_id==2)
                    @if ($loop->first) 
                        class="bg-light"
                        @if ($bench_features[$item_feature->id]->pivot->value == "Yes") 
                            @php ($question_title=$item_feature->title)
                        @else
                            @php ($showDetails = false)
                        @endif
                    @else
                        data-question_{{$item_feature->id}} = "{{$question_title}}"
                        @if ($showDetails==false)
                            style="display:none;"
                        @endif    
                    @endif
                @endif
                >
                    <td>
                    @if ($question->questiontype_id==1)
                        <i class="fa fa-fw fa-cube"></i>
                    @endif
                    @if ($question->questiontype_id==2 && $loop->first)
                        <i class="fa fa-fw fa-cubes"></i>
                    @endif
                    </td>
                    <td style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editValue({{ $item_feature->id }},{{$item_feature->responsetype_id}}, '{{$item_feature->unit()->first()->title}}')">{{ $item_feature->title }}
                    @if ($item_feature->help)
                        <br/><small class="text-success">(<span>{{ $item_feature->help }}</span>)</small>
                    @endif
                    </td>
                    <td style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editValue({{ $item_feature->id }},{{$item_feature->responsetype_id}},'{{$item_feature->unit()->first()->title}}')"><small>
                        {{ $item_feature->responsetype()->first()->title }}
                        @if ($bench_features[$item_feature->id]->responsetype_id == 3)
                            ({{ $bench_features[$item_feature->id]->unit()->first()->title }})
                        @endif
                    </small></td>
                    @if ($bench_features[$item_feature->id]->responsetype_id == 5)
                    <td>
                        <div class="table-responsive">
                          <table class="table table-sm table-hover" width="100%" cellspacing="0">
                            @if (count($item_brands)>0)
                            <thead class="thead-dark" style="cursor: pointer;">
                            @else
                            <thead class="thead-light">
                            @endif
                              <tr>
                                <th class="col-5" id="brand_name_{{$item_feature->id}}" onclick="showBrands({{ $item_feature->id }})"><small>{{ $bench_features[$item_feature->id]->brand_name }}</small></th>
                                <th class="col-5" id="brand_value_{{$item_feature->id}}" onclick="showBrands({{ $item_feature->id }})"><small>{{ $bench_features[$item_feature->id]->brand_value }}</small></th>
                                <th class="col-1 text-center"><a href="#" class="fa fa-plus-square" data-title="New brand" data-toggle="modal" data-target="#modal-brand-admin" onclick="brandAdmin({{ $item_feature->id }},'','{{ $bench_features[$item_feature->id]->brand_value_unit()->first()->title }}',0)"></a></th>
                              </tr>
                            </thead>
                            <tbody id="brand_items_{{ $item_feature->id }}" style="display:none;">
                                @foreach($item_brands as $item_brand)
                                <tr style="cursor: pointer;">
                                    <td class="col-5" id="brand_name_item_{{ $item_brand->pivot->id }}" data-title="Edit item" data-toggle="modal" data-target="#modal-brand-admin" onclick="brandAdmin({{ $item_feature->id }},'{{ $item_brand->pivot->brand_value }}','{{ $bench_features[$item_feature->id]->brand_value_unit()->first()->title }}',{{ $item_brand->pivot->id }})"><small>{{ $item_brand->pivot->brand_name }}</small></td>
                                    <td class="col-5" id="brand_value_item_{{ $item_brand->pivot->id }}" data-title="Edit item" data-toggle="modal" data-target="#modal-brand-admin" onclick="brandAdmin({{ $item_feature->id }},'{{ $item_brand->pivot->brand_value }}','{{ $bench_features[$item_feature->id]->brand_value_unit()->first()->title }}',{{ $item_brand->pivot->id }})">
                                        <small>{{ $item_brand->pivot->brand_value }}&nbsp;({{ $bench_features[$item_feature->id]->brand_value_unit()->first()->title }})</small>
                                    </td>
                                    <td class="col-1 text-center">
                                        <a href="#" class="fa fa-trash m-1" data-title="Delete" data-toggle="modal" data-target="#modal-brand-delete" onclick="delBrandItem({{ $item_feature->id }},'{{ $item_brand->pivot->brand_value }}','{{ $bench_features[$item_feature->id]->brand_value_unit()->first()->title }}',{{ $item_brand->pivot->id }})"></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                          </table>
                        </div>
                     @else
                        <td style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editValue({{ $item_feature->id }},{{$item_feature->responsetype_id}}, '{{$item_feature->unit()->first()->title}}')">
                            <span id="value_{{ $item_feature->id }}">{{ $bench_features[$item_feature->id]->pivot->value }}</span>
                    @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<!-- Edit Item Value -->
<div class="modal fade" id="edit-value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Feature value:</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST" action="{{ route('benches.reports.parameters.feature.value') }}">
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}"/>
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}"/>
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}"/>
                    <input type="hidden" id="feature_id" name="feature_id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="value" id="title" name="title"></label>
                        <small class="text-info"><span id="help"></span></small>
                        <div id="content"></div>
                    </div>
                    <div class="form-group" id="unit" style="display: none;">
                        <label class="control-label" for="unit_id" id="unit_title">Unit:</label>
                        <div id="unit_selection">
                            <select class="form-control" id="unit_id" name="unit_id" disabled>
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

<!-- Modal Brand add/edit item -->
<div class="modal fade" id="modal-brand-admin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="brand_title_label"></h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content bg-light" id="question"></h5>
                <h5 class="modal-content" id="feature"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_brand_admin" method="POST">
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}"/>
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}"/>
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}"/>
                    <input type="hidden" id="feature_id" name="feature_id"/>
                    <input type="hidden" id="id" name="id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="table-responsive">
                          <table class="table table-sm" cellspacing="0">
                            <thead class="thead-dark">
                              <tr>
                                <th class="col-3" id="brand_name_label"></th>
                                <th class="col-3" id="brand_value_label"></th>
                                <th class="col-2 text-center">Unit</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-3"><input type="text" id="brand_name" name="brand_name" class="form-control" data-error="Please enter valid title." required/></td>
                                    <td class="col-3"><input type="text" id="brand_value" name="brand_value" class="form-control" data-error="Please enter valid title." required/></td>
                                    <td class="col-2 text-center" id="brand_value_unit_label"></td>
                                </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success" id="brand_button"></button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Brand item delete -->
<div class="modal fade" id="modal-brand-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete brand item:</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content bg-light" id="question"></h5>
                <h5 class="modal-content" id="feature"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST" action="{{ route('benchfeatures.brand.delete') }}">
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}"/>
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}"/>
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}"/>
                    <input type="hidden" id="feature_id" name="feature_id"/>
                    <input type="hidden" id="id" name="id"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="table-responsive">
                          <table class="table table-sm" cellspacing="0">
                            <thead class="thead-dark">
                              <tr>
                                <th class="col-3" id="brand_name_label"></th>
                                <th class="col-3" id="brand_value_label"></th>
                                <th class="col-2 text-center">Unit</th>
                              </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-3" id="brand_name" class="control-label"></td>
                                    <td class="col-3" id="brand_value" class="control-label"></td>
                                    <td class="col-2 text-center" id="brand_value_unit_label"></td>
                                </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
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
<script>
    function showBrands(feature_id) {
        if ($("#brand_items_"+feature_id).is(":visible"))
            $("#brand_items_"+feature_id).hide();
        else
            $("#brand_items_"+feature_id).show();
    }
    function delBrandItem(feature_id,brand_value,brand_value_unit,brand_id) {
        $("#brand_items_"+feature_id).show();   //To show brand list
        $("#modal-brand-delete #question").text($("#feature_"+feature_id).data("question_"+feature_id));
        $("#modal-brand-delete #feature").text($("#feature_"+feature_id).data("title"));
        $("#modal-brand-delete #feature_id").val(feature_id);
        $("#modal-brand-delete #id").val(brand_id);
        $("#modal-brand-delete #brand_name_label").text($("#brand_name_"+feature_id).text());
        $("#modal-brand-delete #brand_value_label").text($("#brand_value_"+feature_id).text());
        $("#modal-brand-delete #brand_value_unit_label").text(brand_value_unit);
        $("#modal-brand-delete #brand_name").text($("#brand_name_item_"+brand_id).text());
        $("#modal-brand-delete #brand_value").text(brand_value);
    }
    function brandAdmin(feature_id,brand_value,brand_value_unit,brand_id){
        $("#brand_items_"+feature_id).show(); //To show brand list
        $("#modal-brand-admin #question").text($("#feature_"+feature_id).data("question_"+feature_id));
        $("#modal-brand-admin #feature").text($("#feature_"+feature_id).data("title"));
        $("#modal-brand-admin #feature_id").val(feature_id);
        $("#modal-brand-admin #id").val(brand_id);
        $("#modal-brand-admin #brand_name_label").text($("#brand_name_"+feature_id).text());
        $("#modal-brand-admin #brand_value_label").text($("#brand_value_"+feature_id).text());
        $("#modal-brand-admin #brand_value_unit_label").text(brand_value_unit);
        $("#modal-brand-admin #brand_name").val('');
        $("#modal-brand-admin #brand_value").val('');
        $("#modal-brand-admin #form_brand_admin #_method").remove();
        $("#modal-brand-admin #form_brand_admin").removeAttr("action");
        $("#modal-brand-admin #form_brand_admin #_method").remove();
        if (brand_id>0) {
            //Edit
            $("#modal-brand-admin #form_brand_admin").attr("action","{{ route('benchfeatures.brand.update') }}");
            $("#modal-brand-admin #form_brand_admin").append("<input type='hidden' name='_method' id='_method' value='PUT' />");
            $("#modal-brand-admin #brand_title_label").text("Edit brand item:");
            $("#modal-brand-admin #brand_name").val($("#brand_name_item_"+brand_id).text());
            $("#modal-brand-admin #brand_value").val(brand_value);
            $("#modal-brand-admin #brand_button").text("Update");
        } else {
            //Add
            $("#modal-brand-admin #form_brand_admin").attr("action","{{ route('benchfeatures.brand.store') }}");
            $("#modal-brand-admin #brand_title_label").text("Add brand item to:");
            $("#modal-brand-admin #brand_button").text("Add");
        }
    }
    function editValue(feature_id,responsetype_id,unit) {
        $("#edit-value #feature_id").val(feature_id);
        $("#edit-value #title").text($("#feature_"+feature_id).data("title")+":");
        $("#edit-value #comments").text($("#comments_"+feature_id).attr("title"));
        if ($("#help_"+feature_id).text()) {
            $("#edit-value #help").text($("#help_"+feature_id).text());
        }
        $("#edit-value #content").empty();
        switch(responsetype_id) {
            case 2:
                //Yes-No
                if ($("#value_"+feature_id).text()=="Yes" || !$("#value_"+feature_id).text())
                    $("#edit-value #content").append('<select id="value" name="value" class="form-control"><option value="Yes" selected>Yes</option><option value="No">No</option></select>');
                else
                    $("#edit-value #content").append('<select id="value" name="value" class="form-control"><option value="Yes">Yes</option><option value="No" selected>No</option></select>');
                break;
            case 3:
                $("#edit-value #content").append('<input type="text" id="value" name="value" class="form-control" data-error="Please enter valid value."/>');
                $("#edit-value #value").val($("#value_"+feature_id).text());
                $("#edit-value #unit_id").empty();
                $("#edit-value #unit_id").append('<option>'+unit+'</option>');
                $("#edit-value #unit").removeAttr("style");
                break;
            case 4:
                //Date
                $("#edit-value #content").append('<input type="date" id="value" name="value" class="form-control" data-error="Please enter valid date."/>');
                if ($("#value_"+feature_id).text())
                    $("#edit-value #value").val($("#value_"+feature_id).text());
                break;
            default:
                $("#edit-value #content").append('<input type="text" id="value" name="value" class="form-control" data-error="Please enter valid value."/>');
                $("#edit-value #value").val($("#value_"+feature_id).text());
                break;
        }
    }
    function attach(feature_id){
        $("#modal-attach #feature_id").val(feature_id);
        $("#modal-attach #modal_title").text("Attach file to '"+$("#feature_title_"+feature_id).text()+"'");
    }
    function showImage(feature_id,id_file, file_title) {
        var img_src = $("#img_"+id_file).attr("src");
        $("#modal-picture #img-zoom").attr("src",img_src);
        $("#modal-picture #modal_title").text(file_title);
        $("#modal-picture #feature_id").val(feature_id);
        $("#modal-picture #id").val(id_file);
    }
    function showFile(feature_id,id_file, file_title) {
        var file_link = $("#file_"+id_file).data("file_link");
        $("#modal-file #file_link").attr("href",file_link);
        $("#modal-file #modal_title").text(file_title);
        $("#modal-file #feature_id").val(feature_id);
        $("#modal-file #id").val(id_file);
    }
</script>
@endsection