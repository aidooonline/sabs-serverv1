<?php
    $dir= asset(Storage::url('uploads/plan'));
?>
<?php $__env->startPush('script-page'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Plan')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Plans')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Plan')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <?php if(\Auth::user()->type == 'super admin'): ?>
        <a href="#" data-url="<?php echo e(route('plan.create')); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Create New Plan')); ?>" class="btn btn-sm btn-white btn-icon-only rounded-circle ml-4" data-toggle="tooltip">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3">
                <div class="card card-fluid">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e($plan->name); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <?php if( \Auth::user()->type == 'super admin'): ?>
                                        <a title="Edit Plan" data-size="lg" href="#" class="action-item" data-url="<?php echo e(route('plan.edit',$plan->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Plan')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="fas fa-edit"></i></a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center <?php echo e(!empty(\Auth::user()->type != 'super admin')?'plan-box':''); ?>">
                        <a href="#" class="avatar rounded-circle avatar-lg hover-translate-y-n3">
                            <img alt="Image placeholder" src="<?php echo e($dir.'/'.$plan->image); ?>" class="">
                        </a>

                        <h5 class="h6 my-4"> <?php echo e(env('CURRENCY_SYMBOL').$plan->price.' / '.$plan->duration); ?></h5>

                        <?php if(\Auth::user()->type=='owner' && \Auth::user()->plan == $plan->id): ?>
                            <h5 class="h6 my-4">
                                <?php echo e(__('Expired : ')); ?> <?php echo e(\Auth::user()->plan_expire_date ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date):__('Unlimited')); ?>

                            </h5>

                        <?php endif; ?>

                        <p class="my-4"><?php echo e($plan->description); ?></p>

                        <?php if(\Auth::user()->type == 'owner' && \Auth::user()->plan == $plan->id): ?>
                            <span class="clearfix"></span>
                            <span class="badge badge-pill badge-success"><?php echo e(__('Active')); ?></span>
                        <?php endif; ?>
                        <?php if(($plan->id != \Auth::user()->plan) && \Auth::user()->type!='super admin' ): ?>
                            <?php if($plan->price > 0): ?>
                                <a class="badge badge-pill badge-primary" href="<?php echo e(route('stripe',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Buy Plan')); ?>">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-4 text-center">
                                <span class="h5 mb-0"><?php echo e($plan->max_user); ?></span>
                                <span class="d-block text-sm"><?php echo e(__('Users')); ?></span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="h5 mb-0"><?php echo e($plan->max_account); ?></span>
                                <span class="d-block text-sm"> <?php echo e(__('Accounts')); ?></span>
                            </div>
                            <div class="col-4 text-center">
                                <span class="h5 mb-0"><?php echo e($plan->max_contact); ?></span>
                                <span class="d-block text-sm"> <?php echo e(__('Contacts')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/plan/index.blade.php ENDPATH**/ ?>