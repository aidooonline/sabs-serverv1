<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapitalAccountTransaction extends Model
{
    protected $table = 'capital_account_transactions';

    protected $fillable = [
        'capital_account_id',
        'amount',
        'type',
        'description',
        'date',
        'created_by'
    ];

    public function capitalAccount()
    {
        return $this->belongsTo(CapitalAccount::class);
    }
}
