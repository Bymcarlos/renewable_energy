<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo e(url('home')); ?>"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('reports')); ?>">Reports</a></li>
    <li class="breadcrumb-item active">Benches</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>Benches are the ... You can acces to the technical sheet, economical sheet and occupation.</small></td>
        	<td width="14%" class="text-right">
            <?php if(count($benches)>0): ?>
                <a href="<?php echo e(route('benches.export.excel')); ?>" class="btn btn-primary btn-sm fa fa-file-excel-o" title="Export bench" data-title="Export to excel"></a>
            <?php endif; ?>
            </td>
        </tr>
</table>

<!-- List Benches -->
<table class="table-bordered table-sm" id="dt_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>BENCH</th>
            <th>ENTITY</th>
            <th>CTR</th>
            <th>AREA</th>
            <th>COMPONENT</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $benches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bench): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($component = $bench->areaComponent()->first()->component()->first()); ?>
        <tr>
            <td class="small" id="bench_<?php echo e($bench->id); ?>" data-title="<?php echo e($bench->title); ?>" title="<?php echo e($bench->comments); ?>"><?php echo e($bench->title); ?></td>
            <td class="small"><?php echo e($bench->entity()->first()->title); ?></td>
            <td class="text-center"><img src="<?php echo e(asset('icons/flags/flag_'.strtolower($bench->country()->first()->code).'.png')); ?>" title="<?php echo e($bench->country()->first()->title); ?>" width="24"/></td>
            <td class="small">
                <?php $__currentLoopData = $component->areas()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($loop->first ? '' : '-'); ?>

                    <?php echo e($area->title); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td class="small"><?php echo e($bench->areaComponent()->first()->component()->first()->title); ?></td>
            <td class="text-right">
                <a href="<?php echo e(route('bench.reports.assessments.technical',['bench'=>$bench->id])); ?>" class="btn btn-primary btn-sm fa fa-tasks" title="Technical sheet"></a>
                <?php if(Auth::user()->hasRole('admin')): ?>
                <a href="<?php echo e(route('bench.reports.occupation',['bench'=>$bench->id])); ?>" class="btn btn-primary btn-sm fa fa-calendar" title="Occupation"></a>
                <a href="<?php echo e(route('bench.reports.assessments.economical',['bench'=>$bench->id])); ?>" class="btn btn-primary btn-sm fa fa-euro" title="Economic sheet"></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js_custom'); ?>
<script type="text/javascript">
$('#dt_list').DataTable({
    "columnDefs": [{
        "searchable": false,
        "orderable": false,
        "targets": [5]
    },
    {
        "width": "20%", 
        "targets": 0
    },
    {
        "width": "15%", 
        "targets": 1
    },
    {
        "width": "5%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    },
    {
        "width": "30%", 
        "targets": 4
    },
    {
        "width": "15%", 
        "targets": 5
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [[ 0, 'asc' ]],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.intranet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>