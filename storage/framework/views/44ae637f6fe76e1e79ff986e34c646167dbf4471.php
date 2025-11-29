<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Lead')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Leads')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Lead')); ?></li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-btn'); ?>
    <a href="<?php echo e(route('lead.grid')); ?>" class="btn btn-sm btn-primary bor-radius ml-4">
        <?php echo e(__('Kanban View')); ?>

    </a>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Lead')): ?>
        <a href="#" data-size="lg" data-url="<?php echo e(route('lead.create',['lead',0])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Lead')); ?>" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
            <table class="align-items-left dataTable table-sm table-striped table-hover table-light">
                <thead>
                <tr >
                    
                    <th scope="col" style="padding-left:15px !important;" class="sort" data-sort="created_at"><?php echo e(__('Datetime')); ?></th>
                    <th scope="col" class="sort" style="width:40px !important;" data-sort="lead_temperature"><?php echo e(__('Response')); ?></th>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Name')); ?></th>
                    <th scope="col" class="sort" data-sort="country"><?php echo e(__('Ctry')); ?></th>
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Status')); ?></th>
                   
                    <th scope="col" class="sort" data-sort="budget"><?php echo e(__('Email')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Phone')); ?></th>
                    <th scope="col" class="sort" data-sort="status"><?php echo e(__('Assigned user')); ?></th>
                    <?php if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead')): ?>
                        <th scope="col" class="text-right"><?php echo e(__('Action')); ?></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                       
                                <td title="<?php echo e(\Auth::user()->dateFormat($lead->created_at)); ?>" style="padding-left:15px !important;"> 
                           <?php echo e($lead->created_at->diffForHumans()); ?>  
                        </td>
                        <td>
                            <span class=<?php if($lead->lead_temperature == '1'): ?><?php echo e("coldlead"); ?><?php elseif($lead->lead_temperature == '2'): ?><?php echo e("warmlead"); ?><?php else: ?><?php echo e("hotlead"); ?> <?php endif; ?>>
                                <?php if($lead->lead_temperature == '1'): ?><?php echo e('cold'); ?>

                                <?php elseif($lead->lead_temperature == '2'): ?><?php echo e('warm'); ?>

                                <?php else: ?><?php echo e('hot'); ?> 
                                <?php endif; ?>
                            </span>
                        </td>
                        <?php
                        $leadcountry =\App\Country::getcountry($lead->lead_country);
                        $leadiso =\App\Country::getcountryiso($lead->lead_country); 
                        ?>
                       
                      
                        <td>
                            <a href="#" data-size="lg" data-url="<?php echo e(route('lead.show',$lead->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Lead Details')); ?>" class="action-item">
                                <?php echo e(ucfirst($lead->name)); ?>

                            </a>
                        </td>

                        <td>
                            <span class="badge badge-dot">
                                <span title="<?php echo e($leadcountry[0]); ?>" class="flag-icon flag-icon-<?php echo e(strtolower($leadiso[0])); ?>"></span>
                                
                            </span>
                        </td>

                        <td>
                            <?php if($lead  ->status == 0): ?>
                            <span class="badge text-success" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php elseif($lead->status == 1): ?>
                            <span class="badge text-info" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php elseif($lead->status == 2): ?>
                            <span class="badge text-warning" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php elseif($lead->status == 3): ?>
                            <span class="badge text-danger"  style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php elseif($lead->status == 4): ?>
                            <span class="badge text-danger" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php elseif($lead->status == 5): ?>
                            <span class="badge text-warning" style="font-size:13px;"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                        <?php endif; ?>
                        </td>
                       
                        <td class="budget">
                            <a href="#" class="badge badge-dot"><?php echo e($lead->email); ?></a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                <?php echo e($lead->phone); ?>

                            </span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm"><?php echo e(ucfirst(!empty($lead->assign_user)?$lead->assign_user->name:'')); ?></span></span>
                        </td>
                        <?php if(Gate::check('Show Lead') || Gate::check('Edit Lead') || Gate::check('Delete Lead')): ?>
                            <td class="text-right" style="padding-right:15px !important;">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Show Lead')): ?>
                                    <a href="#" data-size="lg" data-url="<?php echo e(route('lead.show',$lead->id)); ?>" data-ajax-popup="true" data-toggle="tooltip" data-original-title="<?php echo e(__('Details')); ?>" data-title="<?php echo e(__('Lead Details')); ?>" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Lead')): ?>
                                    <a href="<?php echo e(route('lead.edit',$lead->id)); ?>" class="action-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>" data-title="<?php echo e(__('Edit Lead')); ?>"><i class="far fa-edit"></i></a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Lead')): ?>
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($lead->id); ?>').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['lead.destroy', $lead->id],'id'=>'delete-form-'.$lead ->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/lead/index.blade.php ENDPATH**/ ?>