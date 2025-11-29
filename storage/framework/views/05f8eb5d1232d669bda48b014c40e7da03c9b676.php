<!DOCTYPE html>

<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
?>
<html lang="en" class="loading">
<head>
    <meta charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Nobs Micro Credit">
    <meta name="author" content="Stephen Aidoo">
    <title><?php echo $__env->yieldContent('page-title'); ?> - <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'Nobs Micro Credit')); ?></title>
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" type="image" sizes="16x16">
 
    
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
     
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/feather/style.min.css')); ?>"> 
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/simple-line-icons/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/fonts/font-awesome/css/font-awesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/vendors/css/perfect-scrollbar.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/vendors/css/prism.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/nobsdocs/css/app.css')); ?>">
    
   
    
</head>



 
  
  
<body data-col="1-column" class=" 1-column  blank-page blank-page">
  
  <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="wrapper"><!--Login Page Starts-->
<section id="login">

    <div class="container-fluid">
    
        <div class="row full-height-vh">
           
            <div class="col-12 d-flex align-items-center justify-content-center gradient-aqua-marine">
          
                <div class="card px-4 py-2 box-shadow-2 width-400">
                    <div class="card-header text-center">
                      <h4 class="text-uppercase text-bold-600 text-center"><?php echo e(env('COMPANY_NAME')); ?></h4> 
                        
                    </div>
                    <div class="card-body">
                        <div class="card-block">
                            <?php echo e(Form::open(array('route'=>'login','method'=>'post','id'=>'loginForm','class'=> 'loginform' ))); ?>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        
                                        
                                         <?php echo e(Form::text('email',null,array('class' => 'email form-control form-control-lg','placeholder'=>__('Email')))); ?>

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
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                    <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Password')))); ?>

                                
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
                                        
                                         
                                    </div>
                                </div>

                                

                                <div class="form-group">
                                    <div class="text-center col-md-12">
                                     <?php echo e(Form::submit(__('Login'),array('class'=>'btn btn-danger px-4 py-2 text-uppercase white font-small-4 box-shadow-2 border-0','id'=>'saveBtn'))); ?>

                                      
                                        
                                    </div>
                                </div>
                              <?php echo e(Form::close()); ?>

                        </div>
                    </div>
                     
                </div>
            </div>
        </div>
    </div>
</section>
<!--Login Page Ends-->
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->


</body>
 


 <!-- BEGIN VENDOR JS-->
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/core/jquery-3.3.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/core/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/core/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/perfect-scrollbar.jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/prism.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/jquery.matchHeight-min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/screenfull.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/vendors/js/pace/pace.min.js')); ?>"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN CONVEX JS-->
    <script src="<?php echo e(asset('assets/nobsdocs/js/app-sidebar.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/nobsdocs/js/notification-sidebar.js')); ?>"></script>
    <!-- END CONVEX JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <!-- END PAGE LEVEL JS-->

<div class="hiddendiv common"></div>

<div id="lightboxOverlay" class="lightboxOverlay" style="display: none;"></div>

<div id="lightbox" class="lightbox" style="display: none;"><div class="lb-outerContainer"><div class="lb-container"><img class="lb-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="><div class="lb-nav"><a class="lb-prev" href=""></a><a class="lb-next" href=""></a></div><div class="lb-loader"><a class="lb-cancel"></a></div></div></div><div class="lb-dataContainer"><div class="lb-data"><div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div><div class="lb-closeContainer"><a class="lb-close"></a></div></div></div></div>


</html>
<?php /**PATH /home/banqgego/public_html/nobs001/resources/views/layouts/auth.blade.php ENDPATH**/ ?>