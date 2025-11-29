<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Report')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Report')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Report')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
       <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Report')): ?>
    <a href="#" data-size="lg" data-url="<?php echo e(route('report.create',['report',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Report')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Entity Type')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Group By')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Chart Type')); ?></th>
                    <?php if(Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report')): ?>
                    <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('report.show',$report->id)); ?>" class="action-item">
                                <?php echo e($report->name); ?>

                            </a>
                        </td>
                        <td class="budget">
                            <span><?php echo e(__(\App\Report::$entity_type[$report->entity_type])); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-primary text-xs small">
                            <?php if($report->entity_type == 'users'): ?>
                                    <?php echo e(__(\App\Report::$users[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'quotes'): ?>
                                    <?php echo e(__(\App\Report::$quotes[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'accounts'): ?>
                                    <?php echo e(__(\App\Report::$accounts[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'contacts'): ?>
                                    <?php echo e(__(\App\Report::$contacts[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'leads'): ?>
                                    <?php echo e(__(\App\Report::$leads[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'opportunities'): ?>
                                    <?php echo e(__(\App\Report::$opportunities[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'invoices'): ?>
                                    <?php echo e(__(\App\Report::$invoices[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'cases'): ?>
                                    <?php echo e(__(\App\Report::$cases[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'products'): ?>
                                    <?php echo e(__(\App\Report::$products[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'tasks'): ?>
                                    <?php echo e(__(\App\Report::$tasks[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'calls'): ?>
                                    <?php echo e(__(\App\Report::$calls[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'campaigns'): ?>
                                    <?php echo e(__(\App\Report::$campaigns[$report->group_by])); ?>

                                <?php elseif($report->entity_type == 'sales_orders'): ?>
                                    <?php echo e(__(\App\Report::$sales_orders[$report->group_by])); ?>

                                <?php else: ?>
                                    <?php echo e(__(\App\Report::$users[$report->group_by])); ?>

                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="budget">
                            <?php echo e(__(\App\Report::$chart_type[$report->chart_type])); ?>

                        </td>
                        <?php if(Gate::check('Show Report') || Gate::check('Edit Report') || Gate::check('Delete Report')): ?>
                        <td>
                            <div class="d-flex float-right">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Report')): ?>
                                <a href="<?php echo e(route('report.show',$report->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Report Details')); ?>">
                                    <i class="far fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Report')): ?>
                                <a href="<?php echo e(route('report.edit',$report->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Report Edit')); ?>"><i class="far fa-edit"></i></a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Report')): ?>
                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($report->id); ?>').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['report.destroy', $report->id],'id'=>'delete-form-'.$report ->id]); ?>

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
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('change', 'select[name=entity_type]', function () {
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '<?php echo e(route('report.getparent')); ?>',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function (data) {
                    console.log(data);
                    $('#group_by').empty();
                    

                    $.each(data, function (key, value) {
                        $('#group_by').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#group_by').empty();
                    }
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/report/index.blade.php ENDPATH**/ ?>