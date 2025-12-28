<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanRepaymentSchedule extends Model
{
    protected $table = 'loan_repayment_schedules';

    protected $fillable = [
        'loan_application_id',
        'installment_number',
        'due_date',
        'principal_due',
        'interest_due',
        'fees_due',
        'total_due',
        'principal_paid',
        'interest_paid',
        'fees_paid',
        'total_paid',
        'status'
    ];

    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
