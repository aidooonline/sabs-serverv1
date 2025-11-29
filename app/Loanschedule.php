<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

   

class Loanschedule extends Model
    
{
    protected $table = 'nobs_loan_schedule_list';
    protected $primaryKey = 'id'; 
    protected     $fillable = [
        'name',
        'created_by',
    ];
}