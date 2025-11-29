<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Deals')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Deals')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Deals')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('opportunities.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Kanban View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Opportunities')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.create',['opportunities',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Opportunities')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Account')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Stage')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Amount')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $opportunitiess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opportunities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.show', $opportunities->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Opportunities Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($opportunities->name)); ?>

                            </a>
                        </td>
                        <td class="budget">
                            <a href="#"><?php echo e(ucfirst(!empty($opportunities->accounts)?$opportunities->accounts->name:'-')); ?></a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                <?php echo e(ucfirst(!empty($opportunities->stages)?$opportunities->stages->name:'-')); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->priceFormat($opportunities->amount)); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(ucfirst(!empty($opportunities->assign_user)?$opportunities->assign_user->name:'-')); ?></span>
                        </td>
                        <?php if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Opportunities')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.show', $opportunities->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Opportunities Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Opportunities')): ?>
                                    <a href="<?php echo e(route('opportunities.edit',$opportunities->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Opportunities Edit')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Opportunities')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($opportunities->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id],'id'=>'delete-form-'.$opportunities ->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/opportunities/index.blade.php ENDPATH**/ ?>