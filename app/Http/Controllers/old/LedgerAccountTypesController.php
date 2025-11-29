<?php

namespace App\Http\Controllers;

use App\LoansAccounts;
use App\Loans;
use App\Loanrequestdetail;
use App\Loanrequests;
use App\Loanpurpose;
use App\Ledgergeneral;
use App\Ledgeraccounttypes;
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

class LedgerAccountTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
 
                $ledgeraccountypes = Ledgeraccounttypes::get();
                return view('ledgeraccounttypes.index', compact('ledgeraccountypes'));
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
                                   'name' => 'required' 
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            else{
 
            $ledgeraccounttypes                       = new Ledgeraccounttypes(); 
            $ledgeraccounttypes['name']               = $request->name;
            $ledgeraccounttypes['description']               = $request->description; 
         
            $ledgeraccounttypes->save();
 
                return redirect()->back()->with('success', __('Ledger Type Successfully Created.'));
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
    public function show(ledgergeneral $ledger)
    {
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            return view('ledgeraccounttypes.view', compact('ledger'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    public function create(){
  
        $pagetitle = '';

        
         
        return view('ledgeraccounttypes.create', compact('pagetitle'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required' 
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $ledgeraccounttypes                       = new Ledgeraccounttypes(); 
             
            
         
            Ledgeraccounttypes::where('id',$request->id)->update(['name'=>$request->name,'description'=>$request->description]);
           
            return redirect()->back()->with('success', __('Ledger Type Updated Successfully.'));
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

 


 
 
   
  

   
}
