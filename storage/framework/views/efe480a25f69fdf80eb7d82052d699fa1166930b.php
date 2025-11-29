<div class="panel listdiv2" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
   

       

    <table class="table" style="vertical-align: center;text-align:center;width:100%;margin-bottom:0 !important;">
        
        <tr> <td><a href="<?php echo e(route('accounts.transactiondetails')); ?>/<?php echo e($account->account_number); ?>/" class="btn btn-xs  rounded"><i class="fa fa-eye"></i> </a></td>
            <td><a href="<?php echo e(route('accounts.edit',$account->id)); ?>" class="btn btn-xs  rounded"><i class="fa fa-pencil"></i> </a></td>
        

         <td><a href="tel:<?php echo e($account->phone_number); ?>" class="btn btn-xs rounded"><i class="fa fa-phone"></i>  </a></td>
         <td><a href="https://wa.me/<?php echo e($account->phone_number); ?>" class="btn btn-xs  rounded"><i class="fa fa-whatsapp"></i> </a></td>
         <td><a href="sms://<?php echo e($account->phone_number); ?>" class="btn btn-xs rounded"><i class="fa fa-sms"></i> </a></td>
        
         <td><a href="mailto:<?php echo e($account->email); ?>" class="btn btn-xs rounded"><i class="fa fa-envelope"></i> </a></td>
         
     
        </tr>
        
        
     </table>
</div><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/accounts/accountspartial_singletotal.blade.php ENDPATH**/ ?>