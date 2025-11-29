<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Account')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Account')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Account')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('account.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Account')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('account.create',['account',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Account')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" src="<?php echo e(asset(url("./storage/upload/profile/profile.png"))); ?>" />
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($account->name)); ?>

                            </a>
                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted"><?php echo e($account->email); ?></a></div>

                        <?php if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account')): ?>

                            <div class="actions d-flex justify-content-between px-4">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Account')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Account')): ?>
                                    <a href="<?php echo e(route('account.edit',$account->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" class="action-item" data-title="<?php echo e(__('Account Edit')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Account')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($account->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account->id]); ?>

                                <?php echo Form::close(); ?>

                                <?php endif; ?>
                            </div>

                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center">
                        <span class="btn-inner--icon text-sm small"><span data-toggle="tooltip" data-placement="top" title="Phone"><?php echo e($account->phone); ?></span></span>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/account/grid.blade.php ENDPATH**/ ?>