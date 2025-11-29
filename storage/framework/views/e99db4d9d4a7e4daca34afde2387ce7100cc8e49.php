<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Document Edit')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Document Edit')); ?>  <?php echo e('('. $document->name .')'); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <div class="btn-group" role="group">
        <?php if(!empty($previous)): ?>
            <a href="<?php echo e(route('document.edit',$previous)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        <?php if(!empty($next)): ?>
            <a href="<?php echo e(route('document.edit',$next)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="<?php echo e(__('Next')); ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="<?php echo e(__('Next')); ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('document.index')); ?>"><?php echo e(__('Document')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Details')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#account_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Overview')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Edit about your document information')); ?></p>
                            </div>
                        </div>
                    </div>

                    <div data-href="#document_account" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-building"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Account')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned account for this document')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--account edit -->
            <div id="account_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0"><?php echo e(__('Overview')); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo e(Form::model($document,array('route' => array('document.update', $document->id), 'method' => 'PUT'))); ?>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('name',__('Name'))); ?>

                                    <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-name" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('folder',__('Folder'))); ?>

                                    <?php echo Form::select('folder', $folders, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                    <?php $__errorArgs = ['folder'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-folder" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('type',__('Type'))); ?>

                                    <?php echo Form::select('type', $type, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-type" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('account',__('Account'))); ?>

                                    <?php echo Form::select('account', $account_name, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['account'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-account" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('opportunities',__('Opportunities'))); ?>

                                    <?php echo Form::select('opportunities', $opportunities, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['opportunities'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-opportunities" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('publish_date',__('Publish Date'))); ?>

                                    <?php echo Form::date('publish_date', null,array('class' => 'form-control','required'=>'required')); ?>

                                    <?php $__errorArgs = ['publish_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-publish_date" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('expiration_date',__('Expiration Date'))); ?>

                                    <?php echo Form::date('expiration_date', null,array('class' => 'form-control','required'=>'required')); ?>

                                    <?php $__errorArgs = ['expiration_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-expiration_date" role="alert">
                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('status',__('Status'))); ?>

                                    <?php echo Form::select('status', $status, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-status" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <?php echo e(Form::label('description',__('Description'))); ?>

                                    <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))); ?>

                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-description" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12 mb-3 field" data-name="attachments">
                                <div class="attachment-upload">
                                    <div class="attachment-button">
                                        <div class="pull-left">
                                            <?php echo e(Form::label('attachment',__('Attachment'))); ?>

                                            <?php echo e(Form::file('attachment',array('class'=>'form-control'))); ?>

                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-description" role="alert">
                                            <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-2">
                                <h6><?php echo e(__('Assigned')); ?></h6>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('user',__('User'))); ?>

                                    <?php echo Form::select('user', $user, $document->user_id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['user'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-user" role="alert">
                                        <strong class="text-danger"><?php echo e($message); ?></strong>
                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="w-100 mt-3 text-right">
                                <?php echo e(Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?>

                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
            </div>
            <!--account edit end-->

            <!--account -->
            <div id="document_account" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Account')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('account.create',['document',$document->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Account')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <!-- Files -->
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Website')); ?></th>
                                        <th scope="col" class="sort" data-sort="status"><?php echo e(__('Type')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Country')); ?></th>
                                        <?php if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account')): ?>
                                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                                    <?php echo e($account->name); ?>

                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#"><?php echo e($account->website); ?></a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                  <?php echo e(!empty($account->AccountType)?$account->AccountType->name:''); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot"><?php echo e($account->shipping_city); ?></span>
                                            </td>
                                            <?php if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account')): ?>
                                            <td class="text-right">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Account')): ?>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('account.show',$account->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Account Details')); ?>" class="action-item">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Account')): ?>
                                                <a href="<?php echo e(route('account.edit',$account->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit Account')); ?>"><i class="far fa-edit"></i></a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Account')): ?>
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($account->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account ->id]); ?>

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
                    </div>
                </div>
            </div>
            <!--account Tasks end-->
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/document/edit.blade.php ENDPATH**/ ?>