<?php

namespace App\Http\Controllers;

use App\Accounts;
use App\LoanAccounts;
use App\LoanRequests;
use App\User;
use App\UserAccountNumbers;
use App\AccountsTransactions;
use App\Gender;
use App\MaritalStatus;
use App\Idtype;
use App\Country;
use App\Stream;
use App\UserDefualtView;
use Illuminate\Http\Request;
use DB;

use Illuminate\Support\Facades\Http;
 


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner')
        {
            $accounts = Accounts::orderBy('id', 'DESC')->paginate(20);
            return view('accounts.index', compact('accounts'));
        }
        else
        {
             
             if(\Auth::user()->type == 'Agents'){
            $accounts = Accounts::orderBy('id', 'DESC')->paginate(20);
            return view('accounts.index', compact('accounts'));
             }
            else
            {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
             }
             
             
        }

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loanrequests()
    {
        //this is literally 'Manage User Register'
         if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents')
        {
            $accounts = LoanRequests::orderBy('id', 'DESC')->paginate(50);
            return view('loan_requests.index', compact('accounts'));
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getagents()
    {
        
       
        
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner')
        {
           
            $accounts = User::where('type','!=','Super Admin')->where('type','!=','owner')->orderBy('id','DESC')->get();
            return view('agents.index', compact('accounts'));
        }
        else
        {
           
           
            if(\Auth::user()->type == 'Agents')
            {
           
            $accounts =  $accounts = User::orderBy('name', 'ASC')->where('type','!=','Super Admin')->where('type','!=','owner')->where('id',\Auth::user()->id)->get();
            return view('agents.index', compact('accounts'));
            }
            else
            {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
            }
           
           
        }

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getwithdrawalrequests()
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            $agentnames = User::orderBy('name', 'ASC')->where('type','!=','Super Admin')->where('type','!=','owner')->get();
            $unapprovedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->count();
            $approvedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',1)->where('is_paid',0)->count();
            $paidcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->count();
            
            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->paginate(10);
            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',1)->where('is_paid',0)->paginate(10);
            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->paginate(10);
            
            return view('accounts.withdrawal_request.index', compact('accounts','unapprovedcounts','approvedcounts','accountsapproved','paidcounts','accountspaid','agentnames'));
        }
        else
        {
            if(\Auth::user()->type=='Agents')
        {
            $agentnames = User::where('created_by_user',\Auth::user()->created_by_user)->get();
            $unapprovedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->where('users',\Auth::user()->created_by_user)->count();
            $approvedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',1)->where('users',\Auth::user()->created_by_user)->where('is_paid',0)->count();
            $paidcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->where('users',\Auth::user()->created_by_user)->count();
            
            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->where('users',\Auth::user()->created_by_user)->paginate(10);
            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',1)->where('users',\Auth::user()->created_by_user)->where('is_paid',0)->paginate(10);
            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->where('users',\Auth::user()->created_by_user)->paginate(10);
            
            return view('accounts.withdrawal_request.index', compact('accounts','unapprovedcounts','approvedcounts','accountsapproved','paidcounts','accountspaid','agentnames'));
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
        }

    }
    




    public function create(){
        $countries = Country::OrderBy('country_id','DESC')->get()->pluck('name','country_id');
        $gender = Gender::OrderBy('id','DESC')->get()->pluck('genderval', 'id');
        $maritalstatus = MaritalStatus::OrderBy('id','DESC')->get()->pluck('marital_stats', 'id');
        $idtype = Idtype::OrderBy('id','DESC')->get()->pluck('idname', 'id');

        $pagetitle = 'Register Customer';

        return view('accounts.create', compact('pagetitle','gender','countries','maritalstatus','idtype'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Accounts $account
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Accounts $account)
    {
         if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
        { 
            $countries = Country::OrderBy('country_id','DESC')->get()->pluck('name','country_id');
            $gender = Gender::OrderBy('id','DESC')->get()->pluck('genderval', 'id');
            $maritalstatus = MaritalStatus::OrderBy('id','DESC')->get()->pluck('marital_stats', 'id');
            $idtype = Idtype::OrderBy('id','DESC')->get()->pluck('idname', 'id');
            $pagetitle = 'Edit Customer';
             
            return view('accounts.edit', compact('pagetitle','account','countries','gender','maritalstatus','idtype'));
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
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
       if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
        {
            $customerinputs = $request->all();
            $validator = \Validator::make(
                $request->all(), [
                                   'first_name' => 'required|max:120',
                                   'surname' => 'required|max:120',
                                   'phone_number' => 'required|min:10',
                                   'email' => 'required|unique:nobs_registration',
                                   'date_of_birth2' => 'required',
                                   'first_name' => 'required|max:120',
                                   'id_number' => 'required|min:10',
                                   'next_of_kin' => 'required',
                                   'next_of_kin' => 'required',
                                   'next_of_kin_id_number' => 'required',
                                   'next_of_kin_phone_number' => 'required|min:10',
                                   'occupation' => 'required',
                                   'residential_address' => 'required' 
                                   
                               ]
            );

           
            if($request->confcode == $request->confirmationcode){

            }else{
                $messages =  'Wrong Phone Code';
                
                return redirect()->back()->with('error', $messages);
            }
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }
            else{
             
            $account                       = new Accounts(); 
            $account['__id__']               = $request->__id__;
            $account['account_number']               = $request->account_number;
            $account['first_name']               = $request->first_name;
            $account['middle_name']            = $request->middle_name;
            $account['surname']              = $request->surname;
            $account['phone_number']              = $request->phone_number;
            $account['date_of_birth2']    = $request->date_of_birth2;
            $account['email']       = $request->email;
            $account['gender']      = $request->gender;
            $account['id_number']    = $request->id_number;
            $account['id_type'] = $request->id_type; 
            $account['marital_status']        = $request->marital_status;  
            $account['nationality']    = $request->nationality;
            $account['next_of_kin']    = $request->next_of_kin;
            $account['next_of_kin_id_number']    = $request->next_of_kin_id_number;
            $account['next_of_kin_phone_number']    = $request->next_of_kin_phone_number;
            $account['occupation']    = $request->occupation;
            $account['postal_address']    = $request->postal_address;
            $account['residential_address']    = $request->residential_address;
            $account['sec_phone_number']    = $request->sec_phone_number;
            $account['user'] = $request->user;
            $account['customer_picture'] = $request->customer_picture;
            $account['is_dataimage'] = 1;

           // $acount['user']    = $request->user; //must be created on accounts.create


           $useracountnumbs  = new UserAccountNumbers;
          
           $useracountnumbs['account_number']  = $request->account_number;
           $useracountnumbs['account_type']  = 'Regular Susu';
           $useracountnumbs['__id__']  = $request->__id__;
           $useracountnumbs['primary_account_number']  = $request->account_number;
           $useracountnumbs['created_by_user']  = \Auth::user()->created_by_user;
           
            
           // $acount['created_by']         = \Auth::user()->creatorId();
            $account->save();
            $useracountnumbs->save();

            //$this->sendmessage($request->phone_number,'Dear, ' . $request->first_name . ' '. $request->surname . ', Your GCI susu account has been created. \n Account Number: ' . $request->account_number );

  
 
                return redirect()->back()->with('success', __('Customer Successfully Registered.'));
            }
            
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
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
       if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
        {
            return view('accounts.view', compact('account'));
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Accounts $account
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Accounts $account)
    {
       if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
        {
            $customerinputs = $request->all();
            $validator = \Validator::make(
                $request->all(), [
                                   'first_name' => 'required|max:120',
                                   'surname' => 'required|max:120',
                                   'phone_number' => 'required|min:10',
                                  // 'email' => 'required',
                                   'date_of_birth2' => 'required',
                                   'first_name' => 'required|max:120',
                                   'id_number' => 'required|min:10',
                                   'next_of_kin' => 'required',
                                   'next_of_kin' => 'required',
                                   'next_of_kin_id_number' => 'required',
                                   'next_of_kin_phone_number' => 'required|min:10',
                                   'occupation' => 'required',
                                   'residential_address' => 'required' 
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
           
            $account                       = new Accounts(); 
          /*  // $account['__id__']               = $request->__id__;
            $account['account_number']               = $request->account_number;
            $account['first_name']               = $request->first_name;
            $account['middle_name']            = $request->middle_name;
            $account['surname']              = $request->surname;
            $account['phone_number']              = $request->phone_number;
            $account['date_of_birth2']    = $request->date_of_birth2;
            $account['email']       = $request->email;
            //$account['gender']      = $request->gender;
            $account['id_number']    = $request->id_number;
            $account['id_type'] = $request->id_type; 
           // $account['marital_status']        = $request->marital_status;  
            $account['nationality']    = $request->nationality;
            $account['next_of_kin']    = $request->next_of_kin;
            $account['next_of_kin_id_number']    = $request->next_of_kin_id_number;
            $account['next_of_kin_phone_number']    = $request->next_of_kin_phone_number;
            $account['occupation']    = $request->occupation;
            $account['postal_address']    = $request->postal_address;
            $account['residential_address']    = $request->residential_address;
            $account['sec_phone_number']    = $request->sec_phone_number;
            $account['user'] = $request->user;
            $account['customer_picture'] = $request->customer_picture;
            $account['is_dataimage'] = 1;
             */

           // $acount['user']    = $request->user; //must be created on accounts.create

            Accounts::where('id',$request->id)->update(['account_number'=>$request->account_number,'first_name'=>$request->first_name,'middle_name'=>$request->middle_name,'surname'=>$request->surname,'phone_number'=>$request->phone_number,'date_of_birth2'=>$request->date_of_birth2,'email'=>$request->email,'id_number'=>$request->id_number,'id_type'=>$request->id_type,'nationality'=>$request->nationality,'next_of_kin'=>$request->next_of_kin,'next_of_kin_id_number'=>$request->next_of_kin_id_number,'next_of_kin_phone_number'=>$request->next_of_kin_phone_number,'occupation'=>$request->occupation,'postal_address'=>$request->postal_address,'residential_address'=>$request->residential_address,'sec_phone_number'=>$request->sec_phone_number,'user'=>$request->user]);
           
          
            

            return redirect()->back()->with('success', __('Record Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
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
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }



    public function searchcustomers($search = ''){
 
        $accounts = Accounts::select("id", "first_name", "surname","account_number","account_type", "accounttype_num", "balance_amount", "created_at", "customer_picture","phone_number","account_type")->where('first_name', $search)->get();
        return view('accounts.index', compact('accounts'));
    

}

public function transactiondetails($accountsid = 1,$transactionview = null){
 //this is literally 'Manage User Register'
if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
 {
    $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(1);
    $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(1); 
    $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->where('row_version',2)->sum('amount');
    $totalcommissions = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Commission')->where('row_version',2)->sum('amount');
    $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->where('row_version',2)->sum('amount');
    $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->where('row_version',2)->sum('amount');
    
    $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommissions;
    return view('accounts_transactions.index', compact('accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','transactionview'));
 }
 else
 {
     return redirect()->back()->with('error', 'You are not allowed to view this page.');
 }

}





public function loantransaction($accountsid = null,$depositer=null,$pbn = null){
    //this is literally 'Manage User Register'
   if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       
       $mainaccountnumber = UserAccountNumbers::where('account_number',$accountsid)->pluck('primary_account_number');
       $accounttype = UserAccountNumbers::where('account_number',$accountsid)->pluck('account_type');

       $useraccountnumbers = UserAccountNumbers::where('primary_account_number',$accountsid)->get();

       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(10); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
        
       $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals,2);
       return view('accounts.loan_repayment.create', compact('pbn','accounttype','mainaccountnumber','useraccountnumbers','accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer','accountsid'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }
   
   
   
public function searchloan($accountsid = 1,$depositer=null){
    //this is literally 'Manage User Register'
  if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(50); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
        
       $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals;
       return view('accounts.loan_repayment.index', compact('accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }


public function deposittransaction($accountsid = null,$depositer=null,$pbn = null){
    //this is literally 'Manage User Register'
   if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       
       $mainaccountnumber = UserAccountNumbers::where('account_number',$accountsid)->pluck('primary_account_number');
       $accounttype = UserAccountNumbers::where('account_number',$accountsid)->pluck('account_type');

       $useraccountnumbers = UserAccountNumbers::where('primary_account_number',$accountsid)->get();

       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->where('row_version',2)->paginate(1); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->where('row_version',2)->sum('amount');
        $totalcommission = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Commission')->where('row_version',2)->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->where('row_version',2)->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->where('row_version',2)->sum('amount');
      
       $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission,3);
       return view('accounts.deposit.create', compact('pbn','accounttype','mainaccountnumber','useraccountnumbers','accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer','accountsid'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }




public function refundtransaction($accountsid = null,$depositer=null,$pbn = null){
    //this is literally 'Manage User Register'
  if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       
       $mainaccountnumber = UserAccountNumbers::where('account_number',$accountsid)->pluck('primary_account_number');
       $accounttype = UserAccountNumbers::where('account_number',$accountsid)->pluck('account_type');

       $useraccountnumbers = UserAccountNumbers::where('primary_account_number',$accountsid)->get();

       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->where('row_version',2)->orderBy('id', 'DESC')->paginate(1); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->where('row_version',2)->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->where('row_version',2)->sum('amount');
       $totalcommission = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Commission')->where('row_version',2)->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->where('row_version',2)->sum('amount');
        
       $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission;
       return view('accounts.refund.create', compact('pbn','accounttype','mainaccountnumber','useraccountnumbers','accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer','accountsid'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }




public function withdrawtransaction($accountsid = null,$withdrawer=null,$pbn = null){
    //this is literally 'Manage User Register'
   if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       
       $mainaccountnumber = UserAccountNumbers::where('account_number',$accountsid)->pluck('primary_account_number');
       $accounttype = UserAccountNumbers::where('account_number',$accountsid)->pluck('account_type');

       $useraccountnumbers = UserAccountNumbers::where('primary_account_number',$accountsid)->get();
        

       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->where('row_version',2)->orderBy('id', 'DESC')->paginate(10); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->where('row_version',2)->sum('amount');
       $totalcommission = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Commission')->where('row_version',2)->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->where('row_version',2)->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->where('row_version',2)->where('row_version',2)->sum('amount');
        
       $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals -$totalcommission;
       
       return view('accounts.withdraw.create', compact('pbn','accounttype','mainaccountnumber','useraccountnumbers','accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','withdrawer','accountsid'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }
   


public function searchwithdrawer($accountsid = 1,$withdrawer=null){
    //this is literally 'Manage User Register'
   if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(50); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
        
       $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals;
       return view('accounts.withdraw.index', compact('accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','withdrawer'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }


public function searchdeposit($accountsid = 1,$depositer=null){
    //this is literally 'Manage User Register'
   if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(50); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
        
       $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals;
       return view('accounts.deposit.index', compact('accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }



public function searchrefund($accountsid = 1,$depositer=''){
    //this is literally 'Manage User Register'
  if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->paginate(50); 
       $totaldeposits = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Deposit')->sum('amount');
       $totalwithdrawals = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Withdraw')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       $totalrefunds = AccountsTransactions::where('account_number',$accountsid)->where('name_of_transaction','Refund')->sum('amount');
       
       $tr_accounts ='';
       
       if($depositer == ''){
           
         $tr_accounts = AccountsTransactions::OrWhere('name_of_transaction','Deposit')->OrWhere('name_of_transaction','Refund')->where('is_shown',1)->where('users',$accountsid)->OrderBy('created_at','DESC')->paginate(20);
      
       }else{
           
           $tr_accounts = AccountsTransactions::OrWhere('name_of_transaction','Deposit')->OrWhere('name_of_transaction','Refund')->where('is_shown',1)->where('users',$accountsid)->where('transaction_id',$depositer)->OrderBy('created_at','DESC')->paginate(20);
           
       }
        
        $totalbalance  = $totaldeposits - $totalrefunds - $totalwithdrawals;
       return view('accounts.refund.index', compact('tr_accounts','accounts','account','totalwithdrawals','totaldeposits','totalrefunds','totalbalance','depositer'));
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   
   }
   
   
   
   public function reversetransaction($id=''){
     if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner' || \Auth::user()->type=='Agents')
    {
       $tr_accounts = AccountsTransactions::where('id',$id)->get();
       
        $reversaccount = new AccountsTransactions(); 
           
            
       foreach($tr_accounts as $r){
        
        
          $reversaccount['__id__'] = uniqid();
          $reversaccount['account_number'] = $r->account_number;
          $reversaccount['account_type'] = $r->account_type;
          $reversaccount['amount'] = $r->amount;
          $reversaccount['det_rep_name_of_transaction'] = $r->det_rep_name_of_transaction;
          $reversaccount['created_at'] = $r->created_at;
          $reversaccount['agentname'] = $r->agentname;
          $reversaccount['name_of_transaction'] = 'Refund';
          $reversaccount['phone_number'] = $r->agentname;
          $reversaccount['transaction_id'] = $this->randString(6);
          $reversaccount['users'] = $r->users;
          $reversaccount['is_shown'] = 1;
          $reversaccount['foreign_id'] = $r->foreign_id;
          $reversaccount['is_loan'] = $r->is_loan;
          $reversaccount['withdrawrequest_approved'] = $r->withdrawrequest_approved;
          $reversaccount['approved_by'] = $r->approved_by;
          $reversaccount['paid_by'] = $r->paid_by;
          $reversaccount['is_paid'] = $r->is_paid;
          $reversaccount['paid_withdrawal_msg'] = $r->paid_withdrawal_msg;
           
        }
        $reversaccount->save();
        
        return redirect()->back();
        
    }
    else
    {
        return redirect()->back()->with('error', 'You are not allowed to view this page.');
    }
   }


private function randString($length) {
    $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $char = str_shuffle($char);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
        $rand .= $char[mt_rand(0, $l)];
    }
    return $rand;
}


   private function sendmessage($to,$msg){
    $response = Http::get('https://apps.mnotify.net/smsapi', [
        'key' => 'NOc1wAUzzMMSdxtHWQvOiWb2w',
        'to' => $to,
        'msg' => $msg,
        'sender_id' => 'GCI susu'
    ]);
   }

   
}
