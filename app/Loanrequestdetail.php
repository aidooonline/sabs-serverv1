<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanrequestdetail extends Model
{

    protected $table = 'nobs_micro_loan_request';
    protected $primaryKey = 'id';
 
    protected $fillable = [
        '__id__',
        'account_number',
        'amount',
        'bus_capital',
        'est_daily_exp',
        'est_daily_sales',
        'ext_credit_facility_amt',
        'ext_credit_facility',
        'guarantor_name',
        'guarantor_number',
        'guarantors_gps_loc',
        'loan_purpose',
        'phone_number',
        'pri_pmt_src',
        'sec_pmt_src',
        'user',
        'disbursement_date',
        'expected_disbursement_date',
        'first_name',
        'irpm',
        'last_name',
        'outstanding_bal',
        'mode_of_pmt',
        'prev_loan',
        'loan_migrated', 
        'loan_id',
        'approved_amount',
        'loan_other_purpose',
        'loan_request_rating',
        'loan_account_number',
        'customer_account_id',
        'agent_id'
    ];

    public static function loanrequestdetail($id)
    {
            return Loanrequestdetail::where('id', '=', $id)->get();
    } 
    
}
