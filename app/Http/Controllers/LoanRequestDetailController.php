<?php

namespace App\Http\Controllers;

use App\LoansAccounts;
use App\Loans;
use App\Loanrequestdetail;
use App\Loanrequests;
use App\Loanpurpose;
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

class LoanRequestDetailController extends Controller
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
            //-------Loan requests ----///
            $loanrequestdetail = '';

            if($id == ''){     
            }else{
                $loanrequestdetail = Loanrequestdetail::where('id',$id)->get();
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

 





     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard($defaultview ='adminview',$agentqueryid=null)
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            
$agentdata = User::where('type','Agents')->where('is_active',1)->get();
$agentqueryname ='';

           //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycountDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->count('amount');

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');
 
 $todaycountWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->count('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('row_version',2)->where('is_shown',1)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

  //-------Withdrawals collections ----///

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');
  
    $todaycountRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->count('amount');


  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');

  



//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->sum('amount');

//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

            
            
            //--------------------------Agent Commission-----------//

$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

            
                //--------------------------Commission-----------//

$todaytotalSCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalSCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalSCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalSCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

            
            
            
             $todaycountDIS = Accounts::whereDate('created_at', Carbon::today())->get();
            $todaycountDIS = count($todaycountDIS);
           
            $thisweekcountDIS =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            $thisweekcountDIS = count($thisweekcountDIS);
            
            
            $thismonthcountDIS = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
            $thismonthcountDIS = count($thismonthcountDIS);

            $thisyearcountDIS = Accounts::whereYear('created_at', date('Y'))->get();
            $thisyearcountDIS = count($thisyearcountDIS);
            
            $alltimecountDIS = Accounts::get();
            $alltimecountDIS = count($alltimecountDIS);
            

            $loansaccounts = LoansAccounts::get();
            return view('dashboard.index', compact('todaytotalSCM','thisweektotalSCM','thismonthtotalSCM','thisyeartotalSCM','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','todaycountDIS','thisweekcountDIS','thismonthcountDIS','thisyearcountDIS','alltimecountDIS','loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','todaycountDP','todaycountWD','todaycountRF','agentdata','agentqueryname'));
        }
        else
        {
            
            
            
                  if(\Auth::user()->type=='Agents')
        {

           //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('users',\Auth::user()->created_by_user)->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycountDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('users',\Auth::user()->created_by_user)->where('is_shown',1)->where('row_version',2)->count('amount');
 

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
 
 $todaycountWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->count('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  //-------Refund collections ----///

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
  
  $todaycountRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  



//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
            
            //--------------------------Agent Commission-----------//

$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
                //--------------------------Commission-----------//

$todaytotalSCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalSCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalSCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalSCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
            
            
             $todaycountDIS = Accounts::whereDate('created_at', Carbon::today())->where('user',\Auth::user()->created_by_user)->get();
            $todaycountDIS = count($todaycountDIS);
           
            $thisweekcountDIS =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user',\Auth::user()->created_by_user)->get();
            $thisweekcountDIS = count($thisweekcountDIS);
            
            
            $thismonthcountDIS = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('user',\Auth::user()->created_by_user)->get();
            $thismonthcountDIS = count($thismonthcountDIS);

            $thisyearcountDIS = Accounts::whereYear('created_at', date('Y'))->where('user',\Auth::user()->created_by_user)->get();
            $thisyearcountDIS = count($thisyearcountDIS);
            
            $alltimecountDIS = Accounts::where('user',\Auth::user()->created_by_user)->get();;
            $alltimecountDIS = count($alltimecountDIS);
            

            $loansaccounts = LoansAccounts::get();
            return view('dashboard.index', compact('todaytotalSCM','thisweektotalSCM','thismonthtotalSCM','thisyeartotalSCM','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','todaycountDIS','thisweekcountDIS','thismonthcountDIS','thisyearcountDIS','alltimecountDIS','loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','todaycountDP','todaycountWD','todaycountRF'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
             
            
        }

    }
    
    
    
    
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function agentquerydashboard($agentqueryid=null)
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->type=='Admin' || \Auth::user()->type=='owner')
        {
            
$agentdata = User::where('type','Agents')->where('is_active',1)->get();

$agentqueryname = User::where('created_by_user',$agentqueryid)->pluck('name')->first();

           //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycountDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->count('amount');

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');
 
 $todaycountWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->count('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Withdraw')->where('row_version',2)->where('is_shown',1)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

  //-------Withdrawals collections ----///

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');
  
    $todaycountRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->count('amount');


  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

  



//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

            
            
            //--------------------------Agent Commission-----------//

$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('users',$agentqueryid)->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

            
                //--------------------------Commission-----------//

$todaytotalSCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Commission')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotalSCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('users',$agentqueryid)->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotalSCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('users',$agentqueryid)->where('name_of_transaction','Commission')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisyeartotalSCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('users',$agentqueryid)->where('is_shown',1)->where('row_version',2)->sum('amount');

            
            
            
             $todaycountDIS = Accounts::whereDate('created_at', Carbon::today())->where('user',$agentqueryid)->get();
            $todaycountDIS = count($todaycountDIS);
           
            $thisweekcountDIS =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user',$agentqueryid)->get();
            $thisweekcountDIS = count($thisweekcountDIS);
            
            
            $thismonthcountDIS = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('user',$agentqueryid)->get();
            $thismonthcountDIS = count($thismonthcountDIS);

            $thisyearcountDIS = Accounts::whereYear('created_at', date('Y'))->where('user',$agentqueryid)->get();
            $thisyearcountDIS = count($thisyearcountDIS);
            
            $alltimecountDIS = Accounts::where('user',$agentqueryid)->get();
            $alltimecountDIS = count($alltimecountDIS);
            

            $loansaccounts = LoansAccounts::get();
            return view('dashboard.index', compact('todaytotalSCM','thisweektotalSCM','thismonthtotalSCM','thisyeartotalSCM','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','todaycountDIS','thisweekcountDIS','thismonthcountDIS','thisyearcountDIS','alltimecountDIS','loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','todaycountDP','todaycountWD','todaycountRF','agentdata','agentqueryid','agentqueryname'));
        }
        else
        {
            
            
            
                  if(\Auth::user()->type=='Agents')
        {

           //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('users',\Auth::user()->created_by_user)->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycountDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('users',\Auth::user()->created_by_user)->where('is_shown',1)->where('row_version',2)->count('amount');
 

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
 
 $todaycountWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->count('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  //-------Refund collections ----///

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
  
  $todaycountRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

  



//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
            
            //--------------------------Agent Commission-----------//

$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
                //--------------------------Commission-----------//

$todaytotalSCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisweektotalSCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thismonthtotalSCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

$thisyeartotalSCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Commission')->where('is_shown',1)->where('users',\Auth::user()->created_by_user)->where('row_version',2)->sum('amount');

            
            
            
             $todaycountDIS = Accounts::whereDate('created_at', Carbon::today())->where('user',\Auth::user()->created_by_user)->get();
            $todaycountDIS = count($todaycountDIS);
           
            $thisweekcountDIS =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user',\Auth::user()->created_by_user)->get();
            $thisweekcountDIS = count($thisweekcountDIS);
            
            
            $thismonthcountDIS = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('user',\Auth::user()->created_by_user)->get();
            $thismonthcountDIS = count($thismonthcountDIS);

            $thisyearcountDIS = Accounts::whereYear('created_at', date('Y'))->where('user',\Auth::user()->created_by_user)->get();
            $thisyearcountDIS = count($thisyearcountDIS);
            
            $alltimecountDIS = Accounts::where('user',\Auth::user()->created_by_user)->get();;
            $alltimecountDIS = count($alltimecountDIS);
            

            $loansaccounts = LoansAccounts::get();
            return view('dashboard.index', compact('todaytotalSCM','thisweektotalSCM','thismonthtotalSCM','thisyeartotalSCM','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','todaycountDIS','thisweekcountDIS','thismonthcountDIS','thisyearcountDIS','alltimecountDIS','loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','todaycountDP','todaycountWD','todaycountRF'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
             
            
        }

    }
    
    
    
    
    
    
    
    

  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function agentdashboard($agentid ='',$agentname = '')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->can('Manage User'))
        {

           //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  //-------Withdrawals collections ----///

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  



//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');


//-----Customers Registered ------////

//--------------------------loan disbursed -----------//

$todaytotalCR = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalCR =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalCR = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalCR = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$todaydpcounts = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->count('amount');

$agentdata = User::where('created_by_user',$agentid)->get();


            $loansaccounts = LoansAccounts::get();
            return view('dashboard.agent', compact('loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','agentname','agentdata','todaydepcount'));
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
    public function agentdashboardsingle($agentid ='',$agentname = '',$nameoftransaction = '')
    {
        //this is literally 'Manage User Register'
        if(\Auth::user()->can('Manage User'))
        {
            
            if($nameoftransaction == 'deposit'){
                
                //-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalDP = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$accounts = AccountsTransactions::where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
$agentdata = User::where('created_by_user',$agentid)->get();


            $loansaccounts = LoansAccounts::get();
            return view('dashboard.agent_deposit', compact('accounts','loansaccounts','todaytotalDP','thisweektotalDP','thismonthtotalDP','thisyeartotalDP','agentname','agentdata'));
      
           
            }
            
            
             
             
               if($nameoftransaction == 'withdraw'){
                          
 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->sum('amount');
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->sum('amount');

 $thisyeartotalWD = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

           $accounts = AccountsTransactions::where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
           
$agentdata = User::where('created_by_user',$agentid)->get();


            $loansaccounts = LoansAccounts::get();
           return view('dashboard.agent_withdraw', compact('accounts','loansaccounts','todaytotalWD','thisweektotalWD','thismonthtotalWD','thisyeartotalWD','agentname','agentdata'));
      
      
           
                 
             }



 if($nameoftransaction == 'refund'){
                          
 //-------Refund collections ----///

 

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->where('users',$agentid)->sum('amount');
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  $thisyeartotalRF = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->where('row_version',2)->sum('amount');

  $agentdata = User::where('created_by_user',$agentid)->get();

$accounts = AccountsTransactions::where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->paginate(20);
  $loansaccounts = LoansAccounts::get();
  return view('dashboard.agent_refund', compact('accounts','loansaccounts','todaytotalRF','thisweektotalRF','thismonthtotalRF','thisyeartotalRF','agentname','agentdata'));
      
  }
 
 
  if($nameoftransaction == 'loanrepayment'){
 

//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$agentdata = User::where('created_by_user',$agentid)->get();

$accounts = AccountsTransactions::where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
 $loansaccounts = LoansAccounts::get();
  return view('dashboard.agent_loanrepayments', compact('accounts','loansaccounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','agentname','agentdata'));
      
  }



if($nameoftransaction == 'loandisbursed'){
//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

   $agentdata = User::where('created_by_user',$agentid)->get();
 $loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
    return view('dashboard.agent_loandisbursed', compact('accounts','loansaccounts','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','agentname','agentdata'));
      
    
}


if($nameoftransaction == 'customersregistered'){

//-----Customers Registered ------////

 

$todaytotalCR = Accounts::whereDate('created_at', Carbon::today())->where('user',$agentid)->count();

$thisweektotalCR =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user',$agentid)->count();

$thismonthtotalCR = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('user',$agentid)->count();

$thisyeartotalCR = Accounts::whereYear('created_at', date('Y'))->where('user',$agentid)->count();

$agentdata = User::where('created_by_user',$agentid)->get();

 

$accounts = Accounts::where('user',$agentid)->OrderBy('created_at','DESC')->paginate(20);
            $loansaccounts = LoansAccounts::get();
            return view('dashboard.agent_customersregistered', compact('accounts','loansaccounts','todaytotalCR','thisweektotalCR','thismonthtotalCR','thisyeartotalCR','agentname','agentdata'));
      
    
    
}


if($nameoftransaction == 'agentcommission'){

//-----Customers Registered ------////
$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

   $agentdata = User::where('created_by_user',$agentid)->get();
 $loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
    return view('dashboard.agent_commission', compact('accounts','loansaccounts','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','agentname','agentdata'));
      
    
    
}


      
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }
    
    
    
    
    
    
    
    
     
    
    //start admin query list.
    
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminquerylist($agentid ='',$agentname = '',$nameoftransaction = '')
    {
         
if($nameoftransaction == 'deposit'){
                
//-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->get();

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->paginate(20);

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->paginate(20);


$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotal=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('row_version',2)->count('amount');
 
$agentdata = User::where('created_by_user',$agentid)->paginate(20);


//$loansaccounts = LoansAccounts::get();
return view('reportsadmin.index', compact('todaytotalDP','thisweektotalDP','thismonthtotalDP','agentname','agentdata','nameoftransaction','todaytotal','thisweektotal','thismonthtotal','todaycount'));
      
}
            
            
             
             
               if($nameoftransaction == 'withdraw'){
                          
 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->get();

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->get();
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->get();


$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thisweektotal=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->sum('amount');

$todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('row_version',2)->count('amount');
 
$agentdata = User::where('created_by_user',$agentid)->get();

$nameoftransaction = 'withdrawals';
//$loansaccounts = LoansAccounts::get();
return view('reportsadmin.reportswd', compact('todaytotalWD','thisweektotalWD','thismonthtotalWD','todaytotal','thisweektotal','thismonthtotal','agentdata','nameoftransaction','todaycount'));
      
      
           
}



 if($nameoftransaction == 'refund'){
                          
 //-------Refund collections ----///

 

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->get();

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->get();
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->get();


 $todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');

  $thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');
  
  $thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->sum('amount');

  
  $todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->count('amount');
  
  $agentdata = User::where('created_by_user',$agentid)->get();

//$accounts = AccountsTransactions::where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->paginate(20);
 // $loansaccounts = LoansAccounts::get();
  return view('reportsadmin.reportrf', compact('todaytotalRF','thisweektotalRF','thismonthtotalRF','agentname','agentdata','todaytotal','thisweektotal','thismonthtotal','nameoftransaction','todaycount'));
      
  }
 
 
  if($nameoftransaction == 'loanrepayment'){
 

//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->get();

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->get();

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->get();

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('row_version',2)->get();

$agentdata = User::where('created_by_user',$agentid)->get();

$accounts = AccountsTransactions::where('name_of_transaction','Loan Repayment')->where('is_shown',1)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
 //$loansaccounts = LoansAccounts::get();
  return view('reportsadmin.index', compact('accounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','agentname','agentdata'));
      
  }



if($nameoftransaction == 'loandisbursed'){
//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->get();

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->get();

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->get();

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('row_version',2)->get();

   $agentdata = User::where('created_by_user',$agentid)->get();
 //$loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
    return view('reportsadmin.index', compact('accounts','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','agentname','agentdata'));
      
    
}


if($nameoftransaction == 'customersregistered'){

//-----Customers Registered ------////

 

$todaytotalCR = Accounts::whereDate('created_at', Carbon::today())->get();

$thisweektotalCR =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();

$thismonthtotalCR = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();

$thisyeartotalCR = Accounts::whereYear('created_at', date('Y'))->get();

$agentdata = User::where('created_by_user',$agentid)->get();

 

$accounts = Accounts::OrderBy('created_at','DESC')->paginate(20);
            //$loansaccounts = LoansAccounts::get();
            return view('reportsadmin.index', compact('accounts','todaytotalCR','thisweektotalCR','thismonthtotalCR','thisyeartotalCR','agentname','agentdata'));
      
    
    
}


if($nameoftransaction == 'agentcommission'){

//-----Customers Registered ------////
$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->get();

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->get();

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->get();

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('row_version',2)->get();
$agentdata = User::where('created_by_user',$agentid)->get();
//$loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
return view('reportsadmin.index', compact('accounts','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','agentname','agentdata'));
      
    
    
}

}
    
    
//End of admin query list.
    
    
    
    
    
    //start agent query list.
    
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function agentquerylist($agentid ='',$agentname = '',$nameoftransaction = '')
    {
         
if($nameoftransaction == 'deposit'){
                
//-------Deposit collections ----///
             
$todaytotalDP = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisweektotalDP=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->paginate(20);

$thismonthtotalDP = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->paginate(20);


$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotal=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Deposit')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->count('amount');
 
$agentdata = User::where('created_by_user',$agentid)->paginate(20);


//$loansaccounts = LoansAccounts::get();
return view('reports.index', compact('todaytotalDP','thisweektotalDP','thismonthtotalDP','agentname','agentdata','nameoftransaction','todaytotal','thisweektotal','thismonthtotal','todaycount'));
      
}
            
            
             
             
               if($nameoftransaction == 'withdraw'){
                          
 //-------Withdrawals collections ----///

 $todaytotalWD = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

 $thisweektotalWD =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->get();
 
 $thismonthtotalWD = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('users',$agentid)->where('is_shown',1)->where('row_version',2)->get();


$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thisweektotal=  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

$todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Withdraw')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->count('amount');
 
$agentdata = User::where('created_by_user',$agentid)->get();

$nameoftransaction = 'withdrawals';
//$loansaccounts = LoansAccounts::get();
return view('reports.reportswd', compact('todaytotalWD','thisweektotalWD','thismonthtotalWD','todaytotal','thisweektotal','thismonthtotal','agentdata','nameoftransaction','todaycount'));
      
      
           
}



 if($nameoftransaction == 'refund'){
                          
 //-------Refund collections ----///

 

  $todaytotalRF = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

  $thisweektotalRF =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->where('users',$agentid)->get();
  
  $thismonthtotalRF = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();


 $todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  $thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Refund')->where('is_shown',1)->where('row_version',2)->where('users',$agentid)->sum('amount');
  
  $thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->sum('amount');

  
  $todaycount = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->count('amount');
  
  $agentdata = User::where('created_by_user',$agentid)->get();

//$accounts = AccountsTransactions::where('name_of_transaction','Refund')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->paginate(20);
 // $loansaccounts = LoansAccounts::get();
  return view('reports.reportrf', compact('todaytotalRF','thisweektotalRF','thismonthtotalRF','agentname','agentdata','todaytotal','thisweektotal','thismonthtotal','nameoftransaction','todaycount'));
      
  }
 
 
  if($nameoftransaction == 'loanrepayment'){
 

//-------Loan collections ----///

$todaytotal = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisweektotal =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thismonthtotal = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisyeartotal = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$agentdata = User::where('created_by_user',$agentid)->get();

$accounts = AccountsTransactions::where('name_of_transaction','Loan Repayment')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
 //$loansaccounts = LoansAccounts::get();
  return view('reports.index', compact('accounts','todaytotal','thisweektotal','thismonthtotal','thisyeartotal','agentname','agentdata'));
      
  }



if($nameoftransaction == 'loandisbursed'){
//--------------------------loan disbursed -----------//

$todaytotalDIS = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisweektotalDIS =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thismonthtotalDIS = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisyeartotalDIS = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

   $agentdata = User::where('created_by_user',$agentid)->get();
 //$loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
    return view('reports.index', compact('accounts','todaytotalDIS','thisweektotalDIS','thismonthtotalDIS','thisyeartotalDIS','agentname','agentdata'));
      
    
}


if($nameoftransaction == 'customersregistered'){

//-----Customers Registered ------////

 

$todaytotalCR = Accounts::whereDate('created_at', Carbon::today())->where('user',$agentid)->get();

$thisweektotalCR =  Accounts::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('user',$agentid)->get();

$thismonthtotalCR = Accounts::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('user',$agentid)->get();

$thisyeartotalCR = Accounts::whereYear('created_at', date('Y'))->where('user',$agentid)->get();

$agentdata = User::where('created_by_user',$agentid)->get();

 

$accounts = Accounts::where('user',$agentid)->OrderBy('created_at','DESC')->paginate(20);
            //$loansaccounts = LoansAccounts::get();
            return view('reports.index', compact('accounts','todaytotalCR','thisweektotalCR','thismonthtotalCR','thisyeartotalCR','agentname','agentdata'));
      
    
    
}


if($nameoftransaction == 'agentcommission'){

//-----Customers Registered ------////
$todaytotalAGTCM = AccountsTransactions::whereDate('created_at', Carbon::today())->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisweektotalAGTCM =  AccountsTransactions::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thismonthtotalAGTCM = AccountsTransactions::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();

$thisyeartotalAGTCM = AccountsTransactions::whereYear('created_at', date('Y'))->where('name_of_transaction','Agent Commission')->where('is_shown',1)->where('users',$agentid)->where('row_version',2)->get();
$agentdata = User::where('created_by_user',$agentid)->get();
//$loansaccounts = LoansAccounts::get();
$accounts = AccountsTransactions::where('name_of_transaction','Loan Disbursed')->where('is_shown',1)->where('users',$agentid)->OrderBy('created_at','DESC')->where('row_version',2)->paginate(20);
return view('reports.index', compact('accounts','todaytotalAGTCM','thisweektotalAGTCM','thismonthtotalAGTCM','thisyeartotalAGTCM','agentname','agentdata'));
      
    
    
}

}
    
    
//End of agent query list.
    
     
    

    public function create(){
        $pagetitle = '';
        $loantypes = LoansAccounts::get()->pluck('id','name');
        return view('loans.create', compact('pagetitle','loantypes'));
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
                                   'account_number' => 'required',
                                   'amount' => 'required',
                                   'bus_capital' => 'required',
                                   'est_daily_exp' => 'required',
                                   'est_daily_sales' => 'required',
                                   'guarantor_name' => 'required',
                                   'guarantor_number' => 'required',
                                   'loan_purpose' => 'required', 
                                   'pri_pmt_src' => 'required', 
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
            else{

               
             
            $loanrequestdetail                       = new Loanrequestdetail(); 
            $loanrequestdetail['account_number']               = $request->account_number;
            $loanrequestdetail['amount']               = $request->amount;
            $loanrequestdetail['bus_capital']               = $request->bus_capital;
            $loanrequestdetail['est_daily_exp']            = $request->est_daily_exp;
            $loanrequestdetail['est_daily_sales']              = $request->est_daily_sales;
            $loanrequestdetail['ext_credit_facility_amt']              = $request->ext_credit_facility_amt;
            $loanrequestdetail['ext_credit_facility']              = $request->ext_credit_facility;
            $loanrequestdetail['guarantor_name']              = $request->guarantor_name;
            $loanrequestdetail['guarantor_number']              = $request->guarantor_number;
            $loanrequestdetail['guarantors_gps_loc']              = $request->guarantors_gps_loc;
            $loanrequestdetail['loan_purpose']              = $request->loan_purpose;
            $loanrequestdetail['phone_number']              = $request->phone_number;
            $loanrequestdetail['pri_pmt_src']              = $request->pri_pmt_src;
            $loanrequestdetail['loan_account_number']              = $request->loan_account_number;
            $loanrequestdetail['user']              = $request->user;
            $loanrequestdetail['disbursement_date']              = $request->disbursement_date;
            $loanrequestdetail['expected_disbursement_date']              = $request->expected_disbursement_date;
            $loanrequestdetail['first_name']              = $request->first_name;
            $loanrequestdetail['irpm']              = $request->irpm;
            $loanrequestdetail['last_name']              = $request->last_name;
            $loanrequestdetail['outstanding_bal']              = $request->outstanding_bal;
            $loanrequestdetail['mode_of_pmt']              = $request->mode_of_pmt;
            $loanrequestdetail['prev_loan']              = $request->prev_loan;
            $loanrequestdetail['mode_of_pmt']              = $request->mode_of_pmt;
            $loanrequestdetail['loan_migrated']              = 0; 
            $loanrequestdetail['loan_id']              = $request->loan_id;
            $loanrequestdetail['approved_amount']              = $request->approved_amount;
            $loanrequestdetail['loan_other_purpose']              = $request->loan_other_purpose;
            $loanrequestdetail['loan_id']              = $request->loan_id;
            $loanrequestdetail['loan_request_rating']              = $request->loan_request_rating; 
            $loanrequestdetail['customer_account_id']              = $request->customer_account_id; 
            $loanrequestdetail['agent_id']              = $request->agent_id; 
         
            $loanrequestdetail->save();

 
                return redirect()->back()->with('success', __('Loan Request Successfully Created.'));
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
