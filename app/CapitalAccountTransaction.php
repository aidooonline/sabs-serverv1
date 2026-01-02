<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class CapitalAccountTransaction extends Model
{
    use HasCompany;

    protected $table = 'capital_account_transactions';

    protected $fillable = [
        'capital_account_id',
        'amount',
        'type',
        'description',
        'date',
        'created_by',
        'comp_id'
    ];

    public function capitalAccount()
    {
        return $this->belongsTo(CapitalAccount::class);
    }
}
