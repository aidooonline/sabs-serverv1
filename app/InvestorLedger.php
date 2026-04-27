<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class InvestorLedger extends Model
{
    use HasCompany;

    protected $table = 'investor_ledger';

    protected $fillable = [
        'comp_id',
        'investor_name',
        'total_invested',
        'total_roi_paid',
        'current_balance',
        'status'
    ];
}
