<?php

namespace App\Http\Controllers;

use App\Accounts;
/* use App\LoanAccounts;
use App\LoanRequests; */
use App\User;
use App\UserAccountNumbers;
use App\AccountsTransactions;
use App\SavingsAccounts;
use App\SusuCycles;
use App\CompanyInfo;
use Illuminate\Support\Facades\Hash;
use App\Plan;
/*
use App\MaritalStatus;
use App\Idtype;
use App\Country;
use App\Stream;
use App\UserDefualtView; */
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
/* use Illuminate\Support\Str;

use Illuminate\Support\Facades\Http; */



class ApiUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function toggleUserStatus(Request $request, $id)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::where('id', $id)->where('comp_id', \Auth::user()->comp_id)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Toggle the is_disabled status
        $user->is_disabled = !$user->is_disabled;
        $user->save();

        return response()->json([
            'success' => true, 
            'message' => 'User status updated successfully.',
            'is_disabled' => $user->is_disabled
        ]);
    }

    public function getagents()
    {

        //this is literally 'Manage User Register'
        if ($this->isManagement()) {
            return DB::table('users')->select('id', 'created_by', 'created_by_user', 'email', 'name', 'phone', 'type', 'avatar', 'gender', DB::raw('IFNULL(is_disabled, 0) as is_disabled'))->where('type', '!=', 'Super Admin')->where('type', '!=', 'owner')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
        } else {
            if (\Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
                return DB::table('users')->select('id', 'created_by', 'created_by_user', 'email', 'name', 'phone', 'type', 'avatar', 'gender', DB::raw('IFNULL(is_disabled, 0) as is_disabled'))->orderBy('id', 'DESC')->where('id', \Auth::user()->id)->paginate(10);
            } else {
                return 'error: Type=[' . \Auth::user()->type . '] Roles=' . json_encode(\Auth::user()->getRoleNames());
            }
        }
    }

        private function isManagement()
            {            $user = \Auth::user();
            if (!$user) return false;
    
            $managementTypes = ['Admin', 'owner', 'super admin', 'God Admin', 'Manager'];
            $managementRoles = ['Admin', 'Owner', 'super admin', 'Manager'];
    
            return in_array($user->type, $managementTypes) || $user->hasRole($managementRoles);
        }
    
        public function reactivateAccount(Request $request)
        {
            if (!$this->isManagement()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
    
            $account = UserAccountNumbers::where('account_number', $request->account_number)
                                           ->where('comp_id', \Auth::user()->comp_id)
                                           ->first();
    
            if (!$account) {
                return response()->json(['success' => false, 'message' => 'Account not found'], 404);
            }
    
            $account->account_status = 'active';
            $account->save();
    
            return response()->json(['success' => true, 'message' => 'Account has been re-activated.']);
        }
    /**
     * Search for agent users by name or phone number.
     */
    public function getActiveAgentsList()
    {
        $agents = DB::table('users')
            ->select('id', 'name')
            ->where('comp_id', \Auth::user()->comp_id)
            ->whereIn('type', ['Agent', 'Agents', 'Admin', 'Manager', 'owner', 'super admin'])
            ->where('is_disabled', 0)
            ->orderBy('name', 'ASC')
            ->get();

        return response()->json($agents);
    }

    public function searchAgents(Request $request)
    {
        if (!\Auth::user()->hasAnyRole(['Admin', 'Owner', 'super admin', 'Manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $searchTerm = $request->input('searchtext');
        $searchType = $request->input('searchtype'); // 1 for name, 2 for phone

        $query = DB::table('users')
                    ->select('id', 'name', 'phone', 'email', 'type')
                    ->where('comp_id', \Auth::user()->comp_id)
                    ->whereIn('type', ['Agent', 'Agents', 'Admin', 'Manager', 'owner', 'super admin']); // Include roles that can be agents/receive commissions

        if ($searchTerm) {
            if ($searchType == '1') { // Search by name
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
            } elseif ($searchType == '2') { // Search by phone
                $query->where('phone', 'like', "%{$searchTerm}%");
            }
        }

        $agents = $query->paginate(10);

        return response()->json(['success' => true, 'data' => $agents], 200);
    }

    public function searchUsersForPayout(Request $request)
    {
        if (!\Auth::user()->hasAnyRole(['Admin', 'Owner', 'super admin', 'Manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $searchTerm = $request->input('searchtext');
        $searchType = $request->input('searchtype'); // 1 for name, 2 for phone/account

        $query = DB::table('nobs_registration')
            ->select('id', 'first_name', 'middle_name', 'surname', 'phone_number', 'account_number')
            ->where('comp_id', \Auth::user()->comp_id);

        if ($searchTerm) {
            if ($searchType == '1') { // Search by name
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                      ->orWhere('surname', 'like', "%{$searchTerm}%")
                      ->orWhere(DB::raw("CONCAT(first_name, ' ', surname)"), 'like', "%{$searchTerm}%");
                });
            } elseif ($searchType == '2') { // Search by phone or account number
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('phone_number', 'like', "%{$searchTerm}%")
                      ->orWhere('account_number', 'like', "%{$searchTerm}%");
                });
            }
        }

        $users = $query->orderBy('surname')->orderBy('first_name')->paginate(15);
        
        // To provide a consistent output with other search functions (like searchAgents that returns a 'name' field)
        // we can add a 'name' field to each result.
        $users->getCollection()->transform(function ($user) {
            $user->name = trim($user->first_name . ' ' . $user->middle_name . ' ' . $user->surname);
            return $user;
        });


        return response()->json(['success' => true, 'data' => $users], 200);
    }




    public function getcustomers()
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {


            $customers = DB::table('nobs_registration')
                ->leftJoin('nobs_user_account_numbers', 'nobs_registration.account_number', '=', 'nobs_user_account_numbers.account_number')
                ->select(
                    'nobs_registration.id',
                    'nobs_registration.user_image',
                    'nobs_registration.is_dataimage',
                    'nobs_registration.customer_picture',
                    'nobs_registration.first_name',
                    'nobs_registration.middle_name',
                    'nobs_registration.surname',
                    'nobs_registration.phone_number',
                    'nobs_registration.occupation',
                    'nobs_registration.residential_address',
                    'nobs_registration.account_number',
                    'nobs_registration.created_at',
                    DB::raw('nobs_registration.created_at as created_at2'),
                    'nobs_user_account_numbers.account_status'
                )
                ->where('nobs_registration.comp_id', \Auth::user()->comp_id)
                ->orderBy('nobs_registration.id', 'DESC')
                ->paginate(8); // Reduced to 8 for better performance
            $customers->getCollection()->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
            /*   } else if (\Auth::user()->type == 'Agents') {
            $customers = DB::table('nobs_registration')->select('id', 'user_image', 'is_dataimage', 'customer_picture', 'first_name', 'middle_name', 'surname', 'phone_number', 'email', 'residential_address', 'account_number', 'created_at', DB::raw('created_at as created_at2'), 'date_of_birth2', 'user', 'sec_phone_number', 'postal_address', 'occupation', 'next_of_kin_phone_number', 'next_of_kin_id_number', 'next_of_kin', 'nationality', 'marital_status', 'id_type', 'id_number', 'gender', 'accounttype_num', 'account_types', '__id__')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else {
            return 'error'; */
        }
    }



    public function getcustomerbyid(Request $request)
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {


            $customers = DB::table('nobs_registration')->select('id', 'user_image', 'is_dataimage', 'customer_picture', 'first_name', 'middle_name', 'surname', 'phone_number', 'email', 'residential_address', 'account_number', 'created_at', DB::raw('created_at as created_at2'), 'date_of_birth2', 'user', 'sec_phone_number', 'postal_address', 'occupation', 'next_of_kin_phone_number', 'next_of_kin_id_number', 'next_of_kin', 'nationality', 'marital_status', 'id_type', 'id_number', 'gender', 'accounttype_num', 'account_types', '__id__')->where('id', $request->id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
            /*    } else if (\Auth::user()->type == 'Agents') {
            $customers = DB::table('nobs_registration')->select('id', 'user_image', 'is_dataimage', 'customer_picture', 'first_name', 'middle_name', 'surname', 'phone_number', 'email', 'residential_address', 'account_number', 'created_at', DB::raw('created_at as created_at2'), 'date_of_birth2', 'user', 'sec_phone_number', 'postal_address', 'occupation', 'next_of_kin_phone_number', 'next_of_kin_id_number', 'next_of_kin', 'nationality', 'marital_status', 'id_type', 'id_number', 'gender', 'accounttype_num', 'account_types', '__id__')->where('id', $request->id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else {
            return 'error'; */
        }
    }

    public function getAccountBalances(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = DB::table('nobs_user_account_numbers as ua')
            ->leftJoin('nobs_registration as reg', 'ua.primary_account_number', '=', 'reg.account_number')
            ->leftJoin('users', 'reg.user', '=', 'users.id')
            ->where('ua.comp_id', \Auth::user()->comp_id)
            ->select(
                'ua.account_number',
                'ua.account_type',
                'ua.balance',
                'ua.account_status',
                'reg.first_name',
                'reg.surname',
                'users.is_disabled'
            );

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ua.account_number', 'like', "%{$searchTerm}%")
                    ->orWhere(DB::raw("CONCAT(reg.first_name, ' ', reg.surname)"), 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('balance_min')) {
            $query->where('ua.balance', '>=', $request->balance_min);
        }

        if ($request->filled('balance_max')) {
            $query->where('ua.balance', '<=', $request->balance_max);
        }

        if ($request->filled('account_type')) {
            $query->where('ua.account_type', $request->account_type);
        }
        
        // Clone the query for summary calculation before applying pagination
        $summaryQuery = clone $query;
        $summary = $summaryQuery->select(
            'ua.account_type', 
            DB::raw('SUM(ua.balance) as total_balance'),
            DB::raw('COUNT(ua.id) as account_count')
        )->groupBy('ua.account_type')->get();


        $balances = $query->orderBy('ua.balance', 'DESC')->paginate(20);
        
        // Add the summary to the pagination response
        $balances->appends($request->all());
        $balances->summary = $summary;

        return response()->json($balances);
    }


    public  function searchbyaccountnumber(Request $request)
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Find the primary_account_number in the nobs_user_account_numbers table
            // Find account numbers that look like the variable passed
            $similarAccountNumbers = DB::table('nobs_user_account_numbers')
                ->where('account_number', 'like', '%' . $request->datatosearch . '%')
                ->pluck('account_number');

            if ($similarAccountNumbers->isEmpty()) {
                // Handle the case where no similar account numbers are found
                return [];
            }

            // Find the registrations in the nobs_registration table using the found similar account numbers
            $registrations = DB::table('nobs_registration')->select('id', 'user_image', 'is_dataimage', 'customer_picture', 'first_name', 'middle_name', 'surname', 'phone_number', 'email', 'residential_address', 'account_number', 'created_at', DB::raw('created_at as created_at2'), 'date_of_birth2', 'user', 'sec_phone_number', 'postal_address', 'occupation', 'next_of_kin_phone_number', 'next_of_kin_id_number', 'next_of_kin', 'nationality', 'marital_status', 'id_type', 'id_number', 'gender', 'accounttype_num', 'account_types', '__id__')
                ->whereIn('account_number', $similarAccountNumbers)
                ->paginate(10);

            $registrations->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });

            return $registrations;
        } else {
            return 403;
        }
    }


    //->where('comp_id',\Auth::user()->comp_id)
    public function searchgetcustomers(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {

            $searchTerm = trim($request->datatosearch);
            
            $query = DB::table('nobs_registration')
                ->leftJoin('nobs_user_account_numbers', 'nobs_registration.account_number', '=', 'nobs_user_account_numbers.account_number')
                ->select(
                    'nobs_registration.id', 
                    'nobs_registration.user_image', 
                    'nobs_registration.is_dataimage', 
                    'nobs_registration.customer_picture', 
                    'nobs_registration.first_name', 
                    'nobs_registration.middle_name', 
                    'nobs_registration.surname', 
                    'nobs_registration.phone_number', 
                    'nobs_registration.email', 
                    'nobs_registration.residential_address', 
                    'nobs_registration.account_number', 
                    'nobs_registration.created_at', 
                    DB::raw('nobs_registration.created_at as created_at2'), 
                    'nobs_registration.date_of_birth2', 
                    'nobs_registration.user', 
                    'nobs_registration.sec_phone_number', 
                    'nobs_registration.postal_address', 
                    'nobs_registration.occupation', 
                    'nobs_registration.next_of_kin_phone_number', 
                    'nobs_registration.next_of_kin_id_number', 
                    'nobs_registration.next_of_kin', 
                    'nobs_registration.nationality', 
                    'nobs_registration.marital_status', 
                    'nobs_registration.id_type', 
                    'nobs_registration.id_number', 
                    'nobs_registration.gender', 
                    'nobs_registration.accounttype_num', 
                    'nobs_registration.account_types', 
                    'nobs_registration.__id__',
                    'nobs_user_account_numbers.account_status'
                )
                ->where('nobs_registration.comp_id', \Auth::user()->comp_id);

            // Optimization: If search term looks like an Account Number or Phone, use direct index lookup
            if (preg_match('/^[A-Z0-9-]{5,}$/i', $searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nobs_registration.account_number', $searchTerm)
                      ->orWhere('nobs_registration.phone_number', 'like', "%$searchTerm%");
                });
            } else {
                // Name search
                $searchTerms = explode(' ', $searchTerm);
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        if (empty($term)) continue;
                        $q->where(function($inner) use ($term) {
                            $inner->where('first_name', 'like', "$term%") // Prefix search is much faster than %term%
                                  ->orWhere('surname', 'like', "$term%");
                        });
                    }
                });
            }

            $customers = $query->orderBy('first_name', 'ASC')
                ->paginate(100);

            $customers->getCollection()->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });

            return response()->json($customers);
        }
    }



    public function getaccountlist()
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            $savingsaccounts = SavingsAccounts::where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(20);
            return $savingsaccounts;
        } else {
            return 'error';
        }
    }

    public function getloanaccountlist()
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            $savingsaccounts = SavingsAccounts::where('comp_id', \Auth::user()->comp_id)->where('is_loan', 1)->where('account_type', 3)->orderBy('id', 'DESC')->paginate(20);
            return $savingsaccounts;
        } else {
            return 'error';
        }
    }


    public function getdashboardlisttoday()
    {
        $user = \Auth::user();
        $compId = $user->comp_id;
        $userType = $user->type;
        $agentUser = ($userType == 'Agents') ? $user->created_by_user : null;
        
        $cacheKey = "dashboard_today_{$compId}_" . ($agentUser ?? 'admin');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($compId, $agentUser) {
            $today = Carbon::today();

            // 1. Consolidated Transaction Metrics (One Query)
            $query = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS totalDP'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS totalWD'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS totalRF'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS totalLN'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Disbursed" THEN amount ELSE 0 END) AS totalDIS'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS totalAGTCM'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS totalSCM'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as totalDPCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as totalWDCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Refund" THEN 1 END) as totalRFCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Repayment" THEN 1 END) as totalLNCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Disbursed" THEN 1 END) as totalDISBCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) as totalWDREQCOUNT')
                )
                ->whereDate('created_at', $today)
                ->where('is_shown', 1)
                ->where('row_version', 2)
                ->where('comp_id', $compId);

            if ($agentUser) {
                $query->where('users', $agentUser);
            }

            $transactionMetrics = $query->first();

            // 2. Count Registered Users
            $regQuery = DB::table('nobs_registration')->where('comp_id', $compId)->whereDate('created_at', $today);
            if ($agentUser) $regQuery->where('user', $agentUser);
            $totalREGISTERED = $regQuery->count('id');

            // 3. Loan Request Metrics
            $loanReqQuery = DB::table('nobs_micro_loan_request')
                ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(amount) as sum'))
                ->where('comp_id', $compId)
                ->where('loan_migrated', 0);
            if ($agentUser) $loanReqQuery->where('user', $agentUser);
            $loanRequestMetrics = $loanReqQuery->first();

            // Format data
            $balance = $transactionMetrics->totalDP - $transactionMetrics->totalWD - $transactionMetrics->totalRF - $transactionMetrics->totalAGTCM - $transactionMetrics->totalSCM;
            
            $dataRow = (object)[
                'totalDP' => $transactionMetrics->totalDP,
                'totalWD' => $transactionMetrics->totalWD,
                'totalRF' => $transactionMetrics->totalRF,
                'totalLN' => $transactionMetrics->totalLN,
                'totalDIS' => $transactionMetrics->totalDIS,
                'totalAGTCM' => $transactionMetrics->totalAGTCM,
                'totalSCM' => $transactionMetrics->totalSCM,
                'balance' => $balance
            ];

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = [$dataRow];
            $result->totalLNREQUESTCOUNT = $loanRequestMetrics->count;
            $result->totalLNREQUESTSUM = $loanRequestMetrics->sum;
            $result->totalDPCOUNT = $transactionMetrics->totalDPCOUNT;
            $result->totalWDCOUNT = $transactionMetrics->totalWDCOUNT;
            $result->totalRFCOUNT = $transactionMetrics->totalRFCOUNT;
            $result->totalLNCOUNT = $transactionMetrics->totalLNCOUNT;
            $result->totalDISBCOUNT = $transactionMetrics->totalDISBCOUNT;
            $result->totalWDREQCOUNT = $transactionMetrics->totalWDREQCOUNT;
            $result->totalREGISTERED = $totalREGISTERED;
            $result->total = 1;

            return response()->json($result);
        });
    }


    public function getdashboardlistthisweek()
    {
        $user = \Auth::user();
        $compId = $user->comp_id;
        $userType = $user->type;
        $agentUser = ($userType == 'Agents') ? $user->created_by_user : null;
        
        $cacheKey = "dashboard_week_{$compId}_" . ($agentUser ?? 'admin');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($compId, $agentUser) {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();

            // 1. Consolidated Transaction Metrics
            $query = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS totalDP'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS totalWD'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS totalRF'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS totalLN'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Disbursed" THEN amount ELSE 0 END) AS totalDIS'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS totalAGTCM'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS totalSCM'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as totalDPCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as totalWDCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Refund" THEN 1 END) as totalRFCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Repayment" THEN 1 END) as totalLNCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Disbursed" THEN 1 END) as totalDISBCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) as totalWDREQCOUNT')
                )
                ->whereBetween('created_at', [$start, $end])
                ->where('is_shown', 1)
                ->where('row_version', 2)
                ->where('comp_id', $compId);

            if ($agentUser) $query->where('users', $agentUser);
            $transactionMetrics = $query->first();

            // 2. Count Registered Users
            $regQuery = DB::table('nobs_registration')->where('comp_id', $compId)->whereBetween('created_at', [$start, $end]);
            if ($agentUser) $regQuery->where('user', $agentUser);
            $totalREGISTERED = $regQuery->count('id');

            // 3. Loan Request Metrics
            $loanReqQuery = DB::table('nobs_micro_loan_request')
                ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(amount) as sum'))
                ->where('comp_id', $compId)
                ->where('loan_migrated', 0);
            if ($agentUser) $loanReqQuery->where('user', $agentUser);
            $loanRequestMetrics = $loanReqQuery->first();

            // Format data
            $balance = $transactionMetrics->totalDP - $transactionMetrics->totalWD - $transactionMetrics->totalRF - $transactionMetrics->totalAGTCM - $transactionMetrics->totalSCM;
            
            $dataRow = (object)[
                'totalDP' => $transactionMetrics->totalDP,
                'totalWD' => $transactionMetrics->totalWD,
                'totalRF' => $transactionMetrics->totalRF,
                'totalLN' => $transactionMetrics->totalLN,
                'totalDIS' => $transactionMetrics->totalDIS,
                'totalAGTCM' => $transactionMetrics->totalAGTCM,
                'totalSCM' => $transactionMetrics->totalSCM,
                'balance' => $balance
            ];

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = [$dataRow];
            $result->totalLNREQUESTCOUNT = $loanRequestMetrics->count;
            $result->totalLNREQUESTSUM = $loanRequestMetrics->sum;
            $result->totalDPCOUNT = $transactionMetrics->totalDPCOUNT;
            $result->totalWDCOUNT = $transactionMetrics->totalWDCOUNT;
            $result->totalRFCOUNT = $transactionMetrics->totalRFCOUNT;
            $result->totalLNCOUNT = $transactionMetrics->totalLNCOUNT;
            $result->totalDISBCOUNT = $transactionMetrics->totalDISBCOUNT;
            $result->totalWDREQCOUNT = $transactionMetrics->totalWDREQCOUNT;
            $result->totalREGISTERED = $totalREGISTERED;
            $result->total = 1;

            return response()->json($result);
        });
    }


    public function getdailycollections(Request $request)
    {

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            // ...

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            // Sum of amounts for both agents and products


            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' =>  $byagents, 'byproducts' =>  $byproducts, 'totalamount' => $totalAmount]);
        } else if (\Auth::user()->type == 'Agents') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
            // ->where('users', \Auth::user()->created_by_user)
            // ...

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' => $byagents, 'byproducts' => $byproducts, 'totalamount' => $totalAmount]);
        }
    }

    public function getdailycollectionswithdraw(Request $request)
    {

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            // ...

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            // Sum of amounts for both agents and products


            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' =>  $byagents, 'byproducts' =>  $byproducts, 'totalamount' => $totalAmount]);
        } else if (\Auth::user()->type == 'Agents') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
            // ->where('users', \Auth::user()->created_by_user)
            // ...

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' => $byagents, 'byproducts' => $byproducts, 'totalamount' => $totalAmount]);
        }
    }

    public function getdailycollectionsloanrepayment(Request $request)
    {

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' =>  $byagents, 'byproducts' =>  $byproducts, 'totalamount' => $totalAmount]);
        } else if (\Auth::user()->type == 'Agents') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byagents = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('users')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);

            $totalAmount = DB::table('nobs_transactions')
                ->where('name_of_transaction', 'Loan Repayment')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', \Auth::user()->created_by_user)
                ->whereDate('created_at', $today)
                ->sum('amount');

            return response()->json(['byagents' => $byagents, 'byproducts' => $byproducts, 'totalamount' => $totalAmount]);
        }
    }

    public function agentmobilizationbyproducts(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', $request->agentid)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);
            return response()->json(['byproducts' => $byproducts]);
        } else if (\Auth::user()->type == 'Agents') {

            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Deposit')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', $request->agentid)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);
            return response()->json(['byproducts' => $byproducts]);
        }
    }

    public function agentmobilizationbyproductswithdrawals(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', $request->agentid)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);
            return response()->json(['byproducts' => $byproducts]);
        } else if (\Auth::user()->type == 'Agents') {

            $today = \Carbon\Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

            $byproducts = DB::table('nobs_transactions')
                ->select('users', 'agentname', 'id', 'account_type', DB::raw('SUM(amount) as total_amount'))
                ->where('name_of_transaction', 'Withdraw')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('users', $request->agentid)
                ->whereDate('created_at', $today)
                ->groupBy('account_type')
                ->orderBy('total_amount', 'DESC')
                ->paginate(100);
            return response()->json(['byproducts' => $byproducts]);
        }
    }


    public function getdashboardlistthismonth()
    {
        $user = \Auth::user();
        $compId = $user->comp_id;
        $userType = $user->type;
        $agentUser = ($userType == 'Agents') ? $user->created_by_user : null;
        
        $cacheKey = "dashboard_month_{$compId}_" . ($agentUser ?? 'admin');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($compId, $agentUser) {
            $month = date('m');
            $year = date('Y');

            // 1. Consolidated Transaction Metrics
            $query = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS totalDP'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS totalWD'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS totalRF'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS totalLN'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Disbursed" THEN amount ELSE 0 END) AS totalDIS'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS totalAGTCM'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS totalSCM'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as totalDPCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as totalWDCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Refund" THEN 1 END) as totalRFCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Repayment" THEN 1 END) as totalLNCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Disbursed" THEN 1 END) as totalDISBCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) as totalWDREQCOUNT')
                )
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('is_shown', 1)
                ->where('row_version', 2)
                ->where('comp_id', $compId);

            if ($agentUser) $query->where('users', $agentUser);
            $transactionMetrics = $query->first();

            // 2. Count Registered Users
            $regQuery = DB::table('nobs_registration')->where('comp_id', $compId)->whereMonth('created_at', $month)->whereYear('created_at', $year);
            if ($agentUser) $regQuery->where('user', $agentUser);
            $totalREGISTERED = $regQuery->count('id');

            // 3. Loan Request Metrics
            $loanReqQuery = DB::table('nobs_micro_loan_request')
                ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(amount) as sum'))
                ->where('comp_id', $compId)
                ->where('loan_migrated', 0);
            if ($agentUser) $loanReqQuery->where('user', $agentUser);
            $loanRequestMetrics = $loanReqQuery->first();

            // Format data
            $balance = $transactionMetrics->totalDP - $transactionMetrics->totalWD - $transactionMetrics->totalRF - $transactionMetrics->totalAGTCM - $transactionMetrics->totalSCM;
            
            $dataRow = (object)[
                'totalDP' => $transactionMetrics->totalDP,
                'totalWD' => $transactionMetrics->totalWD,
                'totalRF' => $transactionMetrics->totalRF,
                'totalLN' => $transactionMetrics->totalLN,
                'totalDIS' => $transactionMetrics->totalDIS,
                'totalAGTCM' => $transactionMetrics->totalAGTCM,
                'totalSCM' => $transactionMetrics->totalSCM,
                'balance' => $balance
            ];

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = [$dataRow];
            $result->totalLNREQUESTCOUNT = $loanRequestMetrics->count;
            $result->totalLNREQUESTSUM = $loanRequestMetrics->sum;
            $result->totalDPCOUNT = $transactionMetrics->totalDPCOUNT;
            $result->totalWDCOUNT = $transactionMetrics->totalWDCOUNT;
            $result->totalRFCOUNT = $transactionMetrics->totalRFCOUNT;
            $result->totalLNCOUNT = $transactionMetrics->totalLNCOUNT;
            $result->totalDISBCOUNT = $transactionMetrics->totalDISBCOUNT;
            $result->totalWDREQCOUNT = $transactionMetrics->totalWDREQCOUNT;
            $result->totalREGISTERED = $totalREGISTERED;
            $result->total = 1;

            return response()->json($result);
        });
    }



    public function getdashboardlistthisyear()
    {
        $user = \Auth::user();
        $compId = $user->comp_id;
        $userType = $user->type;
        $agentUser = ($userType == 'Agents') ? $user->created_by_user : null;
        
        $cacheKey = "dashboard_year_{$compId}_" . ($agentUser ?? 'admin');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($compId, $agentUser) {
            $year = date('Y');

            // 1. Consolidated Transaction Metrics
            $query = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS totalDP'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS totalWD'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS totalRF'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS totalLN'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Disbursed" THEN amount ELSE 0 END) AS totalDIS'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS totalAGTCM'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS totalSCM'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as totalDPCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as totalWDCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Refund" THEN 1 END) as totalRFCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Repayment" THEN 1 END) as totalLNCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Disbursed" THEN 1 END) as totalDISBCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) as totalWDREQCOUNT')
                )
                ->whereYear('created_at', $year)
                ->where('is_shown', 1)
                ->where('row_version', 2)
                ->where('comp_id', $compId);

            if ($agentUser) $query->where('users', $agentUser);
            $transactionMetrics = $query->first();

            // 2. Count Registered Users
            $regQuery = DB::table('nobs_registration')->where('comp_id', $compId)->whereYear('created_at', $year);
            if ($agentUser) $regQuery->where('user', $agentUser);
            $totalREGISTERED = $regQuery->count('id');

            // 3. Loan Request Metrics
            $loanReqQuery = DB::table('nobs_micro_loan_request')
                ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(amount) as sum'))
                ->where('comp_id', $compId)
                ->where('loan_migrated', 0);
            if ($agentUser) $loanReqQuery->where('user', $agentUser);
            $loanRequestMetrics = $loanReqQuery->first();

            // Format data
            $balance = $transactionMetrics->totalDP - $transactionMetrics->totalWD - $transactionMetrics->totalRF - $transactionMetrics->totalAGTCM - $transactionMetrics->totalSCM;
            
            $dataRow = (object)[
                'totalDP' => $transactionMetrics->totalDP,
                'totalWD' => $transactionMetrics->totalWD,
                'totalRF' => $transactionMetrics->totalRF,
                'totalLN' => $transactionMetrics->totalLN,
                'totalDIS' => $transactionMetrics->totalDIS,
                'totalAGTCM' => $transactionMetrics->totalAGTCM,
                'totalSCM' => $transactionMetrics->totalSCM,
                'balance' => $balance
            ];

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = [$dataRow];
            $result->totalLNREQUESTCOUNT = $loanRequestMetrics->count;
            $result->totalLNREQUESTSUM = $loanRequestMetrics->sum;
            $result->totalDPCOUNT = $transactionMetrics->totalDPCOUNT;
            $result->totalWDCOUNT = $transactionMetrics->totalWDCOUNT;
            $result->totalRFCOUNT = $transactionMetrics->totalRFCOUNT;
            $result->totalLNCOUNT = $transactionMetrics->totalLNCOUNT;
            $result->totalDISBCOUNT = $transactionMetrics->totalDISBCOUNT;
            $result->totalWDREQCOUNT = $transactionMetrics->totalWDREQCOUNT;
            $result->totalREGISTERED = $totalREGISTERED;
            $result->total = 1;

            return response()->json($result);
        });
    }


    //

    public function getdashboardlistalltime()
    {
        $user = \Auth::user();
        $compId = $user->comp_id;
        $userType = $user->type;
        $agentUser = ($userType == 'Agents') ? $user->created_by_user : null;
        
        $cacheKey = "dashboard_alltime_{$compId}_" . ($agentUser ?? 'admin');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($compId, $agentUser) {
            // 1. Consolidated Transaction Metrics
            $query = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS totalDP'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS totalWD'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS totalRF'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS totalLN'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Disbursed" THEN amount ELSE 0 END) AS totalDIS'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS totalAGTCM'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS totalSCM'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as totalDPCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as totalWDCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Refund" THEN 1 END) as totalRFCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Repayment" THEN 1 END) as totalLNCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Loan Disbursed" THEN 1 END) as totalDISBCOUNT'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) as totalWDREQCOUNT')
                )
                ->where('is_shown', 1)
                ->where('row_version', 2)
                ->where('comp_id', $compId);

            if ($agentUser) $query->where('users', $agentUser);
            $transactionMetrics = $query->first();

            // 2. Count Registered Users
            $regQuery = DB::table('nobs_registration')->where('comp_id', $compId);
            if ($agentUser) $regQuery->where('user', $agentUser);
            $totalREGISTERED = $regQuery->count('id');

            // 3. Loan Request Metrics
            $loanReqQuery = DB::table('nobs_micro_loan_request')
                ->select(DB::raw('COUNT(id) as count'), DB::raw('SUM(amount) as sum'))
                ->where('comp_id', $compId)
                ->where('loan_migrated', 0);
            if ($agentUser) $loanReqQuery->where('user', $agentUser);
            $loanRequestMetrics = $loanReqQuery->first();

            // Format data
            $balance = $transactionMetrics->totalDP - $transactionMetrics->totalWD - $transactionMetrics->totalRF - $transactionMetrics->totalAGTCM - $transactionMetrics->totalSCM;
            
            $dataRow = (object)[
                'totalDP' => $transactionMetrics->totalDP,
                'totalWD' => $transactionMetrics->totalWD,
                'totalRF' => $transactionMetrics->totalRF,
                'totalLN' => $transactionMetrics->totalLN,
                'totalDIS' => $transactionMetrics->totalDIS,
                'totalAGTCM' => $transactionMetrics->totalAGTCM,
                'totalSCM' => $transactionMetrics->totalSCM,
                'balance' => $balance
            ];

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = [$dataRow];
            $result->totalLNREQUESTCOUNT = $loanRequestMetrics->count;
            $result->totalLNREQUESTSUM = $loanRequestMetrics->sum;
            $result->totalDPCOUNT = $transactionMetrics->totalDPCOUNT;
            $result->totalWDCOUNT = $transactionMetrics->totalWDCOUNT;
            $result->totalRFCOUNT = $transactionMetrics->totalRFCOUNT;
            $result->totalLNCOUNT = $transactionMetrics->totalLNCOUNT;
            $result->totalDISBCOUNT = $transactionMetrics->totalDISBCOUNT;
            $result->totalWDREQCOUNT = $transactionMetrics->totalWDREQCOUNT;
            $result->totalREGISTERED = $totalREGISTERED;
            $result->total = 1;

            return response()->json($result);
        });
    }

    public function getwithdrawallist()
    {

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {

            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Withdraw')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else if (\Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent') {
            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Withdraw')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->orderBy('id', 'DESC')->paginate(100);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else {
            return 'error';
        }
    }

    public function getdepositlist()
    {


        /*`__id__`, `account_number`, `account_type`, `amount`, `created_at3`, `det_rep_name_of_transaction`, `agentname`, `name_of_transaction`, `phone_number`, `transaction_id`, `users`, `deposit_total`, `updated_at`, `withdrawal_total`, `tid`, `id`, `is_shown`, `foreign_id`, `is_loan`, `created_at`, `updated`, `withdrawrequest_approved`, `withdrawrequest_disapproved`, `approved_by`, `paid_by`, `is_paid`, `paid_withdrawal_msg`, `row_version`*/

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {

            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Deposit')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else if (\Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent') {
            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Deposit')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->orderBy('id', 'DESC')->paginate(100);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else {
            return 'error';
        }
    }



    public function getreversallist()
    {


        /*`__id__`, `account_number`, `account_type`, `amount`, `created_at3`, `det_rep_name_of_transaction`, `agentname`, `name_of_transaction`, `phone_number`, `transaction_id`, `users`, `deposit_total`, `updated_at`, `withdrawal_total`, `tid`, `id`, `is_shown`, `foreign_id`, `is_loan`, `created_at`, `updated`, `withdrawrequest_approved`, `withdrawrequest_disapproved`, `approved_by`, `paid_by`, `is_paid`, `paid_withdrawal_msg`, `row_version`*/

        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Refund')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else if (\Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent') {
            $customers = DB::table('nobs_transactions')->select('__id__', 'account_number', 'account_type', 'amount', 'det_rep_name_of_transaction', 'agentname', 'name_of_transaction', 'transaction_id', 'users', 'id', 'created_at', DB::raw('created_at as created_at2'))->where('name_of_transaction', 'Refund')->where('comp_id', \Auth::user()->comp_id)->orderBy('id', 'DESC')->paginate(10);
            $customers->transform(function ($customer) {
                $customer->created_at = Carbon::parse($customer->created_at)->diffForHumans();
                return $customer;
            });
            return $customers;
        } else {
            return 'error';
        }
    }


    public function sendFrogMessage($theusername, $thepass, $thesenderid, $themessage, $thenumbersent)
    {
        $baseUrl = 'https://banqpopulaire.website/nobsimages2/sendfrogmsg.php';

        $params = [
            'theusername' => $theusername,
            'thepass' => $thepass,
            'thesenderid' => $thesenderid,
            'themessage' => $themessage,
            'thenumbersent' => $thenumbersent,
        ];

        // Build the query string
        $queryString = http_build_query($params);

        // Create the full URL
        $url = $baseUrl . '?' . $queryString;

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            // Handle the error
            return response()->json(['status' => 'error', 'message' => curl_error($ch)]);
        }

        // Close cURL session
        curl_close($ch);

        // Handle the response data accordingly
        // ...

        return response()->json(['status' => 'success', 'data' => $response]);
    }



    public function getloanrequests()
    {
        $loanrequests =  DB::table('nobs_micro_loan_request')->where('loan_migrated', 0)->join('nobs_loans_accounts', 'nobs_micro_loan_request.loan_id', '=', 'nobs_loans_accounts.id')->join('nobs_registration', 'nobs_registration.id', '=', 'nobs_micro_loan_request.customer_account_id')->join('nobs_loan_purpose_list', 'nobs_micro_loan_request.loan_purpose', '=', 'nobs_loan_purpose_list.id')->join('users', 'nobs_micro_loan_request.agent_id', '=', 'users.id')->orderBy('nobs_micro_loan_request.id', 'DESC')->paginate(10, ['nobs_micro_loan_request.*', 'nobs_loans_accounts.name', 'nobs_loan_purpose_list.name as purposename', 'nobs_registration.occupation', 'nobs_registration.residential_address', 'nobs_registration.customer_picture', 'users.name as agentname']);


        $loanrequestcounts =  DB::table('nobs_micro_loan_request')->where('loan_migrated', 0)->count('nobs_micro_loan_request.id');
        $loanrequestsum =  DB::table('nobs_micro_loan_request')->where('loan_migrated', 0)->sum('amount');

        $result = new \stdClass();
        $result->current_page = 1;
        $result->loanrequestcount = $loanrequestcounts;
        $result->loanrequestsum = $loanrequestsum;
        $result->data = $loanrequests;
        $result->first_page_url = request()->fullUrl();
        $result->from = null;
        $result->last_page = 1;
        $result->last_page_url = request()->fullUrl();
        $result->next_page_url = null;
        $result->path = request()->url();
        $result->per_page = 10;
        $result->prev_page_url = null;
        $result->to = null;
        $result->total = $loanrequests->count();
        return response()->json($result);

        return $loanrequests;
    }



    public function withdrawalrequests()
    {
        //this is literally 'Manage User Register'
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {

            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 0)->paginate(10);
            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 1)->where('is_paid', 0)->paginate(10);
            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdraw')->where('withdrawrequest_approved', 1)->where('is_paid', 1)->paginate(10);

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = $accounts;
            $result->accountsapproved = $accountsapproved;
            $result->accountspaid = $accountspaid;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 10;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $accounts->count();
            return response()->json($result);
        } else  if (\Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent') {
            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 0)->where('users', \Auth::user()->created_by_user)->paginate(20);

            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 1)->where('is_paid', 0)->paginate(10);

            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->where('name_of_transaction', 'Withdraw')->where('withdrawrequest_approved', 1)->where('is_paid', 1)->paginate(10);

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = $accounts;
            $result->accountsapproved = $accountsapproved;
            $result->accountspaid = $accountspaid;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 10;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $accounts->count();
            return response()->json($result);
        }
    }

    public function system_products_super()
    {
        //this is literally 'Manage User Register'
        if (\Auth::user()->type == 'super admin') {

            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 0)->paginate(10);
            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 1)->where('is_paid', 0)->paginate(10);
            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdraw')->where('withdrawrequest_approved', 1)->where('is_paid', 1)->paginate(10);

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = $accounts;
            $result->accountsapproved = $accountsapproved;
            $result->accountspaid = $accountspaid;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 10;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $accounts->count();
            return response()->json($result);
        } else  if (\Auth::user()->type == 'Agents') {
            $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 0)->where('users', \Auth::user()->created_by_user)->paginate(20);

            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 1)->where('is_paid', 0)->paginate(10);

            $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('comp_id', \Auth::user()->comp_id)->where('users', \Auth::user()->created_by_user)->where('name_of_transaction', 'Withdraw')->where('withdrawrequest_approved', 1)->where('is_paid', 1)->paginate(10);

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = $accounts;
            $result->accountsapproved = $accountsapproved;
            $result->accountspaid = $accountspaid;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 10;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $accounts->count();
            return response()->json($result);
        }
    }

    public function get_system_products_for_clients()
    {
        //this is literally 'Manage User Register'
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {

            //package_type: 1 means SMS
            $sms_packages = Plan::orderBy('price', 'ASC')->where('package_type', 1)->where('isdisabled', 0)->paginate(20);

            //package_type: 1 means SMS
            $transactions_packages = Plan::orderBy('price', 'ASC')->where('package_type', 0)->where('isdisabled', 0)->paginate(20);
            $purchasewhatsappnumber = '233542148020';


            $result = new \stdClass();
            $result->current_page = 1;
            $result->sms_packages = $sms_packages;
            $result->transaction_packages = $transactions_packages;
            $result->purchasewhatsappnumber = $purchasewhatsappnumber;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 20;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $sms_packages->count();

            return response()->json($result);
        } else {
            return null;
        }
    }

    public function approve_withdrawal_request(Request $request)
    {
        //this is literally 'Manage User Register'
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            AccountsTransactions::where('id', $request->transactionid)
                ->update([
                    'withdrawrequest_approved' => 1,
                    'name_of_transaction' => 'Withdrawal Request',
                    'is_paid' => 0,
                    'approved_by' =>   \Auth::user()->name
                ]);
            return "True";
        } else {
            return "False";
        }
    }


    public function company_credit_transaction($operation = 'sub', $value)
    {
        // Check user type
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin' || \Auth::user()->type == 'Agents') {
            // Get the current credit value
            $currentCredit = CompanyInfo::where('id', \Auth::user()->comp_id)->value('transactional_credit');

            if ($operation == 'add') {

                // Decrease the credit by 1
                $newCredit = $currentCredit + $value;

                // Update the database with the new credit value
                $thereturned = CompanyInfo::where('id', \Auth::user()->comp_id)
                    ->update([
                        'transactional_credit' => $newCredit
                    ]);
                return $thereturned;
            } else {
                // Check if there is credit available
                if ($currentCredit > 0) {
                    // Decrease the credit by 1
                    $newCredit = $currentCredit - $value;

                    // Update the database with the new credit value
                    $thereturnedd = CompanyInfo::where('id', \Auth::user()->comp_id)
                        ->update([
                            'transactional_credit' => $newCredit
                        ]);
                    return $thereturnedd;
                } else {


                    return false;
                }
            }
        }
    }

    public function company_sms_transaction($operation, $value)
    {
        // Check user type
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin' || \Auth::user()->type == 'Agents') {
            // Get the current credit value
            $currentCredit = CompanyInfo::where('id', \Auth::user()->comp_id)->value('sms_credit');

            if ($operation == 'add') {

                // Decrease the credit by 1
                $newCredit = $currentCredit + $value;

                // Update the database with the new credit value
                $thereturned = CompanyInfo::where('id', \Auth::user()->comp_id)
                    ->update([
                        'sms_credit' => $newCredit
                    ]);
                return $thereturned;
            } else {
                // Check if there is credit available
                if ($currentCredit > 0) {
                    // Decrease the credit by 1
                    $newCredit = $currentCredit - $value;

                    // Update the database with the new credit value
                    $thereturnedd = CompanyInfo::where('id', \Auth::user()->comp_id)
                        ->update([
                            'sms_credit' => $newCredit
                        ]);
                    return $thereturnedd;
                } else {


                    return false;
                }
            }
        }
    }

    public function company_sms_transaction2(Request $request)
    {
        // Check user type
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin' || \Auth::user()->type == 'Agents') {
            // Get the current credit value
            $currentCredit = CompanyInfo::where('id', \Auth::user()->comp_id)->value('sms_credit');

            if ($request->operation == 'add') {

                // Decrease the credit by 1
                $newCredit = $currentCredit + $request->value;

                // Update the database with the new credit value
                $thereturned = CompanyInfo::where('id', \Auth::user()->comp_id)
                    ->update([
                        'sms_credit' => $newCredit
                    ]);
                return $thereturned;
            } else {
                // Check if there is credit available
                if ($currentCredit > 0) {
                    // Decrease the credit by 1
                    $newCredit = $currentCredit - $request->value;

                    // Update the database with the new credit value
                    $thereturnedd = CompanyInfo::where('id', \Auth::user()->comp_id)
                        ->update([
                            'sms_credit' => $newCredit
                        ]);
                    return $thereturnedd;
                } else {


                    return false;
                }
            }
        }
    }



    //This is for withdrawal request.
    public function withdrawtransaction(Request $request)
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            // Check Account Dormancy (Sprint 8.5)
            $account = UserAccountNumbers::where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->first();

            if ($account && $account->account_status == 'dormant' && !$this->isManagement()) {
                return response()->json(['success' => false, 'message' => 'Account is dormant. Please contact management for re-activation.'], 403);
            }

            $is_transaction_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                ->where('transactional_credit', '>', 0)
                ->exists();

            if ($is_transaction_enabled) {
                $credittrans2 = $this->company_credit_transaction('sub', 1);
              if ($credittrans2) {



                    $customerName =  $request->firstname . " " . $request->middlename . " " . $request->surname;
                    $mydatey = date("Y-m-d H:i:s");

                    $randomCode = \Str::random(6);
                    $__id__ =  \Str::random(30);

                    $transaction = new AccountsTransactions();

                    // Set the model attributes
                    $transaction->__id__ = $__id__;
                    $transaction->account_number = $request->accountnumber;
                    $transaction->account_type = $request->selectedaccounttype;
                    $transaction->created_at = $mydatey;
                    $transaction->det_rep_name_of_transaction = $customerName;
                    $transaction->phone_number = $request->phonenumber;
                    $transaction->transaction_id = $randomCode;
                    $transaction->amount = $request->customerdeposit;
                    $transaction->agentname = \Auth::user()->name;
                    $transaction->name_of_transaction = 'Withdrawal Request';
                    $transaction->users = \Auth::user()->created_by_user;
                    $transaction->is_shown = 1;
                    $transaction->is_loan = 0;
                    $transaction->is_paid = 1;
                    $transaction->paid_withdrawal_msg = $request->themessage;
                    $transaction->row_version = 2;
                    $transaction->comp_id = \Auth::user()->comp_id;

                    // Save the transaction to the database
                    if ($transaction->save()) {
                        // Update last activity date
                        UserAccountNumbers::where('account_number', $request->accountnumber)
                            ->where('comp_id', \Auth::user()->comp_id)
                            ->update(['last_transaction_date' => now()]);
                        
                        return "True";
                    }
                    
                    return "False";
                } 
            } else {
                return "No Credit Balance for Transaction.";
            }
        } else {
            return "False";
        }
    }


    public function withdrawtransaction_susu(Request $request)
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            // Check Account Dormancy (Sprint 8.5)
            $account = UserAccountNumbers::where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->first();

            if ($account && $account->account_status == 'dormant' && !$this->isManagement()) {
                return response()->json(['success' => false, 'message' => 'Account is dormant. Please contact management for re-activation.'], 403);
            }

            $is_transaction_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                ->where('transactional_credit', '>', 0)
                ->exists();

            if ($is_transaction_enabled) {

                $credittrans2 = $this->company_credit_transaction('sub', 1);
                if ($credittrans2) {

                    $customerName =  $request->firstname . " " . $request->middlename . " " . $request->surname;
                    $mydatey = date("Y-m-d H:i:s");

                    $randomCode = \Str::random(6);
                    $__id__ =  \Str::random(30);

                    $transaction = new AccountsTransactions();

                    // Set the model attributes
                    $transaction->__id__ = $__id__;
                    $transaction->account_number = $request->accountnumber;
                    $transaction->account_type = $request->selectedaccounttype;
                    $transaction->created_at = $mydatey;
                    $transaction->det_rep_name_of_transaction = $customerName;
                    $transaction->phone_number = $request->phonenumber;
                    $transaction->transaction_id = $randomCode;
                    $transaction->amount = $request->customerbalance;
                    $transaction->agentname = \Auth::user()->name;
                    $transaction->name_of_transaction = 'Withdrawal Request';
                    $transaction->users = \Auth::user()->created_by_user;
                    $transaction->is_shown = 1;
                    $transaction->is_loan = 0;
                    $transaction->is_paid = 1;
                    $transaction->paid_withdrawal_msg = $request->themessage;
                    $transaction->row_version = 2;
                    $transaction->comp_id = \Auth::user()->comp_id;

                    // Save the transaction to the database
                    if ($transaction->save()) {
                        // Update last activity date
                        UserAccountNumbers::where('account_number', $request->accountnumber)
                            ->where('comp_id', \Auth::user()->comp_id)
                            ->update(['last_transaction_date' => now()]);
                        
                        return "True";
                    }
                    
                    return "False";
                }
            } else {
                return "No Credit Balance for Transaction.";
            }
        } else {

            return "False";
        }
    }




    public function gen_systemreports(Request $request)
    {
        $accountnumbers = UserAccountNumbers::where('comp_id', 2)->paginate(15);

        $htmlRows = '';
        $mydatey = date("Y-m-d H:i:s");

        foreach ($accountnumbers as $thisaccountnumber) {
            $theaccount_number = $thisaccountnumber->account_number;
            $mainaccountnumber = $thisaccountnumber->primary_account_number;

            $useraccount = Accounts::where('account_number', $mainaccountnumber)
                ->where('comp_id', \Auth::user()->comp_id)->first();

            $accountnumber = "";
            $firstname = "";
            $middlename = "";
            $surname = "";
            $phonenumber = "";

            if ($useraccount) {
                $fieldNames = $useraccount->getFillable();
                $fieldValues = [];

                foreach ($fieldNames as $fieldName) {
                    $fieldValues[$fieldName] = $useraccount->{$fieldName};
                }

                $firstname = $fieldValues['first_name'];
                $middlename = $fieldValues['middle_name'];
                $surname = $fieldValues['surname'];
                $phonenumber = $fieldValues['phone_number'];
            }

            $customername = $firstname . ' ' . $middlename . ' ' . $surname;

            $currentbalance = $this->getaccountbalance2($theaccount_number);
            $thisaccountnumber->balance = $currentbalance;

            /*   UserAccountNumbers::where('account_number',  $thisaccountnumber->account_number)
                ->where('comp_id', \Auth::user()->comp_id)
                ->update([
                    'balance' => $currentbalance
                ]); */

            // Generate HTML row
            $htmlRows .= "<tr>";
            $htmlRows .= "<td>{$thisaccountnumber->id}</td>";
            $htmlRows .= "<td>{$customername}</td>";
            $htmlRows .= "<td>{$thisaccountnumber->account_number}</td>";
            $htmlRows .= "<td>{$currentbalance}</td>";
            $htmlRows .= "<td>{$thisaccountnumber->account_type}</td>";
            $htmlRows .= "<td>{$phonenumber}</td>";
            $htmlRows .= "<td>{$thisaccountnumber->created_at}</td>";
            $htmlRows .= "<td>{$thisaccountnumber->updated_at}</td>";
            // Add more fields as needed    
            $htmlRows .= "</tr>";

            usleep(200000); // 300,000 microseconds = 0.3 seconds
        }

        // Wrap HTML rows in a table
        $htmlTable = "{$htmlRows}";

        return response()->make($htmlTable);
    }


    public function monthlydeductions(Request $request)
    {

        // Assuming you have the $rowid variable

        //1. get all accounts that are on monthly deductions
        //2. for each account, take the monthly deduction value from the main account type.
        //3. take the deductions from the amount. if the balance - the deductions < 0 flag that account as liability accounts so later user can check the out.
        //4. insert all accounts being deducted into another table so data can be used as report.

        $accounttypeid = '';
        $commissiontype = '';
        $commissionvalue = 10.00;



        $accountnumbers = UserAccountNumbers::whereIn('account_type', ['Susu Plus', 'Susu Business 1', 'Susu plus 2', 'Susu Business 3', 'Susu Plus 3', 'Susu Business 2', 'Susu Account'])->where('comp_id', 2)->paginate(5);

        $modifiedAccountNumbers = [];
        $mydatey = date("Y-m-d H:i:s");

        foreach ($accountnumbers as $thisaccountnumber) {

            $theaccount_number = $thisaccountnumber->account_number;

            $mainaccountnumber =  $thisaccountnumber->primary_account_number;

            $useraccount = Accounts::where('account_number', $mainaccountnumber)->where('comp_id', \Auth::user()->comp_id)->first();

            $accountnumber = "";
            $firstname = "";
            $middlename = "";
            $surname = "";
            $phonenumber = "";

            if ($useraccount) {
                $fieldNames = $useraccount->getFillable();
                $fieldValues = [];

                foreach ($fieldNames as $fieldName) {
                    $fieldValues[$fieldName] = $useraccount->{$fieldName};
                }


                $firstname = $fieldValues['first_name'];
                $middlename = $fieldValues['middle_name'];
                $surname = $fieldValues['surname'];
                $phonenumber = $fieldValues['phone_number'];
            }
            $customername = $firstname . ' ' . $middlename . ' ' . $surname;


            $randomCode = \Str::random(8);
            $myid = \Str::random(30);
            //customername,xustomerphone,withdrawamount
            $transaction = new AccountsTransactions();
            $transaction->__id__ = $myid;
            $transaction->account_number =  $theaccount_number;
            $transaction->account_type = $thisaccountnumber->account_type;
            $transaction->created_at = $mydatey;
            $transaction->transaction_id = $randomCode;
            $transaction->phone_number = $phonenumber;
            $transaction->det_rep_name_of_transaction = $customername;
            $transaction->amount = $commissionvalue;
            // $transaction->agentname = \Auth::user()->name;
            $transaction->name_of_transaction = 'Commission';
            $transaction->users = \Auth::user()->created_by_user;
            $transaction->is_shown = 1;
            $transaction->is_loan = 0;
            $transaction->row_version = 2;
            $transaction->description = "Monthly Commission";
            $transaction->comp_id = \Auth::user()->comp_id;



            $transactionsaved = $transaction->save();
            $insertedId = $transaction->id;

            if ($transactionsaved) {
                $currentbalance = $this->getaccountbalance2($theaccount_number);
                $thisaccountnumber->balance = $currentbalance;

                UserAccountNumbers::where('account_number',  $thisaccountnumber->account_number)->where('comp_id', \Auth::user()->comp_id)
                    ->update([
                        'balance' => $currentbalance
                    ]);

                AccountsTransactions::where('id', $insertedId)
                    ->update([
                        'balance' => $currentbalance
                    ]);


                $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                    ->where('sms_active', 1)
                    ->where('sms_credit', '>', 0)
                    ->exists();
                //  $withdrawalmsg = AccountsTransactions::where('id',  $request->transactionid)->value('paid_withdrawal_msg');

                $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');


                $themessage = 'Monthly Commission of GHS ' . $commissionvalue . ' charged on account: ' . $theaccount_number . ', ' . $customername . ', ' . $thisaccountnumber->account_type . ', ' . ' on '  . $mydatey . ' , Balance: GHS ' .   $currentbalance;



                if ($is_sms_enabled) {
                    $credittrans = $this->company_sms_transaction('sub', 1);
                    if ($credittrans) {
                        try {
                            //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                            $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                            $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $phonenumber);
                            if ($mymessageresponse) {

                                // return  response()->json(['balance' =>  $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                            } else {
                                // return  response()->json(['balance' => $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            //  return $th->getMessage();
                        }
                    }
                } else {
                    // return  response()->json(['balance' => $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                }
            }

            // Add the modified row to the new array
            $modifiedAccountNumbers[] = $thisaccountnumber;
            usleep(400000); // 500,000 microseconds = 0.5 seconds
        }


        return response()->json(['accountnumbers' => $modifiedAccountNumbers]);
    }

    public function paywithdrawalcustomer(Request $request)
    {

        // Assuming you have the $rowid variable

        $accounttypeid = '';
        $commissiontype = '';
        $commissionvalue = '';
        $balance = UserAccountNumbers::where('account_number', $request->accountnumber)->value('balance');

        $systemaccounts = SavingsAccounts::where('account_name', $request->accounttype)->where('comp_id', \Auth::user()->comp_id)->first();



        if ($systemaccounts) {
            // Retrieve values directly without using getFillable()
            $accounttypeid = $systemaccounts->account_type;
            $commissiontype = $systemaccounts->if_commission_charge_type;
            $commissionvalue = $systemaccounts->withdrawal_commission;
        } else {
            // Handle the case where the record is not found
        }


        $mainaccountnumber = UserAccountNumbers::where('account_number', $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)->first();


        if ($mainaccountnumber) {
            $mainaccountnumber = $mainaccountnumber->primary_account_number;
        } else {
            // Handle the case where the record is not found
        }

        $useraccount = Accounts::where('account_number', $mainaccountnumber)->where('comp_id', \Auth::user()->comp_id)->first();

        $accountnumber = "";
        $firstname = "";
        $middlename = "";
        $surname = "";
        $phonenumber = "";

        if ($useraccount) {

            $fieldNames = $useraccount->getFillable();
            $fieldValues = [];

            foreach ($fieldNames as $fieldName) {
                $fieldValues[$fieldName] = $useraccount->{$fieldName};
            }


            $firstname = $fieldValues['first_name'];
            $middlename = $fieldValues['middle_name'];
            $surname = $fieldValues['surname'];
            $phonenumber = $fieldValues['phone_number'];

            //now check whether the account has any commission to it and initiate it.
            //1. first check if the account is regular susu and has a cycle. 

            //2. if the account is susu cycle and is complete and not "withdrawn" or "closed" 
            $account_exist = SusuCycles::where('account_number', $request->accountnumber)->where('is_complete', 1)->where('cycle_closed', 0)->where('comp_id', \Auth::user()->comp_id)->first();

            $mydatey = date("Y-m-d H:i:s");
            //saving initial deposit


            if ($account_exist) {
                // This is a susu account so perform the susu deductions.
                ///3. now check the commissiontype.
                //4. if commissiontype is "First Deposit" calculate and take the first deposit as commission charged to the account .
                //commissiontype values: 1 == FirstDeposit, 2 == On Each Withdrawall, 3 == Monthly
                if ($commissiontype == 1) {
                    //1 == FirstDeposit
                    //Preparing and taking the first Deposit
                    //Get the rate of the susu and create a commission transaction and making a description called first deposit with the susu rate.

                    $susurate = SusuCycles::where('account_number', $request->accountnumber)
                        ->where('is_complete', 1)
                        ->where('cycle_closed', 0)
                        ->where('comp_id', \Auth::user()->comp_id)
                        ->first();

                    if ($susurate) {
                        $susurate = $susurate->value('cycle_rate');
                    } else {
                        // Handle the case where no matching record is found
                        $susurate = null; // or provide a default value
                    }

                    $susubalance = SusuCycles::where('account_number', $request->accountnumber)
                        ->where('is_complete', 1)
                        ->where('cycle_closed', 0)
                        ->where('comp_id', \Auth::user()->comp_id)
                        ->first();

                    if ($susubalance) {
                        $susubalance = $susubalance->value('total_paid');
                    } else {
                        // Handle the case where no matching record is found
                        $susubalance = null; // or provide a default value
                    }


                    $newbalance = $request->withdrawamount  - $susurate;
                    $after_withdrawbalance =  $newbalance - $newbalance;

                    if ($newbalance < 0) {
                        return "ERROR: Withdrawal Amount is greater than balance after first deposit commission";
                    } else {

                        //COMMISSION DEDUCTIONS
                        //=============================
                        //now create a transaction called commission with the susu rate charged to the account.
                        $randomCode = \Str::random(8);
                        $myid = \Str::random(30);
                        //customername,xustomerphone,withdrawamount
                        $transaction = new AccountsTransactions();
                        $transaction->__id__ = $myid;
                        $transaction->account_number = $request->accountnumber;
                        $transaction->account_type = $request->accounttype;
                        $transaction->created_at = $mydatey;
                        $transaction->transaction_id = $randomCode;
                        $transaction->phone_number = $request->xustomerphone;
                        $transaction->det_rep_name_of_transaction = $request->customername;
                        $transaction->amount = $susurate;
                        $transaction->agentname = \Auth::user()->name;
                        $transaction->name_of_transaction = 'Commission';
                        $transaction->users = \Auth::user()->created_by_user;
                        $transaction->is_shown = 1;
                        $transaction->is_loan = 0;
                        $transaction->row_version = 2;
                        $transaction->description = "Withdrawal Commission on First Deposit";
                        $transaction->comp_id = \Auth::user()->comp_id;



                        $transaction->save();


                        $created_at = Carbon::now();

                        AccountsTransactions::where('id', $request->transactionid)
                            ->update([
                                'paid_by' => \Auth::user()->name,
                                'name_of_transaction' => 'Withdraw',
                                'is_paid' => 1,
                                'balance' => $after_withdrawbalance,
                                'amount' => $newbalance,
                                'created_at' => $created_at
                            ]);

                        UserAccountNumbers::where('account_number',  $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)
                            ->update([
                                'balance' => $after_withdrawbalance
                            ]);

                        SusuCycles::where('account_number',  $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)
                            ->update([
                                'balance' => $after_withdrawbalance
                            ]);




                        $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                            ->where('sms_active', 1)
                            ->where('sms_credit', '>', 0)
                            ->exists();
                        //  $withdrawalmsg = AccountsTransactions::where('id',  $request->transactionid)->value('paid_withdrawal_msg');

                        $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                        $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');
                        $agent_phone = \Auth::user()->phone;
                        $agent_name = \Auth::user()->name;

                        $themessage = 'Withdrawal of GHS ' . $newbalance . ' from ' . $request->accountnumber . ', ' . $request->accounttype . ' ,' . $request->customername . ' on ' . $mydatey . ', Commission:' . $susurate . ' , Balance: GHS ' .   $after_withdrawbalance;

                        $themessage2 = '<span>' . $company_name . '</span><hr/>Withdrawal of <strong>GHS ' .  number_format($request->customerdeposit, 2) . '</strong> from ' . $request->accountnumber . ', ' . $request->accounttype . ', ' . $request->customername . ' <br/>on ' . $mydatey . ' <br/>Commission: ' . $susurate . ' ,<br/> Balance: <strong>GHS ' .   $after_withdrawbalance . '</strong><hr/><span style="font-size:10;">For Enquiries Call:' . $company_phone . '</span>' . '<hr/><span style="font-size:10;">Agent: ' . $agent_name . ' </span>';


                        if ($is_sms_enabled) {
                            $credittrans = $this->company_sms_transaction('sub', 1);
                            if ($credittrans) {
                                try {
                                    //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                                    $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                                    $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->phonenumber);
                                    if ($mymessageresponse) {

                                        return  response()->json(['balance' =>  $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    } else {
                                        return  response()->json(['balance' => $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    }
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    return $th->getMessage();
                                }
                            }
                        } else {
                            return  response()->json(['balance' => $after_withdrawbalance, 'message' => $themessage2, 'message2' => $themessage]);
                        }
                    }
                }
            } else {

                if ($commissiontype == 2 and $commissionvalue > 0) {
                    // 2 == Commission On Each Withdrawal
                    $randomCode = \Str::random(8);
                    $myid = \Str::random(30);
                    $username1 = \Auth::user()->name;


                    //now create a transaction called commission with the susu rate charged to the account.
                    $transaction = new AccountsTransactions();
                    $transaction->__id__ = $myid;
                    $transaction->account_number = $request->accountnumber;
                    $transaction->account_type = $request->accounttype;
                    $transaction->created_at = $mydatey;
                    $transaction->transaction_id = $randomCode;
                    $transaction->phone_number = $phonenumber;
                    $transaction->det_rep_name_of_transaction =  $firstname . " " . $middlename . " " . $surname;

                    //calculating the commissionn
                    //first check the commission percentage value
                    $calculatedcommission = $this->calculatePercentage($request->withdrawamount, $commissionvalue);
                    $newbalance = $balance - $calculatedcommission;

                    $transaction->amount = $calculatedcommission;
                    $transaction->agentname = \Auth::user()->name;
                    $transaction->name_of_transaction = 'Commission';
                    $transaction->users = \Auth::user()->created_by_user;
                    $transaction->is_shown = 1;
                    $transaction->is_loan = 0;
                    $transaction->row_version = 2;
                    $transaction->description = "Withdrawal Commission";
                    $transaction->balance = $newbalance;
                    $transaction->comp_id = \Auth::user()->comp_id;
                    $transaction->save();

                    $currentbalance = $this->getaccountbalance2($request->accountnumber);
                    $currentbalance = $currentbalance - $request->withdrawamount;


                    UserAccountNumbers::where('account_number',  $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)
                        ->update([
                            'balance' => $currentbalance
                        ]);


                    $created_at = Carbon::now();
                    AccountsTransactions::where('id',  $request->transactionid)->where('comp_id', \Auth::user()->comp_id)->update([
                        'paid_by' => $username1,
                        'name_of_transaction' => 'Withdraw',
                        'is_paid' => 1,
                        'balance' => $currentbalance,
                        'created_at' => $created_at
                    ]);




                    $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                        ->where('sms_active', 1)
                        ->where('sms_credit', '>', 0)
                        ->exists();
                    $withdrawalmsg = AccountsTransactions::where('id',  $request->transactionid)->value('paid_withdrawal_msg');

                    $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                    $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');
                    $agent_phone = \Auth::user()->phone;
                    $agent_name = \Auth::user()->name;

                    $themessage = 'Withdrawal of GHS ' . $request->withdrawamount . ' from ' . $request->accountnumber . ', ' . $request->accounttype . ' ,' . $request->customername . ' on ' . $mydatey . ', Charge:' . ROUND($calculatedcommission, 2) . ' ,Balance: GHS ' .   $currentbalance;

                    $themessage2 = '<span>' . $company_name . '</span><hr/>Withdrawal of <strong>GHS ' .  number_format($request->withdrawamount, 2) . '</strong> from ' . $request->accountnumber . ', ' . $request->accounttype . ', ' . $request->customername . ' <br/>on ' . $mydatey . ' <br/>Charge: ' . ROUND($calculatedcommission, 2) . ' , <br/>Balance: <strong>GHS ' .  number_format($currentbalance, 2) . '</strong><hr/><span style="font-size:10;">For Enquiries Call:' . $company_phone . '</span>' . '<hr/><span style="font-size:10;">Agent: ' . $agent_name . ' </span>';


                    if ($is_sms_enabled) {
                        $credittrans = $this->company_sms_transaction('sub', 1);
                        if ($credittrans) {
                            try {
                                //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                                $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                                $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->xustomerphone);
                                if ($mymessageresponse) {
                                    return  response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                } else {
                                    return  response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                return $th->getMessage();
                            }
                        }
                    } else {
                        return response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                    }
                }

                if ($commissiontype == 3) {
                    // 3 == Monthly
                    //Leave for cron job
                    // 2 == Commission On Each Withdrawal
                    $randomCode = \Str::random(8);
                    $myid = \Str::random(30);
                    $username1 = \Auth::user()->name;


                    //now create a transaction called commission with the susu rate charged to the account.


                    //calculating the commissionn
                    //first check the commission percentage value
                    $calculatedcommission = $this->calculatePercentage($request->withdrawamount, $commissionvalue);
                    $newbalance = $balance - $calculatedcommission;


                    $currentbalance = $this->getaccountbalance2($request->accountnumber);
                    $currentbalance = $currentbalance - $request->withdrawamount;


                    UserAccountNumbers::where('account_number',  $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)
                        ->update([
                            'balance' => $currentbalance
                        ]);


                    $created_at = Carbon::now();
                    AccountsTransactions::where('id',  $request->transactionid)->where('comp_id', \Auth::user()->comp_id)->update([
                        'paid_by' => $username1,
                        'name_of_transaction' => 'Withdraw',
                        'is_paid' => 1,
                        'balance' => $currentbalance,
                        'created_at' =>  $created_at
                    ]);


                    $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                        ->where('sms_active', 1)
                        ->where('sms_credit', '>', 0)
                        ->exists();
                    $withdrawalmsg = AccountsTransactions::where('id',  $request->transactionid)->value('paid_withdrawal_msg');

                    $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                    $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');
                    $agent_phone = \Auth::user()->phone;
                    $agent_name = \Auth::user()->name;

                    $themessage = 'Withdrawal of GHS ' . $request->withdrawamount . ' from ' . $request->accountnumber . ', ' . $request->accounttype . ' ,' . $request->customername . ' on ' . $mydatey . ', Charge:' . ROUND($calculatedcommission, 2) . ' ,Balance: GHS ' .   $currentbalance;

                    $themessage2 = '<span>' . $company_name . '</span><hr/>Withdrawal of <strong>GHS ' .  number_format($request->withdrawamount, 2) . '</strong> from ' . $request->accountnumber . ', ' . $request->accounttype . ', ' . $request->customername . ' <br/>on ' . $mydatey . ' <br/>Charge: ' . ROUND($calculatedcommission, 2) . ' , <br/>Balance: <strong>GHS ' .  number_format($currentbalance, 2) . '</strong><hr/><span style="font-size:10;">For Enquiries Call:' . $company_phone . '</span>' . '<hr/><span style="font-size:10;">Agent: ' . $agent_name . ' </span>';


                    if ($is_sms_enabled) {
                        $credittrans = $this->company_sms_transaction('sub', 1);
                        if ($credittrans) {
                            try {
                                //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                                $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                                $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->xustomerphone);
                                if ($mymessageresponse) {
                                    return  response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                } else {
                                    return  response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                return $th->getMessage();
                            }
                        }
                    } else {
                        return response()->json(['balance' => $currentbalance, 'message' => $themessage2, 'message2' => $themessage]);
                    }
                }
            }
        } else {
        }
    }

    public function completesusu(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            SusuCycles::where('account_number',  $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)
                ->update([
                    'is_complete' => 1,
                ]);
            return "True";
        } else {
            return response()->json(['error', 'You are not allowed to perform this action.']);
        }
    }

    public function sendconfirmationcode(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {

            $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                ->where('sms_active', 1)
                ->where('sms_credit', '>', 0)
                ->exists();

            if ($is_sms_enabled) {
                $credittrans = $this->company_sms_transaction('sub', 1);
                if ($credittrans) {
                    try {
                        //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                        $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');
                        $themessage = 'Confirmation Code:' . $request->code;

                        $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->customernumber);
                        if ($mymessageresponse) {
                            return 'sent';
                        } else {
                            return  'not sent';
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        return $th->getMessage();
                    }
                }
            } else {
                return 'sms not enabled';
            }
        } else {
            return 'You are  not allowed to perform this action';
        }
    }



    public function calculatePercentage($amount, $percentage)
    {

        $percentageAmount = ($percentage / 100) * $amount;

        // Return the result
        return $percentageAmount;
    }

    public function withdrawalrequests_approved()
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // $agentnames = User::orderBy('name', 'ASC')->where('type','!=','Super Admin')->where('type','!=','owner')->get();
            // $unapprovedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->count();
            // $approvedcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',1)->where('is_paid',0)->count();
            // $paidcounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->count();

            // $accounts = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdrawal Request')->where('withdrawrequest_approved',0)->paginate(10);
            $accountsapproved = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction', 'Withdrawal Request')->where('withdrawrequest_approved', 1)->where('is_paid', 0)->where('comp_id', \Auth::user()->comp_id)->paginate(10);
            // $accountspaid = AccountsTransactions::orderBy('id', 'DESC')->where('name_of_transaction','Withdraw')->where('withdrawrequest_approved',1)->where('is_paid',1)->paginate(10);

            $result = new \stdClass();
            $result->current_page = 1;
            $result->data = $accountsapproved;
            // $result->accountsapproved = $accountsapproved;
            //   $result->accountspaid = $accountspaid;
            //   $result->unapprovedcounts = $unapprovedcounts;
            // $result->approvedcounts = $approvedcounts;
            // $result->paidcounts = $paidcounts;
            $result->first_page_url = request()->fullUrl();
            $result->from = null;
            $result->last_page = 1;
            $result->last_page_url = request()->fullUrl();
            $result->next_page_url = null;
            $result->path = request()->url();
            $result->per_page = 10;
            $result->prev_page_url = null;
            $result->to = null;
            $result->total = $accounts->count();
            return response()->json($result);
        } else {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }


        public function deposittransaction_susu(Request $request)
        {
            //this is literally 'Manage User Register'
            if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
    
                // Check Account Dormancy (Sprint 8.5)
                $account = UserAccountNumbers::where('account_number', $request->accountnumber)
                    ->where('comp_id', \Auth::user()->comp_id)
                    ->first();
    
                if ($account && $account->account_status == 'dormant' && !$this->isManagement()) {
                    return response()->json(['success' => false, 'message' => 'Account is dormant. Please contact management for re-activation.'], 403);
                }
    
                $is_transaction_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                    ->where('transactional_credit', '>', 0)
                    ->exists();
    
                if ($is_transaction_enabled) {
    
                    $credittrans2 = $this->company_credit_transaction('sub', 1);
                    if ($credittrans2) {
    
                        $randomCode = \Str::random(8);
                        $myid = \Str::random(30);
    
                        $accounttype = UserAccountNumbers::where('account_number', $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)->value('account_type');        
    
                        $mydatey = date("Y-m-d H:i:s");
                        $firstname = $request->firstname;
                        $middlename = $request->middlename;
                        $surname = $request->surname;
                        $customerName = $firstname . ' ' . $middlename . ' ' . $surname;
    
                        $transaction = new AccountsTransactions();
                        $transaction->__id__ = $myid;
                        $transaction->account_number = $request->accountnumber;
                        $transaction->account_type = $accounttype;
                        $transaction->created_at = $mydatey;
                        $transaction->transaction_id = $randomCode;
                        $transaction->phone_number = $request->phonenumber;
                        $transaction->det_rep_name_of_transaction =  $customerName;
                        $transaction->amount = $request->customerdeposit;
                        $transaction->agentname = \Auth::user()->name;
                        $transaction->name_of_transaction = 'Deposit';
                        $transaction->users = \Auth::user()->created_by_user;
                        $transaction->is_shown = 1;
                        $transaction->is_loan = 0;
                        $transaction->row_version = 2;
                        $transaction->comp_id = \Auth::user()->comp_id;
    
                        $totaldeposits = AccountsTransactions::where('account_number', $request->accountnumber)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
                        $totalcommission = AccountsTransactions::where('account_number', $request->accountnumber)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
                        $totalwithdrawals = AccountsTransactions::where('account_number', $request->accountnumber)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
                        $totalrefunds = AccountsTransactions::where('account_number', $request->accountnumber)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
    
                        $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3)  + $request->customerdeposit;
                        $transaction->balance = $totalbalance;
    
                        $themessage = 'GHS ' . number_format($request->customerdeposit, 2) . ' Deposited to ' . $request->accountnumber . ', ' . $accounttype . ' ,' . $customerName . ' on ' . $mydatey . ' Balance: GHS ' .  $totalbalance;
    
    
                        if ($transaction->save()) {
    
                            $account                       = new UserAccountNumbers();
                            $accountnumberbalance =  $totalbalance + $request->customerdeposit;
    
                            $account::where('account_number', $request->accountnumber)
                                ->where('comp_id', \Auth::user()->comp_id)
                                ->update([
                                    'balance' => $accountnumberbalance,
                                    'last_transaction_date' => now() // Update last activity date
                                ]);      
    
                            $susucycle  = new SusuCycles;
    
                            $susucycle::where('account_number', $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)->update(['balance' => $accountnumberbalance, 'total_paid' => $accountnumberbalance]);
                        $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                        $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');

                        $agent_phone = \Auth::user()->phone;
                        $agent_name = \Auth::user()->name;



                        $themessage2 = '<span>' . $company_name . '</span><hr/><strong>GHS ' . number_format($request->customerdeposit, 2) . '</strong> Deposited to ' . $request->accountnumber . ', ' . $accounttype . ', ' . $customerName . ' <br/>on ' . $mydatey . ' <br/><strong>Balance: GHS ' .  $totalbalance . '</strong><hr/><span style="font-size:10;">For Enquiries Call:' . $company_phone . '</span>' . '<hr/><span style="font-size:10;">Agent: ' . $agent_name . ' </span>';


                        $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                            ->where('sms_active', 1)
                            ->where('sms_credit', '>', 0)
                            ->exists();

                        if ($is_sms_enabled) {
                            $credittrans = $this->company_sms_transaction('sub', 1);
                            if ($credittrans) {
                                try {
                                    //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                                    $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                                    $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->phonenumber);
                                    if ($mymessageresponse) {
                                        return  response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    } else {
                                        return  response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    }
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    return $th->getMessage();
                                }
                            }
                        } else {
                            return response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                        }




                        return $totalbalance;
                    } else {
                        return 'ERROR';
                    }
                }
            } else {
                return "No Credit Balance for Transaction.";
            }
        } else {
            return 403;
        }
    }

    public function deposittransaction(Request $request)
    {
        //this is literally 'Manage User Register'
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            
            // Check Account Dormancy (Sprint 8.5)
            $account = UserAccountNumbers::where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->first();

            if ($account && $account->account_status == 'dormant' && !$this->isManagement()) {
                return response()->json(['success' => false, 'message' => 'Account is dormant. Please contact management for re-activation.'], 403);
            }

            $is_transaction_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                ->where('transactional_credit', '>', 0)
                ->exists();

            if ($is_transaction_enabled) {
                $credittrans2 = $this->company_credit_transaction('sub', 1);
                if ($credittrans2) {


                    $randomCode = \Str::random(8);
                    $myid = \Str::random(30);

                    $accounttype = UserAccountNumbers::where('account_number', $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)->value('account_type');
                    $mainaccountnumber = UserAccountNumbers::where('account_number', $request->accountnumber)->where('comp_id', \Auth::user()->comp_id)->value('primary_account_number');
                    $mydatey = date("Y-m-d H:i:s");
                    $firstname = $request->firstname;
                    $middlename = $request->middlename;
                    $surname = $request->surname;
                    $customerName = $firstname . ' ' . $middlename . ' ' . $surname;

                    $transaction = new AccountsTransactions();
                    $transaction->__id__ = $myid;
                    $transaction->account_number = $request->accountnumber;
                    $transaction->account_type = $accounttype;
                    $transaction->created_at = $mydatey;
                    $transaction->transaction_id = $randomCode;
                    $transaction->phone_number = $request->phonenumber;
                    $transaction->det_rep_name_of_transaction =  $customerName;
                    $transaction->amount = $request->customerdeposit;
                    $transaction->agentname = \Auth::user()->name;
                    $transaction->name_of_transaction = 'Deposit';
                    $transaction->users = \Auth::user()->created_by_user;
                    $transaction->is_shown = 1;
                    $transaction->is_loan = 0;
                    $transaction->row_version = 2;
                    $transaction->comp_id = \Auth::user()->comp_id;

                    // --- OPTIMIZATION: Replace 4 SUM queries with 1 query to get last balance ---
                    $lastTransaction = AccountsTransactions::where('account_number', $request->accountnumber)
                        ->where('comp_id', \Auth::user()->comp_id)
                        ->orderBy('id', 'desc')
                        ->first();

                    $previousBalance = $lastTransaction ? $lastTransaction->balance : 0;
                    $totalbalance = ROUND($previousBalance + $request->customerdeposit, 2);
                    $transaction->balance = $totalbalance;
                    // --- END OPTIMIZATION ---

                    $themessage = 'GHS ' . number_format($request->customerdeposit, 2) . ' Deposited to ' . $request->accountnumber . ' ,' . $accounttype . ', ' . $customerName . ' on ' . $mydatey . ' Balance: GHS ' .  $totalbalance;

                    $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                    $company_phone = Companyinfo::where('id', \Auth::user()->comp_id)->value('phone');

                    $agent_phone = \Auth::user()->phone;
                    $agent_name = \Auth::user()->name;



                    $themessage2 = '<span>' . $company_name . '</span><hr/><strong>GHS ' . number_format($request->customerdeposit, 2) . '</strong> Deposited to ' . $request->accountnumber . ', ' . $accounttype . ', ' . $customerName . ' <br/>on ' . $mydatey . ' <br/><strong>Balance: GHS ' .  $totalbalance . '</strong><hr/><span style="font-size:10;">For Enquiries Call:' . $company_phone . '</span>' . '<hr/><span style="font-size:10;">Agent: ' . $agent_name . ' </span>';

                    if ($transaction->save()) {

                 
                        UserAccountNumbers::where('account_number', $request->accountnumber)
                            ->where('comp_id', \Auth::user()->comp_id)
                            ->update([
                                'balance' => $totalbalance,
                                'last_transaction_date' => now() // Update last activity date
                            ]);

                        $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                            ->where('sms_active', 1)
                            ->where('sms_credit', '>', 0)
                            ->exists();

                        if ($is_sms_enabled) {
                            $credittrans = $this->company_sms_transaction('sub', 1);
                            if ($credittrans) {
                                try {
                                    //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent


                                    $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                                    $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->phonenumber);
                                    if ($mymessageresponse) {
                                        return  response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    } else {
                                        return  response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                                    }
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    return $th->getMessage();
                                }
                            }
                        } else {
                            return response()->json(['balance' => $totalbalance, 'message' => $themessage2, 'message2' => $themessage]);
                        }
                    } else {
                        return 'ERROR';
                    }
                }
            } else {
                return "No Credit Balance for Transaction.";
            }
        } else {
            return 403;
        }
    }

    public function registeruserac(Request $request)
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            $mydatey = date("Y-m-d H:i:s");

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
            $account['user'] = \Auth::user()->created_by_user;
            $account['customer_picture'] = $request->customer_picture;
            $account['is_dataimage'] = 1;
            $account['account_types'] = $request->selecteddefaultaccount;
            $account['comp_id'] = \Auth::user()->comp_id;


            $useracountnumbs  = new UserAccountNumbers;

            $useracountnumbs['account_number']  = $request->account_number;
            $useracountnumbs['balance']  = $request->initialdeposit;
            $useracountnumbs['account_type']  = $request->selecteddefaultaccount;
            $useracountnumbs['__id__']  = $request->__id__;
            $useracountnumbs['primary_account_number']  = $request->account_number;
            $useracountnumbs['created_by_user']  = \Auth::user()->created_by_user;
            $useracountnumbs['comp_id'] = \Auth::user()->comp_id;


            $account->save();
            $useracountnumbs->save();

            if ($request->mainaccounttypeselected === "1") {
                $susucycle  = new SusuCycles;

                $susucycle['date_start']  =  $mydatey;
                $susucycle['cycle_rate']  =  $request->susurate;
                $susucycle['account_number']  =  $request->account_number;
                $susucycle['total_paid']  =  $request->initialdeposit;
                $susucycle['is_complete']  =  0;
                $susucycle['cycle_closed']  =  0;
                $susucycle['balance']  =  $request->initialdeposit;
                $susucycle['comp_id'] = \Auth::user()->comp_id;
                $susucycle->save();
            }

            //saving initial deposit
            $randomCode = \Str::random(8);
            $myid = \Str::random(30);



            $firstname = $request->first_name;
            $middlename = $request->middle_name;
            $surname = $request->surname;
            $customerName = $firstname . ' ' . $middlename . ' ' . $surname;

            $transaction = new AccountsTransactions();
            $transaction->__id__ = $myid;
            $transaction->account_number = $request->account_number;
            $transaction->account_type = $request->selecteddefaultaccount;
            $transaction->created_at = $mydatey;
            $transaction->transaction_id = $randomCode;
            $transaction->phone_number = $request->phone_number;
            $transaction->det_rep_name_of_transaction =  $customerName;
            $transaction->amount = $request->initialdeposit;
            $transaction->agentname = \Auth::user()->name;
            $transaction->name_of_transaction = 'Deposit';
            $transaction->users = \Auth::user()->created_by_user;
            $transaction->is_shown = 1;
            $transaction->is_loan = 0;
            $transaction->row_version = 2;
            $transaction->balance = $request->initialdeposit;
            $transaction->comp_id = \Auth::user()->comp_id;

            if ($transaction->save()) {

               
                $company_name = Companyinfo::where('id', \Auth::user()->comp_id)->value('name');
                
                $is_sms_enabled = Companyinfo::where('id', \Auth::user()->comp_id)
                    ->where('sms_active', 1)
                    ->where('sms_credit', '>', 0)
                    ->exists();

                if ($is_sms_enabled) {
                    $credittrans = $this->company_sms_transaction('sub', 1);
                    if ($credittrans) {
                        try {
                            //$theusername,$thepass,$thesenderid,$themessage,$thenumbersent

                            $themessage = 'Welcome, ' .$firstname .' '.$surname . ' to '  . $company_name . '. Your new balance is GHS ' . number_format($request->initialdeposit, 2) . '. Account No: ' . $request->account_number . ' , Account Type: ' . $request->selecteddefaultaccount;

                            $thesmsid = Companyinfo::where('id', \Auth::user()->comp_id)->value('sms_sender_id');

                            $mymessageresponse = $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $request->phone_number);
                            if ($mymessageresponse) {
                                return 'saved';
                            
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            return $th->getMessage();
                        }
                    }
                } else {
                   
                }




               
            } else {
                return 'ERROR';
            }

            
        } else {
            return 'error';
        }
    }

//

    public function registeruserac_update(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            //$customerinputs = $request->all();
            /*  $validator = \Validator::make(
               $request->all(),
               [
                   'first_name' => 'required|max:120',
                   'surname' => 'required|max:120',
                   'phone_number' => 'required|min:10',
                   'email' => 'required|unique:nobs_registration',
                   'date_of_birth2' => 'required', 
                   'id_number' => 'required|min:10',
                   'next_of_kin' => 'required',
                   'next_of_kin_id_number' => 'required',
                   'next_of_kin_phone_number' => 'required|min:10',
                   'occupation' => 'required',
                   'residential_address' => 'required'

               ]
           ); */


            /*  if ($request->confcode == $request->confirmationcode) {
           } else {
               $messages =  'Wrong Phone Code';

               return redirect()->back()->with('error', $messages);
           } */
            /*   if ($validator->fails()) {
               $messages = $validator->getMessageBag();

               return redirect()->back()->withInput()->with('error', $messages->first());
           } else { */

            $account                       = new Accounts();

            Accounts::where('id', $request->id)->update(['account_number' => $request->account_number, 'first_name' => $request->first_name, 'middle_name' => $request->middle_name, 'surname' => $request->surname, 'phone_number' => $request->phone_number, 'date_of_birth2' => $request->date_of_birth2, 'email' => $request->email, 'id_number' => $request->id_number, 'id_type' => $request->id_type, 'nationality' => $request->nationality, 'next_of_kin' => $request->next_of_kin, 'next_of_kin_id_number' => $request->next_of_kin_id_number, 'next_of_kin_phone_number' => $request->next_of_kin_phone_number, 'occupation' => $request->occupation, 'postal_address' => $request->postal_address, 'residential_address' => $request->residential_address, 'sec_phone_number' => $request->sec_phone_number, 'user' => \Auth::user()->created_by_user, 'comp_id' => \Auth::user()->comp_id]);

            return 'saved';

            // $acount['user']    = $request->user; //must be created on accounts.create


            // $acount['created_by']         = \Auth::user()->creatorId();
            // $account->save();
            // $useracountnumbs->save();

            //$this->sendmessage($request->phone_number,'Dear, ' . $request->first_name . ' '. $request->surname . ', Your GCI susu account has been created. \n Account Number: ' . $request->account_number );



            // return redirect()->back()->with('success', __('Customer Successfully Registered.'));
            // }
        } else {
            return 'You have no access to this section';
        }
    }




    public function updatesavingsaccount(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {

            $account                       = new SavingsAccounts();
            $account::where('id', $request->id)->update(['minimum_balance' => $request->minimumbalance, 'account_name' => $request->accountname, 'withdrawal_commission' => $request->withdrawalcommission, 'account_type' => $request->accountTypeValue, 'if_commission_charge_type' => $request->commissionchargeType, 'if_cycle_value' => $request->noofdaystocomplete, 'agent_commission' => $request->agentcommission, 'comp_id' => \Auth::user()->comp_id]);


            return 'saved';
        } else {
            return 'error';
        }
    }

    public function deletesavingsaccount(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $account = new SavingsAccounts();
            $accountname_exist = AccountsTransactions::where('account_type', $request->accountname)->first();

            if ($accountname_exist) {
                return 'error: the account cant be deleted because it has related transactions';
            }

            $account::where('id', $request->id)->delete();

            return 'deleted';
        } else {
            return 'error';
        }
    }



    public function checkifsusuaccount(Request $request)
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            $account_exist = SusuCycles::where('account_number', $request->accountid)->where('comp_id', \Auth::user()->comp_id)->first();

            if ($account_exist) {
                return 'True';
            }

            return 'False';
        } else {
            return 'error';
        }
    }



    public function getsusuaccount(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $susuaccounts = SusuCycles::select('*')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('account_number', $request->accountnumber)->where('is_complete', 0)
                ->get()
                ->toArray();

            return response()->json($susuaccounts);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    // creating new account for the business which can be used for susu, business account or even loans.
    public function insertsavingsaccount(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin') {
            $account = new SavingsAccounts();
            $account->minimum_balance = $request->minimumbalance;
            $account->account_name = $request->accountname;
            $account->withdrawal_commission = $request->withdrawalcommission;
            $account->account_type = $request->accountTypeValue;
            $account->if_commission_charge_type = $request->commissionchargeType;
            $account->if_cycle_value = $request->noofdaystocomplete;
            $account->agent_commission = $request->agentcommission;
            $account->comp_id = \Auth::user()->comp_id;

            try {
                $account->save();
                return 'saved';
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if the error is due to a unique constraint violation
                if ($e->errorInfo[1] == 1062) {
                    return 'error: Account name already exists.';
                } else {
                    return 'error saving record';
                }
            }
        } else {
            return 'error';
        }
    }



    // creating new account for the business which can be used for susu, business account or even loans.
    public function insertcompanyinfo(Request $request)
    {
        $randomCode = \Str::random(8);

        $validator = \Validator::make(
            $request->all(),
            [

                'name' => 'required|max:120',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:accounts',
                'password' => 'required|min:6',
                'username' => 'required|max:120'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return  $messages->first();
        }


        $account = new CompanyInfo;
        $account['company_id'] = $request->company_id;
        $account['name'] = $request->name;
        $account['phone'] = $request->phone;
        $account['billing_address'] = $request->billing_address;
        $account['shipping_country'] = $request->shipping_country;
        $account['sms_credit'] = $request->sms_credit;
        $account['sms_active'] = $request->sms_active;
        $account['sms_api_url'] = $request->sms_api_url;
        $account['sms_sender_id'] = $request->sms_sender_id;
        $account['sms_daily_report'] = $request->sms_daily_report;
        $account['sms_report_phone_no'] = $request->sms_report_phone_no;
        $account['amount_in_cash'] = $request->amount_in_cash;
        $account['message_after_deposit'] = $request->message_after_deposit;
        $account['message_after_withdrawal'] = $request->message_after_withdrawal;
        $account['app_home_url'] = $request->app_home_url;
        $account['app_resource_url'] = $request->app_resource_url;
        $account['email'] = $request->company_email;
        $account['accountno_pretext'] = $request->accountnoprefix;




        try {
            $account->save();

            $accountID = $account->id;

            $user               = new User();
            $user['username']   = $request->username;
            $user['name']       = $request->name;
            $user['email']      = $request->email;
            $user['created_by_user'] =  $randomCode;
            $user['password']   = Hash::make($request->password);
            $user['type']       = 'Admin';
            $user['created_by'] = 2;
            $user['company_id'] = $request->company_id;
            $user['comp_id'] = $accountID;


            $user->save();

            //Creating the savings accounts default templates:

            $account = new SavingsAccounts();
            $account2 = new SavingsAccounts();
            $account3 = new SavingsAccounts();


            // Creating one for Regular Susu

            $account->minimum_balance = 0;
            $account->account_name = 'Regular Susu';
            $account->withdrawal_commission = 3.0;
            $account->account_type = 2;
            $account->if_commission_charge_type = 2; // 2 means charge commission on each withdrawal
            $account->if_cycle_value = 31;
            $account->agent_commission = 0;
            $account->comp_id =  $accountID;
            $account->save();

            // Creating one for Yearly Susu

            $account2->minimum_balance = 0;
            $account2->account_name = 'Yearly Susu';
            $account2->withdrawal_commission = 0;
            $account2->account_type = 1;
            $account2->if_commission_charge_type = 1; // 1 means charge commisson on first deposit
            $account2->agent_commission = 0;
            $account2->comp_id =  $accountID;
            $account2->save();

            // Creating one for Business Account

            $account3->minimum_balance = 30;
            $account3->account_name = 'Susu Business';
            $account3->withdrawal_commission = 0;
            $account3->account_type = 2;
            $account3->if_commission_charge_type = 3; // 2 means charge commission on each month
            $account3->if_cycle_value = 31;
            $account3->agent_commission = 0;
            $account3->comp_id =  $accountID;
            $account3->save();

            return 'saved';
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if the error is due to a unique constraint violation

            return $e->getMessage();
        }
    }


    public function updatecompanyinfo(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $request->userid,
                'phone' => 'required|unique:accounts,phone,' . $request->comp_id,
                'username' => 'required|max:120',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return $messages->first();
        }

        try {
            // Update the CompanyInfo (account) record
            CompanyInfo::where('id', $request->comp_id)->update([
                'name' => $request->name,
                'phone' => $request->company_phone,
                'billing_address' => $request->billing_address,
                'shipping_country' => $request->shipping_country,
                'sms_credit' => $request->sms_credit,
                'sms_active' => $request->sms_active,
                'sms_api_url' => $request->sms_api_url,
                'sms_sender_id' => $request->sms_sender_id,
                'sms_daily_report' => $request->sms_daily_report,
                'sms_report_phone_no' => $request->sms_report_phone_no,
                'amount_in_cash' => $request->amount_in_cash,
                'message_after_deposit' => $request->message_after_deposit,
                'message_after_withdrawal' => $request->message_after_withdrawal,
                'app_home_url' => $request->app_home_url,
                'app_resource_url' => $request->app_resource_url,
                'email' => $request->company_email,
               'accountno_pretext' => $request->accountnoprefix
            ]);

            if ($request->password == '') {
                // Update the User record
                User::where('id', $request->userid)->update([
                    'username' => $request->username,
                    'name' => $request->username,
                    'phone' => $request->phone,
                    'email' => $request->email
                ]);
            } else {
                // Update the User record
                User::where('id', $request->userid)->update([
                    'username' => $request->username,
                    'name' => $request->username,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            }


            return 'saved';
        } catch (\Illuminate\Database\QueryException $e) {
            // Check if the error is due to a unique constraint violation
            return $e->getMessage();
        }
    }




    function getaccountbalance2($accountsid)
    {


        $totaldeposits = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
        $totalcommission = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
        $totalwithdrawals = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
        $totalrefunds = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');

        $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);
        return $totalbalance;
    }




    public function getcommissionvalue(Request $request)
    {



        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            //this is literally 'Manage User Register'
            $accountsid = $request->accountid;
            $commissionvalue = 0;
            $minimumbalance = 0;


            $systemaccounts = SavingsAccounts::where('account_name', $request->selectedaccounttype)->where('comp_id', \Auth::user()->comp_id)->first();

            if ($systemaccounts) {
                // Retrieve values directly without using getFillable()
                $commissionvalue = $systemaccounts->withdrawal_commission;

                $minimumbalance = $systemaccounts->mininum_balance;
                if ($minimumbalance) {
                } else {
                    $minimumbalance = 0.00;
                }
            } else {
                // Handle the case where the record is not found
            }

            $availableamount = $this->calculatePercentage($request->customerbalance, $commissionvalue);
            $thebalance = $request->customerbalance - $availableamount;


            return response()->json(['commissionvalue' => $commissionvalue, 'minimumbalance' => $minimumbalance, 'availablebalance' => $thebalance, 'thecommissionvalue' => $availableamount]);
        } else {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }



    public function getaccountbalaceandcharges(Request $request)
    {
        //this is literally 'Manage User Register'
        $accountsid = $request->accountid;

        $commissionchargetype = '';


        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            $commissionchargetype = '';

            try {
                // Retrieve the account_type field (fixing the typo in the field name)
                $actype = UserAccountNumbers::where('account_number', $request->accountid)->value('account_type');

                // Retrieve system accounts based on the corrected account_type
                $systemaccounts = SavingsAccounts::where('account_name', $actype)
                    ->where('comp_id', \Auth::user()->comp_id)
                    ->first();

                if ($systemaccounts) {
                    // Retrieve the commission charge type from system accounts
                    $commissionchargetype = $systemaccounts->if_commission_charge_type;
                } else {
                    // Handle the case where the record is not found
                    // You might want to log a message or take appropriate action
                }
            } catch (\Throwable $th) {
                // Handle the exception apprzopriately
                // You might want to log the exception or provide feedback
                // Throw the exception again if you don't want to suppress it completely
                // throw $th;
            }

            $totaldeposits = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalcommission = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalwithdrawals = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalrefunds = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');

            $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);


            //this is literally 'Manage User Register'

            $commissionvalue = 0;
            $minimumbalance = 0;


            $systemaccounts = SavingsAccounts::where('account_name', $request->selectedaccounttype)->where('comp_id', \Auth::user()->comp_id)->first();

            if ($systemaccounts) {
                // Retrieve values directly without using getFillable()
                if ($commissionchargetype == 3) {
                    $commissionvalue = 0;
                } else {
                    $commissionvalue = $systemaccounts->withdrawal_commission;
                }


                $minimumbalance = $systemaccounts->minimum_balance;
                if ($minimumbalance) {
                } else {
                    $minimumbalance = 0.00;
                }
            } else {
                // Handle the case where the record is not found
            }

            $availableamount = $this->calculatePercentage($totalbalance, $commissionvalue);
            $thebalance = ($totalbalance - $availableamount);

            return response()->json(['totalbalance' => ROUND($totalbalance, 2), 'commissionvalue' => ROUND($commissionvalue, 2), 'minimumbalance' => ROUND($minimumbalance, 2), 'availablebalance' => ROUND($thebalance, 2), 'thecommissionvalue' => ROUND($availableamount, 2), 'commissionchargetype' => $commissionchargetype]);
        } else {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }




    public function getaccountbalance(Request $request)
    {
        //this is literally 'Manage User Register'
        $accountsid = $request->accountid;


        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {

            /* $mainaccountnumber = UserAccountNumbers::where('account_number',$accountsid)->pluck('primary_account_number');
       $accounttype = UserAccountNumbers::where('account_number',$accountsid)->pluck('account_type');

       $useraccountnumbers = UserAccountNumbers::where('primary_account_number',$accountsid)->get();

       $account = Accounts::where('account_number',$accountsid)->orderBy('id', 'DESC')->get();
       $accounts = AccountsTransactions::where('account_number',$accountsid)->orderBy('id', 'DESC')->where('row_version',2)->paginate(1);  */
            $totaldeposits = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalcommission = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalwithdrawals = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');
            $totalrefunds = AccountsTransactions::where('account_number', $accountsid)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', \Auth::user()->comp_id)->sum('amount');

            $totalbalance  = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);
            return response()->json($totalbalance);
        } else {
            return redirect()->back()->with('error', 'You are not allowed to view this page.');
        }
    }


    public function getuseraccountnumbers(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $accountnumbers = UserAccountNumbers::select('id', 'account_number', 'account_type', 'balance', 'account_status')
                ->where('comp_id', \Auth::user()->comp_id)
                ->where('primary_account_number', $request->accountnumber)
                ->get()
                ->toArray();

            // Create the new JSON object to push into the existing array
            // $newObject = [
            //     'id'=> 2456,
            //     'account_number' => $request->accountnumber,
            //     'account_type' => 'main account' // Set the desired account type
            // ];

            // array_push($accountnumbers, $newObject);
            // Check if the account number already exists in the array
            // $accountExists = false;
            // foreach ($accountnumbers as $account) {
            //     if ($account['account_number'] === $newObject['account_number']) {
            //         $accountExists = true;
            //         break;
            //     }
            // }

            // If the account number doesn't exist, push the new object
            /*  if (!$accountExists) {
            array_push($accountnumbers, $newObject);
        } */

            // Return the updated JSON data in the response
            return response()->json($accountnumbers);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }

    //addaccounttouser
    public function addaccounttouser(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            
            DB::beginTransaction();
            try {
                // Validation check
                if (!$request->accountnumber) {
                    throw new \Exception("Primary Account Number is missing from the request.");
                }

                // Retrieve existing JSON data as an array
                $mydatey = date("Y-m-d H:i:s");

                if ($request->mainaccounttypeselected === "1") {
                    $susucycle  = new SusuCycles;
                    $susucycle['date_start']  =  $mydatey;
                    $susucycle['cycle_rate']  =  $request->susurate;
                    $susucycle['account_number']  =  $request->newaccountnogenerated;
                    $susucycle['total_paid']  =  $request->initialdeposit;
                    $susucycle['is_complete']  =  0;
                    $susucycle['cycle_closed']  =  0;
                    $susucycle['cycle_value']  =   $request->daystocomplete;
                    $susucycle['comp_id'] = \Auth::user()->comp_id;
                    $susucycle['balance']  =  $request->initialdeposit;
                    $susucycle->save();
                }

                $useracountnumbs  = new UserAccountNumbers;
                $useracountnumbs->account_number  = $request->newaccountnogenerated;
                $useracountnumbs->account_type  = $request->selecteddefaultaccount;
                $useracountnumbs->__id__  =  \Str::random(20);
                $useracountnumbs->primary_account_number  = $request->accountnumber;
                $useracountnumbs->balance  = $request->initialdeposit;
                $useracountnumbs->created_by_user  = \Auth::user()->created_by_user;
                $useracountnumbs->comp_id = \Auth::user()->comp_id;
                // Ensure status is set
                $useracountnumbs->account_status = 'active'; 
                $useracountnumbs->save();

                //saving initial deposit
                $randomCode = \Str::random(8);
                $myid = \Str::random(30);

                $accounttype =  $request->selecteddefaultaccount;
                // $mainaccountnumber =  $request->accountnumber; // Unused variable

                $firstname = $request->firstname;
                $middlename = $request->middlename;
                $surname = $request->surname;
                $customerName = $firstname . ' ' . $middlename . ' ' . $surname;

                $transaction = new AccountsTransactions();
                $transaction->__id__ = $myid;
                $transaction->account_number = $request->newaccountnogenerated;
                $transaction->account_type = $accounttype;
                $transaction->created_at = $mydatey;
                $transaction->transaction_id = $randomCode;
                $transaction->phone_number = $request->phonenumber;
                $transaction->det_rep_name_of_transaction =  $customerName;
                $transaction->amount = $request->initialdeposit;
                $transaction->agentname = \Auth::user()->name;
                $transaction->name_of_transaction = 'Deposit';
                $transaction->users = \Auth::user()->created_by_user;
                $transaction->is_shown = 1;
                $transaction->is_loan = 0;
                $transaction->row_version = 2;
                $transaction['balance']  =  $request->initialdeposit;
                $transaction->comp_id = \Auth::user()->comp_id;
                $transaction->save();

                DB::commit();
                return 'account number added successfully';

            } catch (\Exception $e) {
                DB::rollBack();
                // Return detailed error for debugging
                $fullMessage = $e->getMessage();
                if ($e->getPrevious()) {
                    $fullMessage .= " >> Caused by: " . $e->getPrevious()->getMessage();
                }
                return 'ERROR: ' . $fullMessage;
            }

        } else {
            // Handle permission denied
            return 403;
        }
    }


     //addaccounttouser
     public function updatecustomer_accounttype(Request $request)
     {
         // Check user type for permission
         if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
             // Retrieve existing JSON data as an array
             $mydatey = date("Y-m-d H:i:s");
     
             // Update user account number
             $updateUserAccountNumbers = UserAccountNumbers::where('account_number', $request->accountnumber)->update([
                 'account_type' => $request->mainaccounttype
             ]);
     
             // Update accounts
             $updateAccounts = Accounts::where('account_number', $request->accountnumber)->update([
                 'account_types' => $request->mainaccounttype
             ]);
     
             if ($updateUserAccountNumbers !== false && $updateAccounts !== false) {
                 return 'Account updated successfully';
             } else {
                 $errorInfoUserAccountNumbers = implode(', ', DB::getPdo()->errorInfo());
                 $errorInfoAccounts = implode(', ', DB::getPdo()->errorInfo());
                 return "ERROR Saving. UserAccountNumbers: $errorInfoUserAccountNumbers, Accounts: $errorInfoAccounts";
             }
         } else {
             // Handle permission denied
             return 403;
         }
     }
     


    public function getusertransactions(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at', 'transaction_id','agentname')
                ->where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw', 'Commission']) // Select transactions where name_of_transaction is either "Deposit" or "Withdraw"
                ->orderBy('id', 'desc') // Order by 'id' in descending order (assuming 'id' represents the transaction's chronological order)
                ->take(10) // Limit the result to the last 50 transactions
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, y');
                $transaction['created_at'] = $formattedDate;
            }


            // Create the new JSON object to push into the existing array
            // $newObject = [
            //     'id'=> 2456,
            //     'account_number' => $request->accountnumber,
            //     'account_type' => 'main account' // Set the des  ired account type
            // ];

            // array_push($accountnumbers, $newObject);
            // Check if the account number already exists in the array
            // $accountExists = false;
            // foreach ($accountnumbers as $account) {
            //     if ($account['account_number'] === $newObject['account_number']) {
            //         $accountExists = true;
            //         break;
            //     }
            // }

            // If the account number doesn't exist, push the new object
            /*  if (!$accountExists) {
            array_push($accountnumbers, $newObject);
        } */

            // Return the updated JSON data in the response
            return response()->json($usertransactions);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function getprintedstatements(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at')
                ->where('account_number', $request->myuseracnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw', 'Reversal'])
                ->orderBy('id', 'DESC')
                ->take(10)
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y,');
                $transaction['created_at'] = $formattedDate;

                // Format amount to two decimal points
                $transaction['amount'] = number_format($transaction['amount'], 2);
                $transaction['balance'] = number_format($transaction['balance'], 2);
            }

            // Build the raw HTML string
            $html = '<div style="font-size:11px;">';
            foreach ($usertransactions as $transaction) {
                $html .= '<div><span>' . $transaction['created_at'] . '</span></div><div><span>';
                $html .= $transaction['name_of_transaction'] . ': ';
                $html .= $transaction['amount'] . '</span>';
                $html .= '<span>, Bal: ' . $transaction['balance'] . '</span></div>';
                $html .= '<hr>';
            }
            $html .= '</div>';

            // Return raw HTML response
            return response($html);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }



    public function getprintedtransactionsbyid(Request $request)
    {
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at', 'det_rep_name_of_transaction', 'account_type')
                ->where('transaction_id', $request->myuseracnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw', 'Reversal'])
                ->orderBy('id', 'DESC')
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y,');
                $transaction['created_at'] = $formattedDate;

                // Format amount to two decimal points
                $transaction['amount'] = number_format($transaction['amount'], 2);
                $transaction['balance'] = number_format($transaction['balance'], 2);
            }

            // Build the raw HTML string
            $html = '<div style="font-size:11px;">';
            foreach ($usertransactions as $transaction) {
                $html .= '<div><h4>Account No: ' . $transaction['account_number'] . '</h4><span>' . $transaction['det_rep_name_of_transaction'] . '</span> ,<span>' . $transaction['created_at'] . '</span></div><div><span>';
                $html .= $transaction['name_of_transaction'] . ': ';
                $html .= $transaction['amount'] . '</span>';
                $html .= '<span>, Bal: ' . $transaction['balance'] . '</span></div>';
                $html .= '<hr>';
            }
            $html .= '</div>';

            // Return raw HTML response
            return response($html);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function getprintedtransactions(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at')
                ->where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw', 'Reversal']) // Select transactions where name_of_transaction is either "Deposit" or "Withdraw"
                ->orderBy('id') // Order by 'id' in descending order (assuming 'id' represents the transaction's chronological order)
                ->take(10) // Limit the result to the last 50 transactions
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y, h:i A');
                $transaction['created_at'] = $formattedDate;
            }



            // Return the updated JSON data in the response
            return response()->json($usertransactions);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function getprinteddeposits(Request $request)
    {
        // Check user type for permission
        if ($this->isManagement() || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->hasRole('Agent')) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at')
                ->where('account_number', $request->myuseracnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit'])
                ->orderBy('id', 'DESC')
                ->take(10)
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y,');
                $transaction['created_at'] = $formattedDate;

                // Format amount to two decimal points
                $transaction['amount'] = number_format($transaction['amount'], 2);
                $transaction['balance'] = number_format($transaction['balance'], 2);
            }

            // Build the raw HTML string
            $html = '<div style="font-size:11px;">';
            foreach ($usertransactions as $transaction) {
                $html .= '<div><span>' . $transaction['created_at'] . '</span></div><div><span>';
                $html .= $transaction['name_of_transaction'] . ': ';
                $html .= $transaction['amount'] . '</span>';
                $html .= '<span>, Bal: ' . $transaction['balance'] . '</span></div>';
                $html .= '<hr>';
            }
            $html .= '</div>';

            // Return raw HTML response
            return response($html);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }

    public function getprintedwithdrawals(Request $request)
    {
        // Check user type for permission
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at')
                ->where('account_number', $request->myuseracnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Withdraw'])
                ->orderBy('id', 'DESC')
                ->take(10)
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y,');
                $transaction['created_at'] = $formattedDate;

                // Format amount to two decimal points
                $transaction['amount'] = number_format($transaction['amount'], 2);
                $transaction['balance'] = number_format($transaction['balance'], 2);
            }

            // Build the raw HTML string
            $html = '<div style="font-size:11px;">';
            foreach ($usertransactions as $transaction) {
                $html .= '<div><span>' . $transaction['created_at'] . '</span></div><div><span>';
                $html .= $transaction['name_of_transaction'] . ': ';
                $html .= $transaction['amount'] . '</span>';
                $html .= '<span>, Bal: ' . $transaction['balance'] . '</span></div>';
                $html .= '<hr>';
            }
            $html .= '</div>';

            // Return raw HTML response
            return response($html);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function getprintedaccountbalance(Request $request)
    {
        // Check user type for permission
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            // Retrieve existing JSON data as an array
            $usertransactions = AccountsTransactions::select('id', 'account_number', 'amount', 'name_of_transaction', 'balance', 'created_at')
                ->where('account_number', $request->accountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw', 'Reversal']) // Select transactions where name_of_transaction is either "Deposit" or "Withdraw"
                ->orderBy('id') // Order by 'id' in descending order (assuming 'id' represents the transaction's chronological order)
                ->take(10) // Limit the result to the last 50 transactions
                ->get()
                ->toArray();

            foreach ($usertransactions as &$transaction) {
                $formattedDate = Carbon::parse($transaction['created_at'])->format('M j, Y, h:i A');
                $transaction['created_at'] = $formattedDate;
            }


            // Create the new JSON object to push into the existing array
            // $newObject = [
            //     'id'=> 2456,
            //     'account_number' => $request->accountnumber,
            //     'account_type' => 'main account' // Set the des  ired account type
            // ];

            // array_push($accountnumbers, $newObject);
            // Check if the account number already exists in the array
            // $accountExists = false;
            // foreach ($accountnumbers as $account) {
            //     if ($account['account_number'] === $newObject['account_number']) {
            //         $accountExists = true;
            //         break;
            //     }
            // }

            // If the account number doesn't exist, push the new object
            /*  if (!$accountExists) {
            array_push($accountnumbers, $newObject);
        } */

            // Return the updated JSON data in the response
            return response()->json($usertransactions);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function getcustomerinfo(Request $request)
    {
        // Check user type for permission
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            // Retrieve existing JSON data as an array
            $userinfo = Accounts::select('*')
                ->where('id', $request->customerid)
                ->where('comp_id', \Auth::user()->comp_id)
                ->get()
                ->toArray();

            // Return the retrieved data in the response
            return response()->json($userinfo);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    //
    public function getcustomeraccountslist(Request $request)
    {
        // Check user type for permission
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            // Retrieve existing JSON data as an array
            $accountlist = UserAccountNumbers::select('*')
                ->where('primary_account_number', $request->customermmainaccountnumber)
                ->where('comp_id', \Auth::user()->comp_id)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->toArray();

            // Return the retrieved data in the response
            return response()->json($accountlist);
        } else {
            // Handle permission denied
            return response()->json(['message' => 'Permission denied'], 403);
        }
    }


    public function mymtn(Request $request)
    {

        return "Hello World";
    }



    public function getaccounttypes(Request $request)
    {
        $accounttypes = SavingsAccounts::select('id', 'account_name', 'account_type', 'if_cycle_value')
            ->orderBy('id', 'DESC')
            ->where('comp_id', \Auth::user()->comp_id)
            ->get()
            ->toArray();
        return $accounttypes;
    }

    public function changeaccounttypes(Request $request)
    {
        $accounttypes = SavingsAccounts::select('id', 'account_name', 'account_type', 'if_cycle_value')
            ->orderBy('id', 'ASC')
            ->where('comp_id', \Auth::user()->comp_id)
            ->get()
            ->toArray();
        return $accounttypes;
    }

    

    public function getaccounttypes2(Request $request)
    {
        $accounttypes = SavingsAccounts::select('id', 'account_name', 'account_type', 'if_cycle_value','status')
            ->orderBy('id', 'DESC')
            ->where('comp_id', \Auth::user()->comp_id)
            ->get()
            ->toArray();
        return $accounttypes;
    }






    // creating new account for the business which can be used for susu, business account or even loans.
    public function registersystemuser(Request $request)
    {
        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            $validator = \Validator::make(
                $request->all(),
                [

                    'accountname' => 'required|max:200',
                    'email' => 'required', // Allow email to remain the same
                    'phone' => 'required|min:10',
                    'password' => 'required|min:6'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return  $messages->first();
            }

            try {


                $user               = new User();
                $user['username']   = $request->accountname;
                $user['name']       = $request->accountname;
                $user['email']      = $request->email;
                $user['phone']      = $request->phone;
                $user['created_by_user'] =  \Str::random(8);
                $user['password']   = Hash::make($request->password);
                $user['type']       = $request->accountrole;
                $user['created_by'] = 2;
                $user['company_id'] = \Auth::user()->company_id;
                $user['comp_id'] = \Auth::user()->comp_id;


                if ($user->save()) {
                    // Assign Spatie Role
                    if ($request->accountrole) {
                        // Map 'Agents' to 'Agent' if necessary, or just use the value if roles are named 'Agents'
                        // Assuming roles are 'Agent', 'Admin', 'Manager'
                        $roleName = ($request->accountrole == 'Agents') ? 'Agent' : $request->accountrole;
                        try {
                            $user->assignRole($roleName);
                        } catch (\Exception $e) {
                            \Log::error("Failed to assign role: " . $e->getMessage());
                        }
                    }

                    return 'saved';
                } else {
                    return 'not saved';
                }

                return 'saved';
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if the error is due to a unique constraint violation

                return $e->getMessage();
            }
        }
    }



    // creating new account for the business which can be used for susu, business account or even loans.
    public function updatesystemuser(Request $request)
    {

        if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'Agents' || \Auth::user()->type == 'Agent' || \Auth::user()->type == 'super admin' || \Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Agent', 'Manager'])) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|', // Allow email to remain the same
                    'phone' => 'required|min:10', // Allow phone to remain the same
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return $messages->first();
            }

            try {
                if ($request->password == '') {
                    // Update the User record
                    User::where('id', $request->id)->update([
                        'username' => $request->name,
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'type'  => $request->accountrole,
                        'gender'  => $request->gender
                    ]);
                }
                
                // Sync Spatie Role
                $userToUpdate = User::find($request->id);
                if ($userToUpdate && $request->accountrole) {
                    $roleName = ($request->accountrole == 'Agents') ? 'Agent' : $request->accountrole;
                    try {
                        $userToUpdate->syncRoles($roleName);
                    } catch (\Exception $e) {
                        \Log::error("Failed to sync role: " . $e->getMessage());
                    }
                }

                return 'saved';
            } catch (\Illuminate\Database\QueryException $e) {
                return $e->getMessage();
            }
        }
    }
}
