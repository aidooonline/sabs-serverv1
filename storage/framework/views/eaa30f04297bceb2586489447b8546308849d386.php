<?php $__env->startPush('script-page'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Order')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"><?php echo e(__('Order')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Order')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"> <?php echo e(__('Order Id')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Date')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Name')); ?></th>
                    <th scope="col"><?php echo e(__('Plan Name')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Price')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Payment Type')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Coupon')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"> <?php echo e(__('Invoice')); ?></th>

                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($order->order_id); ?></td>
                        <td><?php echo e($order->created_at->format('d M Y')); ?></td>
                        <td><?php echo e($order->user_name); ?></td>
                        <td><?php echo e($order->plan_name); ?></td>
                        <td><?php echo e(env('CURRENCY_SYMBOL').$order->price); ?></td>
                        <td><?php echo e($order->payment_type); ?></td>
                        <td>
                            <?php if($order->payment_status == 'succeeded'): ?>
                                <i class="mdi mdi-circle text-success"></i> <?php echo e(ucfirst($order->payment_status)); ?>

                            <?php else: ?>
                                <i class="mdi mdi-circle text-danger"></i> <?php echo e(ucfirst($order->payment_status)); ?>

                            <?php endif; ?>
                        </td>

                        <td><?php echo e(!empty($order->total_coupon_used)? !empty($order->total_coupon_used->coupon_detail)?$order->total_coupon_used->coupon_detail->code:'-':'-'); ?></td>

                        <td class="text-center">
                            <?php if($order->receipt != 'free coupon' && $order->payment_type == 'STRIPE'): ?>
                                <a href="<?php echo e($order->receipt); ?>" title="Invoice" target="_blank" class=""><i class="fas fa-file-invoice"></i> </a>
                            <?php elseif($order->receipt =='free coupon'): ?>
                                <p><?php echo e(__('Used 100 % discount coupon code.')); ?></p>
                            <?php elseif($order->payment_type == 'Manually'): ?>
                                <p><?php echo e(__('Manually plan upgraded by super admin')); ?></p>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/order/index.blade.php ENDPATH**/ ?>