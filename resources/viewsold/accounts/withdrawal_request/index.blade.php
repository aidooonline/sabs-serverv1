 

 

@extends('layouts.admin')
 
@section('title')
 
@endsection
 
@section('action-btn')

@endsection
@section('content')

@include('layouts.inlinecss')

<div  class="row dashboardtext" style="padding-bottom:150px;padding-top:60px;">

 @include('layouts.search')
<div id="mainsearchdiv">
<h4 class="card-title" style="margin-top:20px;">Withdrawal Requests <span class="text-warning"> </span></h4>
 
<div class="tab">
  <a href="#" id="unapprovedclick" class="btn tablinks" onclick="openCity(event, 'unapproved')">Requests<span class="text-primary">({{$unapprovedcounts}})</span></a>
  <a href="#" class="btn tablinks" onclick="openCity(event, 'approved')">Approved<span class="text-primary">({{$approvedcounts}})</span></a>
  <a href="#" class="btn tablinks" onclick="openCity(event, 'paid')">Paid<span class="text-primary">({{$paidcounts}})</span></a>
  
</div>

 
    <nav aria-label="Page navigation" class="card">
  {{$accounts->links()}}
  
  </nav>
 

<div id="unapproved" class="tabcontent">
    
 
  @foreach($accounts as $account)

<div  id="accountbtnpanel_{{$account->id}}"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">

<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong>{{number_format($account->amount, 2, '.', ',')}}</strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="account_number">{{$account->account_number}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="accounttype">{{$account->account_type}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal">{{$account->det_rep_name_of_transaction}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Agent:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               {{$account->agentname}} 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">{{$account->created_at}}</td>
            </tr>
            
            <tr>
              <td class="mintd" style="padding:1px 1px;">
                   @if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner')
                  
                 
                             <button id="approve_{{$account->id}}" onclick="approvewithdrawaljvs(this);" class="btn btn-purple">Approve</button> 
                 
                         
                  
                  @endif
                   </td>
              <td class="mintd" style="padding:1px 1px;text-align:right !important;" id="thismonthtotal">
                  @if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner')
                  
                  
                               <button  id="decline_{{$account->id}}" onclick="declinewithdrawaljvs(this);" class="btn btn-dark">Decline</button>  
                          
                  
                  
                  
                  @endif
                  
                  
                  </td>
            </tr>
</table>

</div>
    
@endforeach


</div>

<div id="approved" class="tabcontent">
    
    
  @foreach($accountsapproved as $account)

<div  id="accountbtnpanel_{{$account->id}}"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">
<img src="{{env('NOBS_IMAGES')}}icons/approved.gif" style="width:70px;height:auto;position:absolute;top:10px;right:5px;" />
<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong>{{number_format($account->amount, 2, '.', ',')}}</strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal">{{$account->account_number}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal">{{$account->account_type}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal">{{$account->det_rep_name_of_transaction}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Agent:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               {{$account->agentname}} 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">{{$account->created_at}}</td>
            </tr>
             <tr>
              <td class="mintd" style="padding:1px 1px;"> </td>
              <td class="mintd" style="padding:1px 1px;text-align:right !important;" id="thismonthtotal">
                  <button style="float:right;text-align:right;font-size:12px !important;padding:7px 10px;" id="pay_{{$account->id}}" data-toggle="modal" data-target="#exampleModal4" onclick="showpaynowdialog('{{number_format($account->amount, 2, '.', ',')}}' ,this,'{{$account->phone_number}}')" class="btn btn-success">
                      <i class="fas fa-hand-holding-usd"></i> Pay Customer</button> </td>
            </tr>
            
</table>

</div>
    
@endforeach
</div>



<div id="paid" class="tabcontent">
  @foreach($accountspaid as $account)

<div  id="accountbtnpanel_{{$account->id}}"  class="accordion card listdiv" style="padding-bottom:0 !important;background:#fff;padding-left:0;padding-right:0;margin-left:0;margin-right:0;width:100% !important;">
<img src="{{env('NOBS_IMAGES')}}icons/paid.gif" style="width:70px;height:auto;position:absolute;top:10px;right:5px;" />
<table style="background-color:#f4e9f7 !important;margin-top:5px;font-size:17px !important;padding-bottom:0 !important;"  class="table table-striped rounded">
           <tr>
              <td class="mintd" style="padding:1px 1px;">Amount</strong></td> 
              <td class="mintd" style="padding:1px 1px;">
                  <span class="text-muted">GH¢</span><strong>{{number_format($account->amount, 2, '.', ',')}}</strong> 
              </td>
           </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc No:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal">{{$account->account_number}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Acc Type:</td>
              <td class="mintd" style="padding:1px 1px;" id="todaytotal">{{$account->account_type}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Customer: </td>
              <td class="mintd" style="padding:1px 1px;" id="thisweektotal">{{$account->det_rep_name_of_transaction}}</td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Paid By:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">
               {{$account->paid_by}} 
              </td>
            </tr>
            <tr>
              <td class="mintd" style="padding:1px 1px;">Datetime:</td>
              <td class="mintd" style="padding:1px 1px;" id="thismonthtotal">{{$account->created_at}}
              
              </td>
            </tr>
            
            
</table>

</div>
    
@endforeach
</div>


 
<nav aria-label="Page navigation" class="card">
  {{$accounts->links()}}
</nav>
 
    
     

</div>
</div>
<style>

/* Style the tab */
.tab {
  overflow: hidden;
 
  border-radius:10px;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 12px 5px !important;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px; 
  border-top: none;
}
 
table td[class='mintd'] {
        padding: 2px 8px !important;
         font-size:15px !important;
         color:#454545 !important;
    }

    .account_name{
        color:#666666;text-align:left !important;font-weight:bold;font-family:verdana;
    }

    .table-panel td{
        font-size:1em !important;
        color:rgb(65, 6, 65);
        font-family:Verdana, Geneva, Tahoma, sans-serif;
       
    }
    .accordion img {
        width: 65px;
        height: 65px;
    }

    .listdiv  {
        width: 25%;
        height: 120px;

         
    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;

        
    }

    .listdiv .listdiv img {
        width: 70px;
        height: 70px;
    }

    .listdiv .listdiv .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv .listdiv .text a,
    .listdiv .listdiv .text span {
        float: left;
        color: purple;
    }


    .listdiv2{
        height:auto !important;
        height:600px;
    }

    .listdiv2 .listdiv2 .image {
        width: 25%;
        height: auto;
 
    }

    .listdiv2 .listdiv2 img {
        width: 70px;
        height: 70px;
    }

    .listdiv2 .listdiv2 .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv2 .listdiv2 .text a,
    .listdiv2 .listdiv2 .text span {
        float: left;
        color: purple;
    }

.mintd{
    font-size:20px !important;
    font-family:verdana !important;
}

</style>


<script>

// Shorthand for $( document ).ready()
$(function() {
  document.getElementById('unapprovedclick').click();
});
    function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}




</script>




@include("layouts.modalview1")



@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush