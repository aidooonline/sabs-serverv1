<?php

namespace App;

use Illuminate->Database->Eloquent->Model;
use App\Traits\HasCompany;

class LoanDefaultLog extends Model
{
    use HasCompany;

    protected $table = 'loan_default_logs';

    protected $fillable = [
        'loan_application_id',
        'action_type',
        'description',
        'created_by',
        'comp_id'
    ];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by'); // Assuming User model is App\User
    }
}
