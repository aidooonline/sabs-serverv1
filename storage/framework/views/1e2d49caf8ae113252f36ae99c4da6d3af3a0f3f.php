 



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
        <h4 class="card-title" style="margin-top:20px;margin-left:30px;">
            Deposit to : <span id="customer_accountname" class="text-warning"> <?php echo e($useraccount1->first_name); ?> <?php echo e($useraccount1->surname); ?></span>
          </h4>


          <input style="display:none;" type="text" value="<?php echo e($useraccount1->phone_number); ?>" class="form-control" />

          <h5 class="card-title mb-4    " style="margin-top:20px;margin-left:30px;">
            Account No. : <span id="account_number" class="text-info">
                <?php $__currentLoopData = $mainaccountnumber; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maccountno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e($maccountno); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </span> 
            <br/>
            <?php $__currentLoopData = $accounttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <span style="font-size:14px !important;color:#c39dc7;" id="accounttype"><?php echo e($actype); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <br/>
            Balance : <span id="balancespan" class="text-info">
              GH₵ <?php echo number_format($totalbalance, 2); ?>  
                </span>
                
                <span style="display:none" id="balancespan2" class="text-info"><?php echo $totalbalance; ?>  
                </span>
                
           
          </h5>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         
<a href="#" class="rounded pl-2 pr-2 mt-0" style="border:solid 1px;margin-left:30px;" onclick="showotheraccountno();">Show Other Acc. No.s</a>
      
<div style="margin:10px 30px !important;" id="showotheraccountno">
           
  <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<input type="button" class="btn-purple rounded" style="margin:2px 2px;" value="<?php echo e($useraccount1->account_number); ?>" />
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 
        </div>
       


          
          <div class="col-11 insetshadow">
            
            <div class="form-group">
             
          <input type="number" min="0.00" id="amount" name="amount" class="form-control mb-2" placeholder="Amount Here.." step="any" />
            </div>
        </div>
        
        <script>
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


function calculatecurrentbalance(){
    let totalamt = parseFloat($('#amount').val());
    let balance2 = parseFloat($('#balancespan2').html());
    let realbalance = parseFloat(balance2 + totalamt);
    
    return realbalance;
}

function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}

            
            function getcurrentdatejv(){
 
                let today = new Date();
                let date = today.getFullYear()+'-'+(monthNames[today.getMonth()])+'-'+today.getDate()+'-'+formatAMPM(new Date);
                return date;
            }



            function getdepositmessage(){
                
                let amountval = formatter.format($('#amount').val());
                let message1 = 'Deposited to';
                let accountnumberval = $('#account_number').html();
                let customer_accountnameval = $('#customer_accountname').html();
                let customer_accounttype = $('#accounttype').html();
                let message2 = 'on';
                let currendate = getcurrentdatejv();
                let mybalance = formatter.format(calculatecurrentbalance());
                 
                return amountval + ' ' + message1 + ' ' + accountnumberval + ' '+ customer_accountnameval + ' ('+ customer_accounttype + ') ' + message2 + ' '+ currendate + ' Balance: '+ mybalance;
            }
        </script>

             
 

        <div class="col-11 insetshadow">
            
            <div class="form-group">
                
                 
                <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $useraccount2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input class="btn btn-purple mb-2 customercodebtn" id="confirmcustomercode" type="button" onclick="makedepositjvs('<?php echo e($useraccount2->phone_number); ?>',getdepositmessage())" value="Deposit" />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            </div>
    </div>

       
 

 

<div style="margin-top:100px;width:97%;position:relative;height:auto;margin-left:2%;margin-right:1% !important;overflow-x:hidden;">
    <table id="tdetailstable" class=" table-striped tableFixHead table-bordered" style="padding-bottom:0;position:relative;">
        <thead style="background-color:#ffffff !important;z-index:1">
            <tr>
                <th><strong>Tr ID</strong></th>
                <th><strong>Tr Name</strong></th>
                <th><strong>Amount</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>User</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td><?php echo e($transactions->transaction_id); ?></td>
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
 
 <?php echo $__env->make('layouts.depositwithdrawalcss', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

function showotheraccountno(){
    
$( "#showotheraccountno").toggle();
}


</script>





<?php echo $__env->make("layouts.modalview1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 

<?php echo $__env->make("layouts.modalscripts", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/accounts/deposit/create.blade.php ENDPATH**/ ?>