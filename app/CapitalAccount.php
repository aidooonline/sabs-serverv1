<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class CapitalAccount extends Model
{
    use HasCompany;

    protected $table = 'capital_accounts';

    protected $fillable = [
        'name',
        'type',
        'account_details',
        'balance',
        'description',
        'created_by',
        'is_active',
        'comp_id'
    ];

    /**
     * Get the transfers made FROM this account.
     */
    public function transfersOut()
    {
        return $this->hasMany('App\FundTransfer', 'from_account_id');
    }
}
