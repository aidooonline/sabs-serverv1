<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ledgergeneraldetails extends Model
{

    protected $table = 'nobs_ledger_general_details';
    protected $primaryKey = 'id';
 
    protected $fillable = [
       'name', 
       'ac_type',
       'parent_id',
       'amount',
       'dr_account',
       'cr_account',
       'dr_amount',
       'cr_amount',
       'cr_or_dr',
       'trans_id'
    ];

   
    public static function ledgergeneraldetails($id)
    {
            return Ledgergeneraldetails::where('id', '=', $id)->get();
       
    } 
    
}
