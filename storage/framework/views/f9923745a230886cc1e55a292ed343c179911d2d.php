<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Stream')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Stream')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Stream')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="row justify-content-between align-items-center">
            <div class="col-sm-12">
                <div class="card card-fluid">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Latest comments')); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $remark = json_decode($stream->remark);
                            ?>

                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-parent-child">
                                        <img alt="" class="rounded-circle avatar" <?php if(!empty($stream->file_upload)): ?> src="<?php echo e((!empty($stream->file_upload))? asset(Storage::url("upload/profile/".$stream->file_upload)): asset(url("public/assets/img/clients/160x160/img-1.png"))); ?>" <?php else: ?>  avatar="<?php echo e($remark->user_name); ?>" <?php endif; ?>>
                                    </div>
                                    <div class="flex-fill ml-3">
                                        <div class="h6 text-sm mb-0"><?php echo e($remark->user_name); ?><small class="float-right text-muted"><?php echo e($stream->created_at); ?></small></div>
                                        <span class="text-sm lh-140 mb-0">
                                            posted to <a href="#"><?php echo e($remark->title); ?></a> , <?php echo e($stream->log_type); ?>  <a href="#"><?php echo e($remark->stream_comment); ?></a>
                                        </span>
                                        <a href="#" class="action-item float-right" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($stream->id); ?>').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['stream.destroy', $stream->id],'id'=>'delete-form-'.$stream->id]); ?>

                                        <?php echo Form::close(); ?>

                                    </div>
                                </div>
                            </div>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/stream/index.blade.php ENDPATH**/ ?>