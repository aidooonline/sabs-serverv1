<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Invoice')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Invoice')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Invoice')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Invoice')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('invoice.create',['invoice',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Invoice Item')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="id"><?php echo e(__('ID')); ?></th>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Account')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Created At')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Amount')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                    <?php if(Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('invoice.show',$invoice->id)); ?>" class="action-item" data-title="<?php echo e(__('Quote Details')); ?>">
                                <?php echo e(\Auth::user()->invoiceNumberFormat($invoice->invoice_id)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="<?php echo e(route('invoice.show',$invoice->id)); ?>" class="badge badge-dot action-item" data-title="<?php echo e(__('Invoice Details')); ?>">
                                <?php echo e(ucfirst($invoice->name)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> <?php echo e(ucfirst(!empty( $invoice->accounts)? $invoice->accounts->name:'--')); ?></a>
                        </td>
                        <td>
                            <?php if($invoice->status == 0): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php elseif($invoice->status == 1): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php elseif($invoice->status == 2): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php elseif($invoice->status == 3): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php elseif($invoice->status == 4): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php elseif($invoice->status == 5): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Invoice::$status[$invoice->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($invoice->created_at)); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->priceFormat($invoice->getTotal())); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(ucfirst(!empty($invoice->assign_user)?$invoice->assign_user->name:'-')); ?></span>
                        </td>
                        <?php if(Gate::check('Show Invoice') || Gate::check('Edit Invoice') || Gate::check('Delete Invoice')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Invoice')): ?>
                                <a href="<?php echo e(route('invoice.show',$invoice->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" class="action-item" data-title="<?php echo e(__('Invoice Details')); ?>">
                                    <i class="far fa-eye"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Invoice')): ?>
                                    <a href="<?php echo e(route('invoice.edit',$invoice->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" class="action-item" data-title="<?php echo e(__('Edit Invoice')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Invoice')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($invoice->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['invoice.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]); ?>

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
            console.log(opportunities);
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '<?php echo e(route('invoice.getaccount')); ?>',
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/invoice/index.blade.php ENDPATH**/ ?>