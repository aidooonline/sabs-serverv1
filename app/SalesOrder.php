<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'quote',
        'Opportunity',
        'status',
        'account',
        'amount',
        'date_quoted',
        'quote_number',
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
        'billing_contact',
        'shipping_contact',
        'tax',
        'shipping _provider',
        'description',
        'created_by',
    ];

    protected $appends = ['user_id_name','quote_name','opportunity_name','status_name'];

    public static $status   = [
        'Draft',
        'In Review',
        'Presented',
        'Approved',
        'Rejected',
        'Canceled',
    ];


    public function assign_user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function accounts()
    {
        return $this->hasOne('App\Account', 'id', 'account');
    }

    public function quotes()
    {
        return $this->hasOne('App\Quote', 'id', 'quote');
    }

    public function taxs()
    {
        return $this->hasOne('App\ProductTax', 'id', 'tax');
    }

    public function opportunitys()
    {
        return $this->hasOne('App\Opportunities', 'id', 'opportunity');
    }

    public function contacts()
    {
        return $this->hasOne('App\Contact', 'id', 'billing_contact');
    }

    public function shipping_providers()
    {
        return $this->hasOne('App\ShippingProvider', 'id', 'shipping_provider');
    }

    public function getaccount($type, $id)
    {
        $parent = Account::find($id)->name;

        return $parent;
    }

    public function itemsdata()
    {
        return $this->hasMany('App\SalesOrderItem', 'salesorder_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->itemsdata as $product)
        {
            $subTotal += ($product->price * $product->quantity);
        }
        return $subTotal;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach($this->itemsdata as $product)
        {
            $taxes = Utility::totalTaxRate($product->tax);

            $totalTax += ($taxes / 100) * ($product->price * $product->quantity);
        }

        return $totalTax;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->itemsdata as $product)
        {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() + $this->getTotalTax()) - $this->getTotalDiscount();
    }

    public function getUserIdNameAttribute()
    {
        $user_id = SalesOrder::find($this->user_id);

        return $this->attributes['user_id_name'] = !empty($user_id) ? $user_id->name : '';
    }

    public function getQuoteNameAttribute()
    {
        $quote = SalesOrder::find($this->quote_id);

        return $this->attributes['quote_name'] = !empty($quote) ? $quote->name : '';
    }

    public function getOpportunityNameAttribute()
    {
        $opportunity = SalesOrder::find($this->opportunity);

        return $this->attributes['opportunity_name'] = !empty($opportunity) ? $opportunity->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = SalesOrder::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }



}
