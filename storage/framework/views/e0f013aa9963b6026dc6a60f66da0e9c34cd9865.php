<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Accounts')); ?>

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
    <a href="<?php echo e(route('account.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

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
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="Email"><?php echo e(__('Email')); ?></th>
                    <th scope="col" class="sort" data-sort="Phone"><?php echo e(__('Phone')); ?></th>
                    <th scope="col" class="sort" data-sort="Website"><?php echo e(__('Website')); ?></th>
                    <th scope="col" class="sort" data-sort="Assigned User"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>

                            <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($account->name)); ?>

                            </a>
                        </td>
                        <td class="budget">
                            <?php echo e($account->email); ?>


                        </td>
                        <td>
                            <span class="badge badge-dot">
                              <?php echo e($account->phone); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e($account->website); ?></span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm"><?php echo e(ucfirst(!empty($account->assign_user)?$account->assign_user->name:'-')); ?></span></span>
                        </td>

                        <?php if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account')): ?>

                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Account')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Account')): ?>
                                    <a href="<?php echo e(route('account.edit',$account->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Account')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($account->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account ->id]); ?>

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
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('click', '#billing_data', function () {
            console.log('hi');
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/account/index.blade.php ENDPATH**/ ?>