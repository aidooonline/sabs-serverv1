


<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModal4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content">
         
        <div class="modal-body" id="modalbody2" style="padding:5px 10px !important;">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         
        </div>
      </div>
    </div>
  </div>  
  
  
   <table class="bill-details" style="display:none;">
        <tbody>
            <tr>
                <th style="border-bottom:dotted 1px #000;" class="center-align" colspan="2"><span class="receipt">GCI SUSU</span></th>
            </tr>
            
            <tr>
                <td>Transaction No:</td>
                <td>#38757</td>
            </tr>
            <tr>
                <td>Type:</td>
                <td>Deposit</td>
            </tr>
            <tr>
                <td>Name :</td>
                <td>Stephen Aidoo</td>
            </tr>
             <tr>
                <td>Acc No:</td>
                <td>GCL928JD878</td>
            </tr>
            
            <tr>
                <td>Amount :</td>
                <td>Ghs 4000</td>
            </tr>
            <tr>
                <td>Balance :</td>
                <td>3000</td>
            </tr>
            
            
            <tr>
                <th style="border-top:dotted 1px #000;font-weight:normal !important;margin-top:10px;padding-top:10px;" class="center-align" colspan="2">
                    Thank You. <br/>
                   For Enquiries: +233 54 214 8020 / +233 345 4938 
                </th>
                
            </tr>
        </tbody>
    </table>
  

<div class="modal fade" id="printreceipt" tabindex="-1" role="dialog" aria-labelledby="printreceipt" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content">
         
        <div class="modal-body" id="modalbody3" style="padding:5px 10px !important;">
          Do you want to print receipt?
        </div>
        <div id="depositreceipt" style="display:none;">
           
   
           
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
           <button type="button" onclick="initiateprint()" class="btn btn-success" data-dismiss="modal">Yes Print</button>
         
        </div>
      </div>
    </div>
  </div>  
  
  



  <div  class="modal fade row card-stats"  id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel5" aria-hidden="true">

    <div style="margin-bottom:5px;background-color:#4d2257;border-radius:3px;padding-left:18px;padding-right:2px;padding-top:10px;" class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content" style="border-radius:5px;">
         
        <div class="modal-body" id="modalbody" style="padding:5px 10px !important;">
            <div style="float:right;">
                <button type="button" class="btn btn-purple"  style="color:#ffffff !important;float:right;" data-dismiss="modal">
                  <i class="fas fa-times-circle"></i>
                </button>
              </div>
          <div class="card-body pb-20" style="position:relative;float:left;padding:0;">

            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('dashboard.index')); ?>"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Dashboard')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Agents'): ?>
            <a href="<?php echo e(route('dashboard.index')); ?>"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 "><?php echo e(__('My Dashboard')); ?></span>
            </a>
            <?php endif; ?>

            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('ledgergeneral.index')); ?>"
                
                class="col card icondiv" class="col card icondiv"
                style="float:left;">
    
                <i class="fas fa-file-invoice-dollar text-purple"></i>
                <span class="mb-0 "><?php echo e(__('General Ledger')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents'||
            \Auth::user()->type=='Teller'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>accounts/create"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-user-plus text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Register')); ?></span>
            </a>
            <?php endif; ?>
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('agents.index')); ?>"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Users')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Agents'): ?>
            <a href="<?php echo e(route('agents.index')); ?>"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('My Account')); ?></span>
            </a>
            <?php endif; ?>
    
           
            
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>savings/"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-piggy-bank text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Savings')); ?></span>
            </a>
            <?php endif; ?>
    
    
    
    
    
            <a href="#"  class="col card icondiv" style="float:left;display:none">
    
                <i class="fas fa-file text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Report')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchwithdrawer')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                 class="col card icondiv" style="float:left;">
    
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Withdraw')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchdeposit')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Deposit')); ?></span>
            </a>
    
            <a href="<?php echo e(route('accounts.searchrefund')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-left text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Reversal')); ?></span>
            </a>
    
    
    
            <a href="<?php echo e(env('BASE_URL')); ?>accounts/"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-search text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Customers')); ?></span>
            </a>
    
            <a href="<?php echo e(route('withdrawrequests.lists')); ?>"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Withdrawal Request')); ?></span>
            </a>
    
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
    
            <a href="<?php echo e(env('BASE_URL')); ?>loans/"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-hand-holding-usd text-purple"></i>
    
                <span class="mb-0 "><?php echo e(__('Loan Accounts')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>loanrequests/"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Request')); ?></span>
            </a>
            <?php endif; ?>
    
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(env('BASE_URL')); ?>loanmigrations/" class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-right text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Migration')); ?></span>
            </a>
            <?php endif; ?>
    
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a href="<?php echo e(route('accounts.searchloan')); ?>/<?php echo e(Auth::user()->created_by_user); ?>"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Loan Repayment')); ?></span>
            </a>
            <?php endif; ?>
    
            <a href="#" class="col card icondiv" style="float:left;display:none;">
                <i class="fas fa-list-ol text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Daily Log')); ?></span>
            </a>
    
    
    
    
            <a href="#" class="col card icondiv" style="float:left;display:none;">
                <i class="fas fa-door-closed text-purple"></i>
                <span class="mb-0 "><?php echo e(__('Closing/Opening')); ?></span>
            </a>
            
            
            
              <a href="<?php echo e(route('logout')); ?>" class="col card icondiv" style="float:left;" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">

                <i class="fas fa-sign-out-alt text-danger"></i>
                 <span class="mb-0 "><?php echo e(__('Logout')); ?></span>
                 <form id="frm-logout" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                <?php echo e(csrf_field()); ?>

             </form>
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
   </style><?php /**PATH /home/banqgego/public_html/sabs/resources/views/layouts/modalview1.blade.php ENDPATH**/ ?>