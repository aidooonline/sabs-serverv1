<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class TreasuryAccount extends Model
{
    use HasCompany;

    protected $table = 'treasury_accounts';

    protected $fillable = [
        'comp_id',
        'account_type',
        'account_name',
        'balance',
        'is_active',
        'created_by'
    ];
}
