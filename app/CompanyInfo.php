<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $table = 'accounts';
    
    protected $fillable = [
        'user_id',
        'document_id',
        'name',
        'email',
        'phone',
        'website',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postalcode',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_postalcode',
        'type',
        'industry',
        'description', 
        'created_by',
        'is_active', 
        'company_id',
        'sms_credit',
        'sms_active',
        'sms_api_url',
        'sms_sender_id',
        'sms_daily_report',
        'sms_report_phone_no',
        'sms_username',
        'sms_password',
        'amount_in_cash',
        'message_after_deposit',
        'message_after_withdrawal',
        'app_home_url',
        'app_resource_url'
    ];
}

