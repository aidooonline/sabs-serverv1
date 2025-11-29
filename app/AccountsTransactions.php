<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountsTransactions extends Model
{

    protected $table = 'nobs_transactions';
 
    protected $fillable = [
        '__id__',
        'account_number',
        'account_type',
        'amount',
        'det_rep_name_of_transaction',
        'created_at',
        'agentname',
        'name_of_transaction',
        'phone_number',
        'transaction_id',
        'users',
        'deposit_total',
        'updated_at',
        'withdrawal_total',
        'tid',
        'is_shown',
        'foreign_id',
        'is_loan',
        'withdrawrequest_approved',
        'approved_by',
        'paid_by',
        'is_paid',
        'paid_withdrawal_msg'
    ];

     
}
