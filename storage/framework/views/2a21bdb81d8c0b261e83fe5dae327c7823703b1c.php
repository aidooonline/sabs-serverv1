<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($opportunities->name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Account name')); ?></span></dt>

                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($opportunities->accounts)?$opportunities->accounts->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Stage')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($opportunities->stages)?$opportunities->stages->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Amount')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->priceFormat( $opportunities->amount)); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm"><?php echo e(__('Probability')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($opportunities->probability); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Close Date')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($opportunities->close_date)); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Contacts')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($opportunities->contacts)?$opportunities->contacts->name:''); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Lead Source')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($opportunities->leadsource)?$opportunities->leadsource->name:''); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Description')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($opportunities-> description); ?></span></dd>
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
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Assigned User</span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($opportunities->assign_user)?$opportunities->assign_user->name:''); ?></span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Created</span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($opportunities->created_at )); ?></span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Opportunities')): ?>
            <a href="<?php echo e(route('opportunities.edit',$opportunities->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Opportunities Edit')); ?>"><i class="far fa-edit"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/opportunities/view.blade.php ENDPATH**/ ?>