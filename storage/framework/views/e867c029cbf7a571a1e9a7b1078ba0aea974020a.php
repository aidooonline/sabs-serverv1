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

            <?php echo e(Form::open(array('url'=>'accounts','method'=>'post','enctype'=>'multipart/form-data'))); ?>

            <?php echo csrf_field(); ?>  
 
            <div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Account Number</label>
                        <input readonly  id="accountnumbergen" name="account_number" class="form-control" type="text" value="" />
                        
                        <input style="display:none" readonly id="userid" name="__id__" class="form-control" type="text" value="" />
                        
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
                        <?php echo e(Form::text('middle_name',null,array('class'=>'form-control'))); ?>

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
                       
                        <img id="img" src="" class="image round-pill"  style="border-radius:14px 14px;" />
                     
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
                        <?php echo e(Form::text('sec_phone_number',null,array('class'=>'form-control','placeholder'=>__('')))); ?>

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
                        <?php echo Form::date('date_of_birth2', null,array('class' => 'form-control','required'=>'required')); ?>

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
                        <?php echo e(Form::text('email',null,array('class'=>'form-control','required'=>'required'))); ?>

      
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
                    <label>Customer Picture</label>
                     
                    </div>
                    <div class="form-group">
                        
                        
                       
	<div id="camerascript">
        
<script type="text/javascript" src="https://banqpopulaire.com/cam/webcam.min.js"></script>
        <div id="my_camera" style="width:100%; height:340px;"></div>
        <div id="my_result"></div>
        
        <script language="JavaScript">
            Webcam.set('constraints',{
                facingMode: "environment",
                width: 320,
			height: 320,
			image_format: 'jpeg',
			jpeg_quality: 90
            });
            Webcam.attach( '#my_camera' );
        
            function take_snapshot() {
                Webcam.snap( function(data_uri) {
                    document.getElementById('my_result').innerHTML = '<img style="width:200px;height:auto;" src="'+data_uri+'"/>';
                    
                    $('#customer_picture').val(data_uri);
                } );
            }

            function sendconfirmationcode(){
                let isvalid = validatephonenumber($('#phone_number').val());

                if(isvalid){
                    let confcoder = generateconfirmcode();
                $('#confirmationcode').val(confcoder);
                sndmsg($('#phone_number').val(),'Dear ' + $('#first_name').val() + ' ' + $('#surname').val() + ' ,Welcome to GCI susu. Your new Account Number is : ' + $('#accountnumbergen').val() +  '\n \n Kindly use the following code to confirm your registration: ' + confcoder);
 
                }else{
                    alert('Enter Valid Phone Number');
                }
               
            }


            function validatephonenumber(inputtxt)
            {
               let phoneno = /^\d{10}$/;
               if(inputtxt.match(phoneno))
                {
                  return true;
                  }
                 else
                 {
                    
                  return false;
                 }
            }
             
        </script>
        
        <a href="javascript:void(take_snapshot())">Take Snapshot</a>
     </div>
	 
     <a href="#snap" id="snap" class="btn btn-purple" style="padding-left:2px;padding-right:2px;width:50px;height:50px;font-size:20px;" onclick="take_snapshot()"><i class="fas fa-camera"></i></a> 
                      

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
                        <input type="button" id="confirmbutton" onclick="sendconfirmationcode();" class="btn btn-dark" style="width:100%;margin-bottom:15px;" value="Send Confirmation Code" />
                        <input id="phoneconfirmationcodetext" name="confcode" placeholder="Enter Confirmation Code Here.." type="text" class="form-control" />
                   
                        <input style="display:none;" id="confirmationcode" name="confirmationcode" value="" type="text" class="form-control" />
                        <input style="display:none;" id="customer_picture" name="customer_picture"  type="text" class="form-control" />
                   
                    </div>

                    <div class="form-group">
                    <?php echo e(Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill
                    mr-auto'))); ?><?php echo e(Form::close()); ?>

                    </div>
                </div>
 

            </div>


        </div>
        <?php echo e(Form::close()); ?>


    



</div>
</div>
 

<?php echo $__env->make('layouts.listdivstyles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.genrandnumbers', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/accounts/create.blade.php ENDPATH**/ ?>