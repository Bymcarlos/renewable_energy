<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo e(url('home')); ?>"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('ratingtools')); ?>">Rating Tools</a></li>
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Areas</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="99%" class="small">Reports are sorted by area. Select an area to list related reports.</td>
        </tr>
</table>

<!-- List Areas -->
<table class="table-bordered table-sm" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>AREA</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr id="area_<?php echo e($area->id); ?>">
            <td><a href="<?php echo e(route('ratingfiles.index',['area_id'=>$area->id])); ?>"><?php echo e($area->title); ?></a></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.intranet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>