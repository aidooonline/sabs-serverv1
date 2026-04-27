<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class BusinessExpense extends Model
{
    use HasCompany;

    protected $table = 'business_expenses';

    protected $fillable = [
        'comp_id',
        'treasury_account_id',
        'category',
        'amount',
        'description',
        'expense_date',
        'receipt_url',
        'recorded_by'
    ];
}
