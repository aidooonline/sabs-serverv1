<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavingsAccounts extends Model
{
    protected $table = 'nobs_savings_accounts';
    
    protected $fillable = [
        'foreign_id',
        'interest_accumulation_period',
        'minimum_balance',
        'description',
        'lien',
        'maximum_withdrawal_percent',
        'account_name',
        'withdrawal_commission'
    ];
}

