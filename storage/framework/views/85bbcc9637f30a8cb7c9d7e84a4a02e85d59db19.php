<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Permission')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Permission')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Permission')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="#" data-size="lg" data-url="<?php echo e(route('permission.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Permission')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
        <i class="fa fa-plus"></i>
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Permission')); ?></th>
                    <th class="text-right"><?php echo e(__('Action')); ?></th>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="sorting_1"><?php echo e($permission->name); ?></td>
                    <td class="action text-right">
                        <a href="#" data-size="lg" data-url="<?php echo e(route('permission.edit',$permission->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Permission')); ?>" class="action-item">
                            <i class="far fa-edit"></i>
                        </a>
                        <a href="#" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($permission->id); ?>').submit();">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['permission.destroy', $permission->id],'id'=>'delete-form-'.$permission->id]); ?>

                        <?php echo Form::close(); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/permission/index.blade.php ENDPATH**/ ?>