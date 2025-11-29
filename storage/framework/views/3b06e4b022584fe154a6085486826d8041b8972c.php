

<div class="panel listdiv2" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
   

       

    <table  style="vertical-align: center;text-align:center;width:99%;margin-bottom:0 !important;">
        
        <tr> 
         <td class="btn btn-light"><a href="javascript:movetodepositpage('<?php echo e($account->account_number); ?>',this,'<?php echo e($account->phone_number); ?>');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/depositicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Deposit</span>
               </a></td>
               
                <td class="btn btn-light"><a href="javascript:movetowithdrawalpage('<?php echo e($account->account_number); ?>',this,'<?php echo e($account->phone_number); ?>');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Withdraw</span>
               </a></td>
               
                  <td class="btn btn-light"><a href="javascript:movetorefundpage('<?php echo e($account->account_number); ?>',this,'<?php echo e($account->phone_number); ?>');" class="btn btn-xs  rounded">
              <img src="<?php echo e(env('NOBS_IMAGES')); ?>icons/reversal2.png" style="height:23px;width:auto;" class="fa" />
               <br/>
              <span class="smallspan">Reverse</span>
               </a></td>
        
        
        <td class="btn btn-light"><a href="<?php echo e(route('accounts.transactiondetails')); ?>/<?php echo e($account->account_number); ?>/" class="btn btn-xs  rounded"><i class="fa fa-eye"></i>
         <br/>
              <span class="smallspan">View</span>
        </a></td>
        
        
            <td class="btn btn-light">
                <?php if(\Auth::user()->type == 'Agents'): ?>
                
                
                <?php else: ?>
                 <a href="<?php echo e(route('accounts.edit',$account->id)); ?>" class="btn btn-xs  rounded"><i class="fa fa-pencil"></i> 
             <br/>
              <span class="smallspan">Edit</span>
            </a>
            
                
                <?php endif; ?>
               
            
            </td>
             

         <td style="display:none;"><a href="tel:<?php echo e($account->phone_number); ?>" class="btn btn-xs rounded"><i class="fa fa-phone"></i>  </a></td>
         <td style="display:none;"><a href="https://wa.me/<?php echo e($account->phone_number); ?>"    class="btn btn-xs  rounded"><i class="fa fa-whatsapp"></i> </a></td>
         <td style="display:none;"><a href="sms://<?php echo e($account->phone_number); ?>"   class="btn btn-xs rounded"><i class="fa fa-sms"></i> </a></td>
        
         <td style="display:none;"><a href="mailto:<?php echo e($account->email); ?>"   class="btn btn-xs rounded"><i class="fa fa-envelope"></i> </a></td>
         
     
        </tr>
        
        
     </table>
</div><?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/accounts/accountspartial_singletotal.blade.php ENDPATH**/ ?>