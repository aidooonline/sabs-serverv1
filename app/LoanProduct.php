<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class LoanProduct extends Model
{
    use HasCompany;

    protected $table = 'loan_products';

    protected $fillable = [
        'name',
        'description',
        'interest_rate',
        'duration',
        'duration_unit',
        'repayment_frequency',
        'is_active',
        'comp_id'
    ];

    public function fees()
    {
        return $this->belongsToMany(LoanFee::class, 'loan_product_fees', 'loan_product_id', 'loan_fee_id');
    }
}