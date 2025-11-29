<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ledgeraccounttypes extends Model
{

    protected $table = 'nobs_ledger_account_types';
    protected $primaryKey = 'id';
 
    protected $fillable = [
       'name',
       'description'
    ];

   
    public static function ledgeraccounttypes($id)
    {
            return Ledgeraccounttypes::where('id', '=', $id)->get();
       
    } 
    
}
