<?php $__env->startSection('title'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-btn'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('layouts.inlinecss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

    <?php echo $__env->make('layouts.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



    <div id="mainsearchdiv">
        <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h5 class="card-title" style="margin-top:20px;margin-left:30px;">
            Loan Re-Payment : <span class="text-warning"> <?php echo e($useraccount1->first_name); ?> <?php echo e($useraccount1->middle_name); ?>

                <?php echo e($useraccount1->surname); ?></span>
        </h5>


        <input style="display:none;" type="text" value="<?php echo e($useraccount1->phone_number); ?>" class="form-control" />

        <h5 class="card-title mb-4    " style="margin-top:20px;margin-left:30px;">
            Loan Account : <span id="account_number"  class="text-info"> 
                <?php echo e($accountsid); ?> 
            </span>
            <br />
            <?php $__currentLoopData = $accounttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <span style="font-size:14px !important;color:#c39dc7;margin-top:20px;" id="accounttype"><?php echo e($actype); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <br />
            Amount Owed: <label id="loanamountspan" class="text-info" style="font-weight: bolder;font-family:Verdana, Geneva, Tahoma, sans-serif;margin-top:10px;">
                GH₵
                <?php echo number_format($principal_loan, 2); ?>
            </label>
            <br />
            Paid : <label id="balancespan" class="text-success" style="font-weight: bolder;font-family:Verdana, Geneva, Tahoma, sans-serif;margin-top:10px;">
                GH₵
                <?php echo number_format($total_repayment, 2); ?>
            </label>
            <br />
            Balance : <label id="balancespan" class="text-info" style="font-weight: bolder;font-family:Verdana, Geneva, Tahoma, sans-serif;margin-top:10px;">
                GH₵
                <?php echo number_format($totalbalance, 2); ?>
            </label>

        </h5>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <a href="#" class="rounded pl-2 pr-2 mt-0 text-info" style="border:solid 1px;margin-left:30px;"
            onclick="showotheraccountno();">Show Other Acc. No.s</a>

        <div style="margin:10px 30px !important;" id="showotheraccountno">
            <?php $__currentLoopData = $useraccountnumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccountnumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($useraccountnumber->account_number == $accountsid): ?>

            <?php else: ?>

            <input type="button" onclick="movetoloanpage('<?php echo e($useraccountnumber->account_number); ?>',this,<?php echo e($useraccount1->phone_number); ?>);"
                class="btn-purple rounded" style="margin:2px 2px;width:100%;" value="<?php echo e($useraccountnumber->account_type); ?> - <?php echo e($useraccountnumber->account_number); ?>" />

            <?php endif; ?>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>




        <div class="col-11 insetshadow">

            <div class="form-group">

                <input type="number" min="0.00" id="amount" name="amount" class="form-control mb-2"
                    placeholder="Amount Here.." step="any" />
            </div>
        </div>




        <div class="col-11 insetshadow">

            <div class="form-group">


                <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button"
                    onclick="loan_repaymentjvs('<?php echo e($useraccount2->phone_number); ?>','Loan Payment of GHs ' + $('#amount').val() + ' credited to account.')"
                    value="Pay Loan" />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            </div>
        </div>










        <div
            style="margin-top:100px;width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;">
            <table id="tdetailstable" class=" table-striped tableFixHead table-bordered"
                style="padding-bottom:0;position:relative;">
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
                        <td title="<?php echo e(\Auth::user()->dateFormat($transactions->created_at)); ?>">
                            <?php echo e($transactions->created_at->diffForHumans()); ?></td>
                        <td><?php echo e($transactions->agentname); ?></td>
                    </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<?php echo $__env->make('layouts.depositwithdrawalcss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

    function showotheraccountno() {

        $("#showotheraccountno").toggle();
    }


</script>


<style>
    .balancespan{
        font-weight: bold !important;
    }
</style>


<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/accounts/loan_repayment/create.blade.php ENDPATH**/ ?>