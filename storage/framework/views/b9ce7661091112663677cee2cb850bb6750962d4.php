 

 


 
<?php $__env->startSection('title'); ?>
 
<?php $__env->stopSection(); ?>
 
<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 <?php echo $__env->make('layouts.searchrefund', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="mainsearchdiv">
    
  <?php echo $__env->make('quickmenus.agentsmenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 
<style>
    .nav-tabs li a{padding-left:10px;padding-right:10px;}
    .nav-tabs li{
        width:31%;height:26px;
    }
    
    .nav-tabs li.btn-purple{
       height:auto;
    }
    
    .nav-tabs li a{
       height:24px !important;
    }
    .active{
        color:#ffffff !important;
    }
    
    .active strong{
        color:purple;
    }
    
</style> 

 <ul class="nav nav-tabs">
  <li class="active btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#today">Today</a></li>
  <li class="btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#thisweek">This Week</a></li>
  <li class="btn btn-purple"><a data-toggle="tab" style="font-size:12px;" href="#thismonth">This Month</a></li>
</ul>

<div class="tab-content">
  <div id="today"  class="tab-pane active">
        <strong  style="text-transform: capitalize;padding-left:10px;"> <?php echo e($nameoftransaction); ?> today (<span style="color:#800860">GH¢ <?php echo e(number_format($todaytotal, 3, '.', ',')); ?></span>).</strong>
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th>
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $todaytotalWD; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                 <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>"><?php echo e($transactions->created_at); ?></td>
                 <td><strong class="text-success">GH¢ <?php echo e($transactions->amount); ?></strong></td> 
                 <td><?php echo e($transactions->det_rep_name_of_transaction); ?></td> 
                 <td><?php echo e($transactions->agentname); ?></td> 
                
                
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
       
    </table>
  </div>
  
  
  <div id="thisweek" class="tab-pane fade">
         <strong  style="text-transform: capitalize;padding-left:10px;"> <?php echo e($nameoftransaction); ?> this week (<span style="color:#800860">GH¢ <?php echo e(number_format($thisweektotal, 3, '.', ',')); ?></span>).</strong>
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th>
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $thisweektotalWD; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                 <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>"><?php echo e($transactions->created_at); ?></td>
                 <td><strong class="text-success">GH¢ <?php echo e($transactions->amount); ?></strong></td> 
                 <td><?php echo e($transactions->det_rep_name_of_transaction); ?></td> 
                 <td><?php echo e($transactions->agentname); ?></td> 
                
                
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
       
    </table>
  </div>
  
  
  <div id="thismonth" class="tab-pane fade">  
 <strong  style="text-transform: capitalize;padding-left:10px;"> <?php echo e($nameoftransaction); ?> this month (<span style="color:#800860">GH¢ <?php echo e(number_format($thismonthtotal, 3, '.', ',')); ?></span>).</strong>
      
     <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Date</strong></th> 
                <th><strong>Amount</strong></th> 
                <th><strong>Acc Holder</strong></th>
                <th><strong>Agent</strong></th>
                
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $thismonthtotalWD; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                 <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>"><?php echo e($transactions->created_at); ?></td> 
                 <td><strong class="text-success">GH¢ <?php echo e($transactions->amount); ?></strong></td> 
                 <td><?php echo e($transactions->det_rep_name_of_transaction); ?></td> 
                 <td><?php echo e($transactions->agentname); ?></td> 
                
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
       
    </table>
  </div>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/reports/reportswd.blade.php ENDPATH**/ ?>