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
    <li class="breadcrumb-item active">Criticality and test types</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%" valign="top"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="98%" class="small">Set involved test types:
            <br>-Pick a specific test type &nbsp;<img src="{{ asset('icons/ic_applicable_ask.png') }}" width="14"/>
            <br>-Select if this test type is applicable or not by clicking on &nbsp;<i class="btn btn-primary btn-sm fa fa-check-circle"></i>
            <br>-Define requirements criticality level in TBC
            </td>
        </tr>
</table>
<!--Categories -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($rating->techsheet()->first()->techcats()->get() as $item_cat)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_cat->id == $techcat->id)
                @php ($class_active = "active")
            @endif
            @php ($ratingtechchat = $item_cat->ratingtechcats()->where('rating_id','=',$rating->id)->first())
            <a class="nav-link {{ $class_active }}" href="{{ route('ratingtechrequests.index',['rating' => $rating->id,'techcat' => $item_cat->id]) }}">
            @if ($ratingtechchat->applicable_id==1)
                <img src="{{ asset('icons/ic_status_2.png') }}" width="14"/>
            @endif
            @if ($ratingtechchat->applicable_id==2)
                <img src="{{ asset('icons/ic_applicable_no.png') }}" width="14"/>
            @endif
            @if ($ratingtechchat->applicable_id==3)
                <img src="{{ asset('icons/ic_applicable_ask.png') }}" width="14"/>
            @endif
            &nbsp;{{ $item_cat->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
        @if ($techcat->applicable_id==3)
        <a href="#" id="button_apply" class="btn btn-primary btn-sm fa fa-check-circle" title="Change applicable state" data-toggle="modal" data-target="#ratingtechcat-applicable"></a>
        @endif
    </div>
</div>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
    <tr class="border">
      <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
      <td width="85%"><small>Technical requirements list:</small></td>
      <td width="14%" class="text-right"></td>
    </tr>
</table>
<!-- List Features -->
<table class="table-bordered table-sm table-hover" id="dt_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>REQUIREMENTS</th>
            <th>CRITICALITY</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($techcat->techrequests()->orderBy('ordering','asc')->get() as $techrequest)
            @php ($ratingtechrequest = $techrequest->ratingtechrequests()->where('rating_id','=',$rating->id)->first())
            @if ($techrequest->criticality()->first()->type==1)
                @php ($bg = "#FFFFFF")
            @else
                @if ($ratingtechrequest->criticality_id<=3)
                    @php ($bg = "#caffca")
                @else
                    @php ($bg = "#fbff96")
                @endif
            @endif
        <tr bgcolor="{{$bg}}">
            <td id="title_{{$techrequest->id}}"><small>{{$techrequest->title}}</small></td>
            @if ($techrequest->criticality()->first()->type==1)
                <td><small>{{$techrequest->criticality()->first()->title}}</small></td>
            @else
                <td><a href="#" data-toggle="modal" data-target="#ratingtechrequest-criticality" onclick="selectCriticality({{$techrequest->id}},{{$techrequest->ratingtechrequests()->where('rating_id','=',$rating->id)->first()->id}},{{$techrequest->criticality_id}})"><small>{{$ratingtechrequest->criticality()->first()->title}}</small></a></td>
            @endif
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Change ratingtechcat applicable state -->
<div class="modal fade" id="ratingtechcat-applicable" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Category applicable state</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title">{{ $techcat->title }}</h6>
            </div>
            <div class="modal-body">
                <form id="form_delete" data-toggle="validator" method="POST" action="{{route('ratingtechcat.applicable')}}">
                    <input type="hidden" name="ratingtechcat_id" value="{{$ratingtechcat->id}}"/>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <select name="applicable_id" class="form-control" required>
                            <option value="0" selected disabled>Select applicable</option>
                            @foreach($applicables as $applicable)
                            <option value="{{$applicable->id}}">{{$applicable->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Ok</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Change requirement criticality -->
<div class="modal fade" id="ratingtechrequest-criticality" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Select requirement criticality</h4>
            </div>
            <div class="modal-header">
                <h6 class="modal-title" id="request_title"></h6>
            </div>
            <div class="modal-body">
                <form id="form_criticality" data-toggle="validator" method="POST" action="{{route('ratingtechrequest.criticality')}}">
                    <input type="hidden" name="rating_id" value="{{$rating->id}}"/>
                    <input type="hidden" name="techcat_id" value="{{$techcat->id}}"/>
                    <input type="hidden" name="ratingtechrequest_id" id="ratingtechrequest_id"/>
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <select name="criticality_id" id="criticality_id" class="form-control" required>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn crud-submit btn-success">Ok</button>
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
function selectCriticality(techrequest_id,ratingtechrequest_id,techrequest_tbc) {
    //$("#ratingtechrequest-criticality #form_criticality").attr("action","{{ url('inputrequests') }}/"+techrequest_id);
    $("#ratingtechrequest-criticality #request_title").text($("#title_"+techrequest_id).text());
    $("#ratingtechrequest-criticality #ratingtechrequest_id").val(ratingtechrequest_id);
    $("#ratingtechrequest-criticality #criticality_id").empty();
    $("#ratingtechrequest-criticality #criticality_id").append("<option value='0' selected disabled>Select</option>");
    switch (techrequest_tbc) {
        case 4:
            $("#ratingtechrequest-criticality #criticality_id").append("<option value='1'>Primary</option>");
            $("#ratingtechrequest-criticality #criticality_id").append("<option value='2'>Secondary</option>");
            break;
        case 5:
            $("#ratingtechrequest-criticality #criticality_id").append("<option value='2'>Secondary</option>");
            $("#ratingtechrequest-criticality #criticality_id").append("<option value='3'>Tertiary</option>");
            break;
    }
}
$('#dt_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [0,1,2,3]
    },
    {
        "width": "40%", 
        "targets": 0
    },
    {
        "width": "15%", 
        "targets": 1
    },
    {
        "width": "15%", 
        "targets": 2
    },
    {
        "width": "45%", 
        "targets": 3
    }],
    "bSort" : false,
    "dom": 'rt<"bottom"p>',
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection