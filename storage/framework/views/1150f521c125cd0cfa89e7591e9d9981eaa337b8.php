<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Login')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


    <div class="login segments-page2">
        <div class="container">
            <div class="section-title" style="margin-top:100px;margin-bottom:50px;	">
                
 
                <h1 style="display:none"><?php echo e(env('COMPANY_NAME')); ?></h1>
               
            </div>
            <div class="col-sm-8 col-lg-4">
                
                <div class="zindex-100 mb-0">
                    <div class="px-md-5 py-5">
                        
                        <style>

                            #loginform .email{
                                background-color:purple !important;
                            }
                            .loginform input[type='text'], input[type='email'], input[type='password'] {
    height: 60px !important;
    border-radius: 5px !important;
    font-size: 19px !important;
    background-color:purple !important;
}
                        </style>
                        <span class="clearfix"></span>
                        <?php echo e(Form::open(array('route'=>'login','method'=>'post','id'=>'loginForm','class'=> 'loginform' ))); ?>

                        
                            
 
                                <?php echo e(Form::text('email',null,array('class' => 'email','placeholder'=>__('Enter Your Emails')))); ?>

                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-email text-danger" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                           
                       
                               
                                <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Your Password')))); ?>

                                
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-password text-danger" role="alert">
                                            <strong><?php echo e($message); ?></strong>
                                </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                           
                       
                        <div class="form-group">
                            <input type="submit" class="btn btn-sm btn-icon round rounded-pill text-white" id="saveBtn" onclick="showhidediv('loadingdiv');" />
                          
                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
  function showhidediv(divid){
 $('#'+ divid).toggle();
  }
</script>
 
<?php $__env->stopSection(); ?>
    

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsback/resources/views/auth/login.blade.php ENDPATH**/ ?>