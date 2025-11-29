<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{

    protected $table = 'nobs_registration';
    protected $primaryKey = 'id';
 
    protected $fillable = [
        '__id__',
        'account_number',
        'account_types',
        'accounttype_num',
        'balance_amount',
        'created_at',
        'created_time',
        'customer_picture',
        'date_of_birth',
        'email',
        'first_name',
        'gender',
        'id_number',
        'id_type',
        'marital_status',
        'middle_name',
        'type',
        'nationality',
        'next_of_kin',
        'next_of_kin_id_number',
        'next_of_kin_phone_number',
        'occupation',
        'phone_number',
        'postal_address',
        'residential_address',
        'sec_phone_number',
        'surname',
        'user',
        'date_of_birth2',
        'created_time2',
        'is_dataimage',
        'legal_consent',
        'user_image'
    ];

   
    public static function accounts($id)
    {
 
            return Accounts::where('id', '=', $id)->get();
      
       
    } 
    
}
