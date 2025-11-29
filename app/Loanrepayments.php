<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanrepayments extends Model
{

    protected $table = 'nobs_loan_repayment';
    protected $primaryKey = 'id';
 
    protected $fillable = [
        '__id__',
'account_number',
'amount',
'bus_capital',
'created_at',
'est_daily_exp',
'est_daily_sales',
'ext_credit_facility_amt',
'ext_credit_facility',
'guarantor_name',
'guarantor_number',
'guarantor_gps_loc',
'loan_purpose',
'phone_number',
'pri_pmt_src',
'sec_pmt_src',
'user',
'created_time',
'disbursement_date',
'expected_disbursement_date',
'first_name',
'irpm',
'last_name',
'outstanding_bal',
'mode_of_pmt',
'prev_loan',
'id'
    ];

   
    public static function loanrepayments($id)
    {
            return Loanrepayments::where('id', '=', $id)->get();
    } 
    
}
