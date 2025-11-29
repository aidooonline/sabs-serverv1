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
    <a href="<?php echo e(route('user.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create User')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('user.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New User')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="actions-toolbar border-0">
            <div class="actions-search" id="actions-search">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-flush" placeholder="Type and hit enter ...">
                    <div class="input-group-append">
                        <a href="#" class="input-group-text bg-transparent" data-action="search-close" data-target="#actions-search"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    
                    <th scope="col" class="sort" data-sort="username"><?php echo e(__('Avatar')); ?></th>
                    <th scope="col" class="sort" data-sort="username"><?php echo e(__('User Name')); ?></th>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="email"><?php echo e(__('Email')); ?></th>
                    <?php if(\Auth::user()->type != 'super admin'): ?>
                        <th scope="col" class="sort" data-sort="title"><?php echo e(__('Type')); ?></th>    
                        <th scope="col" class="sort" data-sort="isactive"><?php echo e(__('Status')); ?></th>
                    <?php endif; ?>
                    <?php if(Gate::check('Edit User') || Gate::check('Delete User')): ?>
                    <th class="text-right" scope="col"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           
                    <tr>
                        <td>
                            <img alt=""  src="<?php echo e(asset(Storage::url("upload/profile/")).'/'); ?><?php echo e(!empty($user->avatar)?$user->avatar:'avatar.png'); ?>"  width="30px" class="rounded-circle">
                        
                            <span class="position-relative padtop"><a href="#" class="name h6 text-sm ml-2"> </a></span>
                        </td>
                        <td class="budget">
                            <a href="#" data-size="lg" data-url="<?php echo e(route('user.show',$user->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('User Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($user->username)); ?>

                            </a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                 <a href="#" data-size="lg" data-url="<?php echo e(route('user.show',$user->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('User Details')); ?>" class="action-item">
                                    <?php echo e(ucfirst($user->name)); ?>

                                 </a>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"><?php echo e($user->email); ?></a>
                        </td>
                        <?php if(\Auth::user()->type != 'super admin'): ?>
                        <td>
                            <?php echo e(ucfirst($user->type)); ?>

                        </td>
                        <td>
                            <?php if($user->is_active == 1): ?>
                                <span class="badge badge-success"><?php echo e(__('Active')); ?></span>
                            <?php else: ?>
                                <span class="badge badge-danger"><?php echo e(__('In Active')); ?></span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <?php if(Gate::check('Edit User') || Gate::check('Delete User')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show User')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('user.show',$user->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('User Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Manage Plan')): ?>
                                <a href="#" class="action-item" data-size="lg" data-url="<?php echo e(route('plan.upgrade',$user->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-title="<?php echo e(__('Upgrade Plan')); ?>">
                                    <i class="fas fa-trophy"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit User')): ?>
                                    <a href="<?php echo e(route('user.edit',$user->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit User')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete User')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($user->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id],'id'=>'delete-form-'.$user->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/user/index.blade.php ENDPATH**/ ?>