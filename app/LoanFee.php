<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanFee extends Model
{
    protected $table = 'loan_fees';

    protected $fillable = [
        'name',
        'amount',
        'type'
    ];

    public function products()
    {
        return $this->belongsToMany(LoanProduct::class, 'loan_product_fees', 'loan_fee_id', 'loan_product_id');
    }
}