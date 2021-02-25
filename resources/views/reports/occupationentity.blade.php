@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports') }}">Reports</a></li>
    <li class="breadcrumb-item active">Occupation by entity</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>You can filter ... </small></td>
        	<td width="14%" class="text-right">
                @if (count($benches)>0)
                <a href="{{ route('benches.reports.occupationentity.export.excel',['entity'=>$entity_id,'year'=>$year]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o m-1" title="Export to Excel"></a>
                @endif   
            </td>
        </tr>
</table>
<div class="card-body">
    <p class="text-white bg-primary pl-2">SELECT ENTITY AND YEAR:</p>
    <form data-toggle="validator" action="{{ route('benches.reports.occupationentity.filter') }}" method="POST">
        {{ csrf_field() }}
        <table class="table-bordered table-sm" id="filter" width="100%" cellspacing="0">
            <thead class="bg-light">
                <tr>
                    <th>COMPONENT</th>
                    <th>YEAR</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%">
                        <select class="form-control" id="entity_id" name="entity_id">
                            @if ($entity_id>0) 
                            <option disabled value="0">Select entity</option>
                            @else
                            <option disabled selected value="0">Select entity</option>
                            @endif
                            @foreach ($entities as $entity)
                                @if ($entity->id == $entity_id)
                                    <option value="{{ $entity->id }}" selected>{{ $entity->title }}</option>
                                @else
                                    <option value="{{ $entity->id }}">{{ $entity->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </td>
                    <td width="20%">
                        <select class="form-control" id="year" name="year">
                            @if ($year>0) 
                            <option disabled value="0">Select year</option>
                            @else
                            <option disabled selected value="0">Select year</option>
                            @endif
                            @for ($i = 2018; $i<=(date("Y")+15); $i++)
                                @if ($i==$year)
                                <option value="{{ $i }}" selected>{{ $i }}</option>
                                @else
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endif
                            @endfor
                        </select>
                    </td>
                    <td width="60%" class="text-center">
                        <button type="submit" class="btn crud-submit btn-success">Apply filter</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    @if (count($benches)>0)
        <!-- Bench occupation by years -->
        <p class="text-white bg-primary pl-2 mt-4">BENCHES AND OCCUPATION:</p>
        <table class="table-condensed table-striped" width="100%">
          <tbody>
              <tr>
                @foreach ($weekstates as $weekstate)
                    @if ($weekstate->id>1)
                        <td><img src="{{ asset('icons/ic_occ_status_'.$weekstate->id.'.png') }}" width="14"/></td>
                        <td>{{ $weekstate->title }}</td>
                        <td></td>
                    @endif
                @endforeach
                    <td><img src="{{ asset('icons/ic_occ_status_req.png') }}" width="14"/></td>
                    <td>Weeks required</td>
                    <td></td>
              </tr>
          </tbody>
        </table>
        @foreach ($benches as $bench)
        <div class="border rounded p-1 mt-3" style="background-color:#fff9ec">
            <table class="table table-sm table-bordered" width="100%" cellspacing="0">
                <tbody>
                    <tr class="bg-dark text-white">
                        <td class="col-2 small" id="title_{{$bench->id}}">{{ $bench->title }}</td>
                        <td class="col-2 small">{{ $bench->entity()->first()->title }}</td>
                        <td class="col-2 small">
                            @php ($i=1)
                            @php ($component = $bench->areaComponent()->first()->component()->first())
                            @foreach ($component->areas()->get() as $area)
                                @if ($i>1) - @endif
                                {{ $area->title }}
                                @php ($i++)
                            @endforeach
                        </td>
                        <td class="col-4 small">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                        <td class="col-2 text-right">
                            <a href="{{ route('bench.reports.assessments.technical',['bench'=>$bench->id]) }}" class="btn-sm fa fa-tasks" title="Technical sheet"></a>
                <a href="{{ route('bench.reports.occupation',['bench'=>$bench->id]) }}" class="btn-sm fa fa-calendar" title="Occupation"></a>
                <a href="{{ route('bench.reports.assessments.economical',['bench'=>$bench->id]) }}" class="btn-sm fa fa-euro" title="Economic sheet"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table-condensed" width="100%">
                  <thead>
                      <tr class="table-bordered bg-white">
                        <th></th>
                        @for ($i = 1; $i <= 52; $i++)
                            @if ($i < 10)
                                <th class="small text-center">0{{ $i }}</th>
                            @else
                                <th class="small text-center">{{ $i }}</th>
                            @endif
                        @endfor
                      </tr>
                  </thead>
                  <tbody class="table-bordered">
                      @php ($occ_products = $bench->occupations()->where('year','=',$year)->get())
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
        @endforeach
    @endif
</div>
@endsection