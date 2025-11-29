 

 


 
<?php $__env->startSection('title'); ?>
 
<?php $__env->stopSection(); ?>
 
<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 <?php echo $__env->make('layouts.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="mainsearchdiv">
<h4 class="card-title" style="margin-top:20px;">Withdrawal Requests <span class="text-warning"> </span></h4>
 
<div class="tab">
  <a href="#" id="unapprovedclick" class="btn tablinks" onclick="openCity(event, 'unapproved')">Requests<span class="text-primary">(<?php echo e($unapprovedcounts); ?>)</span></a>
  <a href="#" class="btn tablinks" onclick="openCity(event, 'approved')">Approved<span class="text-primary">(<?php echo e($approvedcounts); ?>)</span></a>
  <a href="#" class="btn tablinks" onclick="openCity(event, 'paid')">Paid<span class="text-primary">(<?php echo e($paidcounts); ?>)</span></a>
  
</div>

 
    <nav aria-label="Page navigation" class="card">
  <?php echo e($accounts->links()); ?>

  
  </nav>
 

<div id="unapproved" class="tabcontent">
    
 
  <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div  id="accountbtnpanel_<?php echo e($account->id); ?>"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">

<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong><?php echo e(number_format($account->amount, 2, '.', ',')); ?></strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="account_number"><?php echo e($account->account_number); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="accounttype"><?php echo e($account->account_type); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><?php echo e($account->det_rep_name_of_transaction); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Agent:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               <?php echo e($account->agentname); ?> 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($account->created_at); ?></td>
            </tr>
            
            <tr>
              <td class="mintd" style="padding:1px 1px;">
                   <?php if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner'): ?>
                  
                 
                             <button id="approve_<?php echo e($account->id); ?>" onclick="approvewithdrawaljvs(this);" class="btn btn-purple">Approve</button> 
                 
                         
                  
                  <?php endif; ?>
                   </td>
              <td class="mintd" style="padding:1px 1px;text-align:right !important;" id="thismonthtotal">
                  <?php if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner'): ?>
                  
                  
                               <button  id="decline_<?php echo e($account->id); ?>" onclick="declinewithdrawaljvs(this);" class="btn btn-dark">Decline</button>  
                          
                  
                  
                  
                  <?php endif; ?>
                  
                  
                  </td>
            </tr>
</table>

</div>
    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


</div>

<div id="approved" class="tabcontent">
    
    
  <?php $__currentLoopData = $accountsapproved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div  id="accountbtnpanel_<?php echo e($account->id); ?>"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">
<img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/approved.gif" style="width:70px;height:auto;position:absolute;top:10px;right:5px;" />
<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong><?php echo e(number_format($account->amount, 2, '.', ',')); ?></strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><?php echo e($account->account_number); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><?php echo e($account->account_type); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><?php echo e($account->det_rep_name_of_transaction); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Agent:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               <?php echo e($account->agentname); ?> 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($account->created_at); ?></td>
            </tr>
             <tr>
              <td class="mintd" style="padding:1px 1px;"> </td>
              <td class="mintd" style="padding:1px 1px;text-align:right !important;" id="thismonthtotal">
                  <button style="float:right;text-align:right;font-size:12px !important;padding:7px 10px;" id="pay_<?php echo e($account->id); ?>" data-toggle="modal" data-target="#exampleModal4" onclick="showpaynowdialog('<?php echo e(number_format($account->amount, 2, '.', ',')); ?>' ,this,'<?php echo e($account->phone_number); ?>')" class="btn btn-success">
                      <i class="fas fa-hand-holding-usd"></i> Pay Customer</button> </td>
            </tr>
            
</table>

</div>
    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>



<div id="paid" class="tabcontent">
  <?php $__currentLoopData = $accountspaid; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<div  id="accountbtnpanel_<?php echo e($account->id); ?>"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">
<img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/paid.gif" style="width:70px;height:auto;position:absolute;top:10px;right:5px;" />
<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong><?php echo e(number_format($account->amount, 2, '.', ',')); ?></strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><?php echo e($account->account_number); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><?php echo e($account->account_type); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><?php echo e($account->det_rep_name_of_transaction); ?></td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Paid By:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               <?php echo e($account->paid_by); ?> 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($account->created_at); ?>

              
              </td>
            </tr>
            
            
</table>

</div>
    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


 
<nav aria-label="Page navigation" class="card">
  <?php echo e($accounts->links()); ?>

</nav>
 
    
     

</div>
</div>
<style>

/* Style the tab */
.tab {
  overflow: hidden;
 
  border-radius:10px;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 12px 5px !important;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px; 
  border-top: none;
}
 
table td[class='mintd'] {
        padding: 2px 8px !important;
         font-size:15px !important;
         color:#454545 !important;
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

.mintd{
    font-size:20px !important;
    font-family:verdana !important;
}

</style>


<script>

// Shorthand for $( document ).ready()
$(function() {
  document.getElementById('unapprovedclick').click();
});
    function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}




</script>




<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsback/resources/views/accounts/withdrawal_request/index.blade.php ENDPATH**/ ?>