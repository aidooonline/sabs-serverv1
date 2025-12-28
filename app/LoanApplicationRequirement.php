<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanApplicationRequirement extends Model
{
    protected $table = 'loan_application_requirements';
    protected $guarded = ['id'];

    public function requirement()
    {
        return $this->belongsTo('App\LoanProcessingRequirement', 'requirement_id');
    }
}
