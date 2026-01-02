<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class LoanRepaymentSchedule extends Model
{
    use HasCompany;

    protected $table = 'loan_repayment_schedules';

    protected $fillable = [
        'loan_application_id',
        'installment_number',
        'due_date',
        
        // Expected Amounts
        'principal_due',
        'interest_due',
        'fees_due',
        'total_due',
        
        // Paid Amounts (Tracking)
        'principal_paid',
        'interest_paid',
        'fees_paid',
        'total_paid',
        
        // Status
        'status',
        'comp_id'
    ];

    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
