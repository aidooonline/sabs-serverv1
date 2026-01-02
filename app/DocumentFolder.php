<?php

namespace App;

use Illuminate.Database.Eloquent.Model;
use App.Traits.HasCompany;

class DocumentFolder extends Model
{
    use HasCompany;

    protected $fillable = [
        'name',
        'parent',
        'description',
        'created_by',
        'comp_id'
    ];
}
