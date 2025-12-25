<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CentralLoanAccount extends Model
{
    protected $table = 'central_loan_accounts';

    protected $fillable = [
        'name',
        'balance',
        'currency',
        'description'
    ];

    /**
     * Get the transfers made TO this pool.
     */
    public function transfersIn()
    {
        return $this->hasMany('App\FundTransfer', 'to_account_id');
    }
}
