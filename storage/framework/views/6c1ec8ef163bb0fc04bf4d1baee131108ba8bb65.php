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

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 <?php echo $__env->make('layouts.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="mainsearchdiv">
     
<?php echo $__env->make('loans.savingspartial_alltotal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__currentLoopData = $loansaccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <button id="accountbtnpanel_<?php echo e($loans->id); ?>" onclick="dataapi('<?php echo e($loans->id); ?>',this);" class="accordion card listdiv" style="height:60px;background:#fff;width:99%;padding-left:20px;padding-right:20px;margin-left:0;margin-right:0;">

        <table>
            <tr>
                
                <td  style="text-align:left; ">
                    <div style="text-align:left;padding-top:1px !important;">
                        <h6 class="account_name" style="padding-top:1px !important;">
                            <?php echo e($loans->account_name); ?></h6>
                    
                           
                    </div>
                </td>
            </tr>
        </table>

    </button>
   
 

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<style>

table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .account_name{
        color:#666666;text-align:left !important;font-weight:bold;font-family:verdana;
    }

    .table-panel td{
        font-size:1em !important;
        color:rgb(65, 6, 65);
        font-family:Verdana, Geneva, Tahoma, sans-serif;
    }
    .accordion img {
        width: 65px;
        height: 65px;
    }

    .listdiv  {
        width: 25%;
        height: 120px;

         
    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;

        
    }

    .listdiv .listdiv img {
        width: 70px;
        height: 70px;
    }

    .listdiv .listdiv .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv .listdiv .text a,
    .listdiv .listdiv .text span {
        float: left;
        color: purple;
    }


    .listdiv2{
        height:auto !important;
        height:600px;
    }

    .listdiv2 .listdiv2 .image {
        width: 25%;
        height: auto;
 
    }

    .listdiv2 .listdiv2 img {
        width: 70px;
        height: 70px;
    }

    .listdiv2 .listdiv2 .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv2 .listdiv2 .text a,
    .listdiv2 .listdiv2 .text span {
        float: left;
        color: purple;
    }


</style>







<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/loans/index.blade.php ENDPATH**/ ?>