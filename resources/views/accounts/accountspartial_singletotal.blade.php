

<div class="panel listdiv2" style="margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;margin-top:0;">
       
   

       

    <table  style="vertical-align: center;text-align:center;width:99%;margin-bottom:0 !important;">
        
        <tr> 
         <td class="btn btn-light"><a href="javascript:movetodepositpage('{{$account->account_number}}',this,'{{$account->phone_number}}');" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/depositicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Deposit</span>
               </a></td>
               
                <td class="btn btn-light"><a href="javascript:movetowithdrawalpage('{{$account->account_number}}',this,'{{$account->phone_number}}');" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/withdrawicon2.png" style="height:23px;width:auto;" class="fa" />
              <br/>
              <span class="smallspan">Withdraw</span>
               </a></td>
               
                  <td class="btn btn-light"><a href="javascript:movetorefundpage('{{$account->account_number}}',this,'{{$account->phone_number}}');" class="btn btn-xs  rounded">
              <img src="{{env('NOBS_IMAGES')}}icons/reversal2.png" style="height:23px;width:auto;" class="fa" />
               <br/>
              <span class="smallspan">Reverse</span>
               </a></td>
               
               <td class="btn btn-light"><a href="#" onclick="sendDataToReactNativeApp('{{$account->id}}');"  class="btn btn-xs  rounded" >
              <img src="{{env('NOBS_IMAGES')}}icons/camera.png" style="height:23px;width:auto;" class="fa" />
               <br/>
              <span class="smallspan">Camera</span>
               </a></td>
        
          
                
        
        <td class="btn btn-light"><a href="{{route('accounts.transactiondetails')}}/{{$account->account_number}}/" class="btn btn-xs  rounded"><i class="fa fa-eye"></i>
         <br/>
              <span class="smallspan">View</span>
        </a></td>
        
        
            <td class="btn btn-light">
                @if(\Auth::user()->type == 'Agents')
                
                
                @else
                 <a href="{{ route('accounts.edit',$account->id) }}" class="btn btn-xs  rounded"><i class="fa fa-pencil"></i> 
             <br/>
              <span class="smallspan">Edit</span>
            </a>
            
                
                @endif
               
            
            </td>
             

         <td style="display:none;"><a href="tel:{{$account->phone_number}}" class="btn btn-xs rounded"><i class="fa fa-phone"></i>  </a></td>
         <td style="display:none;"><a href="https://wa.me/{{$account->phone_number}}"    class="btn btn-xs  rounded"><i class="fa fa-whatsapp"></i> </a></td>
         <td style="display:none;"><a href="sms://{{$account->phone_number}}"   class="btn btn-xs rounded"><i class="fa fa-sms"></i> </a></td>
        
         <td style="display:none;"><a href="mailto:{{$account->email}}"   class="btn btn-xs rounded"><i class="fa fa-envelope"></i> </a></td>
         
     
        </tr>
        
        
     </table>
     
     
</div>

 
 