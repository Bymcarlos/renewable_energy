<?php $__env->startSection('content'); ?>
<!-- Breadcrumbs-->
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?php echo e(url('home')); ?>"><span class="nav-link-text">Home</span></a>
    </li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('management')); ?>">Management</a></li>
    <li class="breadcrumb-item active">Partners scopes</li>
</ol>
<table class="table-sm mb-2 bg-light" width="100%" cellspacing="0">
        <tr class="border">
          <td width="1%"><i class="fa fa-fw fa-info-circle"></i></td>
          <td width="85%"><small>Partners are organized in scopes, in this section you can add, edit or remove scopes (remove a scope will remove all partners of the scope and their data sheets, so, remove option is only available for scopes without partners). </small></td>
          <td width="14%" class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-file-o m-1" title="Add new scope" data-toggle="modal" data-target="#add-scope"></a></td>
        </tr>
</table>

<!-- List Scopes -->
<table class="table-bordered table-sm" id="dtRT_list" width="100%" cellspacing="0">
    <thead class="bg-light">
        <tr>
            <th>SCOPE</th>
            <th>DESCRIPTION</th>
            <th>PARTNERS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $scopes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="small" id="title_<?php echo e($scope->id); ?>"><?php echo e($scope->title); ?></td>
            <td class="small" id="description_<?php echo e($scope->id); ?>"><?php echo e($scope->description); ?></td>
            <td class="small">
                <?php $__empty_1 = true; $__currentLoopData = $scope->partners()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?> <div><?php echo e($partner->title); ?></div> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?> Without partners <?php endif; ?></td>
            <td class="text-right"><a href="#" class="btn btn-primary btn-sm fa fa-pencil ml-1" title="Edit scope" data-toggle="modal" data-target="#edit-scope" onclick="editScope(<?php echo e($scope->id); ?>)"></a>
                <?php if($scope->partners()->count()>0): ?>
                <a href="#" class="btn btn-danger btn-sm fa fa-trash ml-1" title="This scope has relations to partners"></a>
                <?php else: ?>
                <a href="#" class="btn btn-primary btn-sm fa fa-trash ml-1" title="Remove scope" data-toggle="modal" data-target="#delete-scope" onclick="deleteScope(<?php echo e($scope->id); ?>)"></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<!-- Create Scope -->
<div class="modal fade" id="add-scope" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create new scope</h4>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" method="POST" action="<?php echo e(route('scopes.store')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <label class="control-label" for="title">Scope title:</label>
                        <input type="text" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
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
<!-- Edit Scope -->
<div class="modal fade" id="edit-scope" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit scope</h4>
            </div>
            <div class="modal-body">
                <form id="form_edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="_method" value="put"/>
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <label class="control-label" for="title">Scope title:</label>
                        <input type="text" id="title" name="title" class="form-control" data-error="Please enter valid title." required />
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
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
<!-- Delete Scope -->
<div class="modal fade" id="delete-scope" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Confirm delete scope:</h4>
            </div>
            <div class="modal-body">
                <h5 class="bg-light" id="title"></h5>
            </div>
            <div class="modal-body">
                <form data-toggle="validator" id="form_delete" method="POST">
                    <input type="hidden" name="_method" value="DELETE" />
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group">
                        <button type="submit" class="btn crud-submit btn-success">Delete</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js_custom'); ?>
<script type="text/javascript">
function editScope(item_id){
    $("#edit-scope #form_edit").attr("action","<?php echo e(url('scopes')); ?>/"+item_id);
    $("#edit-scope #form_edit #title").val($("#title_"+item_id).text());
    $("#edit-scope #form_edit #description").text($("#description_"+item_id).text());
}
function deleteScope(item_id){
    $("#delete-scope #form_delete").attr("action","<?php echo e(url('scopes')); ?>/"+item_id);
    $("#delete-scope #title").text($("#title_"+item_id).text());
}

$('#dtRT_list').DataTable({
    "columnDefs": [ {
        "searchable": false,
        "orderable": false,
        "targets": [3]
    },
    {
        "width": "20%", 
        "targets": 0
    },
    {
        "width": "25%", 
        "targets": 1
    },
    {
        "width": "40%", 
        "targets": 2
    },
    {
        "width": "15%", 
        "targets": 3
    }],
    "dom": 'fprt<"bottom"p>',
    "order": [],
    "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.intranet', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>