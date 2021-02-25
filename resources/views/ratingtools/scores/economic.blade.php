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
    <li class="breadcrumb-item active">Economic rating scores</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%" class="small">Economic rating results summary is presented in Final Score section</td>
          <td width="14%" class="text-right"><a href="{{route('scores.economics.excel',['rating'=>$rating->id])}}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export to excel"></a></td>
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
        @case(1) <!-- BUSINESS -->
          @for($SUBCAT_ID=1;$SUBCAT_ID<=count($business);$SUBCAT_ID++)
          <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
            <thead bgcolor="#d0d0d0">
                <tr>
                    <th class="col-2">{{strtoupper($business[$SUBCAT_ID]["title"])}}</th>
                    @foreach($benches as $bench_id => $bench_title)
                      <th class="text-center"><small>
                        @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                          {{$bench_title}}
                        @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) </strong> @endif
                      </small></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($business[$SUBCAT_ID]["requests"] as $request)
                <tr>
                  <td class="col-2 text-center"><small>{{$request["title"]}}</small></td>
                  @foreach($benches as $bench_id => $bench_title)
                  @php ($value = $request["values"][$bench_id])
                  @if (isset($request["states"]))
                    @php ($state = $request["states"][$bench_id])
                    <td class="col-1 text-right" bgcolor="{{$statecolors[$state]}}"><small>
                  @else
                    <td class="col-1 text-right"><small>
                  @endif
                    @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                    {{number_format($value, 0, ',', '.')}}&nbsp;{{$request["unit"]}}
                    @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) </strong> @endif
                  </small></td>
                  @endforeach
                </tr>
                @endforeach
                @if ($business[$SUBCAT_ID]["subtotals"]["visibility"])
                <tr bgcolor="#e5e9f1">
                  <td class="col-2 text-right"><small>{{$business[$SUBCAT_ID]["subtotals"]["title"]}}</small></td>
                  @foreach($benches as $bench_id => $bench_title)
                    <td class="col-1 text-right"><small>
                      @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) <strong> @endif
                          {{number_format($business[$SUBCAT_ID]["subtotals"]["values"][$bench_id], 0, ',', '.')}}&nbsp;{{$business[$SUBCAT_ID]["subtotals"]["unit"]}}
                      @if (($SUBCAT_ID==3 || $SUBCAT_ID==5 || $SUBCAT_ID==7) && $bench_id==$business[$SUBCAT_ID]["subtotals"]["best_bench"]) </strong> @endif
                    </small></td>
                  @endforeach
                </tr>
                @endif
            </tbody>
          </table>
          @endfor
          @break

        @case(2) <!-- ALTERNATIVE -->
          @for($SUBCAT_ID=1;$SUBCAT_ID<=count($alternative);$SUBCAT_ID++)
          <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
            <thead bgcolor="#d0d0d0">
                <tr>
                    <th class="col-2">{{strtoupper($alternative[$SUBCAT_ID]["title"])}}</th>
                    @foreach($benches as $bench_id => $bench_title)
                      <th class="text-center small">{{$bench_title}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($alternative[$SUBCAT_ID]["requests"] as $request)
                <tr>
                  <td class="col-2 text-center small">{{$request["title"]}}</td>
                  @foreach($benches as $bench_id => $bench_title)
                  @php ($value = $request["values"][$bench_id])
                  @if (isset($request["states"]))
                    @php ($state = $request["states"][$bench_id])
                    <td class="col-1 text-right" bgcolor="{{$statecolors[$state]}}"><small>
                  @else
                    <td class="col-1 text-right"><small>
                  @endif
                    {{number_format($value, 1, ',', '.')}}&nbsp;{{$request["unit"]}}
                  </small></td>
                  @endforeach
                </tr>
                @endforeach
                @if (isset($alternative[$SUBCAT_ID]["subtotals"]))
                <tr>
                  <td class="col-2 text-right"><small>{{$alternative[$SUBCAT_ID]["subtotals"]["title"]}}</small></td>
                  @foreach($benches as $bench_id => $bench_title)
                    <td class="col-1 text-right"><small>
                          {{number_format($alternative[$SUBCAT_ID]["subtotals"]["weights"][$bench_id], 1, ',', '.')}}
                    </small></td>
                  @endforeach
                </tr>
                <tr bgcolor="#e5e9f1">
                  <td class="col-2 text-right"><small>{{$alternative[$SUBCAT_ID]["score"]["title"]}}</small></td>
                  @foreach($benches as $bench_id => $bench_title)
                    <td class="col-1 text-right"><small>
                          {{$alternative[$SUBCAT_ID]["score"]["benches"][$bench_id]}}
                    </small></td>
                  @endforeach
                </tr>
                @endif
            </tbody>
          </table>
          @endfor
          @break
        @case(3)
            @for($SUBCAT_ID=1;$SUBCAT_ID<=count($finalscore);$SUBCAT_ID++)
            <table class="table-bordered table-sm mb-3" cellspacing="0" width="100%">
              <thead bgcolor="#d0d0d0">
                  <tr>
                      <th class="col-2">{{strtoupper($finalscore[$SUBCAT_ID]["title"])}}</th>
                      @foreach($benches as $bench_id => $bench_title)
                        <th class="text-center small">{{$bench_title}}</th>
                      @endforeach
                  </tr>
              </thead>
              <tbody>
                  @foreach($finalscore[$SUBCAT_ID]["requests"] as $request)
                  <tr>
                    <td class="col-2 text-center small">{{$request["title"]}}</td>
                    @foreach($benches as $bench_id => $bench_title)
                    @php ($value = $request["values"][$bench_id])
                    <td class="col-1 text-right"><small>
                      {{number_format($value, 1, ',', '.')}}&nbsp;{{$request["unit"]}}
                    </small></td>
                    @endforeach
                  </tr>
                  @endforeach
              </tbody>
            </table>
            @endfor
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