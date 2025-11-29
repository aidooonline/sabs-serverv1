<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Account Edit')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Account Edit')); ?>  <?php echo e('('. $account->name .')'); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <div class="btn-group" role="group">
        <?php if(!empty($previous)): ?>
            <a href="<?php echo e(route('account.edit',$previous)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        <?php if(!empty($next)): ?>
            <a href="<?php echo e(route('account.edit',$next)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="<?php echo e(__('Next')); ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="<?php echo e(__('Next')); ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
i    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('account.index')); ?>"><?php echo e(__('Account')); ?></a></li>
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
                                <p class="mb-0 text-sm"><?php echo e(__('Edit about your account information')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#account_stream" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-rss"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Comment')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Add comment')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountcontact" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-users"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Contacts')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned contacts for this account')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountopportunities" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-handshake"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Deals')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned deals for this account')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" data-href="#accountcases" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-file-alt"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Cases')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountdocuments" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-book-open"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Documents')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned documents')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" data-href="#accounttasks" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-tasks"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Tasks')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned tasks for this account')); ?></p>
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
                        <?php echo e(Form::model($account,array('route' => array('account.update', $account->id), 'method' => 'PUT'))); ?>

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
                                    <?php echo e(Form::label('email',__('Email'))); ?>

                                    <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter email'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-email" role="alert">
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
                                    <?php echo e(Form::label('phone',__('Phone'))); ?>

                                    <?php echo e(Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter phone'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-phone" role="alert">
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
                                    <?php echo e(Form::label('website',__('Website'))); ?>

                                    <?php echo e(Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website')))); ?>

                                    <?php $__errorArgs = ['website'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-website" role="alert">
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
                                    <?php echo e(Form::label('billing_address',__('Address'))); ?>

                                    <a style="display:none;" class="btn btn-xs small btn-primary rounded-pill mr-auto float-right p-1 px-4" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="fas fa-copy"></i></a>
                                    <span class="clearfix"></span>
                                    <?php echo e(Form::text('billing_address',null,array('class'=>'form-control','placeholder'=>__('Enter Address')))); ?>

                                    <?php $__errorArgs = ['billing_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-billing_address" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('shipping_address',__('Shipping Address'))); ?>

                                    <?php echo e(Form::text('shipping_address',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Address')))); ?>

                                    <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-shipping_address" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <?php echo e(Form::label('city',__('City/Town'))); ?>

                                    <?php echo e(Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('City/Town'),))); ?>

                                    <?php $__errorArgs = ['billing_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-billing_city" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-3" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('state',__('State'))); ?>

                                    <?php echo e(Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Enter Billing State')))); ?>

                                    <?php $__errorArgs = ['billing_state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-billing_state" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            
                            <div class="col-3">
                                <div class="form-group">
                                    <?php echo e(Form::label('billing_country',__('Country'))); ?>

                                    <?php echo e(Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Enter country')))); ?>

                                    <?php $__errorArgs = ['billing_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-billing_country" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-3" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('billing_country',__('Postal Code'))); ?>

                                    <?php echo e(Form::number('billing_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Postal Code') ))); ?>

                                    <?php $__errorArgs = ['billing_postalcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>

                                    <span class="invalid-billing_postalcode" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            
                            
                            <div class="col-12">
                                <hr class="mt-1 mb-2">
                                <h6><?php echo e(__('Detail')); ?></h6>
                            </div>
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('type',__('Type'))); ?>

                                    <?php echo Form::select('type', $accountype, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['type'];
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
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('industry',__('Industry'))); ?>

                                    <?php echo Form::select('industry', $industry, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                    <?php $__errorArgs = ['industry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-industry" role="alert">
                                <strong class="text-danger"><?php echo e($message); ?></strong>
                                </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    <?php echo e(Form::label('document_id',__('Document'))); ?>

                                    <?php echo Form::select('document_id', $document_id, null,array('class' => 'form-control','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['industry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-industry" role="alert">
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

                                    <?php echo e(Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Name')))); ?>

                                </div>
                            </div>

                            
                            <div class="col-6" style="display:none;">
                                <?php echo e(Form::label('user',__('User'))); ?>

                                <?php echo Form::select('user', $user, $account->user_id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

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
                            <div class="w-100 mt-3 text-right">
                                <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?>

                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
            </div>
            <!--account edit end-->

            <!--stream edit -->
            <div id="account_stream" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0"><?php echo e(__('Comments')); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo e(Form::open(array('route' => array('streamstore',['account',$account->name,$account->id]), 'method' => 'post','enctype'=>'multipart/form-data'))); ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <?php echo e(Form::label('stream',__('Comment'))); ?>

                                    <?php echo e(Form::text('stream_comment',null,array('class'=>'form-control','placeholder'=>__('Enter Comment on Account'),'required'=>'required'))); ?>

                                </div>
                            </div>
                            <input type="hidden" name="log_type" value="account comment">
                            <div style="display:none;" class="col-12 mb-3 field" data-name="attachments">
                                <div class="attachment-upload">
                                    <div class="attachment-button">
                                        <div class="pull-left">
                                            <?php echo e(Form::label('attachment',__('Attachment'))); ?>

                                            <?php echo e(Form::file('attachment',array('class'=>'form-control'))); ?>

                                        </div>
                                    </div>
                                    <div class="attachments"></div>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <div class="w-100 mt-3 text-right">
                                    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?>

                                </div>
                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
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
                                        <?php if($remark->data_id == $account->id): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-parent-child">
                                                </div>
                                                <div class="flex-fill ml-3">
                                                    <div class="h6 text-sm mb-0">
                                                     
                                                    <small class="float-right text-muted"><?php echo e($stream->created_at->diffForHumans()); ?> 
                                                            
                                        <span style="color:#61c7f6 !important;"> (<?php echo e($stream->created_at->isoFormat('DD-MMM-YYYY')); ?> - <?php echo e($stream->created_at->format('g:i A')); ?>)</span>
                                                          
                                                    </small>
                                                    
                                                    </div>
                                                    <span class="text-sm lh-140 mb-0">
                                                     <h6 class="commentuser">
                                                        <?php echo e($remark->owner_name); ?>  
                                                     </h6> 
                                                </span>
                                                <p class="commentdiv">
                                                        <?php echo e($remark->stream_comment); ?> 
                                                </p>    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--stream edit end-->

            <!--account contact -->
            <div id="accountcontact" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Contacts')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('contact.create',['account',$account->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Contact')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Email')); ?></th>
                                        <th scope="col" class="sort" data-sort="status"><?php echo e(__('Phone')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('City')); ?></th>
                                        <?php if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact')): ?>
                                            <th scope="col"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('contact.show',$contact->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Contact Details')); ?>" class="action-item">
                                                    <?php echo e($contact->name); ?>

                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#"><?php echo e($contact->email); ?></a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                    <?php echo e($contact->phone); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot"><?php echo e($contact->contact_city); ?></span>
                                            </td>
                                            <?php if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact')): ?>
                                            <td>
                                                <div class="d-flex">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Contact')): ?>
                                                    <a href="#" data-size="lg" data-url="<?php echo e(route('contact.show',$contact->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Contact Details')); ?>" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Contact')): ?>
                                                    <a href="<?php echo e(route('contact.edit',$contact->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Contact Edit')); ?>"><i class="far fa-edit"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Contact')): ?>
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($contact->id); ?>').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $contact->id],'id'=>'delete-form-'.$contact ->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                </div>
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
            <!--account contact end-->

            <!--account opportunities -->
            <div id="accountopportunities" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Deals')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.create',['account',$account->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Add Deal to Account')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col" class="sort" data-sort="status"><?php echo e(__('Deal Stage')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Amount')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                                        <?php if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities')): ?>
                                            <th scope="col"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $opportunitiess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opportunities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.show', $opportunities->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Opportunities Details')); ?>" class="action-item">
                                                    <?php echo e($opportunities->name); ?>

                                                </a>
                                            </td>

                                            <td>
                                                <span class="badge badge-dot">
                                                    <?php echo e(!empty($opportunities->stages)?$opportunities->stages->name:'-'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot"><?php echo e(\Auth::user()->priceFormat($opportunities->amount)); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot"><?php echo e(!empty($opportunities->assign_user)?$opportunities->assign_user->name:'-'); ?></span>
                                            </td>
                                            <?php if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities')): ?>
                                            <td>
                                                <div class="d-flex">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Opportunities')): ?>
                                                    <a href="#" data-size="lg" data-url="<?php echo e(route('opportunities.show', $opportunities->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Opportunities Details')); ?>" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Opportunities')): ?>
                                                    <a href="<?php echo e(route('opportunities.edit',$opportunities->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" class="action-item" data-title="<?php echo e(__('Opportunities Edit')); ?>"><i class="far fa-edit"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Opportunities')): ?>
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($opportunities->id); ?>').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id],'id'=>'delete-form-'.$opportunities ->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                </div>
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
            <!--account opportunities end-->

            <!--account cases -->
            <div id="accountcases" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Cases')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.create',['account',$account->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Common Case')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Number')); ?></th>
                                        <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Priority')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Created At')); ?></th>
                                        <?php if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase')): ?>
                                            <th scope="col"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $cases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $case): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.show',$case->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Cases Details')); ?>" class="action-item">
                                                    <?php echo e($case->name); ?>

                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#"><?php echo e($case->number); ?></a>
                                            </td>
                                            <td>
                                                <?php if($case->status == 0): ?>
                                                    <span class="badge badge-success"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php elseif($case->status == 1): ?>
                                                    <span class="badge badge-info"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php elseif($case->status == 2): ?>
                                                    <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php elseif($case->status == 3): ?>
                                                    <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php elseif($case->status == 4): ?>
                                                    <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php elseif($case->status == 5): ?>
                                                    <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$status[$case->status])); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($case->priority == 0): ?>
                                                    <span class="badge badge-primary"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                                                <?php elseif($case->priority == 1): ?>
                                                    <span class="badge badge-info"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                                                <?php elseif($case->priority == 2): ?>
                                                    <span class="badge badge-warning"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                                                <?php elseif($case->priority == 3): ?>
                                                    <span class="badge badge-danger"><?php echo e(__(\App\CommonCase::$priority[$case->priority])); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($case->created_at->diffForHumans())); ?></span>
                                            </td>
                                            <?php if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase')): ?>
                                            <td>
                                                <div class="d-flex">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show CommonCase')): ?>
                                                    <a href="#" data-size="lg" data-url="<?php echo e(route('commoncases.show',$case->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Cases Details')); ?>" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit CommonCase')): ?>
                                                    <a href="<?php echo e(route('commoncases.edit',$case->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Cases Edit')); ?>"><i class="far fa-edit"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete CommonCase')): ?>
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($case->id); ?>').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id],'id'=>'delete-form-'.$case ->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                </div>
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
            <!--account cases end-->

            <!--account Documents -->
            <div id="accountdocuments" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Documents')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('document.create',['account',$account->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Documents')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col" class="sort" data-sort="budget"><?php echo e(__('File')); ?></th>
                                        <th scope="col" class="sort" data-sort="status"><?php echo e(__('Status')); ?></th>
                                        <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Created At')); ?></th>
                                        <?php if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document')): ?>
                                            <th scope="col"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Document Details')); ?>" class="action-item">
                                                    <?php echo e($document->name); ?></a>
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
                                                <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($document->created_at->diffForHumans())); ?></span>
                                            </td>
                                            <?php if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document')): ?>
                                            <td>
                                                <div class="d-flex">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Document')): ?>
                                                    <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Document Details')); ?>" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Document')): ?>
                                                    <a href="<?php echo e(route('document.edit',$document->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Document Edit')); ?>"><i class="far fa-edit"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Document')): ?>
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($document->id); ?>').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id],'id'=>'delete-form-'.$document ->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                </div>
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
            <!--account Documents end-->

            <!--account Tasks -->
            <div id="accounttasks" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e(__('Tasks')); ?></h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('task.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Task')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <!-- Files -->
                        <div class="mb-3">

                            <table class="table align-items-center dataTable">
                                <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Parent')); ?></th>
                                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Stage')); ?></th>
                                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Date Start')); ?></th>
                                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                                    <?php if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task')): ?>
                                        <th scope="col"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                                </thead>
                                <tbody class="list">
                                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="#" data-size="lg" data-url="<?php echo e(route('task.show',$task->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Task Details')); ?>" class="action-item">
                                                <?php echo e($task->name); ?>

                                            </a>
                                        </td>
                                        <td class="budget">
                                            <a href="#"><?php echo e($task->parent); ?></a>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(!empty($task->stages)?$task->stages->name:''); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($task->start_date)); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(!empty($task->assign_user)?$task->assign_user->name:'-'); ?></span>
                                        </td>
                                        <?php if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task')): ?>
                                        <td>
                                            <div class="d-flex">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Task')): ?>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('task.show',$task->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Task Details')); ?>" class="action-item">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Task')): ?>
                                                <a href="<?php echo e(route('task.edit',$task->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit Task')); ?>"><i class="far fa-edit"></i></a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Task')): ?>
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($task->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id],'id'=>'delete-form-'.$task ->id]); ?>

                                                <?php echo Form::close(); ?>

                                                <?php endif; ?>
                                            </div>
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
            <!--account Tasks end-->

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>

    <script>

        $(document).on('change', 'select[name=parent]', function () {
            console.log('click');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log('getparent', bid);
            $.ajax({
                url: '<?php echo e(route('task.getparent')); ?>',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function (data) {
                    console.log('get data');
                    console.log(data);
                    $('#parent_id').empty();
                    

                    $.each(data, function (key, value) {
                        $('#parent_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#parent_id').empty();
                    }
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('click', '#billing_data', function () {
            console.log('hi');
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/account/edit.blade.php ENDPATH**/ ?>