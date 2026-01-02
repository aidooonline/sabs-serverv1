<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class LoanApplicationRequirement extends Model
{
    use HasCompany;

    protected $table = 'loan_application_requirements';
    protected $guarded = ['id'];
    protected $fillable = ['comp_id']; // Added comp_id to fillable

    public function requirement()
    {
        return $this->belongsTo('App\LoanProcessingRequirement', 'requirement_id');
    }
}
