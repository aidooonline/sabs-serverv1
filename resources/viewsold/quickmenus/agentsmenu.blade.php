  <style>
        #quickmenu{
            position:fixed;right:2px;bottom:46px;z-index:99999;border-radius:40px !important;
            background-color:rgba(0,0,0,0.1);
            width:80px;
        }
        
#mobile-container {
  width: 230px;
  margin: auto;
   background-color:rgba(0,0,0,0.1) !important;
  height: auto;
  color: white;
  border-radius: 10px;
  position:fixed;
  right:5px;
  bottom:102px;
  z-index:999999999;
  display:none;
  
}

/* Style the navigation menu */
.topnav {
    border-radius: 10px;border-radius: 10px;
  overflow: hidden;
   background-color:rgba(0,0,0,0.3) !important;
  position: relative;
  /* Permalink - use to edit and share this gradient: https://colorzilla.com/gradient-editor/#081c2b+0,861d99+50,7db9e8+100&0.76+0,0.62+100 */


}

 

/* Style navigation menu links */
.topnav a {
  color: rgba(225,233,160,1);
  padding: 7px 8px;
  text-decoration: none;
  font-size: 13px;
  display: block;
  border-radius: 10px;  
background: -webkit-linear-gradient(top,  rgba(8,28,43,0.13) 0%,rgba(139,70,153,0.8) 12%,rgba(255,255,255,0.17) 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  rgba(8,28,43,0.13) 0%,rgba(139,70,153,0.8) 12%,rgba(255,255,255,0.17) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
 
 
  background-color:rgba(0,0,0,0.5) !important;
}

/* Style the hamburger menu */
.topnav a.icon {
  background: black;
  display: block;
  position: absolute;
  right: 0;
  top: 0;
}

a.divider{
    border-bottom:solid 1px rgba(255,255,255,0.4);
    margin-bottom:2px;
}

a.divider1{
    border-top:solid 1px rgba(255,255,255,0.9);
}
    </style>
    
    
    <a id="quickmenu" class="btn-purple btn rounded" href="#" onclick="showmobilemenu()">
        <i style="font-size:25px;" class="fas fa-chevron-circle-down"></i>
    </a>
    
<!-- Simulate a smartphone / tablet -->
<div id="mobile-container">

<!-- Top Navigation Menu -->
<div class="topnav">
  
  <div id="myLinks">
      
     @if(\Auth::user()->type=='Agents')
    <a class="divider1" href="#">Reg.Customers</a>
    
    @endif
    
     @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <a class="divider1" href="#">Reg.Customers Today</a>
    
    @endif
    
    @if(\Auth::user()->type=='Agents')
    <a class="divider1" href="#">Deposits</a>
     
    @endif
    
    @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <a class="divider1" href="#">Deposits</a>
     
    @endif
    
   @if(\Auth::user()->type=='Agents')
    <a class="divider1" href="#">Withdrawals</a>
    
    @endif
    
    @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <a class="divider1" href="#">Withdrawals</a>
    
    @endif
    
    @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <a class="divider1" href="#">Reversals</a>
    
    @endif
    
    @if(\Auth::user()->type=='Agents')
    <a class="divider1" href="#">Reversals</a>
    
    @endif
    
    @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    <a class="divider1" href="#">Commissions</a>
    
    @endif
    
    @if(\Auth::user()->type=='Agents')
    <a class="divider1" href="#">Commissions</a>
    
    @endif
    
    
    
  </div>
   
</div>
</div>

<script>
    function showmobilemenu(){
        $("#mobile-container").toggle('fast');
    }
</script>
    
    