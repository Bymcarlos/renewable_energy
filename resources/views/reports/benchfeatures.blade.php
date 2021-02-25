@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.index') }}">Benches</a></li>
    @if ($sheet->assessment()->first()->assessmenttype_id==1)
        <li class="breadcrumb-item active"><a href="{{ route('bench.reports.assessments.technical',['bench'=>$bench->id]) }}">{{ $bench->title }}</a></li>
    @else
        <li class="breadcrumb-item active"><a href="{{ route('bench.reports.assessments.economical',['bench'=>$bench->id]) }}">{{ $bench->title }}</a></li>
    @endif
    <li class="breadcrumb-item active"><span class="text-info">{{ $sheet->assessment()->first()->title }}</span> - {{ $sheet->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Sheet features ... </small></td>
        	<td width="14%" class="text-right">
                @if ($sheet->assessment()->first()->assessmenttype_id==1)
                <a href="{{ route('bench.assessments.technical.export.excel',['bench'=>$bench->id,'sheet'=>$sheet->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export full sheet"></a>
                @else
                <a href="{{ route('bench.assessments.economical.export.excel',['bench'=>$bench->id,'sheet'=>$sheet->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export full sheet"></a>
                @endif
            </td>
        </tr>
</table>
{{-- Array with the bench techsheet features, key by the feature_id --}}
@php ($bench_techsheet_features = $bench->features()->get()->keyBy('pivot.feature_id'))
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
            <a class="nav-link {{ $class_active }}" href="{{ url('rep_benchfeatures') }}/{{ $bench->id }}/{{ $sheet->id }}/{{ $item_cat->id }}">{{ strtoupper($item_cat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right">
        <a href="{{ route('bench.assessments.technical.export.excel',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$cat->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o mr-1" title="Export category and subcategories"></a>
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
            <a class="nav-link {{ $class_active }}" href="{{ url('rep_benchfeatures') }}/{{ $bench->id }}/{{ $sheet->id }}/{{ $cat->id }}/{{ $item_subcat->id }}" >{{ strtoupper($item_subcat->title) }}</a>
          </li>
          @endforeach
        </ul>
    </div>
    <div class="col-3 text-right">
        <a href="{{ route('bench.assessments.technical.export.excel',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$cat->id,'subcat'=>$subcat->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o mr-1" title="Export subcategory"></a>
    </div>
</div>

<!--Features -->
<table class="table-bordered table-sm" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
      <tr>
        <th></th>
        <th class="col-4">Feature</th>
        <th class="col-1">Type</th>
        <th class="col-3">Value</th>
        <th class="col-4">Attachment</th>
      </tr>
    </thead>
    <tbody>
        @foreach($subcat->questions()->get() as $question)
            @php ($showDetails = true)
            @php ($question_title="")
            @foreach($question->features()->get() as $item_feature)
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
                    <td>{{ $item_feature->title }}
                    @if ($item_feature->help)
                        <br/><small class="text-success">(<span>{{ $item_feature->help }}</span>)</small>
                    @endif
                    </td>
                    <td><small>
                        {{ $item_feature->responsetype()->first()->title }}
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
                        <td>
                            <span>{{ $bench_features[$item_feature->id]->pivot->value }}</span>
                            @if ($bench_features[$item_feature->id]->responsetype_id==3 && $bench_features[$item_feature->id]->pivot->value)
                                {{ $bench_features[$item_feature->id]->unit()->first()->title }}
                            @endif
                        @endif
                    </td>
                    <td>
                        @php ($extensions = ['jpg','jpeg','png','gif'])
                        @foreach ($item_files as $item_file)
                            @php ($file_ext= \File::extension($item_file->pivot->file))
                            @if (in_array($file_ext, $extensions))
                                <a href="#" data-title="{{ $item_file->pivot->title }}" data-toggle="modal" data-target="#modal-picture" onclick="showImage({{ $item_feature->id }}, {{ $item_file->pivot->id }},'{{ $item_file->pivot->title }}')">
                                <img src="{{ asset('attachs')}}/{{ $item_file->pivot->file }}" class="img-thumbnail zoom" width="32" id="img_{{ $item_file->pivot->id }}" title="Image: {{ $item_file->pivot->title }}"/></a>
                            @else
                                <a href="#" data-title="{{ $item_file->pivot->title }}" data-toggle="modal" data-target="#modal-file" onclick="showFile({{ $item_feature->id }}, {{ $item_file->pivot->id }},'{{ $item_file->pivot->title }}')"><img src="{{ asset('icons/document.png') }}" class="img-thumbnail zoom" width="32" id="file_{{ $item_file->pivot->id }}" data-file_link="{{ asset('attachs')}}/{{ $item_file->pivot->file }}" title="File: {{ $item_file->pivot->title }}"/></a>
                            @endif
                        @endforeach
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
<!-- Modal zoom picture -->
<div class="modal fade" id="modal-picture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
            </div>
            <div class="modal-body">
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}"/>
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}"/>
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}"/>
                    <input type="hidden" id="feature_id" name="feature_id"/>
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="id" name="id"/>
                    <div class="form-group">
                        <img class="modal-content" id="img-zoom" style="display: block">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal file open -->
<div class="modal fade" id="modal-file" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
            </div>
            <small class="text-center">(Click on image to download file)</small>
            <div class="modal-body">
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="sheet_id" name="sheet_id" value="{{ $sheet->id }}"/>
                    <input type="hidden" id="cat_id" name="cat_id" value="{{ $cat->id }}"/>
                    <input type="hidden" id="subcat_id" name="subcat_id" value="{{ $subcat->id }}"/>
                    <input type="hidden" id="feature_id" name="feature_id"/>
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="id" name="id"/>
                    <div class="form-group">
                        <a id="file_link"><img src="{{ asset('icons/document.png') }}" class="modal-content" id="img-zoom" style="display: block"></a>
                    </div>
                    <div class="form-group mt-3">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
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