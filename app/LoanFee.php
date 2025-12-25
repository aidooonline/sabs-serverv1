<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanFee extends Model
{
    protected $table = 'loan_fees';

    protected $fillable = [
        'name',
        'type',
        'value',
        'is_default',
        'description'
    ];

    /**
     * Get the products that use this fee.
     */
    public function products()
    {
        return $this->belongsToMany('App\LoanProduct', 'loan_product_fees', 'loan_fee_id', 'loan_product_id');
    }
}
