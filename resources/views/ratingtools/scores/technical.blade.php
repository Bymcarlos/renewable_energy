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
    <li class="breadcrumb-item active">Technical scores</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%" class="small">Technical results presents obtained qualitative and quantitate scores for each requirement.
      <ul class="mb-0"><li><strong>NO GO.</strong>&nbsp;A primary requirement is not fulfilled. This message alerts that mitigation is not possible.</li>
        <li><strong>CAUTION.</strong>&nbsp;A secondary requirement that is not fulfilled. It would be necessary a further analysis by a Specialist to assess how this deviation might be mitigated</li></td>
      <td width="14%" class="text-right"><a href="{{route('scores.technical.excel',['rating'=>$rating->id])}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export to excel"></a></td>
    </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($techcats as $item_cat)
        @if ($techcat_id==0) @php ($techcat_id = $item_cat->id) @endif
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $techcat_id)
                @php ($class_active = "active")
            @endif
            <a id="techcat_{{$item_cat->id}}" class="nav-link {{ $class_active }}" href="#" onclick="showTechcat({{$item_cat->id}})">
            <img src="{{ asset('icons/ic_status_2.png') }}" width="14"/>
            &nbsp;{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
       
    </div>
</div>
<!-- List Features -->
@foreach ($techcats as $item_cat)
@if ($item_cat->id == $techcat_id) @php ($style="") @else @php ($style="display:none") @endif
<div id="techrequests_techcat_{{$item_cat->id}}" class="container-fluid" style="{{$style}}">
<div class="row badge-dark">
    <div class="col-3 small">REQUIREMENTS</div>
    <div class="col-1 text-center small">CRITICALITY</div>
    <div class="col-1 text-center small">ID - Factor</div>
    <div class="col-1 text-center small">PARAMS</div>
    <div class="col-6 text-center small">
        <div class="row">
        @foreach($benches as $bench_id => $bench_data)
            <div class="col text-center">{{$bench_data->title}}</div>
        @endforeach
        </div>
    </div>
</div>
@foreach($cat_scores[$item_cat->id]["benches_data"] as $techrequest_id => $request_data)
<div class="row">
    <div class="col-3 border small border p-1" title="{{$request_data['title']}}" @if ($request_data["feature_id"]<0) style="background-color:Moccasin" @endif>
        @if (strlen($request_data["title"])>40)
            {{substr($request_data["title"],0,40)}}...
        @else
            {{$request_data["title"]}}
        @endif
        &nbsp;({{$request_data["feature_id"]}})
    </div>
    <div class="col-1 text-center border">
        <div class="row">
            @foreach($criticalities as $criticality)
            <div class="col small p-0">
                @if ($request_data["criticality_id"]==$criticality->id) 
                    <img src='{{asset('icons/ic_criticality_'.$criticality->id.'.png')}}' width='14'/>
                @endif
            </div>
            @endforeach
            <div class="col-4 small text-secondary p-0"><small>{{$criticalities_totals[$request_data["criticality_id"]]->score}}</small></div>
        </div>
    </div>
    <div class="col-1 border small text-info text-center">
        <div class="row">
            <div class="col small">
                @if ($request_data["input_value_fail"])
                <i class="fa fa-exclamation-circle" style="color:red;" title="Input value required"></i>
                @else
                {{$request_data["input_value"]}}
                @endif
            </div>
            <div class="col small">{{$request_data["input_factor"]}}</div>
        </div>
    </div>
    <div class="col-1 border small text-info text-center">
    <!-- PARAMS: criteriafunc, value, range x-y -->
        <div class="row">
            <div class="col-3"><i class="fa fa-question-circle" title="{{$criteriafuncs[$request_data['criteriafunc_id']]->title}}"></i></div>
            <div class="col-3">
                @if ($criteriafuncs[$request_data['criteriafunc_id']]->askvalue)
                    <i class="fa fa-dot-circle-o" title="{{$request_data['value']}}"></i>
                @endif
            </div>
            <div class="col-3">
                @if ($criteriafuncs[$request_data['criteriafunc_id']]->askrange)
                    <i class="fa fa-columns" title="{{$request_data['range_x']}} - {{$request_data['range_y']}}"></i>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 text-center">
        <div class="row">
        @foreach($request_data["benches"] as $bench_id => $bench_data)
            @if (isset($bench_data["value"]))
                @php ($bg="lightgreen")
                @if ($bench_data["criteriafunc_result"]<1)
                    <!-- @php ($score="0") -->
                    @switch ($request_data["criticality_id"])
                        @case (1)
                            @php ($bg="salmon")
                            @break
                        @case (2)
                            @php ($bg="yellow")
                            @break
                    @endswitch
                @endif
                <div class="col text-center small border">
                    <div class="row">
                        <div class="col small text-center border m-1">{{$bench_data["value"]}}</div>
                        <div class="col small text-center border m-1" style="background-color: {{$bg}};">{{$bench_data["bench_score"]}}</div>
                    </div>
                </div>
            @else
                <div class="col text-center small">
                    <div class="row">
                        <div class="col small text-center"><i class="fa fa-exclamation-circle" style="color:red;" title="Bench value of the feature is null"></i></div>
                        <div class="col small text-center"></div>
                    </div>
                </div>
            @endif
        @endforeach
        </div>
    </div>
</div>
@endforeach
    <div class="row badge-light">
        <div class="col-3 border small border p-1 text-right"><small>CATEGORY SUMMARY:</small></div>
        <div class="col-1 text-center border">
            <div class="row">
                @foreach($criticalities as $criticality)
                <div class="col small p-0 small"><small>
                    @if (isset($techcats[$item_cat->id]->criticalities_totals[$criticality->id]))
                        {{$techcats[$item_cat->id]->criticalities_totals[$criticality->id]->total}}
                    @else
                        0
                    @endif
                </small></div>
                @endforeach
                <div class="col-4 small p-0">
                    <small>{{number_format($techcats[$item_cat->id]->score_total, 4, ',', '.')}}</small>
                </div>
            </div>
        </div>
        <div class="col-1 border"></div>
        <div class="col-1 border"></div>
        <div class="col-6 text-center border">
            <div class="row">
            @foreach($cat_scores[$item_cat->id]["benches_score"] as $bench_id => $data)
               <div class="col text-center small">
                   <div class="row">
                        @switch($cat_scores[$item_cat->id]["benches_result"][$bench_id])
                            @case (-1)
                                @php ($bg="salmon")
                                @php ($label = "NO GO")
                                @break;
                            @case (0)
                                @php ($bg="yellow")
                                @php ($label = "CAUTION")
                                @break;
                            @case (1)
                                @php ($bg="lightgreen")
                                @php ($label = "GO")
                                @break;
                        @endswitch
                        <div class="col small text-center m-1" style="background-color: {{$bg}};">{{$label}}</div>
                        <div class="col small text-center border m-1" style="background-color: {{$bg}};">{{$cat_scores[$item_cat->id]["benches_score"][$bench_id]}}</div>
                        <div class="col small text-center border m-1">
                            @if ($techcats[$item_cat->id]->score_total>0)
                                {{number_format($cat_scores[$item_cat->id]["benches_score"][$bench_id]*100/$techcats[$item_cat->id]->score_total, 0, ',', '.')}}&nbsp;%
                            @else
                                0%
                            @endif</div>
                    </div>
               </div>
            @endforeach
            </div>
        </div>
    </div>
    <div class="row badge-dark">
        <div class="col-3 border small border p-1 text-right">FINAL SCORE:</div>
        <div class="col-1 text-center border">
            <div class="row">
                @foreach($criticalities as $criticality)
                <div class="col small p-1 small">{{$criticalities_totals[$criticality->id]->total}}</div>
                @endforeach
                <div class="col-4 small p-1"></div>
            </div>
        </div>
        <div class="col-1 border"></div>
        <div class="col-1 border"></div>
        <div class="col-6 text-center border">
            <div class="row">
            @foreach($benches as $bench_id => $bench_data)
               <div class="col text-center small">
                   <div class="row">
                        @switch($bench_data->result)
                            @case (-1)
                                @php ($bg="salmon")
                                @php ($label = "NO GO")
                                @break;
                            @case (0)
                                @php ($bg="yellow")
                                @php ($label = "CAUTION")
                                @break;
                            @case (1)
                                @php ($bg="lightgreen")
                                @php ($label = "GO")
                                @break;
                        @endswitch
                        <div class="col small text-center m-1" style="background-color: {{$bg}};color: black;">{{$label}}</div>
                        <div class="col small text-center m-1" style="background-color: {{$bg}};color: black;">{{$bench_data->score}}</div>
                        <div class="col small text-center border m-1">{{number_format($bench_data->score*100, 0, ',', '.')}}&nbsp;%</div>
                    </div>
               </div>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('js_custom')
<script type="text/javascript">
    function showTechcat(techcat_id) {
        $('.nav-link').removeClass('active');
        $('#techcat_'+techcat_id).attr('class','nav-link active');

        $('div[id^="techrequests_techcat_"]').attr('style','display:none');
        $('#techrequests_techcat_'+techcat_id).removeAttr('style');
    }
</script>
@endsection