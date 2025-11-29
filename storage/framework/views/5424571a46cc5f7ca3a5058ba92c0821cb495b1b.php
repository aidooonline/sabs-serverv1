<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">


                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Account')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($contact->assign_account)?$contact->assign_account->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Email')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> email); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Phone')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> phone); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Billing Address')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> contact_address); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('City')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> contact_city); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('State')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> contact_state); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Country')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($contact-> contact_country); ?></span></dd>
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
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($contact->assign_user)?$contact->assign_user->name:'-'); ?></span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0"><?php echo e(__('Created')); ?></span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($contact->created_at)); ?></span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Contact')): ?>
            <a href="<?php echo e(route('contact.edit',$contact->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Contact Edit')); ?>"><i class="far fa-edit"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/contact/view.blade.php ENDPATH**/ ?>