<!DOCTYPE html>
<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SalesGo Saas- Business Sales CRM">
    <meta name="author" content="Rajodiya Infotech">
    <title><?php echo $__env->yieldContent('page-title'); ?> - <?php echo e((Utility::getValByName('header_text')) ? Utility::getValByName('header_text') : config('app.name', 'efloQ')); ?></title>
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" type="image" sizes="16x16">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')); ?> ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" type="text/css">
</head>
<body class="application application-offset">
<div class="container-fluid container-application">
    <div class="main-content position-relative">
        <div class="page-content">
            <div class="min-vh-100 py-5 d-flex align-items-center">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
</div>

</body>
<script src="<?php echo e(asset('assets/js/site.core.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/site.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/demo.js')); ?>"></script>
</html>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/layouts/auth.blade.php ENDPATH**/ ?>