<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class Coupon extends Model
{
    use HasCompany;

    protected $fillable = [
        'name',
        'code',
        'discount',
        'limit',
        'description',
        'comp_id'
    ];


    public function used_coupon()
    {
        return $this->hasMany('App\UserCoupon', 'coupon', 'id')->count();
    }
}
