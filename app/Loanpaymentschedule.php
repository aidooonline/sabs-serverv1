<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanpaymentschedule extends Model
{

    protected $table = 'nobs_loan_payment_schedule';
    protected $primaryKey = 'id';
 
    protected $fillable = [
        'amount',
        'date_to_be_paid',
        'loan_account_id',
        'customer_account_id',
        '__id__',
        'amount_paid',
        'is_paid',
        'balance'
    ];

    public static function loanpaymentschedule($id)
    {
            return Loanpaymentschedule::where('id', '=', $id)->get();
    } 
    
}
