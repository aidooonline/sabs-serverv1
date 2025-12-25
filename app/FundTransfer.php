<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    protected $table = 'fund_transfers';

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'date',
        'description',
        'created_by'
    ];

    public function sourceAccount()
    {
        return $this->belongsTo('App\CapitalAccount', 'from_account_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo('App\CentralLoanAccount', 'to_account_id');
    }
}
