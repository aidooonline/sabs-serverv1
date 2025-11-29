<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Opportunities')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Opportunities Edit')); ?>  <?php echo e('('. $opportunities->name .')'); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <div class="btn-group" role="group">
        <?php if(!empty($previous)): ?>
            <a href="<?php echo e(route('opportunities.edit',$previous)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php else: ?>
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="<?php echo e(__('Previous')); ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        <?php endif; ?>
        <?php if(!empty($next)): ?>
            <a href="<?php echo e(route('opportunities.edit',$next)); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="<?php echo e(__('Next')); ?>">
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
    <li class="breadcrumb-item"><a href="<?php echo e(route('opportunities.index')); ?>"><?php echo e(__('Opportunities')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Details')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#Opportunities_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Overview')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Edit about your opportunities information')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#account_stream" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-rss"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Stream')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Add stream comment')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountdocuments" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-book-open"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Documents')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned document for this opportunities')); ?></p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accounttasks" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-tasks"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1"><?php echo e(__('Tasks')); ?></a>
                                <p class="mb-0 text-sm"><?php echo e(__('Assigned task for this opportunities')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--Opportunities edit -->
            <div id="Opportunities_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0"><?php echo e(__('Overview')); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo e(Form::model($opportunities,array('route' => array('opportunities.update', $opportunities->id), 'method' => 'PUT'))); ?>

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
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('contact',__('Contact'))); ?>

                                    <?php echo Form::select('contact', $contact, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                    <?php $__errorArgs = ['contacts'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-contacts" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <?php echo e(Form::label('campaign',__('Campaign'))); ?>

                                <?php echo Form::select('campaign', $campaign_id, null,array('class' => 'form-control ','data-toggle'=>'select')); ?>

                                <?php $__errorArgs = ['campaign_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-campaign_id" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-6">
                                <?php echo e(Form::label('stage',__('Stage'))); ?>

                                <?php echo Form::select('stage', $stages, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                <?php $__errorArgs = ['stage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-stage" role="alert">
                                    <strong class="text-danger"><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <?php echo e(Form::label('amount',__('Amount'))); ?>

                                    <?php echo e(Form::number('amount',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-amount" role="alert">
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
                                    <?php echo e(Form::label('probability',__('Probability'))); ?>

                                    <?php echo e(Form::number('probability',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['probability'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-probability" role="alert">
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
                                    <?php echo e(Form::label('close_date',__('Close Date'))); ?>

                                    <?php echo e(Form::date('close_date',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))); ?>

                                    <?php $__errorArgs = ['close_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-close_date" role="alert">
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
                                    <?php echo e(Form::label('lead_source',__('Lead Source'))); ?>

                                    <?php echo Form::select('lead_source', $lead_source, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')); ?>

                                    <?php $__errorArgs = ['lead_source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-lead_source" role="alert">
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

                                    <?php echo Form::textarea('description',null,array('class' =>'form-control ','rows'=>3)); ?>

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
                                <?php echo e(Form::label('user',__('User'))); ?>

                                <?php echo Form::select('user', $user,  $opportunities->user_id,array('class' => 'form-control ','data-toggle'=>'select')); ?>

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
                                <?php echo e(Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))); ?>

                            </div>
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
            </div>
            <!--Opportunities edit end-->

            <!--stream edit -->
            <div id="account_stream" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0"><?php echo e(__('Stream')); ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php echo e(Form::open(array('route' => array('streamstore',['opportunities',$opportunities->name,$opportunities->id]), 'method' => 'post','enctype'=>'multipart/form-data'))); ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <?php echo e(Form::label('stream',__('Stream'))); ?>

                                    <?php echo e(Form::text('stream_comment',null,array('class'=>'form-control','placeholder'=>__('Enter Stream Comment'),'required'=>'required'))); ?>

                                </div>
                            </div>
                            <input type="hidden" name="log_type" value="opportunities comment">

                            <div class="col-12 mb-3 field" data-name="attachments">
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
                                        <?php if($remark->data_id == $opportunities->id): ?>
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-parent-child">
                                                        <img alt="" class="rounded-circle avatar" <?php if(!empty($stream->file_upload)): ?> src="<?php echo e((!empty($stream->file_upload))? asset(Storage::url("upload/profile/".$stream->file_upload)): asset(url("./assets/img/clients/160x160/img-1.png"))); ?>" <?php else: ?>  avatar="<?php echo e($remark->user_name); ?>" <?php endif; ?>>
                                                    </div>
                                                    <div class="flex-fill ml-3">
                                                        <div class="h6 text-sm mb-0"><?php echo e($remark->user_name); ?><small class="float-right text-muted"><?php echo e($stream->created_at); ?></small></div>
                                                        <span class="text-sm lh-140 mb-0">
                                                            <?php echo e(__('posted to')); ?> <a href="#"><?php echo e($remark->title); ?></a> , <?php echo e($stream->log_type); ?> <a href="#"><?php echo e($remark->stream_comment); ?></a>
                                                        </span>
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
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('document.create',['opportunities',$opportunities->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Documents')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col"><?php echo e(__('Action')); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Document Details')); ?>" class="badge badge-dot action-item">
                                                    <?php echo e($document->name); ?>

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
                                                <div class="d-flex">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Document')): ?>
                                                    <a href="#" data-size="lg" data-url="<?php echo e(route('document.show',$document->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Document Details')); ?>" class="action-item">
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
                                                </div>
                                            </td>
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
                                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Assigned')); ?></th>
                                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Stage')); ?></th>
                                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Date Start')); ?></th>
                                    <th scope="col" class="sort" data-sort="completion"><?php echo e(__('Assigned User')); ?></th>
                                    <th scope="col"><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="#" data-size="lg"  data-url="<?php echo e(route('task.show',$task->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Task Details')); ?>" class="badge badge-dot action-item">
                                                <?php echo e($task->name); ?>

                                            </a>
                                        </td>
                                        <td class="budget">
                                            <a href="#" class="badge badge-dot"><?php echo e($task->parent); ?></a>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(!empty($task->taskstages)?$task->taskstages->name:''); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(\Auth::user()->dateFormat($task->start_date)); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot"><?php echo e(!empty($task->assign_user)?$task->assign_user->name:''); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Task')): ?>
                                                <a href="#" data-size="lg" data-url="<?php echo e(route('task.show',$task->id)); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Task Details')); ?>" class="action-item">
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
            console.log('h');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '<?php echo e(route('task.getparent')); ?>',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function (data) {
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/opportunities/edit.blade.php ENDPATH**/ ?>