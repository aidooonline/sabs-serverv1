<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Common case')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Common case')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Case')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('commoncases.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create CommonCase')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.create',['commoncases',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Common case')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $commonCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commonCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" <?php if(!empty($commonCase->avatar)): ?> src="<?php echo e((!empty($commonCase->avatar))? asset(Storage::url("upload/profile/".$commonCase->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))); ?>" <?php else: ?>  avatar="<?php echo e($commonCase->name); ?>" <?php endif; ?>>
                        </div>
                        <h5 class="h6 mt-4 mb-1"><?php echo e($commonCase->name); ?></h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Account Name"><?php echo e(!empty($commonCase->accounts)? $commonCase->accounts->name:'-'); ?></a></div>
                    </div>
                    <div class="card-footer text-center">
                        <?php if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase')): ?>
                            <div class="actions d-flex justify-content-between px-4">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show CommonCase')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.show',$commonCase->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Common case')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit CommonCase')): ?>
                                    <a href="<?php echo e(route('commoncases.edit',$commonCase->id)); ?>" class="action-item" data-title="<?php echo e(__('Edit Common case')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete CommonCase')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($commonCase->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $commonCase->id],'id'=>'delete-form-'.$commonCase->id]); ?>

                                <?php echo Form::close(); ?>

                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/commoncase/grid.blade.php ENDPATH**/ ?>