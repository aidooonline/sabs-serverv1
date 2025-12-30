<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
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
        'repayment_frequency',
        'fee_payment_method',
        'status',
        'repayment_start_date'
    ];

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
}
