<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class SusuCycles extends Model
{
    use HasCompany;

    protected $table = 'nobs_susu_cycles';
    
    protected $fillable = [
        'date_start',
        'cycle_rate',
        'account_number',
        'total_paid',
        'is_complete',
        'cycle_closed',
        'balance',
        'created_at',
        'updated_at',
        'comp_id'
    ];
}
