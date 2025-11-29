<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ledgergeneralsub extends Model
{

    protected $table = 'nobs_ledger_general_sub';
    protected $primaryKey = 'id';
 
    protected $fillable = [
       'name', 
       'ac_type',
       'parent_id',
       'amount',
       'description',
       'balance'
    ];

   
    public static function ledgergeneralsub($id)
    {
            return Ledgergeneralsub::where('id', '=', $id)->get();
       
    } 
    
}
