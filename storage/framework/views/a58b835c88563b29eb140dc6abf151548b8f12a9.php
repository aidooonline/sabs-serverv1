 
 
 <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
           <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
          <td class="mintd" style="padding:1px 1px;"><strong>Registered Customers</strong></td>
          <?php endif; ?>
          
           <?php if(\Auth::user()->type=='Agents'): ?>
          <td class="mintd" style="padding:1px 1px;"><strong>My Registered Customers</strong></td>
          <?php endif; ?>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">
                   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
                  Today: 
                   <?php endif; ?>
                   
                   <?php if(\Auth::user()->type=='Agents'): ?>
                 Today:
                   <?php endif; ?>
                   
                   
              </td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><?php echo e($todaycountDIS); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><?php echo e($thisweekcountDIS); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($thismonthcountDIS); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($thisyearcountDIS); ?></td>
            </tr>
              <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
             <tr>
              <td class="mintd" style="padding:1px 1px;">All Time:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><?php echo e($alltimecountDIS); ?></td>
            </tr>
            <?php endif; ?>

      
    </table>
</div>
 
 

<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Deposits</strong></td><td class="mintd"> 
          
          
          
            <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <a class="btn-purple rounded" href="<?php echo e(route('admreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/deposit">More</a></td>
            <?php endif; ?>
            
            <?php if(\Auth::user()->type=='Agents'): ?>
             <a class="btn-purple rounded" href="<?php echo e(route('reportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/deposit">More</a></td>
            <?php endif; ?>
            
          
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">
               
                  Today: (<?php echo e($todaycountDP); ?>)
                   
                   
              </td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalDP, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalDP, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalDP, 3, '.', ',')); ?></td>
            </tr>

  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalDP, 3, '.', ',')); ?></td>
            </tr>
<?php endif; ?>
      
    </table>
</div>


<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Withdrawals</strong></td>
          
          <td class="mintd">
                <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
                  <a class="btn-purple rounded" href="#">More</a>
                   <?php endif; ?>
                   
                   <?php if(\Auth::user()->type=='Agents'): ?>
                  <a class="btn-purple rounded" href="<?php echo e(route('reportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/withdraw">More</a>
                   <?php endif; ?>
          </td>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">
                  
                 Today:(<?php echo e($todaycountWD); ?>)
              </td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalWD, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalWD, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalWD, 3, '.', ',')); ?></td>
            </tr>
  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalWD, 3, '.', ',')); ?></td>
            </tr>
            <?php endif; ?>

      
    </table>
</div>


<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Reversals</strong></td>
           <td class="mintd">  <?php if(\Auth::user()->type=='Agents'): ?>
                  <a class="btn-purple rounded" href="<?php echo e(route('reportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/refund">More</a>
                   <?php endif; ?></td>
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">Today: (<?php echo e($todaycountRF); ?>)</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalRF, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalRF, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalRF, 3, '.', ',')); ?></td>
            </tr>

  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalRF, 3, '.', ',')); ?></td>
            </tr>
<?php endif; ?>
      
    </table>
</div>



<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Loans (Disbursed)</strong></td>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalDIS, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalDIS, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalDIS, 3, '.', ',')); ?></td>
            </tr>

  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalDIS, 3, '.', ',')); ?></td>
            </tr>
<?php endif; ?>
      
    </table>
</div>


<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Loans (Repayments)</strong></td>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotal, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotal, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotal, 3, '.', ',')); ?></td>
            </tr>
  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotal, 3, '.', ',')); ?></td>
            </tr>
<?php endif; ?>
      
    </table>
</div>



<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;background-color:#f4e9f7 !important">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Commission (Agents)</strong></td>
           
        </tr>
          <tr>
              
              
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalAGTCM, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalAGTCM, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalAGTCM, 3, '.', ',')); ?></td>
            </tr>

  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalAGTCM, 3, '.', ',')); ?></td>
            </tr>
<?php endif; ?>
      
    </table>
</div>



<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Commission (System)</strong></td>
           
        </tr>
         <tr>
              
              
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotalscm"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalSCM, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotalscm"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalSCM, 3, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotalscm"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalSCM, 3, '.', ',')); ?></td>
            </tr>
  <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotalscm"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalSCM, 3, '.', ',')); ?></td>
            </tr>

      <?php endif; ?>
    </table>
</div>


<?php /**PATH /home/banqgego/public_html/nobsbackend/resources/views/dashboard/savingspartial_alltotal.blade.php ENDPATH**/ ?>