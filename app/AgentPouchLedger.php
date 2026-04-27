<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class AgentPouchLedger extends Model
{
    use HasCompany;

    protected $table = 'agent_pouch_ledger';

    protected $fillable = [
        'comp_id',
        'agent_id',
        'current_balance',
        'last_closing_date'
    ];
}
