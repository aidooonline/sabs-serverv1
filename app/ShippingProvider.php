<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingProvider extends Model
{
    protected     $fillable = [
        'name',
        'created_by',
    ];
}
