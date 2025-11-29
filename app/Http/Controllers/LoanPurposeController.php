<?php

namespace App\Http\Controllers;
 
use App\Loanpurpose;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class LoanPurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($defaultview ='adminview')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type == 'Admin'|| \Auth::user()->type == 'owner')
        {
            //-------Loan collections ----///
            $loanpurpose = Loanpurpose::get()->pluck('purpose','id');
            return view('loanpurpose.index', compact('loanpurpose'));
        }
        else
        {
           
          
            return redirect()->back()->with('error', 'permission Denied');
        }
           
            
           
        

    }

 
    
//End of agent query list.
    
     
    

    public function create(){
        $pagetitle = '';
        return view('loans.create', compact('pagetitle'));
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Accounts $account
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Loans $loan)
    {
        if(\Auth::user()->can('Edit Contact'))
        { 
           // $loansaccounts = Accounts::where('id', '=', $id)->get();
             
           $pagetitle = '';
             
            return view('loans.edit', compact('pagetitle','loan'));
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
                    'account_name' => 'required|max:200',
                    'interest' => 'required',
                    'duration' => 'required',
                    'interest_per_anum' => 'required',
                    'payment_default_interest' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            else{
             
            $account                       = new Loans(); 
            $account['account_name']               = $request->account_name;
            $account['interest']               = $request->interest;
            $account['duration']               = $request->duration;
            $account['interest_per_anum']            = $request->interest_per_anum;
            $account['payment_default_interest']              = $request->payment_default_interest;
           
         
            $account->save();

 
                return redirect()->back()->with('success', __('Loan Account Successfully Created.'));
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
    public function update(Request $request, Loans $loan)
    {
        if(\Auth::user()->can('Edit Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'account_name' => 'required|max:200',
                                   'interest' => 'required',
                                   'duration' => 'required',
                                   'interest_per_anum' => 'required',
                                   'payment_default_interest' => 'required'
                                   
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            
           
            
            Loans::where('id',$request->id)->update(['account_name'=>$request->account_name,'interest'=>$request->interest,'duration'=>$request->duration,'interest_per_anum'=>$request->interest_per_anum,'payment_default_interest'=>$request->payment_default_interest]);
           
            return redirect()->back()->with('success', __('Loan Account Successfully Updated.'));
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

  
 
 
   
  

   
}
