 





 <div class="listdiv2 rounded" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:10;padding-top:5px;">
     
      <div onclick="showhidediv('loadingdiv');location.reload()" class="dropdown ml-2 pl-2 ml-1 display-inline-block media-right text-right card">
         <a id="filterdash1" href="." data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
             <i class="fas fa-sync text-purple"></i>
         </a>
              
    </div>
     
     
    
    
   
   
   <div class="card-block text-center ml-2 mr-2 pl-2 pr-2">
                        <span class="badge badge-pill badge-secondary"> {{$nameoftransaction}}</span>
                    </div>
   
   
   
   
   <!-- TODAY -->
     @foreach($data as $thedata)
   <div class="displaydivs" id="todaydiv">
   
      <div class="col-xl-4 col-lg-6 col-md-6 col-12">
		<div class="card bg-white pt-2 pb-2">
			<div class="card-body">
				<div class="card-block pt-2 pb-0">
					<div class="media">
					    
					     <div style="padding-left:5px;"> 
  
                          @if($thedata->user_image == 'true')
                          <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle" src="{{env('NOBS_IMAGES')}}images/user_avatar/avatar_{{$thedata->userid}}.jpg?lastmod=<?php echo date("m/d/Y h:i:s a", time())?>" is_dataimage="{{$thedata->is_dataimage}}">
                          @else 
                          <img style="position:relative;float:left;margin-right:10px;" class="rounded-circle" src="{{env('NOBS_IMAGES')}}useraccounts/profileimage.png">
                          @endif 
                         </div>
                      
						<div class="media-body white text-left">
						{{$thedata->first_name}} {{$thedata->middle_name}} {{$thedata->surname}}</h6>
                              <h6 style="color:#724c78;text-align:left !important;">{{$thedata->amount}}</h6> 
                      
                      <h6 style="color:#5e7eb9;text-align:left !important;">{{$thedata->account_number}}</h6>
						</div>
						<div class="media-right text-right">
						    <i class="fas fa-user-friends text-purple font-medium-1" aria-hidden="true"></i>
						</div>
					</div>
				</div>
			 
			</div>
	 	</div>
	 </div>
    @endforeach
    
   
   
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
 
 

</script>





