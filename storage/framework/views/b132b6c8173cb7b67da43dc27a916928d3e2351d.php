<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('click', '.code', function () {
            var type = $(this).find('.icon-input').val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Coupon')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Coupon')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Coupon')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="#" data-url="<?php echo e(route('coupon.create')); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Create New Coupon')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle" data-toggle="tooltip">
        <i class="fas fa-plus"></i>
    </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"> <?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Code')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Discount (%)')); ?></th>
                    <th scope="col"><?php echo e(__('Limit')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Used')); ?></th>
                    <th scope="col" class="action text-right"><?php echo e(__('Action')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>

                        <td class="budget"><?php echo e($coupon->name); ?> </td>
                        <td><?php echo e($coupon->code); ?></td>
                        <td>
                            <?php echo e($coupon->discount); ?>

                        </td>
                        <td><?php echo e($coupon->limit); ?></td>
                        <td><?php echo e($coupon->used_coupon()); ?></td>
                        <td class="text-right">
                            <div class="actions ml-3">
                                <a href="<?php echo e(route('coupon.show',$coupon->id)); ?>" class="action-item" data-toggle="tooltip" title="<?php echo e(__('View')); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#!" class="action-item" data-size="lg" data-url="<?php echo e(route('coupon.edit',$coupon->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Coupon')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <a href="#!" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($coupon->id); ?>').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['coupon.destroy', $coupon->id],'id'=>'delete-form-'.$coupon->id]); ?>

                                <?php echo Form::close(); ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/coupon/index.blade.php ENDPATH**/ ?>