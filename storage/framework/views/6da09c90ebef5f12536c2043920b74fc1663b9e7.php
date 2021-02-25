<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo e(url('home')); ?>"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('reports')); ?>">Reports</a></li>
    <li class="breadcrumb-item active">Occupation by component</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
        	<td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
        	<td width="85%"><small>You can filter ... </small></td>
        	<td width="14%" class="text-right">
                <?php if(count($benches)>0): ?>
                <a href="<?php echo e(route('benches.reports.occupationcomponent.export.excel',['component'=>$component_id,'year'=>$year,'week_from'=>$week_from,'week_to'=>$week_to])); ?>" class="btn btn-primary btn-sm fa fa-file-excel-o m-1" title="Export to Excel"></a>
                <?php endif; ?>
            </td>
        </tr>
</table>
<div class="card-body">
    <p class="text-white bg-primary pl-2">SELECT COMPONENT AND DATE RANGE:</p>
    <form data-toggle="validator" action="<?php echo e(route('benches.reports.occupationcomponent.filter')); ?>" method="POST">
        <?php echo e(csrf_field()); ?>

        <table class="table-bordered table-sm" id="filter" width="100%" cellspacing="0">
            <thead class="bg-light">
                <tr>
                    <th>COMPONENT</th>
                    <th>YEAR</th>
                    <th>WEEK FROM</th>
                    <th>WEEK TO</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="20%">
                        <select class="form-control" id="component_id" name="component_id">
                            <?php if($component_id>0): ?> 
                            <option disabled value="0">Select component</option>
                            <?php else: ?>
                            <option disabled selected value="0">Select component</option>
                            <?php endif; ?>
                            <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($component->id == $component_id): ?>
                                    <option value="<?php echo e($component->id); ?>" selected><?php echo e($component->title); ?></option>
                                <?php else: ?>
                                    <option value="<?php echo e($component->id); ?>"><?php echo e($component->title); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </td>
                    <td width="20%">
                        <select class="form-control" id="year" name="year">
                            <?php if($year>0): ?> 
                            <option disabled value="0">Select year</option>
                            <?php else: ?>
                            <option disabled selected value="0">Select year</option>
                            <?php endif; ?>
                            <?php for($i = 2018; $i<=(date("Y")+15); $i++): ?>
                                <?php if($i==$year): ?>
                                <option value="<?php echo e($i); ?>" selected><?php echo e($i); ?></option>
                                <?php else: ?>
                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td width="20%">
                        <select class="form-control" id="week_from" name="week_from">
                            <?php if($week_from>0): ?> 
                            <option disabled value="0">Select week from</option>
                            <?php else: ?>
                            <option disabled selected value="0">Select week from</option>
                            <?php endif; ?>
                            <?php for($i = 1; $i <= 52; $i++): ?>
                                <?php if($i==$week_from): ?>
                                <option value="<?php echo e($i); ?>" selected>Week <?php echo e($i); ?></option>
                                <?php else: ?>
                                <option value="<?php echo e($i); ?>">Week <?php echo e($i); ?></option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td width="20%">
                        <select class="form-control" id="week_to" name="week_to">
                            <?php if($week_to>0): ?> 
                            <option disabled value="0">Select week to</option>
                            <?php else: ?>
                            <option disabled selected value="0">Select week to</option>
                            <?php endif; ?>
                            <?php for($i = 1; $i <= 52; $i++): ?>
                                <?php if($i==$week_to): ?>
                                <option value="<?php echo e($i); ?>" selected>Week <?php echo e($i); ?></option>
                                <?php else: ?>
                                <option value="<?php echo e($i); ?>">Week <?php echo e($i); ?></option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </td>
                    <td width="20%" class="text-center">
                        <button type="submit" class="btn crud-submit btn-success">Apply filter</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <?php if(count($benches)>0): ?>
        <!-- Bench occupation by years -->
        <p class="text-white bg-primary pl-2 mt-4">BENCHES AND OCCUPATION:</p>
        <table class="table-condensed table-striped" width="100%">
          <tbody>
              <tr>
                <?php $__currentLoopData = $weekstates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekstate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($weekstate->id>1): ?>
                        <td><img src="<?php echo e(asset('icons/ic_occ_status_'.$weekstate->id.'.png')); ?>" width="14"/></td>
                        <td><?php echo e($weekstate->title); ?></td>
                        <td></td>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <td><img src="<?php echo e(asset('icons/ic_occ_status_req.png')); ?>" width="14"/></td>
                    <td>Weeks required</td>
                    <td></td>
              </tr>
          </tbody>
        </table>
        <?php $__currentLoopData = $benches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bench): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border rounded p-1 mt-3" style="background-color:#fff9ec">
            <table class="table table-sm table-bordered" width="100%" cellspacing="0">
                <tbody>
                    <tr class="bg-dark text-white">
                        <td class="col-2 small" id="title_<?php echo e($bench->id); ?>"><?php echo e($bench->title); ?></td>
                        <td class="col-2 small"><?php echo e($bench->entity()->first()->title); ?></td>
                        <td class="col-2 small">
                            <?php ($i=1); ?>
                            <?php ($component = $bench->areaComponent()->first()->component()->first()); ?>
                            <?php $__currentLoopData = $component->areas()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($i>1): ?> - <?php endif; ?>
                                <?php echo e($area->title); ?>

                                <?php ($i++); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="col-4 small"><?php echo e($bench->areaComponent()->first()->component()->first()->title); ?></td>
                        <td class="col-2 text-right">
                            <a href="<?php echo e(route('bench.reports.assessments.technical',['bench'=>$bench->id])); ?>" class="btn-sm fa fa-tasks" title="Technical sheet"></a>
                <a href="<?php echo e(route('bench.reports.occupation',['bench'=>$bench->id])); ?>" class="btn-sm fa fa-calendar" title="Occupation"></a>
                <a href="<?php echo e(route('bench.reports.assessments.economical',['bench'=>$bench->id])); ?>" class="btn-sm fa fa-euro" title="Economic sheet"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table-condensed" width="100%">
                  <thead>
                      <tr class="table-bordered bg-white">
                        <th></th>
                        <?php for($i = 1; $i <= 52; $i++): ?>
                            <?php ($bg=""); ?>
                            <?php if($i>=$week_from && $i<=$week_to): ?>
                                <?php ($bg="bgcolor='#ffc0ff'"); ?>
                            <?php endif; ?>
                            <?php if($i < 10): ?>
                                <th class="small text-center" <?php echo $bg; ?>>0<?php echo e($i); ?></th>
                            <?php else: ?>
                                <th class="small text-center" <?php echo $bg; ?>><?php echo e($i); ?></th>
                            <?php endif; ?>
                        <?php endfor; ?>
                      </tr>
                  </thead>
                  <tbody class="table-bordered">
                      <?php ($occ_products = $bench->occupations()->where('year','=',$year)->get()); ?>
                      <?php $__currentLoopData = $occ_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $occ_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td class="p-1" id="product_<?php echo e($occ_product->id); ?>"><small>
                            <?php echo e($occ_product->product()->first()->platform()->first()->title); ?>:<br/>
                            <?php echo e($occ_product->product()->first()->title); ?></small>&nbsp;&nbsp;
                        </td>
                        <?php $__currentLoopData = $occ_product->occupationweeks()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($week_product->week>=$week_from && $week_product->week<=$week_to): ?>
                                <td style="width: 1.8%" class="text-center" bgcolor="#ffc0ff">
                            <?php else: ?>
                                <td style="width: 1.8%" class="text-center">
                            <?php endif; ?>
                                <?php if($week_product->weekstate_id>1): ?>
                                    <img src="<?php echo e(asset('icons/ic_occ_status_'.$week_product->weekstate_id.'.png')); ?>" width="14"/>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
            </table>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.intranet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>