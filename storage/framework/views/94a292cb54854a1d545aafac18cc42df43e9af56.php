<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo e(url('home')); ?>"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('partners.scopes')); ?>">Partners</a></li>
    <li class="breadcrumb-item active">Select scope</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Partners are organized in scopes, select scope to list the partners</small></td>
          <td width="14%" class="text-right"></td>
        </tr>
</table>

<!-- List Scopes -->
<table class="table-bordered table-sm" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>SCOPE</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><a href="<?php echo e(route('partners.index',['scope_id'=>$scope->id])); ?>"><?php echo e($scope->title); ?></a></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.intranet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>