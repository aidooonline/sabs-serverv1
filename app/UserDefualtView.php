<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class UserDefualtView extends Model
{
    use HasCompany;

    protected $fillable = [
        'module',
        'route',
        'view',
        'user_id',
        'comp_id'
    ];
}
