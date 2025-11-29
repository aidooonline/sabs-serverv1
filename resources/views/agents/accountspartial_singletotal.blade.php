

<div class="panel listdiv2" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
   

       

    <table  id="agenttablstats" style="vertical-align: center;text-align:center;width:100%;margin-bottom:0 !important;">
        
        <tr> 
         <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}{{$account->created_by_user}}/{{$account->name}}/agentcommission" class="btn btn-xs  rounded">
             <i class="fas fa-hand-holding-usd" style="height:23px;width:auto;"  v></i>
            
              <br/>
              <span class="smallspan">Commissions</span>
               </a></td>
               
              <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}{{$account->created_by_user}}/{{$account->name}}/deposit" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/depositicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Deposits</span>
               </a></td>
               
                <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}{{$account->created_by_user}}/{{$account->name}}/withdraw" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/> 
              <span class="smallspan">Withdrawals</span>
               </a></td>
               
                  <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}{{$account->created_by_user}}/{{$account->name}}/refund" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/reversal2.png" style="height:23px;width:auto;" class="fa" />
               <br/>
              <span class="smallspan">Reversals</span>
               </a></td>
        
        
        <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}{{$account->created_by_user}}/{{$account->name}}/customersregistered" class="btn btn-xs  rounded">
            <i class="fa fa-users"></i>
         <br/>
                  <span class="smallspan">Registered</span>
        </a></td>
            
              
        </tr>
        <tr>
            
            
            
            <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}/{{$account->created_by_user}}/{{$account->name}}/loandisbursed" class="btn btn-xs  rounded">
            
            <i class="fa fa-funnel-dollar"></i>
         <br/>
                  <span class="smallspan">Loan Disbursed</span>
        </a></td>
        
         <td class="btn-light"><a href="{{route('agents.singletransactiondetails')}}/{{$account->created_by_user}}/{{$account->name}}/loanrepayment" class="btn btn-xs  rounded">
            
          <i class="fas fa-cart-arrow-down"></i>
         <br/>
                  <span class="smallspan">Loan Repayment</span>
        </a></td>
        
        <td class="btn-light"><a href="{{ env('BASE_URL')}}user/{{$account->id}}/edit" class="btn btn-xs  rounded"><i class="fa fa-pencil"></i> 
             <br/>
              <span class="smallspan">Edit</span>
            </a></td>
        
         <td></td>
            
            <td></td>
            
            <td></td>
        </tr>
        
        
     </table>
</div>