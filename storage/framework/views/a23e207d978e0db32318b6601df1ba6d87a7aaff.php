<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Campaign')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Campaign')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Campaign')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('campaign.index')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('List View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Campaign')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('campaign.create',['campaign',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Campaign')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" <?php if(!empty($campaign->avatar)): ?> src="<?php echo e((!empty($campaign->avatar))? asset(Storage::url("upload/profile/".$campaign->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))); ?>" <?php else: ?>  avatar="<?php echo e($campaign->name); ?>" <?php endif; ?>>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            <?php echo e(ucfirst($campaign->name)); ?>

                        </h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                <?php if($campaign->status == 0): ?>
                                    <span class="badge badge-warning"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                                <?php elseif($campaign->status == 1): ?>
                                    <span class="badge badge-success"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                                <?php elseif($campaign->status == 2): ?>
                                    <span class="badge badge-danger"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                                <?php elseif($campaign->status == 3): ?>
                                    <span class="badge badge-info"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <?php if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign')): ?>
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Campaign')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('campaign.show',$campaign->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Campaign')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Campaign')): ?>
                                    <a href="<?php echo e(route('campaign.edit',$campaign->id)); ?>" class="action-item" data-title="<?php echo e(__('Edit Campaign')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Campaign')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($campaign->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['campaign.destroy', $campaign->id],'id'=>'delete-form-'.$campaign->id]); ?>

                                    <?php echo Form::close(); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/campaign/grid.blade.php ENDPATH**/ ?>