<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('User')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Users')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('User')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('user.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create User')): ?>
        <a href="javascript:setunique();"  data-size="lg" data-url="<?php echo e(route('user.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New User')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if(\Auth::user()->type != 'super admin'): ?>
    <div class="row">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" <?php if(!empty($user->avatar)): ?> src="<?php echo e((!empty($user->avatar))? asset(Storage::url("upload/profile/".$user->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))); ?>" <?php else: ?>  avatar="<?php echo e($user->name); ?>" <?php endif; ?>>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <a href="#" data-size="lg" data-url="<?php echo e(route('user.show',$user->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('User Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($user->name)); ?>

                            </a>
                        </h5>
                        <div class="mb-1"><span href="#" class="text-sm small text-muted"><?php echo e($user->email); ?></span></div>

                        <?php if(Gate::check('Create User') || Gate::check('Edit User') || Gate::check('Delete User')): ?>
                            <div class="actions d-flex justify-content-between px-4">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create User')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('user.show',$user->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('User Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit User')): ?>
                                    <a href="<?php echo e(route('user.edit',$user->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete User')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($user->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]); ?>

                                <?php echo Form::close(); ?>

                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center">
                        <span class="btn-inner--icon text-sm small"><span data-toggle="tooltip" data-placement="top" title="Phone"><?php echo e($user->phone); ?></span></span>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="row">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" src="<?php echo e(asset(Storage::url("upload/profile/")).'/'); ?><?php echo e(!empty($user->avatar)?$user->avatar:'avatar.png'); ?>" class="avatar  rounded-circle avatar-lg">
                        </div>
                        <h5 class="h6 mt-4 mb-0"> <?php echo e($user->name); ?></h5>
                        <a href="#" class="d-block text-sm text-muted mb-3"> <?php echo e($user->email); ?></a>
                            <div class="actions d-flex justify-content-between pl-6">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit User')): ?>
                                <a href="<?php echo e(route('user.edit',$user->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                    <i class="far fa-edit"></i>
                                </a>
                            <?php endif; ?>
                        
                                <a href="#" class="action-item" data-size="lg" data-url="<?php echo e(route('plan.upgrade',$user->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-title="<?php echo e(__('Upgrade Plan')); ?>">
                                    <i class="fas fa-trophy"></i>
                                </a>
                        
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete User')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($user->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]); ?>

                                <?php echo Form::close(); ?>

                            <?php endif; ?>

                        </div>
                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]); ?>

                        <?php echo Form::close(); ?>

                    </div>
                    <div class="card-body border-top">
                        <div class="row justify-content-between align-items-center">
                            <div class="col text-center">
                                <span class="d-block h4 mb-0"><?php echo e($user->countUser($user->id)); ?></span>
                                <span class="d-block text-sm text-muted"><?php echo e(__('User')); ?></span>
                            </div>
                            <div class="col text-center">
                                <span class="d-block h4 mb-0"><?php echo e($user->countAccount($user->id)); ?></span>
                                <span class="d-block text-sm text-muted"><?php echo e(__('Account')); ?></span>
                            </div>
                            <div class="col text-center">
                                <span class="d-block h4 mb-0"><?php echo e($user->countContact($user->id)); ?></span>
                                <span class="d-block text-sm text-muted"><?php echo e(__('Contact')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="actions d-flex justify-content-between">
                            <span class="d-block text-sm text-muted"> <?php echo e(__('Plan')); ?> :  <?php echo e(!empty($user->currentPlan)?$user->currentPlan->name:__('Free')); ?></span>

                        </div>
                        <div class="actions d-flex justify-content-between mt-1">
                            <span class="d-block text-sm text-muted"><?php echo e(__('Plan Expired')); ?> : <?php echo e(!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date):'Unlimited'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/user/grid.blade.php ENDPATH**/ ?>