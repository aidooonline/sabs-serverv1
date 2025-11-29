 



<h4 style="width:100%;position:relative;padding-left:20px;" class="text-warning"> General Ledger
</h4>

 <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:10px;padding-top:5px;padding-bottom:10px;">
      
    <a href="<?php echo e(route('ledgergeneral.create')); ?>" style="position:absolute;right:15px;top:30px;" href="#" class="btn btn-purple  mr-1 btn-fab btn-sm">
      <i class="fa fa-plus"></i>
    </a>
     
   
   
   
   <!-- TODAY -->
   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
  

   <?php $__currentLoopData = $ledgergeneral; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
   <div class="col-xl-4 col-lg-6 col-md-6 col-12">
	 <div class="card bg-white pt-2 pb-2">
		 <div class="card-body">
			 <div class="card-block pt-2 pb-0">
				 <div class="media"> 
                    <div class="media-left text-left">
                        <i class="fas fa-list text-purple font-medium-1"></i>
                    </div>
					 <div class="media-body white text-left">
						 <a href="<?php echo e(route('subledger.list')); ?>/<?php echo e($ledger->name); ?>/<?php echo e($ledger->id); ?>/<?php echo e($ledger->acname); ?>/<?php echo e($ledger->acid); ?>" ><span class="grey darken-1"><?php echo e($ledger->name); ?></span></a><br/>
                         <span class="text-secondary sm" style="float:right;font-size:12px;"><?php echo e($ledger->sub_count); ?> <?php echo e($ledger->acname); ?> </span>
                        <h6 class="font-small-5 mb-0 text-purple"><span  class="text-muted ghs"></span></h6>
					 </div>
					 
				 </div>
			 </div>
		  
		 </div>
	  </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
   

   

  <?php endif; ?>
   <!-- END TODAY -->
   
    
    
   
  
   
   
 
   
     
   
 

<style>
    .ghs{
        font-weight:normal !important;
        font-size:13px;
    }
    
    .displaydivs{
        padding-bottom:50px;
    }
    
     
    #thisweekdiv,
    #thismonthdiv,
    #thisyeardiv,
    #alltimediv{
        display:none;
    }
</style>

<script type="text/javascript">

function getfilter(that){
    switch(that){
        case 'Today':
        
         $('.displaydivs').hide();
         $('#todaydiv').show(300);
         $('#filtertext').html(that);
        break;
        
        case 'This Week':
         $('.displaydivs').hide();
         $('#thisweekdiv').show(300); 
         $('#filtertext').html(that);
         
        break;
        
        case 'This Month':
           $('.displaydivs').hide();
         $('#thismonthdiv').show(300);
         $('#filtertext').html(that);
         
        break;
        
        case 'This Year':
            $('.displaydivs').hide();
         $('#thisyeardiv').show(300);
         $('#filtertext').html(that);
         
        break;
        
        case 'All Time':
            $('.displaydivs').hide();
         $('#alltimediv').show(300);
         $('#filtertext').html(that);
         
        break;
            
       
    }
}

function getfilterbyagent(that){
   
  if(that == 'agentid_agentallid12345'){
      showhidediv('loadingdiv');
      location.href="<?php echo e(route('dashboard.index')); ?>";
      
  }else{
      showhidediv('loadingdiv');
      location.href="<?php echo e(route('agentquerydashboard.index')); ?>/" + that;
      
  }
}

</script>





<?php /**PATH /home/banqgego/public_html/nobs001/resources/views/ledgergeneral/savingspartial_alltotal.blade.php ENDPATH**/ ?>