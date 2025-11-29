<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($document-> name); ?></span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Status')); ?></span></dt>
                    <dd class="col-sm-8">
                        <?php if($document->status == 0): ?>
                            <span class="badge badge-success"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                        <?php elseif($document->status == 1): ?>
                            <span class="badge badge-info"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                        <?php elseif($document->status == 2): ?>
                            <span class="badge badge-warning"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                        <?php elseif($document->status == 3): ?>
                            <span class="badge badge-danger"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Type')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($document->types->name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Account')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($document->accounts)?$document->accounts->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Opportunities')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($document->opportunitys)?$document->opportunitys->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Publish Date')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($document->publish_date)); ?></span></dd>

                    <dt style="display:none;" class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Expiration Date')); ?></span></dt>
                    <dd style="display:none;" class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($document->expiration_date)); ?></span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Description')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($document->description); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('File')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($document->attachment)?$document->attachment:'No File'); ?></span></dd>
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
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($document->assign_user)?$document->assign_user->name:''); ?></span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0"><?php echo e(__('Created')); ?></span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($document->created_at)); ?></span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Document')): ?>
        <a href="<?php echo e(route('document.edit',$document->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Document Edit')); ?>"><i class="far fa-edit"></i>
        </a>
        <?php endif; ?>
    </div>
</div>


<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/document/view.blade.php ENDPATH**/ ?>