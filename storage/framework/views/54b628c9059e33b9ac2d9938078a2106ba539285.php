<?php $__env->startSection('page-title'); ?>
    <?php echo e($form->name.__("'s Response")); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">  <?php echo e($form->name.__("'s Response")); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('form_builder.index')); ?>"><?php echo e(__('Form Builder')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Response')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center">
                <?php if($form->response->count() > 0): ?>
                    <tbody>
                    <?php
                        $first = null;
                        $second = null;
                        $third = null;
                        $i = 0;
                    ?>
                    <?php $__currentLoopData = $form->response; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $i++;
                                $resp = json_decode($response->response,true);
                                if(count($resp) == 1)
                                {
                                    $resp[''] = '';
                                    $resp[' '] = '';
                                }
                                elseif(count($resp) == 2)
                                {
                                    $resp[''] = '';
                                }
                                $firstThreeElements = array_slice($resp, 0, 3);

                                $thead= array_keys($firstThreeElements);
                                $head1 = ($first != $thead[0]) ? $thead[0] : '';
                                $head2 = (!empty($thead[1]) && $second != $thead[1]) ? $thead[1] : '';
                                $head3 = (!empty($thead[2]) && $third != $thead[2]) ? $thead[2] : '';
                        ?>
                        <?php if(!empty($head1) || !empty($head2) || !empty($head3) && $head3 != ' '): ?>
                            <tr>
                                <th><?php echo e($head1); ?></th>
                                <th><?php echo e($head2); ?></th>
                                <th><?php echo e($head3); ?></th>
                                <th><?php echo e(__('Type')); ?></th>
                                <th>#</th>
                            </tr>
                        <?php endif; ?>
                        <?php
                            $first =  $thead[0];
                            $second =  $thead[1];
                            $third =  $thead[2];
                        ?>
                        <tr>
                            <?php $__currentLoopData = array_values($firstThreeElements); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td><?php echo e($ans); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td><?php echo e(ucfirst($response->type)); ?></td>
                            <td class="Action">
                                <a href="#" data-url="<?php echo e(route('response.detail',$response->id)); ?>" data-ajax-popup="true" data-size="lg" data-title="<?php echo e(__('Response Detail')); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Response Detail')); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                <?php else: ?>
                    <tbody>
                    <tr>
                        <td class="text-center"><?php echo e(__('No data available in table')); ?></td>
                    </tr>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/form_builder/response.blade.php ENDPATH**/ ?>