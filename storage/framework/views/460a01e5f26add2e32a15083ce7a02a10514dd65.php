<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Document')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Document')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Document')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('document.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Document')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('document.create',['document',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Document')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('File')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Created At')); ?></th>
                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assign User')); ?></th>
                    <?php if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Document Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($document->name)); ?>

                            </a>
                        </td>
                        <td class="budget">
                            <?php if(!empty($document->attachment)): ?>
                                <a href="<?php echo e(asset(Storage::url('upload/profile')).'/'.$document->attachment); ?>" download=""><i class="fas fa-download"></i></a>    
                            <?php else: ?>
                                <span>
                                    <?php echo e(__('No File')); ?>

                                </span>
                            <?php endif; ?>
                            
                        </td>
                        <td>
                            <?php if($document->status == 0): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                            <?php elseif($document->status == 1): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                            <?php elseif($document->status == 2): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                            <?php elseif($document->status == 3): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Document::$status[$document->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($document->created_at)); ?></span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm"><?php echo e(ucfirst(!empty($document->assign_user)?$document->assign_user->name:'-')); ?></span></span>
                        </td>
                        <?php if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Document')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Document Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Document')): ?>
                                    <a href="<?php echo e(route('document.edit',$document->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit Document')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Document')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($document->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id],'id'=>'delete-form-'.$document ->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/document/index.blade.php ENDPATH**/ ?>