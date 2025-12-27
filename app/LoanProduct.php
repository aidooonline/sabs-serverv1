<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table = 'loan_products';

    protected $fillable = [
        'name',
        'description',
        'interest_rate',
        'duration',
        'duration_unit',
        'repayment_frequency',
        'is_active'
    ];

    public function fees()
    {
        return $this->belongsToMany(LoanFee::class, 'loan_product_fees', 'loan_product_id', 'loan_fee_id');
    }
}