<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($task-> name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Stage')); ?></span></dt>
                    <dd class="col-sm-8">
                        <?php echo e(!empty($task->stages->name)?$task->stages->name:'-'); ?>

                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Priority')); ?></span></dt>
                    <dd class="col-sm-8">
                        <?php if($task->priority == 0): ?>
                            <span class="badge badge-success"><?php echo e(__(\App\Task::$priority[$task->priority])); ?></span>
                        <?php elseif($task->priority == 1): ?>
                            <span class="badge badge-info"><?php echo e(__(\App\Task::$priority[$task->priority])); ?></span>
                        <?php elseif($task->priority == 2): ?>
                            <span class="badge badge-warning"><?php echo e(__(\App\Task::$priority[$task->priority])); ?></span>
                        <?php elseif($task->priority == 3): ?>
                            <span class="badge badge-danger"><?php echo e(__(\App\Task::$priority[$task->priority])); ?></span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Start Date')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($task->start_date)); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Due Date')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($task->due_date  )); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Assigned')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($task->parent); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Assigned Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($task->getparent($task->parent,$task->parent_id)); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Description')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($task->description); ?></span></dd>

                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-footer py-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="row align-items-center">
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0"><?php echo e(__('Assigned User')); ?></span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($task->assign_user)?$task->assign_user->name:''); ?></span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0"><?php echo e(__('Created')); ?></span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($task->created_at)); ?></span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Task')): ?>
        <a href="<?php echo e(route('task.edit',$task->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Edit Call')); ?>"><i class="far fa-edit"></i>
        </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/task/view.blade.php ENDPATH**/ ?>