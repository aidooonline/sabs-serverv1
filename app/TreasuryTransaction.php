<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class TreasuryTransaction extends Model
{
    use HasCompany;

    protected $table = 'treasury_transactions';

    protected $fillable = [
        'comp_id',
        'transaction_code',
        'source_type',
        'source_id',
        'destination_type',
        'destination_id',
        'amount',
        'transaction_type',
        'description',
        'related_legacy_tx_id',
        'status',
        'performed_by'
    ];
}
