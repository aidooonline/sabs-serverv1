 

<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Total Disbursed</strong></td>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($todaytotalDIS, 2, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisweektotalDIS, 2, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thismonthtotalDIS, 2, '.', ',')); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e(number_format($thisyeartotalDIS, 2, '.', ',')); ?></td>
            </tr>

      
    </table>
</div>

<div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
  <table style="background-color:#f4e9f7 !important;margin-top:20px;"  class="table-striped table-border table-panel rounded">
     
      <tr>
          <td class="mintd" style="padding:1px 1px;"><strong>Total Collections</strong></td>
           
        </tr>
          <tr>
              <td class="mintd" style="padding:1px 1px;">Today:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal"><span class="text-muted">GH¢</span> <?php echo e($todaytotal); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Week:</td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal"><span class="text-muted">GH¢</span> <?php echo e($thisweektotal); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Month:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e($thismonthtotal); ?></td>
            </tr>

            <tr>
              <td class="mintd" style="padding:1px 1px;">This Year:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal"><span class="text-muted">GH¢</span> <?php echo e($thisyeartotal); ?></td>
            </tr>

      
    </table>
</div>

<?php /**PATH /home/banqgego/public_html/nobsback/resources/views/loans/savingspartial_alltotal.blade.php ENDPATH**/ ?>