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
    <li class="breadcrumb-item active">Timing scores</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%" class="small">Timing results summary is presented in Final Score section.</td>
          <td width="14%" class="text-right"><a href="{{route('scores.timing.excel',['rating'=>$rating->id])}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export to excel"></a></td>
        </tr>
</table>
<div>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($cats as $item_cat)
        @if (!isset($cat_type)) @php ($cat_type = $item_cat->type) @endif
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->type == $cat_type)
                @php ($class_active = "active")
            @endif
            <a id="techcat_{{$item_cat->type}}" class="nav-link {{ $class_active }}" href="#" onclick="showTechcat({{$item_cat->type}})">
            {{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
       
    </div>
</div>
@foreach ($cats as $item_cat)
  @if ($item_cat->type == $cat_type) @php ($style="") @else @php ($style="display:none") @endif
  <div id="techrequests_techcat_{{$item_cat->type}}" class="container-fluid" style="{{$style}}">
    @switch($item_cat->type)
        @case(1)
            <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
                <thead bgcolor="#d0d0d0">
                    <tr>
                        <th class="col-4">PHASE</th>
                        @foreach($benches as $bench_id => $bench_title)
                          <th class="text-center small">{{$bench_title}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                      <td class="small">{{$availability["request"]}}</td>
                      @foreach($benches as $bench_id => $bench_title)
                        <td class="text-center small">{{$availability["benches"][$bench_id]["percent"]}}%</td>
                      @endforeach
                    </tr>
                </tbody>
            </table>
            @break

        @case(2)
            <!-- subcat: GENERAL: -->
            @for($SUBCAT_ID=1;$SUBCAT_ID<=count($executions);$SUBCAT_ID++)
            <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
              <thead bgcolor="#d0d0d0">
                  <tr>
                      <th class="col-2">{{strtoupper($executions[$SUBCAT_ID]["title"])}}</th>
                      @foreach($benches as $bench_id => $bench_title)
                        <th class="text-center"><small>
                        <!-- Set bold best bench, but only in TOTALS subcats (3 y 5) -->
                        @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) && $bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                        {{$bench_title}}
                        @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) &&$bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) </strong> @endif
                        </small></th>
                      @endforeach
                  </tr>
              </thead>
              <tbody>
                  @foreach($executions[$SUBCAT_ID]["requests"] as $request)
                  <tr>
                    <td class="col-2 text-center"><small>{{$request["title"]}}</small></td>
                    @foreach($benches as $bench_id => $bench_title)
                    @php ($value = $request["values"][$bench_id])
                    <td class="col-1 text-right"><small>
                    @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) && $bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                    {{number_format($value, 2, ',', '.')}}&nbsp;{{$request["unit"]}}
                    @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) && $bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) </strong> @endif
                    </small></td>
                    @endforeach
                  </tr>
                  @endforeach
                  <tr bgcolor="#e5e9f1">
                    <td class="col-2 text-right"><small>{{$executions[$SUBCAT_ID]["subtotals"]["title"]}}</small></td>
                    @foreach($benches as $bench_id => $bench_title)
                      <td class="col-1 text-right"><small>
                      @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) && $bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                      {{number_format($executions[$SUBCAT_ID]["subtotals"]["values"][$bench_id], 2, ',', '.')}}&nbsp;{{$executions[$SUBCAT_ID]["subtotals"]["unit"]}}
                      @if (($SUBCAT_ID==3 || $SUBCAT_ID==5) && $bench_id==$executions[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                      </small></td>
                    @endforeach
                  </tr>
              </tbody>
            </table>
            @endfor
            @break

        @case(3)
            <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
                <thead bgcolor="#d0d0d0">
                    <tr>
                        <th class="col-4">PHASE</th>
                        @foreach($benches as $bench_id => $bench_title)
                          <th class="text-center small">{{$bench_title}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                      <td class="small">{{$flexibility["request"]}}</td>
                      @foreach($benches as $bench_id => $bench_title)
                        <td class="text-center small">{{$flexibility["benches"][$bench_id]["percent"]}}%</td>
                      @endforeach
                    </tr>
                </tbody>
            </table>
            @break
        @case(4)
            <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
                <thead bgcolor="#d0d0d0">
                    <tr>
                        <th class="col-4">FINAL SCORE</th>
                        @foreach($benches as $bench_id => $bench_title)
                          <th class="text-center small">{{$bench_title}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for($i=1;$i<=3;$i++)
                    <tr>
                      <td class="small">{{$finalscore[$i]["request"]}}</td>
                      @foreach($benches as $bench_id => $bench_title)
                        <td class="text-center small">{{number_format($finalscore[$i]["benches"][$bench_id]["percent"], 0, ',', '.')}}%</td>
                      @endforeach
                    </tr>
                    @endfor
                </tbody>
            </table>
            @break
    @endswitch
  </div>
@endforeach

@endsection

@section('js_custom')
<script type="text/javascript">
    function showTechcat(cat_type) {
        $('.nav-link').removeClass('active');
        $('#techcat_'+cat_type).attr('class','nav-link active');

        $('div[id^="techrequests_techcat_"]').attr('style','display:none');
        $('#techrequests_techcat_'+cat_type).removeAttr('style');
    }
</script>
@endsection