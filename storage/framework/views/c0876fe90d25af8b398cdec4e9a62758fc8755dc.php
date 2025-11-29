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
    <a href="<?php echo e(route('campaign.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

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
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Type')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Budget')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('campaign.show',$campaign->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Campaign Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($campaign->name)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> <?php echo e(ucfirst(!empty($campaign->types->name)?$campaign->types->name:'-')); ?></a>
                        </td>
                        <td>
                            <?php if($campaign->status == 0): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                            <?php elseif($campaign->status == 1): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                            <?php elseif($campaign->status == 2): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                            <?php elseif($campaign->status == 3): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\Campaign::$status[$campaign->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e($campaign->budget); ?></span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm"><?php echo e(ucfirst(!empty($campaign->assign_user)?$campaign->assign_user->name:'-')); ?></span></span>
                        </td>
                       
                        <?php if(Gate::check('Show Campaign') || Gate::check('Edit Campaign') || Gate::check('Delete Campaign')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Campaign')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('campaign.show',$campaign->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Campaign Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Campaign')): ?>
                                    <a href="<?php echo e(route('campaign.edit',$campaign->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" class="action-item" data-title="<?php echo e(__('Edit Campaign')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Campaign')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($campaign->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['campaign.destroy', $campaign->id],'id'=>'delete-form-'.$campaign->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/campaign/index.blade.php ENDPATH**/ ?>