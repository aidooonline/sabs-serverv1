<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class CentralLoanAccount extends Model
{
    use HasCompany;

    protected $table = 'central_loan_accounts';

    protected $fillable = [
        'name',
        'balance',
        'currency',
        'description',
        'comp_id'
    ];

    /**
     * Get the transfers made TO this pool.
     */
    public function transfersIn()
    {
        return $this->hasMany('App\FundTransfer', 'to_account_id');
    }
}
