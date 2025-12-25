<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $table = 'loan_products';

    protected $fillable = [
        'name',
        'description',
        'min_principal',
        'max_principal',
        'duration_options',
        'repayment_frequency_options',
        'is_active',
        'created_by'
    ];

    /**
     * Get the fees associated with this product.
     */
    public function fees()
    {
        return $this->belongsToMany('App\LoanFee', 'loan_product_fees', 'loan_product_id', 'loan_fee_id');
    }

    /**
     * Accessor to get durations as an array
     */
    public function getDurationsAttribute()
    {
        return explode(',', $this->duration_options);
    }

    /**
     * Accessor to get frequencies as an array
     */
    public function getFrequenciesAttribute()
    {
        return explode(',', $this->repayment_frequency_options);
    }
}
