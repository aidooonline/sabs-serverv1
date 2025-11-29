 





 <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:10;padding-top:5px;">
     
      <div onclick="showhidediv('loadingdiv');location.reload()" class="dropdown ml-2 pl-2 ml-1 display-inline-block media-right text-right card">
         <a id="filterdash1" href="." data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
             <i class="fas fa-sync text-purple"></i>
         </a>
              
    </div>
     
     <div style="margin-right:0" class="dropdown display-inline-block media-right text-right card">
         <a id="filterdash1" href="#" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
             <i class="fas fa-filter text-purple"></i>

             <span id="filtertext" class="mx-1 blue-grey darken-4 text-bold-400">Today</span>
         </a>
              <div class="apps dropdown-menu text-purple">
                <div class="arrow_box">
                    <a href="#" onclick="getfilter('Today')" class="dropdown-item py-1"><span>Today</span></a>
                    <a href="#" onclick="getfilter('This Week')" class="dropdown-item py-1"><span>This Week</span></a>
                    <a href="#" onclick="getfilter('This Month')" class="dropdown-item py-1"><span>This Month</span></a>
                    <a href="#" onclick="getfilter('This Year')" class="dropdown-item py-1"><span>This Year</span></a>
                    <a href="#" onclick="getfilter('All Time')" class="dropdown-item py-1"><span>All Time</span></a>
                </div>
              </div>
    </div>
    
    
   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
    
     <div class="dropdown display-inline-block media-right text-right card">
         <a id="filterdash3" href="#" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
             <i class="fas fa-user text-purple"></i>
              
             <span id="agentquerynamediv" class="mx-1 blue-grey darken-4 text-bold-400">Select</span>
         </a>
              <div class="apps dropdown-menu text-purple">
                <div class="arrow_box">
                    <a href="#" onclick="getfilterbyagent('agentid_agentallid12345')" id="agentid_agentallid12345" class="dropdown-item py-1"><span>All</span></a>
                    
                    <?php $__currentLoopData = $agentdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theagent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <a href="#" onclick="getfilterbyagent('<?php echo e($theagent->created_by_user); ?>')" id="agentid_<?php echo e($theagent->created_by_user); ?>" class="dropdown-item py-1"><span><?php echo e($theagent->name); ?></span></a>
                    
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                </div>
              </div>
    </div>
   <?php endif; ?>
   
   <div class="card-block text-center ml-2 mr-2 pl-2 pr-2">
                        <span class="badge badge-pill badge-secondary"><?php echo e($agentqueryname); ?></span>
                    </div>
   
   
   
   
   <!-- TODAY -->
   <div class="displaydivs" id="todaydiv">
   
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><?php echo e($todaycountDIS); ?></h6>
							<span class="grey darken-1">Registered Customers</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
    
   
   
    
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/deposit`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalDP, 3, '.', ',')); ?></h6>
							<span class="grey darken-1"><?php echo e($todaycountDP); ?> Deposits</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-down text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   
   
     
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/withdraw`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalWD, 3, '.', ',')); ?></h6>
							<span class="grey darken-1"><?php echo e($todaycountWD); ?> Withdrawals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-up text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   
   
    
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/refund`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalRF, 3, '.', ',')); ?></h6>
							<span class="grey darken-1"><?php echo e($todaycountRF); ?> Reversals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-undo text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/loandisbursed`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalDIS, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Disbursed</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
      <?php endif; ?>
   
   
     
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/loanrepayment`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotal, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Payments</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-reply-all text-purple font-medium-1"></i>
						    
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
    
   
    
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/agentcommission`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalAGTCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Agents Commission</span>
						</div>
						<div class="media-right text-right"> 
						<i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div onclick="location.href = `<?php echo e(route('dashboardadmreportsview')); ?>/<?php echo e(\Auth::user()->created_by_user); ?>/<?php echo e(\Auth::user()->name); ?>/systemcommission`" class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($todaytotalSCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">System Commission</span>
						</div>
						<div class="media-right text-right">
						  <i class="fas fa-file-invoice-dollar text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   </div>
   <!-- END TODAY -->
   
   
   <!-- THIS WEEK -->
   <div class="displaydivs" id="thisweekdiv">
   
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><?php echo e($thisweekcountDIS); ?></h6>
							<span class="grey darken-1">Registered Customers</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
    
   
   
    
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalDP, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Deposits</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-down text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
  
   
     
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalWD, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Withdrawals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-up text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   
   
   
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalRF, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Reversals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-undo text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
  
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalDIS, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Disbursed</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotal, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Payments</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-reply-all text-purple font-medium-1"></i>
						    
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalAGTCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Agents Commission</span>
						</div>
						<div class="media-right text-right"> 
						<i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisweektotalSCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">System Commission</span>
						</div>
						<div class="media-right text-right">
						  <i class="fas fa-file-invoice-dollar text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   </div>
   <!-- END THIS WEEK -->
   
   
   <!-- THIS MONTH -->
   <div class="displaydivs" id="thismonthdiv">
   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><?php echo e($thismonthcountDIS); ?></h6>
							<span class="grey darken-1">Registered Customers</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalDP, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Deposits</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-down text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalWD, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Withdrawals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-up text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalRF, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Reversals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-undo text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalDIS, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Disbursed</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotal, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Payments</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-reply-all text-purple font-medium-1"></i>
						    
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalAGTCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Agents Commission</span>
						</div>
						<div class="media-right text-right"> 
						<i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thismonthtotalSCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">System Commission</span>
						</div>
						<div class="media-right text-right">
						  <i class="fas fa-file-invoice-dollar text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   </div>
   <!-- END THIS WEEK -->
   
   
    <!-- THIS YEAR -->
   <div class="displaydivs" id="thisyeardiv">
   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><?php echo e($thisyearcountDIS); ?></h6>
							<span class="grey darken-1">Registered Customers</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalDP, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Deposits</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-down text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalWD, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Withdrawals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-arrow-circle-up text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalRF, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Reversals</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-undo text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6 class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalDIS, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Disbursed</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
     <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6   class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotal, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Loan Payments</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-reply-all text-purple font-medium-1"></i>
						    
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalAGTCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">Agents Commission</span>
						</div>
						<div class="media-right text-right"> 
						<i class="fas fa-hand-holding-usd text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
    <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media"> 
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><span  class="text-muted ghs">GH¢ </span><?php echo e(number_format($thisyeartotalSCM, 3, '.', ',')); ?></h6>
							<span class="grey darken-1">System Commission</span>
						</div>
						<div class="media-right text-right">
						  <i class="fas fa-file-invoice-dollar text-purple font-medium-1"></i>
						    
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   </div>
   <!-- END THIS YEAR -->
   
   
   
    <!-- ALL TIME -->
   <div class="displaydivs" id="alltimediv">
   <?php if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner'): ?>
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
						<div class="media-body white text-left">
							<h6  class="font-medium-5 mb-0 text-purple"><?php echo e($alltimecountDIS); ?></h6>
							<span class="grey darken-1">Registered Customers</span>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
   <?php endif; ?>
   
   
    
    
   
   
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





<?php /**PATH /home/banqgego/public_html/sabs2/resources/views/dashboard/savingspartial_alltotal.blade.php ENDPATH**/ ?>