<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_favicon=Utility::getValByName('company_favicon');
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="efloQ - CRM">
    <meta name="author" content="Rajodiya Infotech">
    <title><?php echo e((Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'SalesGo')); ?> - <?php echo $__env->yieldContent('page-title'); ?></title>
    <link rel="icon" href="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" type="image" sizes="16x16">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/@fortawesome/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/animate.css/animate.min.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/select2/dist/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/site.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/ac.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/jquery.dataTables.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('css/site-'.Auth::user()->mode.'.css')); ?>" id="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" id="stylesheet')}}">
    <link id="themecss" rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/js/all.min.css')); ?>"/>
    <script type="text/javascript" src="<?php echo e(asset('assets/js/jquery-1.11.1.min.js')); ?>"></script>
    <link href="<?php echo e(asset('assets/flag-icon-css-master/css/flag-icon.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldPushContent('css-page'); ?>
  
</head>
<?php /**PATH /Applications/MAMP/htdocs/efloq/resources/views/partials/admin/head.blade.php ENDPATH**/ ?>