<style>
    .bgpurple {
        color: #807f81; 
        background-color: #dbd8dd57 !important;
        font-weight: :normal !important;
  
    }
  
    .textcolor1 {
        color: #29B6F6;
        font-weight: :normal !importan; 
    }
  </style>
  
   
   
      <h4 style="width:100%;position:relative;padding-left:20px;" class="text-warning"> Loan Migrations
      </h4>
   
      <a style="position:absolute;right:20px;top:40px;" href="{{route('loanrequests.create')}}"  href="#"
      class="btn btn-purple  mr-1 btn-fab btn-sm">
      <i class="fa fa-plus"></i>
      </a>
  
  
    @foreach($loanmigrations as $loanrequest)
          <!-- TODAY -->
        
          <div class="displaydivs"  id="todaydiv">
        
              @if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
              <div  id="accountbtnpanel_{{$loanrequest->id}}" class="col-xl-4 col-lg-6 col-md-6 col-12">
               
                  <div style="padding-top:0 !important;" class="card bg-white pt-2 pl-2">
                      <div  class="card-body">
                          <div class="card-block pt-1 pb-0 pl-0">
                             <ul style="border:0 !important;padding-left:15px;" class="list-group">
                                  @if($loanrequest->customer_picture)
                                  <img src="" profilevalue="{{$loanrequest->customer_picture}}" class="profilepic" style="width:auto;max-width:70px;height:70px;position:absolute;top:10px;right:5px;z-index:2;border-radius:20px;" />
                                  @else
                                  <img src="{{env('NOBS_IMAGES')}}/useraccounts/profileimage.png" style="width:auto;height:70px;max-width:70px;position:absolute;top:10px;right:5px;z-index:2;border-radius:20px;" />
                                 
                                  @endif    
   
                                  <li style="padding-top:0 !important;padding-bottom:0 !important;padding-left:10px;">
                                      <span class="row  mt-1"><strong class="font-medium-2 mb-0 pl-0 ml-0" style="color:#1a84b6">
                                              {{number_format($loanrequest->approved_amount, 2, '.', ',')}}</strong></span>
                                  </li>
                                 <li style="padding-top:0 !important;padding-bottom:0 !important" class="list-group-item"> 
                                      <span class="row mt-1"><span style="padding-left:10px;" class="textcolor1">
                                          {{$loanrequest->first_name}} {{$loanrequest->surname}} 
                                      </span> <span class="row mt-1"> <span style="padding-left:10px;">  </span></span>
                                  </li>
                                  <li style="padding-top:0 !important;padding-bottom:0 !important" class="list-group-item">
                                      <span class="row mt-1"><span style="padding-left:10px;font-weight:bold;color:#0e72a0" class="textcolor1">
                                          {{$loanrequest->loanname}},<span 
                                          style="font-weight:normal !important;padding-left:5px;">To be paid in 
                                          {{number_format($loanrequest->payment_duration, 0, '.', ',')}} days
                                      </span></span></span>
                                  </li>
                                  <li style="padding-top:0 !important;padding-bottom:0 !important;padding-right:0;padding-left:0 !important;" class="list-group-item">
                              
                                   
                                    <a style="float:right;" onclick="addto_disbursement_form('{{$loanrequest->first_name}} {{$loanrequest->surname}}',{{$loanrequest->amount}},'{{$loanrequest->loan_account_number}}',{{$loanrequest->id}},'{{$loanrequest->customer_account_number}}','{{$loanrequest->loanname}}','{{$loanrequest->__id__}}','{{$loanrequest->phone_number}}','{{$loanrequest->phone}}','{{$loanrequest->agentname}}');" href="#"
                                        class="btn btn-dark mr-1 btn-fab btn-sm">
                                        Disburse <i class="fa fa-angle-double-right"></i>
                                    </a>
                                  </li>
                              </ul>
        
                            
                          </div>
                          
                      </div>
                  </div>
              </div>
        
        
              @endif
        
          </div>
        
        

       

    
          <!-- END TODAY -->
          @endforeach
   <nav style="margin-left:13px;margin-right:13px;padding-bottom:10px;padding-left:5px;padding-right:5px;padding-top:0 !important;" aria-label="Page navigation" class="card">
              {{$loanmigrations->links()}}
   </nav>


   <div class="modal fade" id="ledgerdetailcreateform" tabindex="-1" role="dialog" aria-labelledby="ledgerdetailcreateform" aria-hidden="true">
   <div class="modal-dialog modal-dialog-slideout modal-lg" role="document">
       <div style="padding-bottom:0 !important;margin-bottom:0 !important;" class="modal-content">

           <div class="modal-body" style="padding:25px 5px !important;margin-bottom:0 !important;">

               <div style="padding-top:10px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">

                   {{Form::open(array('id'=>'mysubmitform','url'=>'ledgergeneralsub/storesubledger/','method'=>'post','enctype'=>'multipart/form-data'))}}
                   @csrf
                   
                   <div>
                       <div style="display:none;" class="col-12">
                           <div class="form-group">
                               <label>Sub Ledger Name</label>
                               <input id="dr_name" name="dr_name" class="form-control" type="text" value="" />
                               <input id="cr_name" name="cr_name" class="form-control" type="text" value="" />
                               <input type="hidden" name="myselected_ledger" id="myselected_ledger"
                                   value="" />

                               <input id="name" name="name" class="form-control" type="text"
                                   value="" />
                                  
                

                               <input type="text" id="ac_type" name="ac_type" class="form-control"
                                   value="" readonly />
                                   
                               <input type="text" id="amount" name="amount" class="form-control" value="" />
                               <input type="text" id="parent_id" name="parent_id" class="form-control"
                                   value="" readonly />
                               <input type="text" id="trans_id" name="trans_id" class="form-control" value=""
                                   readonly />
                               <input type="number" id="actual_value" name="actual_value" class="form-control"
                                   value="" />
                                   <input type="number" id="disbursed_id" name="disbursed_id" class="form-control"
                                   value="" />
                                   <input type="text" id="customer_account_number" name="customer_account_number" class="form-control"
                                   value="" />
                                   <input type="text" id="loanname" name="loanname" class="form-control"
                                   value="" />
                                   <input type="text" id="__id__" name="__id__" class="form-control"
                                   value="" />

                                   <input type="text" id="customer_phone" name="customer_phone" class="form-control"
                                   value="" />

                                   <input type="text" id="userphone" name="userphone" class="form-control"
                                   value="" />
                                   <input type="text" id="customer_name" name="customer_name" class="form-control"
                                   value="" />
                                   <input type="text" id="agentname" name="agentname" class="form-control"
                                   value="" />

                                 
                           </div>
                       </div>

                      

                       <div class="col-12">

                        <div class="col-12">
                            <div class="form-group">
                              <h3>Loan Disbursement</h3>
                            </div>
                        </div>

                           <div class="col-12">
                               <div class="form-group">
                                   <label>Customer Name</label>
                                   <input type="text" id="c_name" name="c_name" class="form-control" value="" readonly/>
                               </div>
                           </div>
 

                           <div class="col-12">
                            <div class="form-group">
                                <label>Loan Amount</label>
                                <input type="text" id="loan_amount" name="loan_amount" class="form-control" value="" readonly/>
                            </div>
                           </div>
                             
                           <div class="col-12">
                            <div class="card border-left my-4 border-danger border-3">
                                <div class="card-block pt-3">
                                    <div class="clearfix">
                                        <h5 class="text-bold-500 info float-left">Prompt:</h5>
                                         
                                    </div>

                                    <input type="hidden" value="Loan Receivable <b>to account number</b>: <span id='customer_loan_account_no_disp_id2' class='text-info'></span>" />
                                    
                                    <p>You are about to make a deposit to the above <b>customer loan account number</b>: <span id="customer_loan_account_no_disp_id" class="text-info"></span>. Kindly verify the amount before proceeding. </p>
                                   
                                </div>
                            </div>
                            <div class="form-group">
                                {{Form::submit(__('Continue Disbursement'),array('class'=>'btn btn-sm btn-purple rounded-pill
                                mr-auto','id'=>'submitdata'))}}{{Form::close()}}
 
                                <input type="button" value="Cancel" class="btn btn-sm btn-danger rounded-pill mr-auto" data-dismiss="modal" />
                            </div>
                           </div>
                           
                        </div>

                          

                      
                   </div>

                   {{Form::close()}}

               </div>
           </div>

       </div>
   </div>
</div>


<script>
  $(document).ready(function () {
       // dr_getselectedtexter();
        //cr_getselectedtexter();

        $('form').on('submit', function (e) {
            e.preventDefault();
            //rest of code
            
            let thevalue1 = $('#loan_amount').val();
            thevalue1 = -Math.abs(thevalue1);

            let thevalue = $('#loan_amount').val();
            let mydisbursedid = $('#disbursed_id').val();
            let loanaccountnumber = $('#customer_loan_account_no_disp_id').html().trim();
            
            let customer_account_number = $('#customer_account_number').val();
            let loanname = $('#loanname').val();

            let customerphone = $('#customer_phone').val();
            let userphone = $('#userphone').val();
            let customer_name =$('#customer_name').val();
            let agentname =$('#agentname').val();

            

            let __id__ = $('#__id__').val();
            let description = thevalue + ' Debit to Loan Receivables for customer loan account number: ' + $('#customer_loan_account_no_disp_id2').html() + 'Transacion ID: ' + mydisbursedid;

            let msg = 'Dear ' + customer_name + ', GHS ' + thevalue + ' has been deposited to your loan account number: ' + $('#customer_loan_account_no_disp_id').html() + ' Call your agent on ' + userphone + ' to verify and withdraw your loan.';

            let agentmsge = 'Dear ' + agentname + ', a disbursal of GHS ' + thevalue + ' has been issued to your customers loan account: ' + $('#customer_loan_account_no_disp_id').html() + '. Verify by informing customer to withdraw. Thanks.';
            
            // For Debit

            
            
            disburse_loan('Loan Receivables',35,24,thevalue,description,'Debit','Loan Receivables',4,4,thevalue,'Debit',mydisbursedid,loanaccountnumber,loanname,__id__,customer_account_number,'true',customerphone,userphone,msg,agentname);

            description = thevalue1 + ' Credit to the customer loan account number: ' + $('#customer_loan_account_no_disp_id2').html() + 'Transacion ID: ' + mydisbursedid;

            setTimeout(() => {
                //For Credit
                
                disburse_loan('Customer Deposits',35,24,thevalue,description,'Credit','Customer Deposits',4,4,thevalue1,'Debit',mydisbursedid,loanaccountnumber,loanname,__id__,customer_account_number,'false',customerphone,userphone,msg,agentname);
            }, 300);


            setTimeout(() => {
                make_auto_depositjvs(customerphone,msg,loanaccountnumber,loanname,thevalue,agentname,customer_account_number,agentmsge,userphone);
            }, 400);
           
            //document.getElementById("mysubmitform").submit();   
           
        })


    });
 
    function addto_disbursement_form(customer_name,loan_amount,loan_account_no,disbursedid,customer_account_number,loanname,__id__,customer_phone,userphone,agentname){
 
        
        $('#c_name').val(customer_name);
        $('#loan_amount').val(loan_amount);
 
        $('#customer_loan_account_no_disp_id').html(loan_account_no);
        $('#customer_loan_account_no_disp_id2').html(loan_account_no);
        
        $('#disbursed_id').val(disbursedid);
        $('#customer_account_number').val(customer_account_number);
        $('#loanname').val(loanname);
        $('#__id__').val(__id__);

        $('#customer_phone').val(customer_phone);
        $('#customer_name').val(customer_name);
        $('#userphone').val(userphone);
        $('#agentname').val(agentname);

        

        $('#ledgerdetailcreateform').modal('show');
    }



 

function disburse_loan(name,ac_type,parent_id,actual_value,description,debitorcredit_id,dr_name,dr_account,cr_account,theamount,cr_name,disbursedid,acnumber,account_type,__id__,primary_account_number,isdisbursement,customer_phone,agent_phone,msg,agentname){
   
   $.ajaxSetup({
       headers:{
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });
      
    $.ajax({
            url: "{{route('ledger.storesub')}}",
            method: "POST",      // The HTTP method to use for the request
            dataType: "html",   // The type of data that you're exerciseecting back 	
            data: {                             // Data to be sent to the server.
                name:name,
                ac_type:ac_type,
                parent_id:parent_id,
                actual_value: theamount,
                description: description,
                debitorcredit_id:debitorcredit_id,
                dr_name:dr_name,
                cr_name:cr_name,
                dr_account: dr_account,
                cr_account:cr_account,
                dr_amount:theamount,
                cr_amount:theamount,
                amount:theamount,
                isdisbursement:isdisbursement,
                disbursed_id:disbursedid,
                account_number:acnumber,
                account_type:account_type,
                __id__:__id__,
                primary_account_number:primary_account_number
 
            },
            error: function (response) {
 
                // A function to be called if the request fails.					
            },
            beforeSend: function () {
                showhidediv('loadingdiv');
                // A function to be called if before the request is made.
            },
            success: function (response){
                  
                showhidediv('loadingdiv');
                $('#ledgerdetailcreateform').modal('hide');
      

                //location.reload();
                
                
            },
            complete: function (response) {

                // A function to be called when the request finishes
            }
        });
}




function make_auto_depositjvs(customer_number,msg,account_number,account_type,amount,agentname,primary_account_number,agentmsge,agentnumber){
  let __id__ = uuidv4();
  let users = "{{\Auth::user()->created_by_user}}";
  let acc = account_number;
 

  showhidediv('loadingdiv');
      $.ajax({
          url: "{{env('NOBS_IMAGES')}}disburse.php",
          method: "POST",      // The HTTP method to use for the request
          dataType: "html",   // The type of data that you're exerciseecting back 	
          data: {  
              __id__:__id__,
              account_number:acc,
              account_type:account_type,
              amount:amount,
              agentname:agentname, 
              users:users,
              insertuseraccount:'insertuseraccount',
              transaction_id:generatetranscode()
          },
          error: function () {
              showhidediv('loadingdiv');
              // A function to be called if the request fails.	
               opensystemdialog('Kindly check your internet. It seems deposit taking too long.');
          },
          beforeSend: function () {
              // A function to be called if before the request is made.
          },
          success: function (response) {
            
            
  if(response == 'ERROR'){
         opensystemdialog('There was a problem connecting to server. Kindly re-connect again.'); 
        showhidediv('loadingdiv');
  }else{
  
 
     sndmsg(customer_number,msg);

     setTimeout(() => {
        sndmsg(agentnumber,agentmsge);
     }, 400);
     
     
     opensystemdialog(response);
     
     $('#exampleModal4').on('hidden.bs.modal', function () {
       location.reload();
      });
 
  }
              // A function to be called if the request succeeds.
          },
          timeout:4000,// set timeout to 4 seconds
          complete: function (response) {
  
              // A function to be called when the request finishes
          }
      });
   
  

}

</script>


  <style>
  
        .pagination .page-item:active a{
            background-color:purple;
        }
        body > div.container-fluid.container-application > div.main-content.position-relative > div.page-content > div.row.dashboardtext > div.col-xl-3.col-md-6{
            z-index:3 !important;
        }
  
      a.fa-star {
          width: 100%;
          display: inline-block;
          color: #F62;
      }
  
      input.star {
          display: none;
      }
  
      label.star {
          float: right;
          padding: 2px;
          font-size: 15px;
          color: #444;
          transition: all .2s;
      }
  
      a.fa-star:checked~label.star:before {
          content: '\f005';
          transition: all .25s;
      }
  
  
      label.star:hover {
          transform: rotate(-15deg) scale(1.3);
      }
  
      label.star:before {
          content: '\f006';
          font-family: FontAwesome;
      }
  
  
      table td[class='mintd'] {
          padding: 5px 25px !important;
      }
  
  
        ul.list-group {
            padding: 5px 5px;
        }
  
        .list-group .list-group-item span{
            margin-top:0 !important;
            margin-bottom:0 !important;
        }
   
  
        .list-group .list-group-item {
            padding: 0 2px !important; 
            border: solid 0 !important;
            padding-top:0;
            padding-bottom:0;
            margin-bottom:0 !important;
            margin-bottom:0 !important;
            height:20p
        }
  
        .ghs {
            font-weight: normal !important;
            font-size: 13px;
        }
  
        .displaydivs {
        }
  
        .tabpanel, .tabpanel p{
          background-color:#ffffff !important;
          padding:10px 10px;
        }
  
  
        #thisweekdiv,
        #thismonthdiv,
        #thisyeardiv,
        #alltimediv {
            display: none;
        }
    </style>
  
    <script type="text/javascript">
      
  
        function getfilter(that) {
            switch (that) {
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
  
        function getfilterbyagent(that) {
  
            if (that == 'agentid_agentallid12345') {
                showhidediv('loadingdiv');
                location.href = "{{route('dashboard.index')}}";
  
            } else {
                showhidediv('loadingdiv');
                location.href = "{{route('agentquerydashboard.index')}}/" + that;
  
            }
        }
  
    </script>