<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Task Stage')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Task Stage')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Task Stage')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>

   <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create TaskStage')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('task_stage.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Task Stage')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
   <?php endif; ?>
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
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Stage')); ?></th>
                   <?php if(Gate::check('Edit TaskStage') || Gate::check('Delete TaskStage')): ?>
                        <th class="text-right"><?php echo e(__('Action')); ?></th>
                   <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="sorting_1"><?php echo e($stage->name); ?></td>
                       <?php if(Gate::check('Edit TaskStage') || Gate::check('Delete TaskStage')): ?>
                            <td class="action text-right">
                               <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit TaskStage')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('task_stage.edit',$stage->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit stage')); ?>" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                               <?php endif; ?>
                               <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete TaskStage')): ?>
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($stage->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['task_stage.destroy', $stage->id],'id'=>'delete-form-'.$stage->id]); ?>

                                    <?php echo Form::close(); ?>

                               <?php endif; ?>
                            </td>
                       <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/task_stage/index.blade.php ENDPATH**/ ?>