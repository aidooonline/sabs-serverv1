 

 


 
<?php $__env->startSection('title'); ?>
 
<?php $__env->stopSection(); ?>
 
<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 <?php echo $__env->make('layouts.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="mainsearchdiv">
     
    <a data-size="lg" data-url="<?php echo e(env('BASE_URL')); ?>user/create" data-ajax-popup="true" class="btn btn-purple" href="#" style="position:absolute;right:10px;"><i class="fas fa-plus-circle"></i></a>
    <h4 class="card-title" style="margin-top:20px;">
        Users
      </h4>
    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

     
 <button id="accountbtnpanel_<?php echo e($account->id); ?>"  class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">
 
        <table>
            <tr>
                <td width="23%">
                    <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="<?php echo e(env('NOBS_IMAGES')); ?>/profileimage.png" profilevalue="<?php echo e($account->customer_picture); ?>"></div>
                </td>
                <td width="77%" style="text-align:left; ">
                    <div style="text-align:left;padding-top:1px !important;">
                        <h6 class="account_name" style="padding-top:1px !important;">
                            <?php echo e($account->name); ?></h6>
                    <h6 style="color:#724c78;text-align:left !important;"><?php echo e($account->phone); ?></h6>
                    
                    <?php echo e($account->email); ?> -- <?php echo e($account->residential_address); ?>

                           
                    </div>
                </td>
                <td><span class="rounded bg-dark pr-2 pl-2 text-white"><?php echo e($account->type); ?></span></td>
            </tr>
        </table>
</button>
     <?php echo $__env->make("agents.accountspartial_singletotal", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    


    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
</div>
<style>

#agenttablstats td{
    padding:0 0 !important;
    margin-left:0 !important;
}


#agenttablstats td 

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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/agents/index.blade.php ENDPATH**/ ?>