<?php

namespace App\Http\Controllers;

use App\LoansAccounts;
use App\Loanmigrations; 
use App\Loanschedule; 
use App\Loanrequests;
use App\Loanpaymentschedule;
use App\Loanpurpose;
use App\UserAccountNumbers;
use App\Accounts;
use App\AccountsTransactions;
use App\Gender;
use App\MaritalStatus;
use App\Idtype;
use App\Country;
use App\Stream;
use App\User;
use App\UserDefualtView;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class LoanMigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *≠
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
                $loanmigrations = DB::table('nobs_loan_migration')->where('disbursed',false)->join('users','users.id','=','nobs_loan_migration.agent_id')->join('nobs_registration','nobs_registration.id','=','nobs_loan_migration.customer_id')->join('nobs_loans_accounts','nobs_loans_accounts.id','=','nobs_loan_migration.loan_account_id')->orderBy('nobs_loan_migration.id','DESC')->paginate(10,['nobs_loan_migration.*', 'users.name as agentname','nobs_registration.first_name','nobs_registration.surname','nobs_registration.customer_picture','nobs_loans_accounts.name as loanname','nobs_registration.phone_number','users.phone']);

                
                 //
                return view('loanmigration.index', compact('loanmigrations'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
           
         
    }

    
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($id ='')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
            //-------Loan requests ----///
            $loanrequestdetail = '';

            if($id == ''){     
            }else{

                $loanrequestdetail =  DB::table('nobs_micro_loan_request')->where('nobs_micro_loan_request.id',$id)->join('users','users.id','=','nobs_micro_loan_request.agent_id')->join('nobs_loans_accounts','nobs_micro_loan_request.loan_id','=','nobs_loans_accounts.id')->join('nobs_loan_purpose_list','nobs_micro_loan_request.loan_purpose','=','nobs_loan_purpose_list.id')->orderBy('nobs_micro_loan_request.id','DESC')->get(['nobs_micro_loan_request.*', 'nobs_loans_accounts.name','nobs_loan_purpose_list.name as purposename','users.username as agent_name','users.id as agent_id']);
                return view('loanrequestdetail.index', compact('loanrequestdetail'));

            }
        }
        else
        {
           
           
            
            return redirect()->back()->with('error', 'permission Denied');
        }
           
         
    }



    
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loanedit(LoansAccounts $account)
    {
        //this is literally 'Manage User Register'
        
             
            if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
        { 
            $countries = Country::OrderBy('country_id','DESC')->get()->pluck('name','country_id');
            $gender = Gender::OrderBy('id','DESC')->get()->pluck('genderval', 'id');
            $maritalstatus = MaritalStatus::OrderBy('id','DESC')->get()->pluck('marital_stats', 'id');
            $idtype = Idtype::OrderBy('id','DESC')->get()->pluck('idname', 'id');
            $pagetitle = 'Edit Customer';
             
            return view('loans.edit', compact('pagetitle','account','countries','gender','maritalstatus','idtype'));
        }
         
            

   }

 

 
    

    public function create($loanrequestid='id',$customerid='customerid'){
     
        $pagetitle = '';
        $requestedloan =  DB::table('nobs_micro_loan_request')->join('nobs_loans_accounts','nobs_micro_loan_request.loan_id','=','nobs_loans_accounts.id')->where('nobs_micro_loan_request.id',$loanrequestid)->get(['nobs_micro_loan_request.*', 'nobs_loans_accounts.interest_per_anum']);
        
       // Loanrequests::where('id',$loanrequestid)-join('nobs_loans_accounts','')->get();
        
        $customer_pic = Accounts::where('id',$customerid)->get();

        $loantypes = LoansAccounts::get()->pluck('id','name');
        $loanschedule = Loanschedule::get()->pluck('name','id');
        return view('loanmigration.create', compact('pagetitle','requestedloan','loanschedule','customer_pic','customerid','loanrequestid'));
    }


    public function disburseloan($id=''){

        if(\Auth::user()->can('Edit Contact'))
        { 
           // $loansaccounts = Accounts::where('id', '=', $id)->get();
             
        Loanmigrations::where('id',$id)->update(['disbursed'=>1]);

        

        return redirect()->back()->with('success', 'Loan Disbursed Successfully');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


     /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Accounts $account
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Loanrequestdetail $loanrequestdetail)
    {
        if(\Auth::user()->can('Edit Contact'))
        { 
           // $loansaccounts = Accounts::where('id', '=', $id)->get();
             
           $pagetitle = '';
           $loantypes = LoansAccounts::get()->pluck('name','id');
            $loanpurpose = Loanpurpose::get()->pluck('name', 'id');
            return view('loanrequestdetail.edit', compact('pagetitle','loanrequestdetail','loanpurpose','loantypes'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


        /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            $validator = \Validator::make(
 
                $request->all(), [
                                   'loan_account_number' => 'required',
                                   'amount' => 'required',
                                   'approved_amount' => 'required',
                                   'loan_schedule' => 'required'

                                   
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            else{

            $loanrequestdetail                       = new Loanmigrations(); 
            $loanrequestdetail['loan_account_number']               = $request->loan_account_number;
            $loanrequestdetail['amount']               = $request->amount;
            $loanrequestdetail['payment_duration']               = $request->payment_duration;
            $loanrequestdetail['interest_per_schedule']            = $request->interest_per_schedule;
            $loanrequestdetail['user']              = $request->user;
            $loanrequestdetail['cash_collateral']              = $request->cash_collateral;
            $loanrequestdetail['processing_fee']              = $request->processing_fee;
            $loanrequestdetail['agent_id']              = $request->agent_id;
            $loanrequestdetail['approved_amount']              = $request->approved_amount;
            $loanrequestdetail['customer_id']              = $request->customer_id;
            $loanrequestdetail['loan_account_id']              = $request->loan_account_id; 
            $loanrequestdetail['loan_schedule']              = $request->loan_schedule;
            $loanrequestdetail['customer_account_number']              = $request->customer_account_number;
            $loanrequestdetail['__id__']              = $request->loan_migration_id;
            

            
             Loanrequests::where('id',$request->loanrequestid)->update(['loan_migrated'=>1]);
             $loan_account_name = LoansAccounts::where('id',$request->loan_account_id)->pluck('name')->first();

             //agent_generated_id
             $created_by_user = User::where('id',$request->agent_id)->pluck('created_by_user')->first();
 

             $loanrequestdetail->save();


              // create the new loan account to the useraccountsnumbers.
              $useracountnumbs  = new UserAccountNumbers;
          
              $useracountnumbs['account_number']  = $request->loan_account_number;
              $useracountnumbs['account_type']  = $loan_account_name;
              $useracountnumbs['__id__']  = $request->loan_migration_id;
              $useracountnumbs['primary_account_number']  = $request->customer_account_number;
              $useracountnumbs['created_by_user']  = $created_by_user;
              $useracountnumbs->save();
 
           

            $loanschedulepayEXPLODE = explode('_____',$request->loan_schedule_data);

            foreach($loanschedulepayEXPLODE as $loanexp){

            $loanexpchild = explode('__',$loanexp);
            $loanpaymentsschedule                       = new Loanpaymentschedule(); 

              $loanpaymentsschedule['amount'] = $loanexpchild[0];
              $loanpaymentsschedule['date_to_be_paid'] = $loanexpchild[1];
              $loanpaymentsschedule['loan_account_id'] = $loanexpchild[2];
              $loanpaymentsschedule['customer_account_id'] = $loanexpchild[3];
              $loanpaymentsschedule['__id__'] = $loanexpchild[4];
              $loanpaymentsschedule->save();


            
          

            }

                return redirect()->route('loanmigrations.index'); 

               // return redirect()->back()->with('success', __('Loan Migrated Successfully.'));
            }
            
            }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Accounts $account)
    {
        if(\Auth::user()->can('Show Contact'))
        {
            return view('accounts.view', compact('account'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loanrequestdetail $loan)
    {
        if(\Auth::user()->can('Edit Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'account_number' => 'required',
                                   'amount' => 'required',
                                   'bus_capital' => 'required',
                                   'est_daily_exp' => 'required',
                                   'est_daily_sales' => 'required',
                                   'guarantor_name' => 'required',
                                   'guarantor_number' => 'required',
                                   'loan_purpose' => 'required',
                                   'phone_number' => 'required',
                                   'pri_pmt_src' => 'required',
                                   'phone_number' => 'required',
                                   'phone_number' => 'required',
                                   'first_name' => 'required',
                                   'last_name' => 'required',
                                   'mode_of_pmt' => 'required'
                                   
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            
             
            
            Loanrequestdetail::where('id',$request->id)->update(['account_number'=>$request->account_number,'amount'=>$request->amount,'bus_capital'=>$request->bus_capital,'est_daily_exp'=>$request->est_daily_exp,'est_daily_sales'=>$request->est_daily_sales,'ext_credit_facility_amt'=>$request->ext_credit_facility_amt,'ext_credit_facility'=>$request->ext_credit_facility,'guarantor_name'=>$request->guarantor_name,'guarantor_number'=>$request->guarantor_number,'guarantors_gps_loc'=>$request->guarantors_gps_loc,'loan_purpose'=>$request->loan_purpose,'phone_number'=>$request->phone_number,'pri_pmt_src'=>$request->pri_pmt_src,'user'=>$request->user,'disbursement_date'=>$request->disbursement_date,'expected_disbursement_date'=>$request->expected_disbursement_date,'first_name'=>$request->first_name,'irpm'=>$request->irpm,'last_name'=>$request->last_name,'outstanding_bal'=>$request->outstanding_bal,'mode_of_pmt'=>$request->mode_of_pmt,'prev_loan'=>$request->prev_loan,'loan_migrated'=>$request->loan_migrated,'loan_id'=>$request->loan_id,'approved_amount'=>$request->approved_amount,'loan_other_purpose'=>$request->loan_other_purpose,'loan_request_rating'=>$request->loan_request_rating]);
           
            return redirect()->back()->with('success', __('Loan Request Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

 /**
     * Remove the specified resource from storage.
     *
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accounts $account)
    {
        if(\Auth::user()->can('Delete Contact'))
        {
            $account->delete();

            return redirect()->back()->with('success', __('Record Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }



    public function searchcustomers($search = ''){
 
        $accounts = Accounts::select("id", "first_name", "surname","account_number","account_type", "accounttype_num", "balance_amount", "created_at", "customer_picture","phone_number","account_type")->Where('first_name', $search)->get();
        return view('accounts.index', compact('accounts'));
    

}

public function transactiondetails($accountsid = 1){
 //this is literally 'Manage User Register'
 if(\Auth::user()->can('Manage User'))
 {
    $account = Accounts::Where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
    $accounts = AccountsTransactions::Where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(50); 
    $totaldeposits = AccountsTransactions::Where('account_number',$accountsid)->Where('name_of_transaction','Deposit')->sum('amount');
    $totalwithdrawals = AccountsTransactions::Where('account_number',$accountsid)->Where('name_of_transaction','Withdraw')->sum('amount');
    $totalrefunds = AccountsTransactions::Where('account_number',$accountsid)->Where('name_of_transaction','Refund')->sum('amount');
    $totalrefunds = AccountsTransactions::Where('account_number',$accountsid)->Where('name_of_transaction','Refund')->sum('amount');
     
    $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals;
    return view('accounts_transactions.index', compact('accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance'));
 }
 else
 {
     return redirect()->back()->with('error', 'permission Denied');
 }

}


 
 
   
  

   
}
