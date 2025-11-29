<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    protected $fillable = [
        'name',
        'parent',
        'description',
        'created_by',
    ];
}
