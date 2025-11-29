<?php $__env->startSection('action-btn'); ?>

<!-- literally user can create accounts -->
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Product')): ?>
<a href="#" data-size="lg" data-url="<?php echo e(route('accounts.create')); ?>" data-ajax-popup="true"
    data-title="<?php echo e(__('Create New Account')); ?>" class="btn btn-sm btn-purple btn-icon-only rounded-circle">
    <i class="fa fa-plus"></i>
</a>
<?php endif; ?>
<?php $__env->stopSection(); ?>





<?php $__env->startSection('title'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row dashboardtext" style="padding-bottom:80px;padding-top:10px;">
    <h4 class="card-title">
       <?php echo e($pagetitle); ?>

      </h4>

 

        <div  style="margin-left:15px;margin-right:15px;padding-top:20px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">
            <?php echo e(Form::model($account,array('route' => array('accounts.update', $account->id), 'method' => 'PUT'))); ?>

            
            <div>
                
                
                
                 <div class="col-12">
                    <div class="form-group">
                        
                        <a href="#" onclick="sendDataToReactNativeApp('<?php echo e($account->id); ?>')" class="rounded-circle" >
                            <?php if($account->user_image == 'true'): ?>
                            
                         
                             <img src="<?php echo e(env('NOBS_IMAGES')); ?>images/user_avatar/avatar_<?php echo e($account->id); ?>.jpg?lastmod=<?php echo date("m/d/Y h:i:s a", time())?>" class="rounded-circle mx-auto d-block" style="height: 150px;width:auto;border:solid 2px white"
  alt="Avatar" /> 
                            <?php else: ?>
                              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/avatar1.png" class="rounded-circle mx-auto d-block" style="width: 150px;border:solid 2px white"
  alt="Avatar" /> 
                             
                            <?php endif; ?>
                            
                          
                        </a>
                        
                         
                        
                        
                        <div style="display:none;" id="datatobeprinted">
                   
        
                        </div>
                       
                        
                
                        
                    </div>
                </div>
                
               
                

                <div class="col-12">
                    <div class="form-group">
                        <label>Account Number</label>
                        
                        <?php echo e(Form::text('account_number',null,array('class'=>'form-control','required'=>'readonly','readonly'))); ?>

                         <?php echo e(Form::hidden('id',null,array('class'=>'form-control'))); ?>

                        
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>First Name</label>
                        <?php echo e(Form::text('first_name',null,array('class'=>'form-control','required'=>'required','id'=>'first_name'))); ?>

                    </div>
                </div>

<input type="text" style="display:none" value="<?php echo e(\Auth::user()->created_by_user); ?>" name="user" />

                <div class="col-12">
                    <div class="form-group">
                        <label>Middle Name</label>
                        <?php echo e(Form::text('middle_name',null,array('class'=>'form-control','id'=>'middle_name'))); ?>

                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Surname</label>
                        <?php echo e(Form::text('surname',null,array('class'=>'form-control','required'=>'required','id'=>'surname'))); ?>

                    </div>
                </div>
              

                
 

 


              
               
                <div class="col-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <?php echo e(Form::text('phone_number',null,array('class'=>'form-control','required'=>'required','id'=>'phone_number'))); ?>

                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Other Phone Number</label>
                        <?php echo e(Form::text('sec_phone_number',null,array('class'=>'form-control'))); ?>

                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label>Occupation</label>
                        <?php echo e(Form::text('occupation',null,array('class'=>'form-control','placeholder'=>__('')))); ?>

                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Residential Address</label>
                        <?php echo e(Form::text('residential_address',null,array('class'=>'form-control','placeholder'=>__('')))); ?>

                    </div>
                </div>

                 

                <div class="col-12">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        
                        <?php echo Form::text('date_of_birth2',null,array('class' => 'form-control','required'=>'required')); ?>

                       
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Postal Address</label>
                        <?php echo e(Form::text('postal_address',null,array('class'=>'form-control'))); ?>

                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Email</label>
                        <?php echo e(Form::text('email',null,array('class'=>'form-control'))); ?>

      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Select Nationality</label>

                        <?php echo Form::select('nationality', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')); ?> 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>ID Number</label>
                        <?php echo e(Form::text('id_number',null,array('class'=>'form-control','required'=>'required'))); ?>

      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin</label>
                        <?php echo e(Form::text('next_of_kin',null,array('class'=>'form-control','required'=>'required'))); ?>

      
                    </div>
                </div>
                

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Id Number</label>
                        <?php echo e(Form::text('next_of_kin_id_number',null,array('class'=>'form-control','required'=>'required'))); ?>

      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Phone Number</label>
                        <?php echo e(Form::text('next_of_kin_phone_number',null,array('class'=>'form-control','required'=>'required'))); ?>

      
                    </div>
                </div>

               
                 
                <div class="col-12">
                    <div class="form-group">
                        <label>Legal Consent:</label>
                        <p style="padding:5px 10px;">I certify that the information provided above is true and I am aware that detection of any false declaration renders my application void.</p>
                        <?php echo e(Form::checkbox('legalconsent',null,array('class'=>'form-control','required'=>'required'))); ?>

      
                    </div>
                </div>


                  
                <div class="col-12" style="margin-bottom:20px;padding-bottom:20px;">

                    

                    <div class="form-group">
                    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill mr-auto'))); ?><?php echo e(Form::close()); ?>

                    </div>
                </div>
 

            </div>


        </div>
        <?php echo e(Form::close()); ?>


    
<script type="text/ja"


</div>
</div>


 

<?php echo $__env->make('layouts.listdivstyles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.genrandnumbers', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/accounts/edit.blade.php ENDPATH**/ ?>