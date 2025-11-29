 

 


 
<?php $__env->startSection('title'); ?>
 
<?php $__env->stopSection(); ?>
 
<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 <?php echo $__env->make('layouts.searchrefund', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="mainsearchdiv">
    <h4 class="card-title" style="margin-top:20px;">
       Search Transaction ID to <span class="text-warning">Reverse</span>
      </h4>

<div><?php echo e($depositer); ?></div>
    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <a href="<?php echo e(route('accounts.searchrefund')); ?>" id="accountbtnpanel_<?php echo e($account->id); ?>"  class="accordion card listdiv" style="background:#fff;width:99%;padding-left:0;padding-right:0;margin-left:0;margin-right:0;">

        <table>
            <tr>
                <td width="23%">
                    <div> <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="<?php echo e(env('NOBS_IMAGES')); ?>/profileimage.png" profilevalue="<?php echo e($account->customer_picture); ?>"></div>
                </td>
                <td width="77%" style="text-align:left; ">
                    <div style="text-align:left;padding-top:1px !important;">
                        <h6 class="account_name" style="padding-top:1px !important;">
                            <?php echo e($account->first_name); ?> <?php echo e($account->surname); ?></h6>
                    <h6 style="color:#724c78;text-align:left !important;"><?php echo e($account->account_number); ?></h6>
                    
                    <?php echo e($account->occupation); ?> -- <?php echo e($account->residential_address); ?>

                           
                    </div>
                </td>
            </tr>
        </table>

    </a>
     


    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>



<div style="width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;overflow-x:hidden;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr ID</strong></th>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>Reverse</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $tr_accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td><?php echo e($transactions->transaction_id); ?></td>
                
                 <?php if($transactions->name_of_transaction == 'Refund'): ?>
                 <td class="text-info">Reversal</td>
                <?php else: ?>
                 
                 <td class="text-success"><?php echo e($transactions->name_of_transaction); ?></td>
                <?php endif; ?>
               
                <td><strong class="text-success">GH¢ <?php echo e($transactions->amount); ?></strong></td>
                <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>"><?php echo e($transactions->created_at); ?></td>
                <?php if($transactions->name_of_transaction == 'Refund'): ?>
                 <td> </td>
                <?php else: ?>
                
                <?php if(\Carbon\Carbon::parse($transactions->created_at)->addMinutes(30) > \Carbon\Carbon::now()->subMinutes(30)): ?>
                 <td><a href="<?php echo e(route('reverse.transaction')); ?>/<?php echo e($transactions->id); ?>" class="btn-secondary">Reverse</a></td>
                 
                 <?php else: ?>
                 
                 <td></td>
                <?php endif; ?>
                <?php endif; ?>
                
               
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
       
    </table>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/accounts/refund/index.blade.php ENDPATH**/ ?>