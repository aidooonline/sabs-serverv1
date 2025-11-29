<?php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $favicon=Utility::getValByName('company_favicon');
?>

    <!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CRMGo SaaS - Projects, Accounting, Leads, Deals & HRM Tool">
    <meta name="author" content="Rajodiya Infotech">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" href="<?php echo e(asset(Storage::url('uploads/logo/favicon.png'))); ?>" type="image" sizes="16x16">
    <title><?php echo e(__('Form')); ?> &dash; <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'LeadGo')); ?>

        <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'CRMGo')); ?>

    </title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/site-light.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>" type="text/css">

</head>


<body class="application application-offset">
<div class="container-fluid container-application">
    <div class="main-content position-relative">
        <div class="page-content">
            <div class="min-vh-100 py-5 d-flex align-items-center">
                <div class="w-100">
                    <div class="row justify-content-center">
                        <div class="col-sm-8 col-lg-5">
                            <div class="row justify-content-center mb-3">
                                <a class="navbar-brand" href="#">
                                    <img src="<?php echo e(asset(Storage::url('uploads/logo/logo.png'))); ?>" class="auth-logo" width="250">
                                </a>
                            </div>
                            <div class="card shadow zindex-100 mb-0">
                                <?php if($form->is_active == 1): ?>
                                    <?php echo e(Form::open(array('route'=>array('form.view.store'),'method'=>'post'))); ?>

                                    <div class="card-body px-md-5 py-5">
                                        <div class="mb-4">
                                            <h6 class="h3"><?php echo e($form->name); ?></h6>
                                        </div>
                                        <input type="hidden" value="<?php echo e($code); ?>" name="code">
                                        <div class="form-group">
                                            <div class="d-flex radio-check">
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="lead_on" value="lead" name="is_convert" class="custom-control-input lead_radio">
                                                    <label class="custom-control-label form-control-label" for="lead_on"><?php echo e(__('Lead')); ?></label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="account_on" value="account" name="is_convert" class="custom-control-input lead_radio" >
                                                    <label class="custom-control-label form-control-label" for="account_on"><?php echo e(__('Account')); ?></label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="contact_on" value="contact" name="is_convert" class="custom-control-input lead_radio">
                                                    <label class="custom-control-label form-control-label" for="contact_on"><?php echo e(__('Contact')); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($objFields && $objFields->count() > 0): ?>
                                            <?php $__currentLoopData = $objFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $objField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($objField->type == 'text'): ?>
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label'])); ?>

                                                        <?php echo e(Form::text('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id))); ?>

                                                    </div>
                                                <?php elseif($objField->type == 'email'): ?>
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label'])); ?>

                                                        <?php echo e(Form::email('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id))); ?>

                                                    </div>
                                                <?php elseif($objField->type == 'number'): ?>
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label'])); ?>

                                                        <?php echo e(Form::number('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id))); ?>

                                                    </div>
                                                <?php elseif($objField->type == 'date'): ?>
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label'])); ?>

                                                        <?php echo e(Form::date('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id))); ?>

                                                    </div>
                                                <?php elseif($objField->type == 'textarea'): ?>
                                                    <div class="form-group">
                                                        <?php echo e(Form::label('field-'.$objField->id, __($objField->name),['class'=>'form-control-label'])); ?>

                                                        <?php echo e(Form::textarea('field['.$objField->id.']', null, array('class' => 'form-control','required'=>'required','id'=>'field-'.$objField->id,'rows'=>'3'))); ?>

                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                        <div class="mt-4">
                                            <?php echo e(Form::submit(__('Submit'),array('class'=>'btn btn-sm btn-primary btn-icon rounded-pill'))); ?>

                                        </div>
                                    </div>

                                    <?php echo e(Form::close()); ?>

                                <?php else: ?>
                                    <div class="page-title"><h5><?php echo e(__('Form is not active.')); ?></h5></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo e(asset('assets/js/site.core.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/site.js')); ?>"></script>
<script src="<?php echo e(asset('assets/libs/bootstrap-notify/bootstrap-notify.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/demo.js')); ?>"></script>
<script>
    function toastrs(title, message, type) {
        var o, i;
        var icon = '';
        var cls = '';
        if (type == 'success') {
            icon = 'fas fa-check-circle';
            cls = 'success';
        } else {
            icon = 'fas fa-times-circle';
            cls = 'danger';
        }
        $.notify({icon: icon, title: " " + title, message: message, url: ""}, {
            element: "body",
            type: cls,
            allow_dismiss: !0,
            placement: {from: 'top', align: 'right'},
            offset: {x: 15, y: 15},
            spacing: 10,
            z_index: 1080,
            delay: 2500,
            timer: 2000,
            url_target: "_blank",
            mouse_over: !1,
            animate: {enter: o, exit: i},
            template: '<div class="alert alert-{0} alert-icon alert-group alert-notify" data-notify="container" role="alert"><div class="alert-group-prepend alert-content"><span class="alert-group-icon"><i data-notify="icon"></i></span></div><div class="alert-content"><strong data-notify="title">{1}</strong><div data-notify="message">{2}</div></div><button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
        });
    }
</script>
<?php if(Session::has('success')): ?>
    <script>
        toastrs('<?php echo e(__('Success')); ?>', '<?php echo session('success'); ?>', 'success');
    </script>
    <?php echo e(Session::forget('success')); ?>

<?php endif; ?>
<?php if(Session::has('error')): ?>
    <script>
        toastrs('<?php echo e(__('Error')); ?>', '<?php echo session('error'); ?>', 'error');
    </script>
    <?php echo e(Session::forget('error')); ?>

<?php endif; ?>
</body>
</html>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/form_builder/form_view.blade.php ENDPATH**/ ?>