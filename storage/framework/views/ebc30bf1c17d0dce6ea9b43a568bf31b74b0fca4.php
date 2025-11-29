<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Sales Order')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Sales Order')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Sales Order')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create SalesOrder')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('salesorder.create',['salesorder',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Sales Order')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('ID')); ?></th>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Account')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Created At')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Amount')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $salesorders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesorder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('salesorder.show',$salesorder->id)); ?>" class="action-item" data-title="<?php echo e(__('Quote Details')); ?>">
                                <?php echo e(\Auth::user()->salesorderNumberFormat($salesorder->salesorder_id)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('salesorder.show',$salesorder->id)); ?>" class="badge badge-dot action-item" data-title="<?php echo e(__('SalesOrders Details')); ?>">
                                <?php echo e(ucfirst($salesorder->name)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> <?php echo e(ucfirst(!empty($salesorder->accounts)?$salesorder->accounts->name:'--')); ?></a>
                        </td>
                        <td>
                            <?php if($salesorder->status == 0): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php elseif($salesorder->status == 1): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php elseif($salesorder->status == 2): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php elseif($salesorder->status == 3): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php elseif($salesorder->status == 4): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php elseif($salesorder->status == 5): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\SalesOrder::$status[$salesorder->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($salesorder->created_at)); ?></span>
                        </td>
                        <td>
                          
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->priceFormat($salesorder->getTotal())); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(ucfirst(!empty($salesorder->assign_user)?$salesorder->assign_user->name:'-')); ?></span>
                        </td>
                        <?php if(Gate::check('Show SalesOrder') || Gate::check('Edit SalesOrder') || Gate::check('Delete SalesOrder')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show SalesOrder')): ?>
                                    <a href="<?php echo e(route('salesorder.show',$salesorder->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('SalesOrders Details')); ?>">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit SalesOrder')): ?>
                                    <a href="<?php echo e(route('salesorder.edit',$salesorder->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit SalesOrders')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete SalesOrder')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($salesorder->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['salesorder.destroy', $salesorder->id],'id'=>'delete-form-'.$salesorder->id]); ?>

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
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function () {

            var opportunities = $(this).val();

            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            
            $.ajax({
                url: '<?php echo e(route('salesorder.getaccount')); ?>',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id, "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function (data) {
                    console.log(data);
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/salesorder/index.blade.php ENDPATH**/ ?>