<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> name); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Account name')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($lead->accounts)?$lead->accounts->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Email')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> email); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Phone')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> phone); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Title')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> title); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Website')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> website); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('lead Address')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> lead_address); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">City</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> lead_city); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">State</span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e($lead-> lead_state); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">Country</span></dt>

                    <?php
                    $leadcountry =\App\Country::getcountry($lead->lead_country);
                    $leadiso =\App\Country::getcountryiso($lead->lead_country);
                    ?>
                   
                    <dd class="col-sm-8">
                        <span class="flag-icon flag-icon-<?php echo e(strtolower($leadiso[0])); ?>"></span>
                        <span class="text-sm"><?php echo e($leadcountry[0]); ?></span>
                    </dd>

                  
                    <div class="col-12">
                        <hr class="mt-2 mb-2">
                        <h5><?php echo e(__('Details')); ?></h5>
                    </div>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Status')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm">
                            <?php if($lead  ->status == 0): ?>
                                <span class="badge badge-success"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php elseif($lead->status == 1): ?>
                                <span class="badge badge-info"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php elseif($lead->status == 2): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php elseif($lead->status == 3): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php elseif($lead->status == 4): ?>
                                <span class="badge badge-danger"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php elseif($lead->status == 5): ?>
                                <span class="badge badge-warning"><?php echo e(__(\App\Lead::$status[$lead->status])); ?></span>
                            <?php endif; ?>
                        </span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Source')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($lead->LeadSource)?$lead->LeadSource->name:''); ?></span></dd>

                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(\Auth::user()->priceFormat($lead->opportunity_amount)); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Campaign')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($lead->campaigns)?$lead->campaigns->name:'-'); ?></span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0"><?php echo e(__('Industry')); ?></span></dt>
                    <dd class="col-sm-8"><span class="text-sm"><?php echo e(!empty($lead->accountIndustry)?$lead->accountIndustry->name:''); ?></span></dd>


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
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(!empty($lead->assign_user)?$lead->assign_user->name:''); ?></span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0"><?php echo e(__('Created')); ?></span></dt>
                            <dd class="col-sm-12"><span class="text-sm"><?php echo e(\Auth::user()->dateFormat($lead->created_at)); ?></span></dd>
                        </div>
                    </li>
                </ul>

                <div >

                    <?php if($lead->call_made): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="<?php echo e($lead->call_made === 1 ? true :false); ?>"  <?php echo e($lead->call_made === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label" for="flexCheckDefault">
                              Call Made
                            </label>
                          </div>
                       
                    </div>
                    <?php endif; ?>
                    
                    <?php if($lead->mail_sent): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input  type="checkbox" value="<?php echo e($lead->mail_sent === 1 ? true :false); ?>"  <?php echo e($lead->mail_sent === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label">
                             Mail Sent
                            </label>
                          </div>
                    </div>
                    <?php endif; ?>


                    <?php if($lead->visited_site): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input     type="checkbox" value="<?php echo e($lead->visited_site === 1 ? true :false); ?>"  <?php echo e($lead->visited_site === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label" >
                            Visited Site
                            </label>
                          </div>
                    </div>
                    <?php endif; ?>

                    <?php if($lead->offer_letter): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input      type="checkbox" value="<?php echo e($lead->offer_letter === 1 ? true :false); ?>"  <?php echo e($lead->offer_letter === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label">
                            Offer Letter Sent
                            </label>
                          </div>
                    </div>
                    <?php endif; ?>

                    <?php if($lead->contract): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="<?php echo e($lead->contract === 1 ? true :false); ?>"  <?php echo e($lead->contract === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label">
                           Contract Sent
                            </label>
                          </div>
                    </div>
                    <?php endif; ?>

                    <?php if($lead->payment): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input     type="checkbox" value="<?php echo e($lead->payment === 1 ? true :false); ?>"  <?php echo e($lead->payment === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label">
                            Payment Made
                            </label>
                          </div>
                    </div>
                    <?php endif; ?>

                    <?php if($lead->receipt): ?>
                    <div class="form-group"> 
                        <div class="form-check">
                            <input    type="checkbox" value="<?php echo e($lead->receipt === 1 ? true :false); ?>"  <?php echo e($lead->receipt === 1 ? 'checked' :''); ?> >
                            <label class="form-check-label" >
                            Receipt Sent
                            </label>
                          </div>
                    </div>
                    <?php endif; ?> 

                    
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Lead')): ?>
            <a href="<?php echo e(route('lead.edit',$lead->id)); ?>" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="<?php echo e(__('Lead Edit')); ?>"><i class="far fa-edit"></i>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    input[type=checkbox][disabled]{
color:darkolivegreen !important; // or whatever
}

label{
    font-size:13px !important;
}
    </style><?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/lead/view.blade.php ENDPATH**/ ?>