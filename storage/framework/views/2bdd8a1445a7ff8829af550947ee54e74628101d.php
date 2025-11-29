<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Reset Password')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="w-100">
        <div class="row justify-content-center">
           
            <div class="col-sm-8 col-lg-5 col-xl-4">
                <div class="row justify-content-center mb-3">
                    <a  style="color:#ffffff;" class="navbar-brand" href="#">
                        efloQ
                      
                    </a>
                </div>
                <div class="card shadow zindex-100 mb-0">
                    <div class="card-body px-md-5 py-5">
                        <div class="mb-5">
                            <h6 class="h3"><?php echo e(_('Password Reset')); ?></h6>
                            <p class="text-muted mb-0"><?php echo e(_('Enter your email below to proceed.')); ?></p>
                        </div>
                        <?php if(session('status')): ?>
                            <small class="text-muted"><?php echo e(session('status')); ?></small>
                        <?php endif; ?>
                        <span class="clearfix"></span>
                        <?php echo e(Form::open(array('route'=>'password.email','method'=>'post','id'=>'loginForm'))); ?>

                        <div class="form-group">
                            <?php echo e(Form::label('email',__('Email'),array('class' => 'form-control-label') )); ?>

                            <div class="input-group input-group-merge">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Your Email')))); ?>

                            </div>
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
                        </div>
                        <div class="mt-4">
                            <?php echo e(Form::submit(__(' Forgot Password'),array('class'=>'btn btn-sm btn-primary btn-icon rounded-pill','id'=>'saveBtn'))); ?>

                        </div>
                        <?php echo e(Form::close()); ?>

                    </div>
                    <div class="card-footer px-md-5"><small>Back to?</small>
                        <a href="<?php echo e(url('login',$lang)); ?>" class="small font-weight-bold">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>