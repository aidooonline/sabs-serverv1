<?php

namespace App\Http\Controllers;

use App\SavingsAccounts;
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

class SavingsAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($defaultview ='adminview')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->can('Manage User'))
        {
            //$querycast = "CAST(created_at AS Date())";

            //$thisweektotal2 = 'thisweektotal' ;//DB::table('nobs_transactions')->Where('name_of_transactions', '=', 'Deposit')->Where($querycast,')->sum('amount');
            $thisweektotal = '';
            $todaytotal = '';
            $thismonthtotal = '';

            $savingsaccounts = SavingsAccounts::get();
            return view('savings.index', compact('savingsaccounts','todaytotal','thisweektotal','thismonthtotal'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    public function create(){
        $countries = Country::OrderBy('country_id','DESC')->get()->pluck('name','country_id');
        $gender = Gender::OrderBy('id','DESC')->get()->pluck('genderval', 'id');
        $maritalstatus = MaritalStatus::OrderBy('id','DESC')->get()->pluck('marital_stats', 'id');
        $idtype = Idtype::OrderBy('id','DESC')->get()->pluck('idname', 'id');

        return view('accounts.create', compact('gender','countries','maritalstatus','idtype'));
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
        if(\Auth::user()->can('Edit Contact'))
        { 
            $countries = Country::OrderBy('country_id','DESC')->get()->pluck('name','country_id');
            $gender = Gender::OrderBy('id','DESC')->get()->pluck('genderval', 'id');
            $maritalstatus = MaritalStatus::OrderBy('id','DESC')->get()->pluck('marital_stats', 'id');
        $idtype = Idtype::OrderBy('id','DESC')->get()->pluck('idname', 'id');
               
            return view('accounts.edit', compact('account','countries','gender','maritalstatus','idtype'));
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
        if(\Auth::user()->can('Create Contact'))
        {
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
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
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
           // $acount['user']    = $request->user; //must be created on accounts.create

            
           // $acount['created_by']         = \Auth::user()->creatorId();
            $account->save();

 
                return redirect()->back()->with('success', __('Customer Successfully Registered.'));
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
    public function update(Request $request, Accounts $account)
    {
        if(\Auth::user()->can('Edit Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users',
                                   'contact_postalcode' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            
            $account                       = new Accounts(); 
            $acount['first_name']               = $request->first_name;
            $acount['middle_name']            = $request->middle_name;
            $acount['surname']              = $request->surname;
            $acount['phone_number']              = $request->phone_number;
            $acount['date_of_birth2']    = $request->date_of_birth2;
            $acount['email']       = $request->email;
            $acount['gender']      = $request->gender;//must be created on accounts.create
            $acount['id_number']    = $request->id_number;
            $acount['id_type'] = $request->id_type;/// must be created on accounts.create
            $acount['marital_status']        = $request->marital_status; //must be created on accounts.created
            $acount['nationality']    = $request->nationality;
            $acount['next_of_kin']    = $request->next_of_kin;
            $acount['next_of_kin_id_number']    = $request->next_of_kin_id_number;
            $acount['next_of_kin_phone_number']    = $request->next_of_kin_phone_number;
            $acount['occupation']    = $request->occupation;
            $acount['postal_address']    = $request->postal_address;
            $acount['residential_address']    = $request->residential_address;
            $acount['sec_phone_number']    = $request->sec_phone_number;
           // $acount['user']    = $request->user; //must be created on accounts.create

            

            return redirect()->back()->with('success', __('Record Successfully Updated.'));
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
