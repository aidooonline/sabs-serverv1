<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class AgentCommission extends Model
{
    use HasCompany;

    protected $table = 'agent_commissions';

    protected $fillable = [
        'agent_id',
        'loan_application_id',
        'transaction_id',
        'amount',
        'calculation_base',
        'percentage',
        'status',
        'payout_id',
        'comp_id'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function loan()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
