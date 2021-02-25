@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.parameters') }}">Search by parameters</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.parameters.show',['bench'=>$bench->id]) }}">{{ $bench->title }}</a></li>
    <li class="breadcrumb-item active">Parameters</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
            <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
            <td width="85%"><small>Create a ficticial bench and define the desired values of the features, to compare with real benches.</small></td>
            <td width="14%" class="text-right"></td>
        </tr>
</table>

<div class="card-body">
    <p class="text-white bg-primary pl-2">TITLE - COMMENTS - AREA - COMPONENT:</p>
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td width="20%">{{ $bench->title }}</td>
                    <td width="45%">{{ $bench->comments }}</td>
                    <td width="10%">{{ $bench->areaComponent()->first()->area()->first()->title }}</td>
                    <td width="20%">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                    <td width="5%" class="text-right"></td>
                </tr>
            </tbody>
        </table>
    </div>
    @if (count($benches)>0)
        <p class="text-white bg-primary pl-2 mt-2">SET PARAMETERS:</p>
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
                    <a class="nav-link {{ $class_active }}" href="{{ route('benches.reports.parameters.show.features',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$item_cat->id]) }}">{{ strtoupper($item_cat->title) }}</a>
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
                    <a class="nav-link {{ $class_active }}" href="{{ route('benches.reports.parameters.show.features',['bench'=>$bench->id,'sheet'=>$sheet->id,'cat'=>$item_cat->id,'subcat'=>$item_subcat->id]) }}" >{{ strtoupper($item_subcat->title) }}</a>
                  </li>
                  @endforeach
                </ul>
            </div>
            <div class="col-3 text-right"></div>
        </div>

        <!--Features -->
        <table class="table-bordered table-sm table-responsive" id="filter" width="100%" cellspacing="0">
            <thead bgcolor="#d0d0d0">
              <tr>
                <th width="1%"></th>
                <th width="24%">Feature</th>
                <th width="7%">Search value</th>
                <th width="5%">Unit</th>
                @php ($total=0)
                @foreach ($benches as $bench)
                    @php ($total++)
                    <th class="text-center" width="6%"><small>{{ $bench->title }}</small></th>
                @endforeach
                @while ($total<10)
                    @php ($total++)
                    <th class="text-center" width="6%"></th>
                @endwhile
              </tr>
            </thead>
            <tbody>
                @foreach($subcat->questions()->get() as $question)
                    @php ($showDetails = true)
                    @php ($question_title="")
                    @foreach($question->features()->get() as $item_feature)
                        @if ($item_feature->parameter==0) @continue @endif
                        <tr>
                            <td>
                                @if ($question->questiontype_id==1)
                                    <i class="fa fa-fw fa-cube"></i>
                                @endif
                                @if ($question->questiontype_id==2 && $loop->first)
                                    <i class="fa fa-fw fa-cubes"></i>
                                @endif
                            </td>
                            <td><small>{{ $item_feature->title }}</small>
                                @if ($item_feature->help)
                                    <br/><small class="text-success">(<span>{{ $item_feature->help }}</span>)</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @php ($value = $bench_features[$item_feature->id]->pivot->value)
                                {{  $value }}
                            </td>
                            <td><small>
                                    @if ($item_feature->responsetype_id == 3)
                                        {{ $item_feature->unit()->first()->title }}
                                    @else
                                        {{ $item_feature->responsetype()->first()->title }}
                                    @endif
                                </small>
                            </td>
                            @php ($num_bench=0)
                            @foreach ($benches as $bench)
                                @php ($num_bench++)
                                <td class="border-left p-1" bgcolor="#e8e8e8">
                                    @php ($bf=$bench->features()->wherePivot('feature_id',$item_feature->id)->first())
                                    @if (strlen($bf->pivot->value)>0)
                                    <table class="table-bordered table-sm small" width="100%" cellspacing="0">
                                        <tr @if ($bf) class='bg-light' @endif>
                                            <td class="col-6 text-center" id="value_{{$num_bench}}_{{$item_feature->id}}">
                                                @if ($item_feature->responsetype_id == 1)
                                                    <img src="{{asset('icons/ic_info.png')}}" width="14" title="{{ $bf->pivot->value }}"/>
                                                @else
                                                    {{ $bf->pivot->value }}
                                                @endif
                                            </td>
                                            <td class="col-6 text-center" id="percent_{{$num_bench}}_{{$item_feature->id}}">
                                                @if (strlen($value)>0)
                                                    @if ($item_feature->responsetype_id == 1)
                                                        @if (strpos($bf->pivot->value, $value) !== false)
                                                            <img src="{{asset('icons/ic_status_3.png')}}" width="14" title="{{$bf->pivot->value}}"/>
                                                        @else
                                                            <img src="{{asset('icons/ic_status_1.png')}}" width="14" title="{{$bf->pivot->value}}"/>
                                                        @endif
                                                    @endif
                                                    @if ($item_feature->responsetype_id == 2)
                                                        @if (strtolower($value)==strtolower($bf->pivot->value)) 
                                                            <img src="{{asset('icons/ic_status_3.png')}}" width="14"/>
                                                        @else
                                                            <img src="{{asset('icons/ic_status_1.png')}}" width="14"/>
                                                        @endif
                                                    @endif
                                                    @if ($item_feature->responsetype_id == 3)
                                                        @php ($percent = ($bf->pivot->value/$value)*100)
                                                        @if ($percent>=100)
                                                            @php ($class="text-success")
                                                        @endif
                                                        @if ($percent>=85 && $percent<100)
                                                            @php ($class="text-warning")
                                                        @endif
                                                        @if ($percent<85)
                                                            @php ($class="text-danger")
                                                        @endif
                                                        <span class="{{$class}}">{{number_format($percent,0)}}%</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
@section('js_custom')
<script type="text/javascript">
    function compareValue(feature_id,num_benches) {
        //Value to search:
        filter_value = $("#filter #feature_"+feature_id).val();
        //Value of the feature of each bench:
        for (bench=1;bench<=num_benches;bench++) {
            bench_value = $("#filter #value_"+bench+"_"+feature_id).text();
            percent = Math.round((filter_value/bench_value)*100);
            $("#filter #percent_"+bench+"_"+feature_id).text(percent+"%");
        }
    }
</script>
@endsection