<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class UserAccountNumbers extends Model
{
    use HasCompany;

    protected $table = 'nobs_user_account_numbers';
    
    protected $fillable = [
        'account_number',
        'account_type',
        '__id__',
        'created_at',
        'created_by_user',
        'updated_at',
        'comp_id'
    ];
}


