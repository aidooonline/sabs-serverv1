<?php

namespace App\Http\Controllers;

use App\LoansAccounts;
use App\Loans;
use App\Loanrequestdetail;
use App\Loanmigrations;
use App\Loanrequests;
use App\Loanpurpose;
use App\Ledgergeneral;
use App\Ledgergeneralsub;
use App\Ledgergeneraldetails;
use App\Ledgeraccounttypes;
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

class LedgerGeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id ='')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
            $ledgergeneral =  DB::table('nobs_ledger_general')->join('nobs_ledger_account_types','nobs_ledger_account_types.id','=','nobs_ledger_general.ac_type')->orderBy('nobs_ledger_general.updated_at','DESC')->get(['nobs_ledger_general.*', 'nobs_ledger_account_types.name AS acname','nobs_ledger_account_types.id as acid']);
                return view('ledgergeneral.index', compact('ledgergeneral'));
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
    public function subledger($parentname='',$parentid ='',$ledgername='',$ledgertype='')
    {

        //{parentname?}/{parentid?}/{ledgername?}/{ledgertype?}
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
            $ledger_type_list_query = Ledgergeneral::get()->pluck('name','id');
            $ledger_type_list_query2 =  $ledgergeneral =  DB::table('nobs_ledger_general')->join('nobs_ledger_account_types','nobs_ledger_account_types.id','=','nobs_ledger_general.ac_type')->orderBy('acname','ASC')->orderBy('nobs_ledger_general.name','ASC')->get(['nobs_ledger_general.*', 'nobs_ledger_account_types.name AS acname','nobs_ledger_account_types.id as acid']);
            $ledgerdetail = Ledgergeneraldetails::where('parent_id',$parentid)->get();
            $ledgergeneralsub_sum = DB::table('nobs_ledger_general_sub')->where('parent_id', '=', $parentid)->sum('amount');
            $ledgergeneralsub =  DB::table('nobs_ledger_general_sub')->where('nobs_ledger_general_sub.parent_id','=',$parentid)->orderBy('nobs_ledger_general_sub.created_at','DESC')->get(['nobs_ledger_general_sub.*', 'nobs_ledger_general_sub.name AS acname']);
                return view('ledgergeneralsub.create', compact('ledger_type_list_query2','ledgergeneralsub_sum','ledgergeneralsub','parentname','parentid','ledgertype','ledgername','ledger_type_list_query','ledgerdetail'));
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
                                   'name' => 'required',
                                   'ac_type' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            else{
 
            $ledgergeneral                       = new Ledgergeneral(); 
            $ledgergeneral['name']               = $request->name;
            $ledgergeneral['ac_type']               = $request->ac_type;
            $ledgergeneral['parent_id']               = $request->parent_id;
            $ledgergeneral['amount']               = $request->amount;
            $ledgergeneral['description']            = $request->description;
         
            $ledgergeneral->save();
 
                return redirect()->back()->with('success', __('Ledger Successfully Created.'));
            }
            
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
    public function storesubledger(Request $request)
    {
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'ac_type' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            else{

                //get the sum first. will need it to insert
            
 
            $ledgergeneralsub                       = new Ledgergeneralsub(); 
            $ledgergeneralsub['name']               = $request->name;
            $ledgergeneralsub['ac_type']               = $request->ac_type;
            $ledgergeneralsub['parent_id']               = $request->parent_id;
            $ledgergeneralsub['amount']               = $request->actual_value;
            $ledgergeneralsub['description']            = $request->description;
            

            $ledgergeneralsub->save();
 
            $savedid = $ledgergeneralsub->id;

            //Now update the parent ledgergeneral sub_count column.
            $ledgergeneralsub_count = DB::table('nobs_ledger_general_sub')->where('parent_id', '=', $request->parent_id)->get();

            $ledgergeneralsub_sum = DB::table('nobs_ledger_general_sub')->where('parent_id', '=', $request->parent_id)->sum('amount');


            $ledgergeneralsub_count = count($ledgergeneralsub_count);

            //Now update the parent ledgergeneral amount sum column.
            Ledgergeneral::where('id',$request->parent_id)->update(['sub_count'=>$ledgergeneralsub_count,'amount'=>$ledgergeneralsub_sum]);
            Ledgergeneralsub::where('id',$savedid)->update(['balance'=>$ledgergeneralsub_sum]);



            $current_time = \Carbon\Carbon::now()->timestamp;


            //saving the ledger general details
            //for debit
            if($request->debitorcredit_id == 'Debit'){
                $ledgergeneraldetails_debit                      = new Ledgergeneraldetails(); 
                $ledgergeneraldetails_debit['name']               = $request->name;
               $ledgergeneraldetails_debit['ac_type']               = $request->ac_type;
                $ledgergeneraldetails_debit['parent_id']               = $request->parent_id;
               $ledgergeneraldetails_debit['dr_account']               = $request->dr_account;
               $ledgergeneraldetails_debit['dr_amount']               = $request->cr_amount;
               $ledgergeneraldetails_debit['actual_value']               = $request->actual_value;
               $ledgergeneraldetails_debit['dr_or_cr']               = true;
               $ledgergeneraldetails_debit['trans_id']               = $current_time;
                $ledgergeneraldetails_debit->save();
            }
             //saving the ledger general details
            //for credit
            if($request->debitorcredit_id == 'Credit'){

                $ledgergeneraldetails_credit                      = new Ledgergeneraldetails(); 
                $ledgergeneraldetails_credit['name']               = $request->name;
               $ledgergeneraldetails_credit['ac_type']               = $request->ac_type;
               $ledgergeneraldetails_credit['parent_id']               = $request->parent_id;
                $ledgergeneraldetails_credit['cr_account']               = $request->cr_account;
               $ledgergeneraldetails_credit['cr_amount']               = $request->cr_amount;
                $ledgergeneraldetails_credit['actual_value']               = $request->actual_value;
                $ledgergeneraldetails_credit['dr_or_cr']               = false;
               $ledgergeneraldetails_credit['trans_id']               = $current_time;


                $ledgergeneraldetails_credit->save();

               
    
            }
 

            //we are disbursing the loan.
if($request->isdisbursement == 'true'){
    Loanmigrations::where('id',$request->disbursed_id)->update(['disbursed'=>1]);

  
}
          //now make the deposit transaction to the loann account.

            
            
                return redirect()->back()->with('success', __('Sub Ledger Successfully Created.'));
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
        if(\Auth::user()->can('Show Contact'))
        {
            return view('ledgergeneral.view', compact('ledger'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }



    public function create(){
  
        $pagetitle = '';

        $ledgertype = Ledgeraccounttypes::orderBy('name','ASC')->get()->pluck('name','id');

        $parentcategory = Ledgergeneral::orderBy('name','ASC')->get()->pluck('name','id');
        $parentcategory->prepend('--', '');
        
         
        return view('ledgergeneral.create', compact('pagetitle','ledgertype','parentcategory'));
    }



    public function createsubledger($parentname='',$parentid='',$ledgername='',$ledgertype=''){
  
        $pagetitle = '';

        $ledger_type_list_query = Ledgergeneral::where('ac_type',$ledgertype)->get()->pluck('name','id');

        $ledgerdetail = Ledgergeneraldetails::where('parent_id',$ledgertype)->get();

        return view('ledgergeneralsub.create', compact('pagetitle','ledgertype','parentname','parentid','ledgername','ledger_type_list_query','ledgerdetail'));
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
        if(\Auth::user()->can('Edit Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'ac_type' => 'required'
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            
            
         
            Ledgergeneral::where('id',$request->id)->update(['name'=>$request->name,'ac_type'=>$request->ac_type,'parent_type'=>$request->parent_id,'description'=>$request->parent_id]);
           
            return redirect()->back()->with('success', __('Ledger Updated Created.'));
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
