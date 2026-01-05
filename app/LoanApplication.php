<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class LoanApplication extends Model
{
    use HasCompany;

    protected $table = 'loan_applications';

    protected $fillable = [
        'customer_id',
        'loan_product_id',
        'created_by_user_id',
        'assigned_to_user_id',
        'amount',
        'total_interest',
        'total_fees',
        'total_repayment',
        'duration',
        'number_of_installments',
        'installment_amount',
        'repayment_frequency',
        'fee_payment_method',
        'status',
        'repayment_start_date',
        'comp_id'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['total_paid', 'outstanding_balance'];

    public function loan_product()
    {
        return $this->belongsTo(LoanProduct::class, 'loan_product_id');
    }

    public function customer()
    {
        // Assuming your user model is App\User or similar, linking via customer_id
        return $this->belongsTo(Accounts::class, 'customer_id', 'id'); // Adjust foreign key if needed based on legacy system
    }

    /**
     * Get the requirements (documents) for the loan application.
     */
    public function requirements()
    {
        return $this->hasMany(LoanApplicationRequirement::class, 'loan_application_id');
    }

    /**
     * Get the repayment schedules for the loan application.
     */
    public function repaymentSchedules()
    {
        return $this->hasMany(LoanRepaymentSchedule::class);
    }

    /**
     * Get the user who created the loan application.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the user currently assigned to the loan application.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Calculate the total amount paid so far.
     *
     * @return float
     */
    public function getTotalPaidAttribute()
    {
        // Sum the total_paid field from all related repayment schedules
        return (float) $this->repaymentSchedules()->sum('total_paid');
    }

    /**
     * Calculate the outstanding balance for the loan.
     *
     * @return float
     */
    public function getOutstandingBalanceAttribute()
    {
        return (float) $this->attributes['total_repayment'] - $this->getTotalPaidAttribute();
    }
}
