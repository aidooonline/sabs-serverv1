


<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content">
         
        <div class="modal-body" id="modalbody" style="padding:5px 10px !important;">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         
        </div>
      </div>
    </div>
  </div>  



  <div  class="modal fade row card-stats"  id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel5" aria-hidden="true">

    <div style="margin-bottom:90px;background-color:#4d2257;border-radius:3px;padding-left:18px;padding-right:2px;padding-top:10px;" class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content" style="border-radius:5px;">
         
        <div class="modal-body" id="modalbody" style="padding:5px 10px !important;">
            <div style="float:right;">
                <button type="button" class="btn btn-purple"  style="color:#ffffff !important;float:right;" data-dismiss="modal">
                  <i class="fas fa-times-circle"></i>
                </button>
              </div>
          <div class="card-body pb-20" style="position:relative;float:left;padding:0;">

            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('dashboard.index')); ?>" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Dashboard')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Agents'): ?>
            <a href="<?php echo e(route('dashboard.index')); ?>" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 "><?php echo e(__('My Dashboard')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents'||
            \Auth::user()->type=='Teller'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>accounts/create" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                style="float:left;">
                <i class="fas fa-user-plus text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Register')); ?></span>
            </a>
            <?php endif; ?>
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('agents.index')); ?>" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Users')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Agents'): ?>
            <a href="<?php echo e(route('agents.index')); ?>" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('My Account')); ?></span>
            </a>
            <?php endif; ?>
    
    
            <a style="display:none" href="#"
                onclick="showhidediv('loadingdiv');getbuttons('Savings***<?php echo e(env('BASE_URL')); ?>savings/___Loans***<?php echo e(env('BASE_URL')); ?>loans/')"
                class="col card icondiv" data-toggle="modal" data-target="#exampleModal4" class="col card icondiv"
                style="float:left;">
    
                <i class="fas fa-file-invoice-dollar text-purple"></i>
                <span class="mb-0 "><?php echo e(__('System Accounts')); ?></span>
            </a>
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>savings/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-piggy-bank text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Savings')); ?></span>
            </a>
            <?php endif; ?>
    
    
    
    
    
            <a href="#" onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;display:none">
    
                <i class="fas fa-file text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Report')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchwithdrawer')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
    
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Withdraw')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchdeposit')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Deposit')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchrefund')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-left text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Reversal')); ?></span>
            </a>
    
    
    
            <a href="<?php echo e(env('BASE_URL')); ?>accounts/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                style="float:left;">
                <i class="fas fa-search text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Customers')); ?></span>
            </a>
    
            <a href="<?php echo e(route('withdrawrequests.lists')); ?>" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Withdrawal Request')); ?></span>
            </a>
    
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
    
            <a href="<?php echo e(env('BASE_URL')); ?>loans/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-hand-holding-usd text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Loans Accounts')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>loanrequests/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Request')); ?></span>
            </a>
            <?php endif; ?>
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-right text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Migration')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('accounts.searchloan')); ?>/<?php echo e(Auth::user()->created_by_user); ?>"
                onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Repayment')); ?></span>
            </a>
            <?php endif; ?>
    
            <a href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-list-ol text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Daily Log')); ?></span>
            </a>
    
    
    
    
            <a href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-door-closed text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Closing/Opening')); ?></span>
            </a>
    
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a style="display:none" href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-calculator text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Calculator')); ?></span>
            </a>
            <?php endif; ?>
    
        </div>
        </div>
        <div class="modal-footer">
          
         
      

          
         
        </div>
      </div>
    </div>
  
  
  
  

   


</div>

<style>
    
   .icondiv span {
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    font-size: 13px; color:purple !important;
}
   </style><?php /**PATH /home/banqgego/public_html/nobs/resources/views/layouts/modalview1.blade.php ENDPATH**/ ?>