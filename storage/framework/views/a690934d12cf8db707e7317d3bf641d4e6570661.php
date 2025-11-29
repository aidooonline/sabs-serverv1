 

  <div  class="modal fade row card-stats"  id="addcustomertoloan" tabindex="-1" role="dialog" aria-labelledby="addcustomertoloan" aria-hidden="true">

    <div style="margin-bottom:90px;background-color:#4d2257;border-radius:3px;padding-left:18px;padding-right:2px;padding-top:10px;height:570px;" class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content" style="border-radius:5px;">
        <?php echo $__env->make("layouts.searchloancustomer", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="modal-body" id="modalbody" style="background-color:#4d2257;padding:5px 10px !important; overflow-y: scroll;height:490px !important;padding-top:50px;margint-top:40px;">
            
            
        </div>
        </div>
        <div class="modal-footer">
          
         
        </div>
      </div>
    </div>
   
</div>

<style>
    .searchpanelinput{
        width:90%;
    }
    .searchpanelbtn{
        width:10%;
    }
</style>
<?php /**PATH /home/banqgego/public_html/nobs/resources/views/loanrequestdetail/addcustomertoloan.blade.php ENDPATH**/ ?>