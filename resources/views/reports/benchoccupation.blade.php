@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('benches.reports.index') }}">Benches</a></li>
    <li class="breadcrumb-item">{{ $bench->title }}</li>
    <li class="breadcrumb-item active">Occupation</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Occupation is the ... </small></td>
        	<td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="card-body">
    <p class="text-white bg-primary pl-2">BENCH DATA (Name-Entity-Area-Component):</p>
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td class="col-2"><img src="{{ asset('icons/ic_status_'.$bench->status.'.png') }}"/>&nbsp;&nbsp;{{ $bench->title }}</td>
                    <td class="col-2">{{ $bench->entity()->first()->title }}</td>
                    <td class="col-2">{{ $bench->areaComponent()->first()->area()->first()->title }}</td>
                    <td class="col-4">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                    <td class="col-2 text-right">
                        <a href="#" class="btn btn-primary btn-sm fa fa-file-pdf-o" title="Export to pdf" data-title="Export to pdf"></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Bench occupation by years -->
    <p class="text-white bg-primary pl-2">BENCH OCCUPATION:</p>
    <table class="table-condensed table-striped" width="100%">
      <thead>
          <tr>
            <th>
                
            </th>
          </tr>
      </thead>
      <tbody>
          <tr>
            @foreach ($weekstates as $weekstate)
                @if ($weekstate->id>1)
                    <td><img src="{{ asset('icons/ic_occ_status_'.$weekstate->id.'.png') }}" width="14"/></td>
                    <td>{{ $weekstate->title }}</td>
                    <td></td>
                @endif
            @endforeach
          </tr>
      </tbody>
    </table>
    <div class="table-responsive mt-2">
        <table class="table-condensed table-striped" width="100%">
              <thead>
                  <tr>
                    <th>
                        
                    </th>
                        <th colspan="52">
                            <ul class="nav nav-tabs d-inline-flex">
                              @foreach ($occyears as $occyear)
                                  @php ($last_year = $occyear->year)
                                  <li class="nav-item">
                                    @php ($active="")
                                    @if ($occyear->year == $current_year)
                                      @php ($active="active")
                                    @endif
                                    <a class="nav-link {{ $active }}" href="{{ url('rep_bench_occupation') }}/{{ $bench->id }}/{{ $occyear->year }}">{{ $occyear->year }}&nbsp; weeks</a>
                                  </li>
                              @endforeach
                            </ul>
                        </th>
                  </tr>
                  <tr class="table-bordered">
                    <th class="small">PRODUCTS</th>
                    @for ($i = 1; $i <= 52; $i++)
                        @if ($i < 10)
                            <th class="small">0{{ $i }}</th>
                        @else
                            <th class="small">{{ $i }}</th>
                        @endif
                    @endfor
                  </tr>
              </thead>
              <tbody class="table-bordered">
                  @php ($occ_products = $bench->occupations()->where('year','=',$current_year)->get())
                  @foreach($occ_products as $occ_product)
                  <tr>
                    <td class="p-1" id="product_{{ $occ_product->id }}"><small>
                        {{ $occ_product->product()->first()->platform()->first()->title }}:<br/>
                        {{ $occ_product->product()->first()->title }}</small>&nbsp;&nbsp;
                    </td>
                    @foreach($occ_product->occupationweeks()->get() as $week_product)
                        <td style="width: 1.8%" class="text-center">
                            @if ($week_product->weekstate_id>1)
                                <img src="{{ asset('icons/ic_occ_status_'.$week_product->weekstate_id.'.png') }}" width="14"/>
                            @endif
                        </td>
                    @endforeach
                  </tr>
                  @endforeach
              </tbody>
        </table>    
    </div>

</div>
@endsection