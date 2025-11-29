 
<style>
  .bgpurple{
    color:#807f81;


    background-color:#dbd8dd57 !important;
    font-weight: :normal !important;

  }
  .textcolor1{
    color:#29B6F6;
    font-weight: :normal !importan;

  }
</style>

 <div style="padding-top:30px;" class="listdiv2 rounded">
    <a href="{{route('loans.create')}}" style="position:absolute;right:15px;top:50px;" href="#" class="btn btn-purple  mr-1 btn-fab btn-sm">
      <i class="fa fa-plus"></i>
    </a>
 
 
  @foreach($loansaccounts as $loans)

  
<!-- TODAY -->



<div class="displaydivs" id="todaydiv">


  
  @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <div id="accountbtnpanel_{{$loans->id}}" class="col-xl-4 col-lg-6 col-md-6 col-12">
  <div class="card bg-white pt-2 pb-2">
    <div class="card-body">
      <div class="card-block pt-2 pb-0">
        <div class="media">
          
          <div class="media-body white text-left">
            <h6  class="font-medium-5 mb-0 text-purple">{{$loans->name}}</h6>
            <span class="tag bgpurple">Interest: <span class="textcolor1">{{$loans->interest}}</span></span> 
            <span class="tag bgpurple">Duration: <span class="textcolor1">{{$loans->duration}}</span></span> 
            <span class="tag bgpurple">Interest Per Anum: <span class="textcolor1">{{$loans->interest_per_anum}}</span></span> 
            <span class="tag bgpurple">Processing Fee: <span class="textcolor1">{{$loans->processing_fee}}%</span></span> 
            <span class="tag bgpurple">Collateral Fee: <span class="textcolor1">{{$loans->collateral_fee}}%</span></span> 
            <span class="grey darken-1"></span>
          </div>
          
        </div>

        <div class="media-right text-left mr-2 mt-2">
          <a href="{{ route('loans.edit',$loans->id) }}" class="btn btn-light mr-1 btn-fab btn-sm">
            <i class="fa fa-pencil"></i>
          </a>
          <a class="btn btn-light mr-1 btn-fab btn-sm">
            <i class="fa fa-eye"></i>
          </a>
        </div>
      </div>
     
    </div>
   </div>
  </div>
  @endif
   
  <!-- END TODAY -->
 
 
@endforeach
 
 






 




<style>
.bgpurple,.tag{
  background-color:aliceblue !important;
}
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
  location.href="{{route('dashboard.index')}}";
  
}else{
  showhidediv('loadingdiv');
  location.href="{{route('agentquerydashboard.index')}}/" + that;
  
}
}

</script>


 
