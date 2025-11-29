


<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content">
         
        <div class="modal-body" id="modalbody2" style="padding:5px 10px !important;">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         
        </div>
      </div>
    </div>
  </div>  



  <div  class="modal fade row card-stats"  id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel5" aria-hidden="true">

    <div style="margin-bottom:5px;background-color:#4d2257;border-radius:3px;padding-left:18px;padding-right:2px;padding-top:10px;" class="modal-dialog modal-dialog-slideout modal-lg" role="document">
      <div class="modal-content" style="border-radius:5px;">
         
        <div class="modal-body" id="modalbody" style="padding:5px 10px !important;">
            <div style="float:right;">
                <button type="button" class="btn btn-purple"  style="color:#ffffff !important;float:right;" data-dismiss="modal">
                  <i class="fas fa-times-circle"></i>
                </button>
              </div>
          <div class="card-body pb-20" style="position:relative;float:left;padding:0;">

            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{route('dashboard.index')}}"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 ">{{__('Dashboard')}}</span>
            </a>
            @endif
    
            @if(\Auth::user()->type=='Agents')
            <a href="{{route('dashboard.index')}}"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
    
                <i class="fas fa-tachometer-alt text-purple"></i>
                <span class="mb-0 ">{{__('My Dashboard')}}</span>
            </a>
            @endif

            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{route('ledgergeneral.index')}}"
                {{-- onclick="showhidediv('loadingdiv');getbuttons('Savings***{{env('BASE_URL')}}savings/___Loans***{{env('BASE_URL')}}loans/')" --}}
                class="col card icondiv" class="col card icondiv"
                style="float:left;">
    
                <i class="fas fa-file-invoice-dollar text-purple"></i>
                <span class="mb-0 ">{{__('General Ledger')}}</span>
            </a>
            @endif
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents'||
            \Auth::user()->type=='Teller')
            <a href="{{env('BASE_URL')}}accounts/create"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-user-plus text-purple"></i>
                <span class="mb-0 ">{{__('Register')}}</span>
            </a>
            @endif
    
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{route('agents.index')}}"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 ">{{__('Users')}}</span>
            </a>
            @endif
    
            @if(\Auth::user()->type=='Agents')
            <a href="{{route('agents.index')}}"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-user-friends text-purple"></i>
    
                <span class="mb-0 ">{{__('My Account')}}</span>
            </a>
            @endif
    
           
            
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{env('BASE_URL')}}savings/"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-piggy-bank text-purple"></i>
    
                <span class="mb-0 ">{{__('Savings')}}</span>
            </a>
            @endif
    
    
    
    
    
            <a href="#"  class="col card icondiv" style="float:left;display:none">
    
                <i class="fas fa-file text-purple"></i>
                <span class="mb-0 ">{{__('Report')}}</span>
            </a>
    
            <a href="{{route('accounts.searchwithdrawer')}}/{{\Auth::user()->created_by_user}}"
                 class="col card icondiv" style="float:left;">
    
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 ">{{__('Withdraw')}}</span>
            </a>
    
            <a href="{{route('accounts.searchdeposit')}}/{{\Auth::user()->created_by_user}}"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 ">{{__('Deposit')}}</span>
            </a>
    
            <a href="{{route('accounts.searchrefund')}}/{{\Auth::user()->created_by_user}}"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-left text-purple"></i>
                <span class="mb-0 ">{{__('Reversal')}}</span>
            </a>
    
    
    
            <a href="{{env('BASE_URL')}}accounts/"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-search text-purple"></i>
                <span class="mb-0 ">{{__('Customers')}}</span>
            </a>
    
            <a href="{{route('withdrawrequests.lists')}}"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 ">{{__('Withdrawal Request')}}</span>
            </a>
    
    
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
    
            <a href="{{env('BASE_URL')}}loans/"  class="col card icondiv"
                class="col card icondiv" style="float:left;">
                <i class="fas fa-hand-holding-usd text-purple"></i>
    
                <span class="mb-0 ">{{__('Loan Accounts')}}</span>
            </a>
            @endif
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{env('BASE_URL')}}loanrequests/"  class="col card icondiv"
                style="float:left;">
                <i class="fas fa-arrow-down text-purple"></i>
                <span class="mb-0 ">{{__('Loan Request')}}</span>
            </a>
            @endif
    
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{env('BASE_URL')}}loanmigrations/" class="col card icondiv" style="float:left;">
                <i class="fas fa-angle-double-right text-purple"></i>
                <span class="mb-0 ">{{__('Loan Migration')}}</span>
            </a>
            @endif
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a href="{{route('accounts.searchloan')}}/{{Auth::user()->created_by_user}}"
                 class="col card icondiv" style="float:left;">
                <i class="fas fa-arrow-up text-purple"></i>
                <span class="mb-0 ">{{__('Loan Repayment')}}</span>
            </a>
            @endif
    
            <a href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-list-ol text-purple"></i>
                <span class="mb-0 ">{{__('Daily Log')}}</span>
            </a>
    
    
    
    
            <a href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-door-closed text-purple"></i>
                <span class="mb-0 ">{{__('Closing/Opening')}}</span>
            </a>
    
    
    
            @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
            <a style="display:none" href="#" class="col card icondiv" style="float:left;">
                <i class="fas fa-calculator text-purple"></i>
                <span class="mb-0 ">{{__('Loan Calculator')}}</span>
            </a>
            @endif
    
        </div>
        </div>
        <div class="modal-footer">
          
         
      

          
         
        </div>
      </div>
    </div>
  
  
  
  

   


</div>

<style>
    
   .icondiv span {
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    font-size: 13px; color:purple !important;
}
   </style>