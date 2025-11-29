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

<div class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

    <?php echo $__env->make('layouts.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

   

      <div id="mainsearchdiv">

       
        <style>
           
                section {
  width: 98% ;
  border: 1px solid #ece6e6;
  border-radius:5px;
  display: flex;
  justify-content: center !important ;
  align-items: center !important;
  
 
}

section img{
    height:150px;
}
            </style>



<h4 class="card-title" style="margin-top:20px;">
    Customer Info
  </h4>

    <div class="listdiv"
        style="width:100%;margin-top:0 !important;padding-bottom:140px;background-color:#ffffff;padding:10px 10px;border-radius:10px 10px;">
        <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <section>
            <div>
                <?php if($useraccount->is_dataimage == 1): ?>
                <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="<?php echo e($useraccount->customer_customer_picture); ?>" is_dataimage="<?php echo e($useraccount->is_dataimage); ?>" profilevalue="<?php echo e($useraccount->customer_picture); ?>">
                <?php endif; ?>
            
                <?php if($useraccount->is_dataimage == 0): ?>
                <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle profilepic" src="<?php echo e(env('NOBS_IMAGES')); ?>/profileimage.png" profilevalue="<?php echo e($useraccount->customer_picture); ?>">
                <?php endif; ?>

            </div>
          </section>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       
        <table class="table" style="vertical-align: center;text-align:center;width:95%;">
          
            
         </table>
        <table class="table" style="border-radius:10px !important;padding-top:25px;">

            <tbody>
                <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="mintd"><strong style="">Name</strong>:</td>
                    <td class="mintd"><strong style="color:purple;">
                            <?php echo e($useraccount->first_name); ?> <?php echo e($useraccount->surname); ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Account No.</strong>:</td>
                    <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->account_number); ?></strong></td>
                </tr>

                <tr>
                    <td class="mintd"><strong style="">Account Types</strong>:</td>
                    <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->account_types); ?></strong></td>
                </tr>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="mintd"><strong style="">Deposits:</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totaldeposits"
                            style="color:purple "><?php echo e(number_format($totaldeposits,2)); ?></strong></td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Withdrawals</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalwithdrawals"
                            style="color:purple "><?php echo e(number_format($totalwithdrawals, 2)); ?></strong></td>
                </tr>

                <tr>
                    <td class="mintd"><strong style="">Refunds</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalwithdrawals"
                            style="color:purple "><?php echo e(number_format($totalrefunds, 2)); ?></strong></td>
                </tr>
                <tr>
                    <td class="mintd"><strong style="">Balance</strong>:</td>
                    <td class="mintd">GH¢ <strong id="totalbalance" style="color:rgb(58, 140, 247) "><?php echo e(number_format($totalbalance, 2)); ?></strong></td>
                </tr>


                

            </tbody>
        </table>





    </div><div class="listdiv" style="width:100%;margin-top:0 !important;padding-bottom:140px;background-color:#ffffff;padding:10px 10px;border-radius:10px 10px;">
    
    <table class="table" style="vertical-align: center;text-align:left;width:95%;">
    <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td class="mintd"><strong style="">ID Number</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->id_number); ?>  </strong>
        </td>
    </tr>
    <tr>
        <td class="mintd"><strong style="">ID Type</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->id_type); ?>  </strong>
        </td>
    </tr>
    <tr>
        <td class="mintd"><strong style="">Occupation</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->occupation); ?>  </strong>
        </td>
    </tr>
    <tr>
        <td class="mintd"><strong style="">Marital Status</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->marital_status); ?>  </strong>
        </td>
    </tr>
    <tr>
        <td class="mintd"><strong style="">Gender</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->gender); ?>  </strong>
        </td>
    </tr>
    
    <tr>
        <td class="mintd"><strong style="">Date of Birth</strong>:</td>
        <td class="mintd"><strong style="color:purple;">
                <?php echo e($useraccount->date_of_birth2); ?>  </strong>
        </td>
    </tr>
    <tr>
        <td class="mintd"><strong style="">Email</strong>:</td>
        <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->email); ?></strong></td>
    </tr>
    
    <tr>
        <td class="mintd"><strong style="">Phone</strong>:</td>
        <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->phone_number); ?></strong></td>
    </tr>

    <tr>
        <td class="mintd"><strong style="">Next of Kin</strong>:</td>
        <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->next_of_kin); ?></strong></td>
    </tr>
    
    <tr>
        <td class="mintd"><strong style="">Next of Kin Contact</strong>:</td>
        <td class="mintd"><strong style="color:purple "><?php echo e($useraccount->next_of_kin_phone_number); ?></strong></td>
    </tr>
   
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    </div>



<div style="width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>User</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td><?php echo e($transactions->name_of_transaction); ?></td>
                <td><strong>GH¢ <?php echo e($transactions->amount); ?></strong></td>
                <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>"><?php echo e($transactions->created_at->diffForHumans()); ?></td>
                <td><?php echo e($transactions->agentname); ?></td>
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        
    </table>
</div>

      </div>
</div>
<style>

    #tdetailstable{
        width:99% !important;
    }
    .tableFixHead {
        overflow: auto;
        height: 100px;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    /* Just common table stuff. Really. */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 8px 16px;
    }

    th {
        background: #eee;
    }

    .account_name {
        color: #666666;
        text-align: left !important;
        font-weight: bold;
        font-family: verdana;
    }

    .table-panel td {
        font-size: 1em !important;
        color: rgb(65, 6, 65);
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .accordion img {
        width: 65px;
        height: 65px;
    }

    table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .listdiv {
        width:97% !important;
        height: auto;
    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;

        background-color: yellow;
    }

    .listdiv .listdiv img {
        width: 70px;

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


    .listdiv2 {
        height: auto !important;
        height: 600px;
        padding-left:0;padding-right:0;
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/accounts_transactions/index.blade.php ENDPATH**/ ?>