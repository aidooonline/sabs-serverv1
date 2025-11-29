 





 <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:10;padding-top:5px;">
     
      <div onclick="showhidediv('loadingdiv');location.reload()" class="dropdown ml-2 pl-2 ml-1 display-inline-block media-right text-right card">
         <a id="filterdash1" href="." data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
             <i class="fas fa-sync text-purple"></i>
         </a>
              
    </div>
     
     
    
    
   
   
   <div class="card-block text-center ml-2 mr-2 pl-2 pr-2">
                        <span class="badge badge-pill badge-secondary"> Agent: <?php echo e($agentname); ?></span> <span class="badge badge-pill badge-secondary"> <?php echo e($nameoftransaction); ?></span>
                    </div>
   
   
   
   
   <!-- TODAY -->
     <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thedata): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
   <div class="displaydivs" id="todaydiv">
   
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
					    
					     <div style="padding-left:2px;"> 
  
                          <?php if($thedata->user_image == 'true'): ?>
                          <img style="position:relative;float:left;margin-right:10px;height:70px;width:70px;" class="rounded-circle" src="<?php echo e(env('NOBS_IMAGES')); ?>images/user_avatar/avatar_<?php echo e($thedata->userid); ?>.jpg?lastmod=<?php echo date("m/d/Y h:i:s a", time())?>" is_dataimage="<?php echo e($thedata->is_dataimage); ?>">
                          <?php else: ?> 
                          <img style="position:relative;float:left;margin-right:10px;height:70px;width:70px;" class="rounded-circle" src="<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/profileimage.png">
                          <?php endif; ?> 
                         </div>
                      
						<div class="media-body white text-left">
						 <h5 style="color:#724c78;text-align:left !important;"><?php echo e($thedata->amount); ?></h5> 
						<h6><?php echo e($thedata->det_rep_name_of_transaction); ?></h6> 
                      <h6 style="color:#5e7eb9;text-align:left !important;"><?php echo e($thedata->account_number); ?></h6>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     <div class="displaydivs">
    <div class="col-xl-4 col-lg-6 col-md-6 col-12">
        	<div class="card bg-white pt-2 pb-2">
        	    	<div class="card-body">
				<div class="card-block pt-2 pb-0">
    <nav aria-label="Page navigation" class="card">
  <?php echo e($data->links()); ?>

  
  </nav>
  </div>
  </div>
  </div>
  </div>
  </div>
   
   </div>
   <!-- END ALL TIME -->
   
   
 

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





<?php /**PATH /home/banqgego/public_html/nobs002/resources/views/dashboard/transactiondetails.blade.php ENDPATH**/ ?>