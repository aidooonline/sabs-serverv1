

<div class="panel listdiv2" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
   

       

    <table  id="agenttablstats" style="vertical-align: center;text-align:center;width:100%;margin-bottom:0 !important;">
        
        <tr> 
         <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?><?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/agentcommission" class="btn btn-xs  rounded">
             <i class="fas fa-hand-holding-usd" style="height:23px;width:auto;"  v></i>
            
              <br/>
              <span class="smallspan">Commissions</span>
               </a></td>
               
              <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?><?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/deposit" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/depositicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Deposits</span>
               </a></td>
               
                <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?><?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/withdraw" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/> 
              <span class="smallspan">Withdrawals</span>
               </a></td>
               
                  <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?><?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/refund" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/reversal2.png" style="height:23px;width:auto;" class="fa" />
               <br/>
              <span class="smallspan">Reversals</span>
               </a></td>
        
        
        <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?><?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/customersregistered" class="btn btn-xs  rounded">
            <i class="fa fa-users"></i>
         <br/>
                  <span class="smallspan">Registered</span>
        </a></td>
            
              
        </tr>
        <tr>
            
            
            
            <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?>/<?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/loandisbursed" class="btn btn-xs  rounded">
            
            <i class="fa fa-funnel-dollar"></i>
         <br/>
                  <span class="smallspan">Loan Disbursed</span>
        </a></td>
        
         <td class="btn-light"><a href="<?php echo e(route('agents.singletransactiondetails')); ?>/<?php echo e($account->created_by_user); ?>/<?php echo e($account->name); ?>/loanrepayment" class="btn btn-xs  rounded">
            
          <i class="fas fa-cart-arrow-down"></i>
         <br/>
                  <span class="smallspan">Loan Repayment</span>
        </a></td>
        
        <td class="btn-light"><a href="<?php echo e(env('BASE_URL')); ?>user/<?php echo e($account->id); ?>/edit" class="btn btn-xs  rounded"><i class="fa fa-pencil"></i> 
             <br/>
              <span class="smallspan">Edit</span>
            </a></td>
        
         <td></td>
            
            <td></td>
            
            <td></td>
        </tr>
        
        
     </table>
</div><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/agents/accountspartial_singletotal.blade.php ENDPATH**/ ?>