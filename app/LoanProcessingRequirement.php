<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanProcessingRequirement extends Model
{
    protected $table = 'loan_processing_requirements';
    protected $guarded = ['id'];
}
