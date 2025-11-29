<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseType extends Model
{
    protected     $fillable = [
        'name',
        'created_by',
    ];
}
