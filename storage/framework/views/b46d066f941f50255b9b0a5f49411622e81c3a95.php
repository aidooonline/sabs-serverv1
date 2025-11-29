<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Cases')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Cases')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Cases')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('commoncases.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create CommonCase')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.create',['commoncases',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Case')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Account')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Priority')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase')): ?>
                        <th scope="col"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.show',$case->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Cases Details')); ?>" class="badge badge-dot action-item">
                                <?php echo e($case->name); ?>

                            </a>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(!empty($case->accounts->name)?$case->accounts->name:'--'); ?></span>
                        </td>
                        <td>
                            <?php if($case->status == 0): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php elseif($case->status == 1): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php elseif($case->status == 2): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php elseif($case->status == 3): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php elseif($case->status == 4): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php elseif($case->status == 5): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($case->priority == 0): ?>
                                <span class="badge badge-primary"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                            <?php elseif($case->priority == 1): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                            <?php elseif($case->priority == 2): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                            <?php elseif($case->priority == 3): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(!empty($case->assign_user)?$case->assign_user->name:''); ?></span>
                        </td>
                        <?php if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase')): ?>
                            <td>
                                <div class="d-flex">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show CommonCase')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.show',$case->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Cases Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit CommonCase')): ?>
                                        <a href="<?php echo e(route('commoncases.edit',$case->id)); ?>" class="action-item" data-title="<?php echo e(__('Edit Cases')); ?>"><i class="far fa-edit"></i></a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete CommonCase')): ?>
                                        <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($case->id); ?>').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id],'id'=>'delete-form-'.$case ->id]); ?>

                                    <?php echo Form::close(); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/commoncase/index.blade.php ENDPATH**/ ?>