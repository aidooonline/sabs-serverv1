<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">


                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Name</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Website</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> website); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Email</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> email); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Phone</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> phone); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Billing Address</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> billing_address); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">City</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> billing_city); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Country</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($account-> billing_country); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Type</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($account->accountType)?$account->accountType->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Industry</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($account->accountIndustry)?$account->accountIndustry->name:'-'); ?></span></dd>
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
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($account->assign_user)?$account->assign_user->name:'-'); ?></span></dd>


                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Created</span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($account->created_at)); ?></span></dd>

                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Account')): ?>
            <a href="<?php echo e(route('account.edit',$account->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Account Edit')); ?>"><i class="far fa-edit"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/account/view.blade.php ENDPATH**/ ?>