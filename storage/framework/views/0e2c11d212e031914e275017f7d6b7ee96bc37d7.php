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
    <h4 class="card-title" style="margin-top:20px;">
      Agent Deposits: <strong class="text-warning"><?php echo e($agentname); ?> </strong>
      </h4> 
 

    <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
      <table class="table" style="vertical-align: center;text-align:center;">
       <?php $__currentLoopData = $agentdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       <tr>
        <td><a href="tel:<?php echo e($agent->phone); ?>" class="btn btn-xs btn-purple rounded"><i class="fa fa-phone"></i> Call </a></td>
        <td><a href="mailto:<?php echo e($agent->email); ?>" class="btn btn-xs btn-purple rounded"><i class="fa fa-envelope"></i> Mail</a></td>
        <td><a href="sms://<?php echo e($agent->phone); ?>" class="btn btn-xs btn-purple rounded"><i class="fa fa-envelope-open-text"></i> Sms</a></td>
        <td><a href="https://wa.me/<?php echo e($agent->phone); ?>" class="btn btn-xs btn-purple rounded"><i class="fa fa-whatsapp"></i> Whatsapp</a></td>
    </tr>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       
    </table>
      
      
      <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
           
            <tr>
                <td class="mintd" style="padding:1px 1px;"><strong>Deposits</strong></td>
                 
              </tr>
                <tr>
                    <td class="mintd" style="padding:1px 1px;">Today:</td>
                    <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalDP, 2, '.', ',')); ?></td>
                  </tr>
      
                  <tr>
                    <td class="mintd" style="padding:1px 1px;">This Week:</td>
                    <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalDP, 2, '.', ',')); ?></td>
                  </tr>
      
                  <tr>
                    <td class="mintd" style="padding:1px 1px;">This Month:</td>
                    <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalDP, 2, '.', ',')); ?></td>
                  </tr>
      
                  <tr>
                    <td class="mintd" style="padding:1px 1px;">This Year:</td>
                    <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalDP, 2, '.', ',')); ?></td>
                  </tr>
      
            
          </table>
      </div>
      
       
      
      
      
<div style="width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>Bal</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td><?php echo e($transactions->name_of_transaction); ?></td>
                <td><strong>GH¢ <?php echo e($transactions->amount); ?></strong></td>
                <td><?php echo e($transactions->created_at->isoFormat('ddd d MMM Y, h:s A')); ?></td>
                <td>GH¢ <?php echo e($transactions->balance); ?></td>
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        
    </table>
</div>
      
       
       
      
      

    
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/dashboard/agent_deposit.blade.php ENDPATH**/ ?>