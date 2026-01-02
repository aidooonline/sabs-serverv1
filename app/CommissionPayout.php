<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class CommissionPayout extends Model
{
    use HasCompany;

    protected $table = 'commission_payouts';

    protected $fillable = [
        'agent_id',
        'amount',
        'destination_account_number',
        'transaction_ref',
        'performed_by_user_id',
        'comp_id'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}
