@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('partners.scopes') }}">Partners scopes</a></li>
    <li class="breadcrumb-item"><a href="{{ route('partners.index',['scope_id'=>$partner->scope_id]) }}">{{$partner->title}}</a></li>
    <li class="breadcrumb-item active">{{$sheet->title}}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>{{$sheet->title}} sections</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>
<!--Sections -->
<div class="row mb-1">
    <div class="col-10">
        <ul class="nav nav-tabs d-inline-flex">
        @foreach ($sheet->sections()->get() as $item_section)
          <li class="nav-item">
            @php ($class_active = "")
            @if ($item_section->id == $section->id)
                @php ($class_active = "active")
            @endif
            <a class="nav-link {{ $class_active }}" href="{{ route('partner.sheet',['partner_id' => $partner->id,'sheet_id' => $sheet->id,'section_id' => $item_section->id]) }}">{{ $item_section->title }}</a>
          </li>
        @endforeach
        </ul>
    </div>
    <div class="col-2 text-right">
    </div>
</div>
<!-- List Requests -->
<table class="table-bordered table-sm table-hover" id="dtRT_list" width="100%" cellspacing="0">
    <thead bgcolor="#d0d0d0">
        <tr>
            <th>REQUEST</th>
            <th>VALUE</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($section->generalrequests()->get() as $generalrequest)
        @php ($value = $partner_generalrequests[$generalrequest->id]->value)
        <tr class="bg-light" style="cursor: pointer;" data-toggle="modal" data-target="#edit-value" onclick="editValue('{{$generalrequest->id}}','{{$value}}')">
            <td class="small" id="title_{{$generalrequest->id}}">{{$generalrequest->title}}
                @if ($generalrequest->help)
                    <br/><small class="text-success">({{$generalrequest->help}})</small>
                @endif
            </td>
            <td class="small">{{$value}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Edit Value -->
<div class="modal fade" id="edit-value" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Set request value:</h4>
            </div>
            <div class="modal-body">
                <form id="form_value" data-toggle="validator" method="post" action="{{route('partner.request')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="partner_id" value="{{$partner->id}}">
                    <input type="hidden" name="section_id" value="{{$section->id}}">
                    <input type="hidden" name="sheet_id" value="{{$sheet->id}}">
                    <input type="hidden" name="generalrequest_id" id="generalrequest_id">
                    <div class="form-group">
                        <label class="control-label" id="title"></label>
                        <small class="text-info"><span id="help"></span></small>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="value" name="value"/>
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
@endsection
@section('js_custom')
<script type="text/javascript">
function editValue(generalrequest_id,value) {
    $("#edit-value #form_value #title").text($("#title_"+generalrequest_id).text());
    $("#edit-value #form_value #value").val(value);
    $("#edit-value #form_value #generalrequest_id").val(generalrequest_id);
};
$('#dtRT_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [1]
    },
    {
        "width": "50%", 
        "targets": 0
    },
    {
        "width": "50%", 
        "targets": 1
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
@endsection