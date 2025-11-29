<?php $__env->startSection('page-title'); ?>
    <?php echo e($formBuilder->name.__("'s Form Field")); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"> <?php echo e($formBuilder->name.__(" Form Field")); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('form_builder.index')); ?>"><?php echo e(__('Form Builder')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Field')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Form Field')): ?>
        <a href="#" data-size='md' data-url="<?php echo e(route('form.field.create',$formBuilder->id)); ?>" data-size="md" data-ajax-popup="true" data-title="<?php echo e(__('Create New Filed')); ?>" class="btn btn-sm btn-white btn-icon-only rounded-circle" data-toggle="tooltip">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th><?php echo e(__('Name')); ?></th>
                    <th><?php echo e(__('Type')); ?></th>
                    <?php if(\Auth::user()->can('Edit Form Field') || \Auth::user()->can('Delete Form Field')): ?>
                        <th class="text-right" width="200px"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if($formBuilder->form_field->count()): ?>
                    <?php $__currentLoopData = $formBuilder->form_field; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($field->name); ?></td>
                            <td><?php echo e(ucfirst($field->type)); ?></td>
                            <?php if(\Auth::user()->can('Edit Form Field') || \Auth::user()->can('Delete Form Field')): ?>
                                <td class="text-right">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Form Field')): ?>
                                        <a href="#" class="action-item" data-url="<?php echo e(route('form.field.edit',[$formBuilder->id,$field->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Field')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                            <i class="far fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Form Field')): ?>
                                        <a href="#!" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-<?php echo e($field->id); ?>').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['form.field.destroy', [$formBuilder->id,$field->id]],'id'=>'delete-form-'.$field->id]); ?>

                                        <?php echo Form::close(); ?>

                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center"><?php echo e(__('No data available in table')); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/form_builder/show.blade.php ENDPATH**/ ?>