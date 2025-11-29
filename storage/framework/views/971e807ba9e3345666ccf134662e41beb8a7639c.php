 
<style>
  .bgpurple{
    color:#807f81;


    background-color:#dbd8dd57 !important;
    font-weight: :normal !important;

  }
  .textcolor1{
    color:#29B6F6;
    font-weight: :normal !importan;

  }
</style>

<h4 style="width:100%;position:relative;padding-left:20px;" class="text-warning"> Requested Loan



</h4>
 
    <a href="<?php echo e(route('loanrequests.create')); ?>" style="position:absolute;right:15px;top:30px;" href="#" class="btn btn-purple  mr-1 btn-fab btn-sm">
      <i class="fa fa-plus"></i>
    </a>

   
 
 
<?php $__currentLoopData = $loanrequestdetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loanrequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!-- TODAY -->

<div class="displaydivs" id="todaydiv" style="margin-top:20px;">
  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>

    <div id="accountbtnpanel_<?php echo e($loanrequest->id); ?>" class="col-xl-4 col-lg-6 col-md-6 col-12">
  <div class="card bg-white pt-2 pb-2">
    <div class="card-body">
      <div class="card-block pt-2 pb-0">
        <ul  style="border:0 !important;" class="list-group">
          <li  class="list-group-item"><div class="row  mt-1"><strong class="font-medium-5 mb-0 text-purple pl-0 ml-0">
            <?php echo e(number_format($loanrequest->amount, 2, '.', ',')); ?></strong></div></li>
          <li class="list-group-item"> <div class="row mt-1">Customer: <div style="padding-left:10px;" class="textcolor1"><?php echo e($loanrequest->account_number); ?></div></div> </li>
 
          <li class="list-group-item"><div class="row mt-1">Customer Name: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->first_name); ?>

            <?php echo e($loanrequest->last_name); ?>

          </div></li>
          <li class="list-group-item"><div class="row mt-1">Agent Name: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->agent_name); ?></div></li>
          <li class="list-group-item"> <div class="row mt-1">Loan: <div style="padding-left:10px;" class="textcolor1"><?php echo e($loanrequest->loan_name); ?></div></div> </li>
          <li class="list-group-item"><div class="row mt-1">Purpose: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->loan_purpose); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Mode of Payment: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->mode_of_pmt); ?></div></li>
          

          <li class="list-group-item"><div class="row mt-1">Outstanding Balance: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->outstanding_bal); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Previous Loan: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->prev_loan); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Loan Migrated: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;">
             
            <input type="checkbox" name="loan_migrated"  value="<?php echo e($loanrequest->loan_migrated); ?>" 
            <?php echo e($loanrequest->loan_migrated? 
           "checked" : ''); ?> >
          </div></li>

          <li class="list-group-item" style="display:none;"><div class="row mt-1"><div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->id); ?></div></li>

          <li class="list-group-item" style="display:none;"><div class="row mt-1"><div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->loan_id); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Requested Date: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->created_at); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">Business Capital: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->bus_capital); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Expected Disbursement Date: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->expected_disbursement_date); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Disbursement Date: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->disbursement_date); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Esitmated Daily Expense: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->est_daily_exp); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">External Credit Facilty: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->ext_credit_facility); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">External Credit Facility Amount: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->ext_credit_facility_amt); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">Guarantor: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->guarantor_name); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">Guarantor's Number: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->guarantor_number); ?></div></li>
          <li class="list-group-item"><div class="row mt-1">Guarantor's GPS Loc: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->guarantors_gps_loc); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Loan Purpose: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->loan_purpose); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Primary Payment Source: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->pri_pmt_src); ?></div></li>

          <li class="list-group-item"><div class="row mt-1">Secondary Payment Source: <div class="textcolor1" style="font-weight:normal !important;padding-left:10px;"><?php echo e($loanrequest->sec_pmt_src); ?></div></li>

      
          
        </ul>

        <div class="media-right text-left mr-2">
          <a href="<?php echo e(route('loanrequestdetail.edit',$loanrequest->id)); ?>" class="btn btn-light mr-1 btn-fab btn-sm">
            <i class="fa fa-pencil"></i>
          </a>
          
          <a href="<?php echo e(env('BASE_URL')); ?>loanrequestdetail/detail/{$loanrequest->id}" class="btn btn-purple mr-1 btn-fab btn-sm">
            <i class="fa fa-eye"></i> Migrate Loan
          </a>

          

         
        </div>
      </div>
     
    </div>
   </div>
  </div>
  <?php endif; ?>




  <!-- END TODAY -->
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 



<style>

  ul.list-group{
    
    padding:10px 5px;
  }


  li div,li{
position:relative;float:left;
  }
  

  .list-group-item{
    padding:1px 14px;border:solid 0 !important;
    margin-bottom:5px;
    border-radius:8px;
    border-bottom: solid 2px #e4e4e4 !important;
    position:relative;
    width:100%;
    margin-top:10px;
  }


.ghs{
    font-weight:normal !important;
    font-size:13px;
}

.displaydivs{
    padding-bottom:50px;
   
}

 
#thisweekdiv,
#thismonthdiv,
#thisyeardiv,
#alltimediv{
    display:none;
}
</style>

<script type="text/javascript">

function getfilter(that){
switch(that){
    case 'Today':
    
     $('.displaydivs').hide();
     $('#todaydiv').show(300);
     $('#filtertext').html(that);
    break;
    
    case 'This Week':
     $('.displaydivs').hide();
     $('#thisweekdiv').show(300); 
     $('#filtertext').html(that);
     
    break;
    
    case 'This Month':
       $('.displaydivs').hide();
     $('#thismonthdiv').show(300);
     $('#filtertext').html(that);
     
    break;
    
    case 'This Year':
        $('.displaydivs').hide();
     $('#thisyeardiv').show(300);
     $('#filtertext').html(that);
     
    break;
    
    case 'All Time':
        $('.displaydivs').hide();
     $('#alltimediv').show(300);
     $('#filtertext').html(that);
     
    break;
        
   
}
}

function getfilterbyagent(that){

if(that == 'agentid_agentallid12345'){
  showhidediv('loadingdiv');
  location.href="<?php echo e(route('dashboard.index')); ?>";
  
}else{
  showhidediv('loadingdiv');
  location.href="<?php echo e(route('agentquerydashboard.index')); ?>/" + that;
  
}
}

</script>


 
<?php /**PATH /home/banqgego/public_html/nobs/resources/views/loanrequestdetail/savingspartial_alltotal.blade.php ENDPATH**/ ?>