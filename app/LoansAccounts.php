<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoansAccounts extends Model
{
    protected $table = 'nobs_loans_accounts';
    protected $primaryKey = 'id';
     
    protected $fillable = [
        'name',
        'foreign_id',
        'interest',
        'duration',
        'interest_per_anum',
        'payment_default_interest',
        'is_shown'
    ];

    public static function loansaccounts($id)
    {

            return LoansAccounts::where('id', '=', $id)->get();
      
       
    } 
}

