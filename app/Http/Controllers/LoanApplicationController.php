<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanProduct;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    /**
     * List Loan Applications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        $query = LoanApplication::with(['loan_product', 'customer']);

        // Role-based filtering (Agent Privacy)
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        if (!$user->hasRole($managerRoles) && !in_array($user->type, $managerRoles)) {
            // Agent sees loans assigned to them OR created by them
            $query->where(function($q) use ($user) {
                $q->where('assigned_to_user_id', $user->id)
                  ->orWhere('created_by_user_id', $user->id);
            });
        }

        if ($request->has('status')) {
            $status = $request->status;
            if (is_array($status)) {
                $query->whereIn('status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        // Order by newest first
        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $applications
        ], 200);
    }

    public function getCustomerLoanHistory(Request $request)
    {
        try {
            $inputIdentifier = $request->query('customer_id');
            if (!$inputIdentifier) {
                return response()->json(['success' => false, 'message' => 'Customer identifier is required.'], 400);
            }

            $user = Auth::user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            
            $compId = $user->comp_id;

            // 1. Find the customer record using raw DB query
            $customer = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->where(function($q) use ($inputIdentifier) {
                    $q->where('id', $inputIdentifier)
                      ->orWhere('account_number', $inputIdentifier)
                      ->orWhere('__id__', $inputIdentifier);
                })
                ->first();

            // 2. Gather all possible keys
            $searchKeys = [$inputIdentifier]; 
            if ($customer) {
                $searchKeys[] = $customer->id;
                $searchKeys[] = $customer->account_number;
                $searchKeys[] = $customer->__id__;
            }
            $searchKeys = array_unique(array_filter($searchKeys));

            // 3. Fetch loans using raw DB query with a robust subquery for total paid amount
            // We use COALESCE to ensure NULL results from the subquery are treated as 0
            $loans = DB::table('loan_applications')
                ->join('loan_products', 'loan_applications.loan_product_id', '=', 'loan_products.id')
                ->select(
                    'loan_applications.*', 
                    'loan_products.name as product_name',
                    DB::raw('(SELECT COALESCE(SUM(total_paid), 0) FROM loan_repayment_schedules WHERE loan_application_id = loan_applications.id) as amount_paid')
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereIn('loan_applications.customer_id', $searchKeys)
                ->orderBy('loan_applications.created_at', 'desc')
                ->get();

            // 4. Manually transform the array to match what the frontend expects
            $formattedLoans = [];
            foreach ($loans as $loan) {
                // IMPORTANT: Use the exact same math as the model accessors
                $totalPaid = (float)$loan->amount_paid;
                $totalRepayable = (float)$loan->total_repayment;
                
                // For active/disbursed/defaulted loans, the balance is the remaining debt
                // If it's already paid or in other states, we still want to show the current mathematical balance
                $balance = $totalRepayable - $totalPaid;
                
                $formattedLoans[] = [
                    'id' => $loan->id,
                    'amount' => (float)$loan->amount,
                    'total_repayment' => $totalRepayable,
                    'total_paid' => $totalPaid,
                    'outstanding_balance' => round($balance, 2),
                    'status' => $loan->status,
                    'created_at' => $loan->created_at,
                    'loan_product' => [
                        'name' => $loan->product_name
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedLoans
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Configuration Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get loans with installments due today (or overdue).
     */
    public function getLoansDueToday(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $today = now()->toDateString();
            
            // Define manager roles for consistent filtering
            $managerRoles = ['admin', 'manager', 'super admin', 'owner'];
            $userRoleNames = $user->roles->pluck('name')->map(function($role) { return strtolower($role); })->toArray();
            $userType = strtolower($user->type);
            $isManager = !empty(array_intersect($userRoleNames, $managerRoles)) || in_array($userType, $managerRoles);

            // Fetch loans that have pending schedules due today or overdue
            // We eager load ONLY the pending/overdue schedules so we can aggregate them
            $query = LoanApplication::with(['customer', 'loan_product', 'repaymentSchedules' => function($q) use ($today) {
                    $q->where('status', 'pending')->whereDate('due_date', '<=', $today);
                }])
                ->where('comp_id', $user->comp_id)
                ->where('status', 'active')
                ->whereHas('repaymentSchedules', function($q) use ($today) {
                    $q->where('status', 'pending')->whereDate('due_date', '<=', $today);
                });

            // Apply Role Filter
            if (!$isManager) {
                $query->where(function($q) use ($user) {
                    $q->where('assigned_to_user_id', $user->id)
                      ->orWhere('created_by_user_id', $user->id);
                });
            }

            // Search Filter
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->whereHas('customer', function($q) use ($searchTerm) {
                    $q->where(function($inner) use ($searchTerm) {
                        $inner->where('first_name', 'like', "%{$searchTerm}%")
                              ->orWhere('surname', 'like', "%{$searchTerm}%")
                              ->orWhere('account_number', 'like', "%{$searchTerm}%")
                              ->orWhere(\DB::raw("CONCAT(first_name, ' ', surname)"), 'like', "%{$searchTerm}%");
                    });
                });
            }

            $applications = $query->orderBy('updated_at', 'desc')->paginate(20);

            // Transform to aggregate the data
            $applications->getCollection()->transform(function($app) use ($today) {
                $customer = $app->customer;
                $dueSchedules = $app->repaymentSchedules; 
                $earliestDueDate = $dueSchedules->min('due_date');
                
                // Calculate days overdue
                $daysOverdue = 0;
                if ($earliestDueDate) {
                    $due = \Carbon\Carbon::parse($earliestDueDate);
                    $now = \Carbon\Carbon::parse($today);
                    if ($due->lt($now)) {
                        $daysOverdue = $due->diffInDays($now);
                    }
                }
                
                return [
                    'id' => $app->id,
                    'customer_id' => $app->customer_id,
                    'status' => $app->status,
                    'amount' => $app->amount,
                    'installment_amount' => $dueSchedules->sum('total_due'),
                    'installments_count' => $dueSchedules->count(),
                    'due_date' => $earliestDueDate,
                    'applied_date' => $app->created_at, // The date the loan was created
                    'days_overdue' => $daysOverdue,
                    'loan_product' => $app->loan_product,
                    'first_name' => $customer->first_name ?? 'N/A',
                    'surname' => $customer->surname ?? '',
                    'phone_number' => $customer->phone_number ?? '',
                    'account_number' => $customer->account_number ?? 'N/A',
                    'user_image' => $customer->user_image ?? 'false',
                    'customer' => $customer
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $applications
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'A server error occurred while fetching due loans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate loan details (Preview).
     * Does NOT save to database.
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'loan_product_id' => 'required|exists:loan_products,id',
            'fee_payment_method' => 'required|in:deduct_upfront,pay_separately'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $amount = $request->amount;
        $product = LoanProduct::with('fees')->find($request->loan_product_id);
        $method = $request->fee_payment_method;

        // --- THE GOLDEN FORMULA: DURATION & FREQUENCY NORMALIZATION (WORKING DAYS) ---
        // Year = 240 days, Month = 20 days, Week = 5 days
        
        // 1. Normalize Duration to Total Days
        $durationValue = (float)$product->duration;
        $unit = strtolower($product->duration_unit);
        $totalDays = 0;
        
        if (str_contains($unit, 'year')) {
            $totalDays = $durationValue * 240;
        } elseif (str_contains($unit, 'month')) {
            $totalDays = $durationValue * 20;
        } elseif (str_contains($unit, 'week')) {
            $totalDays = $durationValue * 5;
        } else { // Days
            $totalDays = $durationValue;
        }

        // 2. Normalize Frequency to Interval Days
        $frequency = strtolower($product->repayment_frequency);
        $intervalDays = 1; // Default Daily
        
        if ($frequency === 'monthly') {
            $intervalDays = 20;
        } elseif ($frequency === 'weekly') {
            $intervalDays = 5;
        }

        // 3. Calculate Number of Installments
        $numberOfInstallments = floor($totalDays / $intervalDays);
        if ($numberOfInstallments <= 0) $numberOfInstallments = 1;

        // 4. Calculate Interest based on normalized months (Working Months)
        $durationInMonths = $totalDays / 20;
        $totalInterest = $amount * ($product->interest_rate / 100) * $durationInMonths;

        // 2. Calculate Fees
        $totalFees = 0;
        $breakdownFees = [];

        foreach ($product->fees as $fee) {
            $feeAmount = 0;
            if ($fee->type == 'fixed' || $fee->type == 'flat') {
                $feeAmount = $fee->value;
            } elseif ($fee->type == 'percent' || $fee->type == 'percentage') {
                $feeAmount = $amount * ($fee->value / 100);
            }
            
            $totalFees += $feeAmount;
            $breakdownFees[] = [
                'name' => $fee->name,
                'type' => $fee->type,
                'value' => $fee->value,
                'amount' => $feeAmount
            ];
        }

        // 3. Calculate Totals
        $totalRepayment = $amount + $totalInterest; // Client pays back Principal + Interest
        
        // Installment
        $installmentAmount = $totalRepayment / $numberOfInstallments;

        // Disbursement Logic
        $disbursementAmount = $amount;
        if ($method === 'deduct_upfront') {
            $disbursementAmount = $amount - $totalFees;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'principal' => (float)$amount,
                'total_interest' => $totalInterest,
                'interest_rate' => $product->interest_rate,
                'total_fees' => $totalFees,
                'total_repayment' => $totalRepayment,
                'disbursement_amount' => $disbursementAmount,
                'installment_amount' => $installmentAmount,
                'number_of_installments' => $numberOfInstallments,
                'duration' => $product->duration,
                'duration_unit' => $product->duration_unit,
                'fee_breakdown' => $breakdownFees
            ]
        ], 200);
    }

    /**
     * Create a new Loan Application.
     */
    public function store(Request $request)
    {
        // Similar validation
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'loan_product_id' => 'required|exists:loan_products,id',
            'amount' => 'required|numeric|min:1',
            'fee_payment_method' => 'required|in:deduct_upfront,pay_separately',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        // Check for existing active/pending loans for this customer
        $existingLoan = LoanApplication::where('customer_id', $request->customer_id)
            ->whereIn('status', ['pending', 'pending_approval', 'approved', 'active', 'disbursed', 'defaulted'])
            ->first();

        if ($existingLoan) {
            return response()->json([
                'success' => false, 
                'message' => 'This customer already has an active or pending loan application. Outstanding loans must be fully repaid before applying for a new one.'
            ], 422);
        }

        // Re-run calculation logic (simplified for storage)
        $amount = $request->amount;
        $product = LoanProduct::with('fees')->find($request->loan_product_id);
        
        // --- THE GOLDEN FORMULA (WORKING DAYS) ---
        $durationValue = (float)$product->duration;
        $unit = strtolower($product->duration_unit);
        $totalDays = 0;
        
        if (str_contains($unit, 'year')) {
            $totalDays = $durationValue * 240;
        } elseif (str_contains($unit, 'month')) {
            $totalDays = $durationValue * 20;
        } elseif (str_contains($unit, 'week')) {
            $totalDays = $durationValue * 5;
        } else {
            $totalDays = $durationValue;
        }

        $durationInMonths = $totalDays / 20;
        $totalInterest = $amount * ($product->interest_rate / 100) * $durationInMonths;

        // --- Calculate Installments (Golden Formula) ---
        $frequency = strtolower($product->repayment_frequency);
        $intervalDays = 1;
        if ($frequency === 'monthly') {
            $intervalDays = 20;
        } elseif ($frequency === 'weekly') {
            $intervalDays = 5;
        }
        
        $numInstallments = floor($totalDays / $intervalDays);
        if ($numInstallments <= 0) $numInstallments = 1;

        // Calculate Fees & Create Snapshot
        $totalFees = 0;
        $feesSnapshot = [];

        foreach ($product->fees as $fee) {
            $feeAmount = 0;
            if ($fee->type == 'fixed' || $fee->type == 'flat') {
                $feeAmount = $fee->value;
            } elseif ($fee->type == 'percent' || $fee->type == 'percentage') {
                $feeAmount = $amount * ($fee->value / 100);
            }
            $totalFees += $feeAmount;
            
            $feesSnapshot[] = [
                'name' => $fee->name,
                'type' => $fee->type,
                'value' => $fee->value,
                'amount' => $feeAmount
            ];
        }

        $totalRepayment = $amount + $totalInterest;

        // Determine Assigned User
        $assignedUserId = Auth::id();
        $user = Auth::user();
        if ($user && $user->hasRole(['Admin', 'Owner', 'super admin', 'Manager']) && $request->has('assigned_to_user_id')) {
            $assignedUserId = $request->assigned_to_user_id;
        }

        $application = LoanApplication::create([
            'customer_id' => $request->customer_id,
            'loan_product_id' => $request->loan_product_id,
            'created_by_user_id' => Auth::id(), 
            'assigned_to_user_id' => $assignedUserId,
            'amount' => $amount,
            'total_interest' => $totalInterest,
            'interest_rate_snapshot' => $product->interest_rate,
            'total_fees' => $totalFees,
            'applied_fees_snapshot' => json_encode($feesSnapshot),
            'total_repayment' => $totalRepayment,
            'duration' => $product->duration,
            'number_of_installments' => $numInstallments,
            'installment_amount' => $totalRepayment / $numInstallments,
            'repayment_frequency' => $product->repayment_frequency,
            'fee_payment_method' => $request->fee_payment_method,
            'status' => 'pending',
            'repayment_start_date' => now()->addMonth() // Default start date
        ]);

        return response()->json(['success' => true, 'data' => $application, 'message' => 'Loan Application submitted successfully.'], 201);
    }

    /**
     * Update the specified loan application (e.g., change amount).
     */
    public function update(Request $request, $id)
    {
        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found'], 404);
        }

        $user = Auth::user();
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        $isManager = $user->hasRole($managerRoles) || in_array($user->type, $managerRoles);

        if (!$isManager) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Agents cannot edit submitted loans.'], 403);
        }

        // Scenario 1: Loan is active, only allow agent reassignment
        if ($application->status === 'active') {
            $validator = Validator::make($request->all(), [
                'assigned_to_user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()], 400);
            }
            
            $newAgent = User::find($request->assigned_to_user_id);
            if (!$newAgent || !$newAgent->hasRole('Agent')) {
                 return response()->json(['success' => false, 'message' => 'Invalid agent selected.'], 400);
            }

            $application->assigned_to_user_id = $request->assigned_to_user_id;
            $application->save();
            
            // Eager load the new agent's data to return
            $application->load('assignedTo');

            return response()->json(['success' => true, 'message' => 'Loan agent updated successfully.', 'data' => $application], 200);
        }

        // Scenario 2: Loan is pending, allow amount update
        if (in_array($application->status, ['pending', 'pending_approval'])) {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()], 400);
            }

            // Re-calculate based on new amount
            $amount = $request->amount;
            $product = $application->loan_product; // Relationship must be defined

            if (!$product) {
                // Fallback if relation not loaded or missing
                $product = LoanProduct::with('fees')->find($application->loan_product_id);
            }

            // --- THE GOLDEN FORMULA (WORKING DAYS) ---
            $durationValue = (float)$product->duration;
            $unit = strtolower($product->duration_unit);
            $totalDays = 0;
            
            if (str_contains($unit, 'year')) {
                $totalDays = $durationValue * 240;
            } elseif (str_contains($unit, 'month')) {
                $totalDays = $durationValue * 20;
            } elseif (str_contains($unit, 'week')) {
                $totalDays = $durationValue * 5;
            } else {
                $totalDays = $durationValue;
            }

            $durationInMonths = $totalDays / 20;
            $totalInterest = $amount * ($product->interest_rate / 100) * $durationInMonths;

            // --- Calculate Installments (Golden Formula) ---
            $frequency = strtolower($product->repayment_frequency);
            $intervalDays = 1;
            if ($frequency === 'monthly') {
                $intervalDays = 20;
            } elseif ($frequency === 'weekly') {
                $intervalDays = 5;
            }
            $numInstallments = floor($totalDays / $intervalDays);
            if ($numInstallments <= 0) $numInstallments = 1;

            $totalFees = 0;
            $feesSnapshot = [];
            if (!$product->relationLoaded('fees')) {
                $product->load('fees');
            }
            
            foreach ($product->fees as $fee) {
                $feeAmount = ($fee->type == 'fixed' || $fee->type == 'flat') ? $fee->value : $amount * ($fee->value / 100);
                $totalFees += $feeAmount;
                $feesSnapshot[] = ['name' => $fee->name, 'type' => $fee->type, 'value' => $fee->value, 'amount' => $feeAmount];
            }

            $application->amount = $amount;
            $application->total_interest = $totalInterest;
            $application->total_fees = $totalFees;
            $application->applied_fees_snapshot = json_encode($feesSnapshot);
            $application->total_repayment = $amount + $totalInterest;
            $application->number_of_installments = $numInstallments;
            $application->installment_amount = $application->total_repayment / $numInstallments;
            
            $application->save();

            return response()->json(['success' => true, 'message' => 'Application updated successfully.', 'data' => $application], 200);
        }

        // Default: If status is not editable
        return response()->json(['success' => false, 'message' => 'Cannot edit application in its current status.'], 400);
    }

    /**
     * Submit a pending application for approval.
     */
    public function submitForApproval(Request $request, $id)
    {
        $user = Auth::user();
        $managerRoles = ['Admin', 'Manager', 'super admin', 'Owner'];
        if (!$user->hasRole($managerRoles) && !in_array($user->type, $managerRoles)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found'], 404);
        }

        // Anti-Duplicate Logic: Only allow submission if status is 'pending' or 'rejected'
        if (!in_array($application->status, ['pending', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Application has already been submitted or processed.'], 400);
        }

        $application->status = 'pending_approval';
        $application->save();

        return response()->json(['success' => true, 'message' => 'Application submitted for approval.', 'data' => $application], 200);
    }

    /**
     * Display the specified loan application.
     */
    public function show(Request $request, $id)
    {
        $application = LoanApplication::with(['loan_product', 'customer', 'requirements.requirement', 'assignedTo'])->find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $application], 200);
    }

    /**
     * Get active loans ('disbursed' or 'defaulted').
     * Filters by agent if the user has the 'Agent' role.
     * Allows searching by customer name for all roles.
     */
    public function getActiveLoans(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            $query = LoanApplication::with(['customer', 'assignedTo'])
                ->whereIn('status', ['disbursed', 'defaulted', 'active']);

            // Define manager-level roles (case-insensitive for safety)
            $managerRoles = ['admin', 'manager', 'super admin', 'owner'];
            $userRoleNames = $user->roles->pluck('name')->map(function($role) { return strtolower($role); })->toArray();
            $userType = strtolower($user->type);
            
            $isManager = !empty(array_intersect($userRoleNames, $managerRoles)) || in_array($userType, $managerRoles);

            // Role-based filtering
            if (!$isManager) {
                 // If user is not a manager, assume they are an agent and show only their loans (assigned OR created)
                $query->where(function($q) use ($user) {
                    $q->where('assigned_to_user_id', $user->id)
                      ->orWhere('created_by_user_id', $user->id);
                });
            }
            
            // Search functionality for all roles
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->whereHas('customer', function ($q) use ($searchTerm) {
                    $q->where(function($inner) use ($searchTerm) {
                        $inner->where('first_name', 'like', "%{$searchTerm}%")
                              ->orWhere('middle_name', 'like', "%{$searchTerm}%")
                              ->orWhere('surname', 'like', "%{$searchTerm}%")
                              ->orWhere('account_number', 'like', "%{$searchTerm}%")
                              ->orWhere(\DB::raw("CONCAT(first_name, ' ', surname)"), 'like', "%{$searchTerm}%")
                              ->orWhere(\DB::raw("CONCAT(first_name, ' ', middle_name, ' ', surname)"), 'like', "%{$searchTerm}%");
                    });
                });
            }
            
            // Allow managers to filter by a specific agent
            if ($isManager && $request->has('agent_id') && !empty($request->agent_id)) {
                $query->where('assigned_to_user_id', $request->agent_id);
            }

            $applications = $query->orderBy('updated_at', 'desc')->paginate(20);

            return response()->json(['success' => true, 'data' => $applications], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'A server error occurred while fetching active loans.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer a loan to another agent.
     */
    public function transferLoan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $application = LoanApplication::find($id);

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Loan application not found.'], 404);
        }

        $newAgent = User::find($request->new_agent_id);
        
        // Allow if user has Spatie Role OR legacy type
        $isValidAgent = $newAgent && (
            $newAgent->hasRole('Agent') || 
            in_array($newAgent->type, ['Agent', 'Agents', 'agent', 'agents'])
        );

        if (!$isValidAgent) {
            // Or whatever roles are allowed to manage loans
            return response()->json(['success' => false, 'message' => 'The specified user is not a valid agent.'], 400);
        }

        $application->assigned_to_user_id = $request->new_agent_id;
        $application->save();

        return response()->json(['success' => true, 'message' => 'Loan successfully transferred.', 'data' => $application], 200);
    }
}
