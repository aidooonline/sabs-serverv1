<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Lead')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Transactions')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Transactions')); ?></li>
<?php $__env->stopSection(); ?>
 
<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <!-- Table -->
       
        <script>
   function exportTasks(_this) {
      let _url = $(_this).data('href');
      window.location.href = _url;
   }
</script>
        <div class="table-responsive">
             <span data-href="<?php echo e(route('export.transactions')); ?>" id="export" class="btn btn-light" onclick="exportTasks(event.target);">Export</span>
            <table class="align-items-left dataTable table-sm table-striped table-hover table-light">
                <thead>
                <tr >
                    <th scope="col" class="sort" data-sort="name"><?php echo e(__('Tr ID')); ?></th>
                    <th scope="col" style="padding-left:15px !important;" class="sort" data-sort="created_at"><?php echo e(__('Datetime')); ?></th>
                    <th scope="col" class="sort" style="width:40px !important;" data-sort="lead_temperature"><?php echo e(__('Acc No')); ?></th>
                    <th scope="col" class="sort" data-sort="acctype"><?php echo e(__('Acc Type')); ?></th>
                    <th scope="col" class="sort" data-sort="transactionname"><?php echo e(__('Tr Name')); ?></th>
                    <th scope="col" class="sort" data-sort="amount"><?php echo e(__('Amount')); ?></th>
                    <th scope="col" class="sort" data-sort="accname"><?php echo e(__('Acc Name')); ?></th> 
                    <th scope="col" class="sort" data-sort="phone"><?php echo e(__('Phone')); ?></th>
                    <th scope="col" class="sort" data-sort="agentname"><?php echo e(__('Agent Name')); ?></th>
                    
                    <th scope="col" class="sort" data-sort="userid"><?php echo e(__('User ID')); ?></th>
                </tr>
                </thead>
                <tbody class="list">
                <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                
                
                        <tr>
                          <td title="Transaction ID" style="padding-left:15px !important;"> 
                           <?php echo e($lead->transaction_id); ?> 
                        </td>
                        <td title="<?php echo e($lead->created_at->diffForHumans()); ?>" style="padding-left:15px !important;"> 
                          <?php echo e(\Auth::user()->dateFormat($lead->created_at)); ?>   
                        </td>
                          <td title="Account Number" style="padding-left:15px !important;"> 
                           <?php echo e($lead->account_number); ?>  
                           
                           
                        </td>
                        
                        
                           
                        
                        <td title="Account Type" style="padding-left:15px !important;"> 
                           <?php echo e($lead->account_type); ?>  
                        </td>
                        
                         <td>
                            <span class=<?php if($lead->name_of_transaction == 'Deposit'): ?><?php echo e("coldlead"); ?><?php elseif($lead->name_of_transaction == 'Withdraw'): ?><?php echo e("hotlead"); ?><?php else: ?><?php echo e("warmlead"); ?> <?php endif; ?>>
                                <?php echo e($lead->name_of_transaction); ?> 
                            </span>
                        </td>
                       
                        
                        <td title="Amount" style="padding-left:15px !important;"> 
                           GHS <?php echo e($lead->amount); ?>  
                        </td>
                        
                        <td title="Account Name" style="padding-left:15px !important;"> 
                           <?php echo e($lead->det_rep_name_of_transaction); ?>  
                        </td>
                        
                        <td title="Phone" style="padding-left:15px !important;"> 
                           <?php echo e($lead->phone_number); ?>  
                        </td>
                        
                        <td title="Agent Name" style="padding-left:15px !important;"> 
                           <?php echo e($lead->agentname); ?>  
                        </td>
                        <td title="Agent ID" style="padding-left:15px !important;"> 
                           <?php echo e($lead->users); ?>  
                        </td>
                         
                         
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/transactions/index.blade.php ENDPATH**/ ?>