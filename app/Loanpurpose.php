<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanpurpose extends Model
{

    protected $table = 'nobs_loan_purpose_list';
    protected $primaryKey = 'id';
 
    protected $fillable = [
      
       'name'
    ];

   
    public static function loanpurpose($id)
    {
            return Loanpurpose::where('id', '=', $id)->get();
       
    } 
    
}
