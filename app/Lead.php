<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
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
        'call_made',
        'industry',
        'is_converted',
        'created_by',
        'description',
    ];

    protected $appends = [
        'status_name',
        'account_name',
        'source_name',
        'campaign_name',
    ];

    public static $status = [
        'New',
        'Assigned',
        'Profiled',
        'Purchased'
    ];

    public function assign_user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function accountIndustry()
    {
        return $this->hasOne('App\AccountIndustry', 'id', 'industry');
    }

    public function LeadSource()
    {
        return $this->hasOne('App\LeadSource', 'id', 'source');
    }

    public function campaigns()
    {
        return $this->hasOne('App\Campaign', 'id', 'campaign');
    }

    public function accounts()
    {
        return $this->hasOne('App\Account', 'id', 'account');
    }

    public static function leads($id)
    {

        if(\Auth::user()->type == 'owner'){

            return Lead::where('status', '=', $id)->get();
        
        }else{

            return Lead::where('status', '=', $id)->where('user_id', \Auth::user()->id)->get();            
        }
        
       
    }

    public function getStatusNameAttribute()
    {
        $status = Lead::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getAccountNameAttribute()
    {
        $account = Lead::find($this->account);


        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getCampaignNameAttribute()
    {
        $campaign = Lead::find($this->campaign);

        return $this->attributes['campaign_name'] = !empty($campaign) ? $campaign->name : '';
    }

    public function getSourceNameAttribute()
    {
        $lead_source = Lead::find($this->source);

        return $this->attributes['source_name'] = !empty($lead_source) ? $lead_source->name : '';
    }

    public function stages()
    {
        return $this->hasOne('App\TaskStage', 'id', 'stage');
    }


}
