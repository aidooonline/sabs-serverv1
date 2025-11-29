<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignType extends Model
{
    protected     $fillable = [
        'name',
        'created_by',
    ];
}
