<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanmigrations extends Model
{

    protected $table = 'nobs_loan_migration';
    protected $primaryKey = 'id';
 
    protected $fillable = [
        '__id__',
        'loan_account_number',
        'amount',
        'dist_date',
        'created_at', 
        'payment_duration',
        'interest_per_schedule',  
        'repaid',
        'user',
        'cash_collateral',
        'processing_fee',
        'approved',
        'agent_id',
        'approved_amount', 
        'customer_id',
        'loan_account_id',
        'loan_schedule',
        'fully_paid',
        'customer_account_number',
        'disbursed'
    ];

    public static function loanmigrations($id)
    {
            return Loanmigrations::where('id', '=', $id)->get();
    } 
    
}
