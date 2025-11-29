<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccountNumbers extends Model
{
    protected $table = 'nobs_user_account_numbers';
    
    protected $fillable = [
        'account_number',
        'account_type',
        '__id__',
        'created_at',
        'created_by_user',
        'updated_at'
    ];
}

