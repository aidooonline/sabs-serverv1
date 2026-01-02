<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class DocumentType extends Model
{
    use HasCompany;

    protected     $fillable = [
        'name',
        'created_by',
        'comp_id'
    ];
