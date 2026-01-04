<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompany;

class LoanFee extends Model
{
    use HasCompany, SoftDeletes;

    protected $table = 'loan_fees';

    protected $fillable = [
        'name',
        'value',
        'type',
        'comp_id'
    ];

    public function products()
    {
        return $this->belongsToMany(LoanProduct::class, 'loan_product_fees', 'loan_fee_id', 'loan_product_id');
    }
}