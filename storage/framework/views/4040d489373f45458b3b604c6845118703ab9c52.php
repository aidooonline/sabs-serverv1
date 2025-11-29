<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Properties')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Properties')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Properties')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('product.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Grid View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Product')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('product.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Property')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="Brand"><?php echo e(__('Category')); ?></th>
                    <th scope="col" class="sort" data-sort="Status"><?php echo e(__('Status')); ?></th>
                    <th scope="col" class="sort" data-sort="Price"><?php echo e(__('Price')); ?></th>
                    <th scope="col" class="sort" data-sort="No of Bedrooms"><?php echo e(__('No of Bedrooms')); ?></th>
                    <th style="display:none;" scope="col" class="sort" data-sort="assign User"><?php echo e(__('assign User')); ?></th>
                    <?php if(Gate::check('Show Product') || Gate::check('Edit Property') || Gate::check('Delete Product')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('product.show',$product->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Product Details')); ?>" class="badge badge-dot action-item">
                                <?php echo e(ucfirst($product->name)); ?>

                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot"> <?php echo e(ucfirst($product->brands->name)); ?></a>
                        </td>
                        <td>
                            <?php if($product->status == 0): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\Product::$status[$product->status])); ?></span>
                            <?php elseif($product->status == 1): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Product::$status[$product->status])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-dot"><?php echo e(\Auth::user()->priceFormat($product->price)); ?></span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm"><?php echo e(ucfirst(!empty($product->assign_user)?$product->assign_user->name:'-')); ?></span></span>
                        </td>
                        <?php if(Gate::check('Show Product') || Gate::check('Edit Product') || Gate::check('Delete Product')): ?>
                            <td class="text-right">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Product')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('product.show',$product->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Product Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Product')): ?>
                                    <a href="<?php echo e(route('product.edit',$product->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit Product')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Product')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($product->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id],'id'=>'delete-form-'.$product->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/product/index.blade.php ENDPATH**/ ?>