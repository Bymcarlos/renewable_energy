@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('benches.index') }}">Benches</a></li>
    <li class="breadcrumb-item">{{ $bench->title }}</li>
    <li class="breadcrumb-item active">Occupation</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Occupation is the ... You can ....</small></td>
        	<td width="14%" class="text-right"></td>
        </tr>
</table>
<div class="card-body">
    <p class="text-white bg-primary pl-2">BENCH DATA (Name-Entity-Area-Component):</p>
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td class="col-4"><img src="{{ asset('icons/ic_status_'.$bench->status.'.png') }}"/>&nbsp;&nbsp;{{ $bench->title }}</td>
                    <td class="col-2">{{ $bench->entity()->first()->title }}</td>
                    <td class="col-2">{{ $bench->areaComponent()->first()->area()->first()->title }}</td>
                    <td class="col-3">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                    <td class="col-1 text-right">
                        <a href="{{ route('occupation.bench.export.excel',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export to Excel" data-title="Export to Excel"></a>
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
    <table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
            <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
            <td width="85%"><small>At 'Others' choosable options are: Not avaible and in negotiations with others customer. At SGRE choosable options are: Contracted, Prereserved, In negotiation with SGRE.</small></td>
            <td width="14%" class="text-right"></td>
        </tr>
    </table>
    <div class="table-responsive mt-2">
        <table class="table-condensed table-striped" width="100%">
              <thead>
                  <tr>
                    <th></th>
                        <th colspan="52">
                            <ul class="nav nav-tabs d-inline-flex">
                              @foreach ($occyears as $occyear)
                                  @php ($last_year = $occyear->year)
                                  <li class="nav-item">
                                    @php ($active="")
                                    @if ($occyear->year == $current_year)
                                      @php ($active="active")
                                    @endif
                                    <a class="nav-link {{ $active }}" href="{{ url('occupation') }}/{{ $bench->id }}/{{ $occyear->year }}">{{ $occyear->year }}&nbsp; weeks</a>
                                  </li>
                              @endforeach
                            </ul>
                            <a href="#" class="fa fa-plus-square m-1" data-toggle="modal" data-target="#year-add-item" title="Add next year"></a>
                            @if (count($occyears)>1 && $current_year==$last_year)
                                <a href="#" class="fa fa-trash m-1" data-title="Del" data-toggle="modal" data-target="#year-del-item" title="Delete this year"></a>
                            @endif
                        </th>
                  </tr>
                  <tr class="table-bordered">
                    <th class="small">PRODs<a href="#" class="fa fa-plus-square float-right m-1" data-toggle="modal" data-target="#product-add" title="Add product"></a></th>
                    @for ($i = 1; $i <= 52; $i++)
                        @if ($i < 10)
                            <th class="small text-center">0{{ $i }}</th>
                        @else
                            <th class="small text-center">{{ $i }}</th>
                        @endif
                    @endfor
                    <th></th>
                  </tr>
              </thead>
              <tbody class="table-bordered">
                  @php ($occ_products = $bench->occupations()->where('year','=',$current_year)->get())
                  @php ($occ_products_id = array())
                  @foreach($occ_products as $occ_product)
                      @php ($occ_products_id[] = $occ_product->product_id)
                      <tr @if ($loop->first) 
                        class="bg-secondary"
                        @endif
                        >
                        <td class="p-1 @if ($loop->first) text-white @endif" id="product_{{ $occ_product->id }}"><small>
                            {{ $occ_product->product()->first()->platform()->first()->title }}:<br/>
                            {{ $occ_product->product()->first()->title }}</small>
                            
                        </td>
                        @foreach($occ_product->occupationweeks()->get() as $week_product)
                            <td style="width: 1.7%; cursor: pointer;" class="text-center" data-toggle="modal" data-target="#occ-edit" onclick="editOccupation({{$current_year}},'{{ $occ_product->product()->first()->platform()->first()->title }}','{{ $occ_product->product()->first()->title }}',{{$week_product->week}},{{$occ_product->id}})">
                                @if ($week_product->weekstate_id>1)
                                    <img src="{{ asset('icons/ic_occ_status_'.$week_product->weekstate_id.'.png') }}" width="14"/>
                                @endif
                            </td>
                        @endforeach
                        <td style="width: 1%;">
                            @if ($occ_product->product_id>1)
                            <a href="#" class="fa fa-trash-o" data-toggle="modal" data-target="#occ-del" title="Delete product occupation" onclick="deleteOccupation('{{ $occ_product->product()->first()->platform()->first()->title }}','{{ $occ_product->product()->first()->title }}',{{$occ_product->id}})"></a>
                            @endif
                        </td>
                      </tr>
                  @endforeach
              </tbody>
        </table>    
    </div>

</div>
<!-- Add Year Modal -->
<div class="modal fade" id="year-add-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add new year: {{ $last_year+1 }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="{{ route('occupation.year.add') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Year Modal -->
<div class="modal fade" id="year-del-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete year</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="customer">Confirm delete year {{ $current_year }} and all customers occupations data?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST" action="{{ route('occupation.year.delete') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="year" name="year" value="{{ $current_year }}"/>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Create Product Modal -->
<div class="modal fade" id="product-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add new product occupation in {{ $current_year }}</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" action="{{ route('occupation.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="year" name="year" value="{{ $current_year }}"/>
                    <input type="hidden" id="product_opt" name="product_opt" value="1"/>
                    <div class="form-group row" id="select_product">
                        <div class="col-sm-4 form-check">
                            <label class="control-label" for="customer_id">Select product:</label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control" id="product_id" name="product_id" required>
                                <option disabled selected value="0">Select product</option>
                                @foreach ($platforms as $platform)
                                    <optgroup label="{{ $platform->title }}">
                                        @foreach ($platform->products()->get() as $product)
                                            @if (!in_array($product->id,$occ_products_id))
                                            <option value="{{ $product->id }}"><small>{{ $product->title }}</small></option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12"><small>(if not exist, <a href="#" onclick="addProduct(true)">create a new product</a>)</small></div>
                    </div>
                    
                    <div class="form-group" id="new_product" style="display: none;">
                        <div class="row">
                            <div class="col-sm-4 form-check">
                                <label class="control-label" for="platform_id">Platform:</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" id="platform_id" name="platform_id">
                                <option disabled selected value="0">Select platform</option>
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}"><small>{{ $platform->title }}</small></option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-4 form-check">
                                <label class="control-label" for="customer_id">New product:</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product name" data-error="Please enter valid name." />
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12"><small>(<a href="#" onclick="addProduct(false)">select an existing product</a>)</small></div>
                        </div>
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
<!-- Occupation -->
<div class="modal fade" id="occ-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Week State</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="occ_year"></h5>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="occ_product"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_occ_edit" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT" />
                    <div class="form-group row">
                        <div class="col-3">
                            <label class="control-label" for="occ_week_from">Week From:</label>
                        </div>
                        <div class="col-3">
                            <label class="control-label" for="occ_week_to">Week To:</label>
                        </div>
                        <div class="col-6">
                            <label class="control-label" for="occ_week_state">State:</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-3">
                            <select class="form-control" id="occ_week_from" name="occ_week_from" onchange="loadWeekTo(this.value)" required>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="form-control" id="occ_week_to" name="occ_week_to" required>
                            </select>
                        </div>
                        <div class="col-6">
                            <select class="form-control" id="occ_week_state" name="occ_week_state" required>
                                @foreach ($weekstates as $weekstate)
                                    <option value="{{$weekstate->id}}">{{$weekstate->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div><small>At 'Others' choosable options are: Not avaible and in negotiations with others customer. At SGRE choosable options are: Contracted, Prereserved, In negotiation with SGRE.</small></div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Delete Occupation -->
<div class="modal fade" id="occ-del" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Delete product occupation in {{ $current_year }}:</h4>
            </div>
            <div class="modal-body">
                <h5 class="modal-content" id="occ_prod"></h5>
            </div>
            <div class="modal-body">
                <h5 class="modal-content">Confirm delete product occupation?</h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    <input type="hidden" id="year" name="year" value="{{ $current_year }}"/>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-danger">Delete</button>
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
    function addProduct(add_new) {
        $("#product-add #platform_id").val(0);
        $("#product-add #product_id").val(0);
        $("#product-add #platform_id").removeAttr("required");
        $("#product-add #product_name").removeAttr("required");
        $("#product-add #product_id").removeAttr("required");
        if (add_new) {
            $("#product-add #select_product").attr("style","display: none;");
            $("#product-add #new_product").removeAttr("style");
            $("#product-add #platform_id").attr("required","required");
            $("#product-add #product_name").val("");
            $("#product-add #product_name").attr("required","required");
            $("#product-add #product_opt").val(2);
        } else {
            $("#product-add #select_product").removeAttr("style");
            $("#product-add #new_product").attr("style","display: none;");
            $("#product-add #select_product").removeAttr("style");
            $("#product-add #new_customer").attr("style","display: none;");
            $("#product-add #product_id").attr("required","required");
            $("#product-add #product_opt").val(1);
        }
    }
    function editOccupation(year,platform,product,week,occ_id) {
        var route = "{{ route('occupation.update',['occupation'=>':id']) }}".replace(':id', occ_id);
        $("#occ-edit #form_occ_edit").attr("action",route);
        $("#occ-edit #occ_year").text("Year: "+year);
        $("#occ-edit #occ_product").text(platform+": "+product);
        $("#occ-edit #occ_week_from").empty();
        $("#occ-edit #occ_week_to").empty();
        $("#occ-edit #occ_week_state").val(1);
        for(w=week;w<=52;w++) {
            $("#occ-edit #occ_week_from").append('<option value="'+w+'">'+w+'</option>');
            $("#occ-edit #occ_week_to").append('<option value="'+w+'">'+w+'</option>');
        }
    }
    function deleteOccupation(platform,product,occ_id) {
        var route = "{{ route('occupation.destroy',['occupation'=>':id']) }}".replace(':id', occ_id);
        $("#occ-del #form_delete").attr("action",route);
        $("#occ-del #occ_prod").text(platform+": "+product);
    }
    function loadWeekTo(week) {
        $("#occ-edit #occ_week_to").empty();
        for(w=week;w<=52;w++) {
            $("#occ-edit #occ_week_to").append('<option value="'+w+'">'+w+'</option>');
        }
    }
</script>
@endsection