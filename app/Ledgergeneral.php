<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ledgergeneral extends Model
{

    protected $table = 'nobs_ledger_general';
    protected $primaryKey = 'id';
 
    protected $fillable = [
       'name', 
       'ac_type',
       'parent_id',
       'amount',
       'sub_count',
       'description'
    ];

   
    public static function ledgergeneral($id)
    {
            return Ledgergeneral::where('id', '=', $id)->get();
       
    } 
    
}
