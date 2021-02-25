@extends('layouts.intranet')
@section('content')
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ url('home')}}"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item active"><a href="{{ route('benches.index') }}">Benches</a></li>
    <li class="breadcrumb-item active">{{ $bench->title }}</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Benches are the ... You can acces to the technical sheet, economical sheet and occupation.</small></td>
        	<td width="14%" class="text-right"></td>
        </tr>
</table>
{{-- Get the right Technical Sheet id (from Tech Assessment / Technical capabilities not required group) for this bench (It depends on the component) --}}
@php ($tech_cap = $bench->areaComponent()->first()->component()->first()->sheet_id)
<div class="card-body">
    <p class="text-white bg-primary pl-2">BENCH DATA (Name-Entity-Area-Component):</p>
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <tbody>
                <tr>
                    <td width="20%"><img src="{{ asset('icons/ic_status_'.$bench->status.'.png') }}"/>&nbsp;&nbsp;{{ $bench->title }}</td>
                    <td width="10%">{{ $bench->entity()->first()->title }}</td>
                    <td width="10%">{{ $bench->areaComponent()->first()->area()->first()->title }}</td>
                    <td width="25%">{{ $bench->areaComponent()->first()->component()->first()->title }}</td>
                    <td width="20%"></td>
                    <td width="15%" class="text-right">
                        @if ($ass_type->key=="tech")
                        <a href="{{ route('bench.assessments.technical.export.excel',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o ml-1" title="Export to Excel"></a>
                        @endif
                        @if ($ass_type->key=="economic")
                        <a href="{{ route('bench.assessments.economical.export.excel',['bench'=>$bench->id]) }}" class="btn btn-primary btn-sm fa fa-file-excel-o ml-1" title="Export to Excel"></a>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Assessment list -->
    <p class="text-white bg-primary pl-2">ASSESSMENTS:</p>
    @foreach($assessments as $assessment)
    <div class="table-responsive pl-3">
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="col">{{ $assessment->title }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                @foreach($assessment->sheets()->get() as $sheet)
                                    @if ($sheet->required == 1 || $sheet->id == $tech_cap)
                                    <tr>
                                        <td class="col"><img src="{{ asset('icons/ic_status_'.$bench->sheets()->where('sheet_id',$sheet->id)->first()->pivot->status.'.png') }}"/>&nbsp;&nbsp;
                                            <a href="{{ route('benchfeatures.index',['bench'=>$bench->id,'sheet'=>$sheet->id]) }}">{{ $sheet->title }}</a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endforeach
</div>
<!-- Modal import bench -->
<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Import bench features</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_edit" method="POST" enctype="multipart/form-data" action="{{ route('bench.import',['bench'=>$bench->id])}}">
                    <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="control-label" for="value">Select file to import:</label>
                        <input type="file" id="import_file" name="import_file" class="form-control" data-error="Select excel file"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Import</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal bench imported -->
<div class="modal fade" id="modal-imported" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Import bench features</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bench_id" name="bench_id" value="{{ $bench->id }}"/>
                {{ csrf_field() }}
                <div class="form-group">
                    <label class="control-label" for="value">Bench features has been imported successfully</label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn crud-submit btn-success">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js_custom')
@if ($imported)
<script type="text/javascript">
    alert("Bench features has been imported successfully");
</script>
@endif
@endsection