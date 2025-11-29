<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    

    protected $table = 'countries';

    public static function getcountry($id)
    {
    return Country::where('country_id', '=', $id)->pluck('name');
          
    }
   
    public static function getcountryiso($id)
    {
    return Country::where('country_id', '=', $id)->pluck('iso2');
          
    } 

}
