<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{

    protected $table = 'nobs_loans_accounts';
    protected $primaryKey = 'id';
     
    protected $fillable = [
        'account_name',
        'foreign_id',
        'interest',
        'duration',
        'interest_per_anum',
        'payment_default_interest',
        'processing_fee',
        'collateral_fee',
        'is_shown'
    ];
   
    public static function loans($id)
    {
 
            return Loans::where('id', '=', $id)->get();
      
       
    } 
    
}
