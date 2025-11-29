


 
 
<?php $__env->startSection('title'); ?>


<?php $__env->stopSection(); ?>


<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
      
<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="row dashboardtext">

        <div class="col-xl-3 col-md-6">
            <div class="card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <span class="text-muted mb-1"><?php echo e(__('Welcome, ')); ?></span>
                            <span class="h3 font-weight-bold mb-0 text-light"><?php echo e(\Auth::user()->username); ?></span>
                        </div>
                        <div class="col-auto">
                            <div class="icon bg-gradient-secondary text-white rounded-circle icon-shape"> 
                                
                                <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" style="height:30px;font-size:12px;padding: 2px 2px;float:right;">
                   
                                    <i class="fas fa-sign-out-alt text-purple"></i>
                                </a>
                                <form id="frm-logout" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                    <?php echo e(csrf_field()); ?>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  
       
    
<?php if(\Auth::user()->type == 'owner'): ?>
        <div class="row card-stats">

            


 <div class="card-body pb-20">


    <a href="<?php echo e(route('dashboard.index')); ?>" class="col card icondiv"  class="col card icondiv" style="float:left;">
                
        <i class="fas fa-tachometer-alt text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Dashboard')); ?></span>
    </a> 

    <a href="#" onclick="getbuttons('Customer Registration***<?php echo e(env('BASE_URL')); ?>accounts/create___User Accounts***#')" class="col card icondiv" data-toggle="modal" data-target="#exampleModal4"  style="float:left;">
        <i class="fas fa-user-plus text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Register')); ?></span>
    </a>
    
    <a href="#" onclick="getbuttons('Savings***<?php echo e(env('BASE_URL')); ?>savings/___Loans***<?php echo e(env('BASE_URL')); ?>loans/')" class="col card icondiv" data-toggle="modal" data-target="#exampleModal4"  class="col card icondiv" style="float:left;">
        
        <i class="fas fa-file-invoice-dollar text-purple"></i>
        <span class="mb-0 "><?php echo e(__('System Accounts')); ?></span>
    </a> 

    <a href="<?php echo e(route('agents.index')); ?>"  class="col card icondiv"  class="col card icondiv" style="float:left;">
        <i class="fas fa-user-friends text-purple"></i>
        
        <span class="mb-0 "><?php echo e(__('Agents')); ?></span>
    </a> 



    
    
    <a href="#"  class="col card icondiv" style="float:left;display:none">
        
        <i class="fas fa-file text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Report')); ?></span>
    </a>
    
    <a href="<?php echo e(route('accounts.searchwithdrawer')); ?>/<?php echo e(Auth::user()->created_by_user); ?>" class="col card icondiv" style="float:left;">
        
        <i class="fas fa-arrow-down text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Withdraw')); ?></span>
    </a>
    
    <a href="<?php echo e(route('accounts.searchdeposit')); ?>/<?php echo e(Auth::user()->created_by_user); ?>"  class="col card icondiv" style="float:left;">
        <i class="fas fa-arrow-up text-purple"></i>  
        <span class="mb-0 "><?php echo e(__('Deposit')); ?></span>
    </a>
    
    <a href="<?php echo e(route('accounts.searchrefund')); ?>/<?php echo e(Auth::user()->created_by_user); ?>"  class="col card icondiv" style="float:left;">
        <i class="fas fa-solid fa-reply text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Refund')); ?></span>
    </a>
    
    
    
    <a href="<?php echo e(env('BASE_URL')); ?>accounts/"  class="col card icondiv" style="float:left;">  
        <i class="fas fa-search text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Customers')); ?></span>
    </a>
    
    <a href="#"  class="col card icondiv" style="float:left;">
           <i class="fas fa-arrow-down text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Withdrawal Request')); ?></span>
    </a>
    
    <a href="<?php echo e(route('loanrequests.requests')); ?>"  class="col card icondiv" style="float:left;"> 
        <i class="fas fa-arrow-down text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Loan Request')); ?></span>
    </a>
    
    <a href="#"  class="col card icondiv" style="float:left;">
        <i class="fas fa-arrow-up text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Loan Repayment')); ?></span>
    </a>
    
    
    <a href="#"  class="col card icondiv" style="float:left;"> 
        <i class="fas fa-list-ol text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Daily Log')); ?></span>
    </a>
     


    <a href="#"  class="col card icondiv" style="float:left;">   
        <i class="fas fa-angle-double-right text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Loan Migration')); ?></span>
    </a>
    
    <a href="#"  class="col card icondiv" style="float:left;"> 
        <i class="fas fa-door-closed text-purple"></i> 
        <span class="mb-0 "><?php echo e(__('Closing/Opening')); ?></span>
    </a>
    
    
    
    
    <a href="#"  class="col card icondiv" style="float:left;">  
        <i class="fas fa-calculator text-purple"></i>
        <span class="mb-0 "><?php echo e(__('Loan Calculator')); ?></span>
    </a>
    

    </div>

            <div style="display:none;" class="col-md-12 d-none">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h6 class="text-muted mb-1"><?php echo e(__('Total Users')); ?></h6>
                                <span class="h3 font-weight-bold mb-0 "><?php echo e($data['totalUser']); ?></span>
                            </div>
                            <div class="col-auto">
                                <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
          
        <?php endif; ?>
    </div>
    
   
<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
   
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
   
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/home.blade.php ENDPATH**/ ?>