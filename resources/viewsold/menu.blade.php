<div class="row card-stats">

    <div class="card-body pb-20">

        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        <a href="{{route('dashboard.index')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">

            <i class="fas fa-tachometer-alt text-purple"></i>
            <span class="mb-0 ">{{__('Dashboard')}}</span>
        </a>
        @endif

        @if(\Auth::user()->type=='Agents')
        <a href="{{route('dashboard.index')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">

            <i class="fas fa-tachometer-alt text-purple"></i>
            <span class="mb-0 ">{{__('My Dashboard')}}</span>
        </a>
        @endif

        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents'||
        \Auth::user()->type=='Teller')
        <a href="{{env('BASE_URL')}}accounts/create" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            style="float:left;">
            <i class="fas fa-user-plus text-purple"></i>
            <span class="mb-0 ">{{__('Register')}}</span>
        </a>
        @endif


        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        <a href="{{route('agents.index')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">
            <i class="fas fa-user-friends text-purple"></i>

            <span class="mb-0 ">{{__('Users')}}</span>
        </a>
        @endif

        @if(\Auth::user()->type=='Agents')
        <a href="{{route('agents.index')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">
            <i class="fas fa-user-friends text-purple"></i>

            <span class="mb-0 ">{{__('My Account')}}</span>
        </a>
        @endif


        <a style="display:none" href="#"
            onclick="showhidediv('loadingdiv');getbuttons('Savings***{{env('BASE_URL')}}savings/___Loans***{{env('BASE_URL')}}loans/')"
            class="col card icondiv" data-toggle="modal" data-target="#exampleModal4" class="col card icondiv"
            style="float:left;">

            <i class="fas fa-file-invoice-dollar text-purple"></i>
            <span class="mb-0 ">{{__('System Accounts')}}</span>
        </a>
        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        <a href="{{env('BASE_URL')}}savings/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">
            <i class="fas fa-piggy-bank text-purple"></i>

            <span class="mb-0 ">{{__('Savings')}}</span>
        </a>
        @endif





        <a href="#" onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;display:none">

            <i class="fas fa-file text-purple"></i>
            <span class="mb-0 ">{{__('Report')}}</span>
        </a>

        <a href="{{route('accounts.searchwithdrawer')}}/{{\Auth::user()->created_by_user}}"
            onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">

            <i class="fas fa-arrow-down text-purple"></i>
            <span class="mb-0 ">{{__('Withdraw')}}</span>
        </a>

        <a href="{{route('accounts.searchdeposit')}}/{{\Auth::user()->created_by_user}}"
            onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
            <i class="fas fa-arrow-up text-purple"></i>
            <span class="mb-0 ">{{__('Deposit')}}</span>
        </a>

        <a href="{{route('accounts.searchrefund')}}/{{\Auth::user()->created_by_user}}"
            onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
            <i class="fas fa-angle-double-left text-purple"></i>
            <span class="mb-0 ">{{__('Reversal')}}</span>
        </a>



        <a href="{{env('BASE_URL')}}accounts/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            style="float:left;">
            <i class="fas fa-search text-purple"></i>
            <span class="mb-0 ">{{__('Customers')}}</span>
        </a>

        <a href="{{route('withdrawrequests.lists')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            style="float:left;">
            <i class="fas fa-arrow-down text-purple"></i>
            <span class="mb-0 ">{{__('Withdrawal Request')}}</span>
        </a>



        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')

        <a href="{{env('BASE_URL')}}loans/" onclick="showhidediv('loadingdiv');" class="col card icondiv"
            class="col card icondiv" style="float:left;">
            <i class="fas fa-hand-holding-usd text-purple"></i>

            <span class="mb-0 ">{{__('Loans Accounts')}}</span>
        </a>
        @endif

        @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        <a href="{{route('loanrequests.requests')}}" onclick="showhidediv('loadingdiv');" class="col card icondiv"
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
            onclick="showhidediv('loadingdiv');" class="col card icondiv" style="float:left;">
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

    <div style="display:none;" class="col-md-12 d-none">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h6 class="text-muted mb-1">{{__('Total Users')}}</h6>
                        <span class="h3 font-weight-bold mb-0 ">{{$data['totalUser']}}</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon bg-gradient-primary text-white rounded-circle icon-shape">
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>