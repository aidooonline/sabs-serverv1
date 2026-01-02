<?php

namespace App;

use Illuminate->Database->Eloquent->Model;
use Illuminate->Validation->Rules->In;
use App\Traits\HasCompany;

class Invoice extends Model
{
    use HasCompany;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'name',
        'salesorder',
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
        'comp_id'
    ];

    protected $appends = [
        'opportunity_name',
        'account_name',
        'status_name',
        'salesorder_name',
        'quote_name',

    ];

    public static $statuesColor = [
        'Draft' => 'secondary',
        'In Review' => 'info',
        'Presented' => 'dark',
        'Approved' => 'success',
        'Rejected' => 'warning',
        'Canceled' => 'danger',
    ];

    public static $status = [
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
        return $this->hasMany('App\InvoiceItem', 'invoice_id', 'id');
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

    public function getOpportunityNameAttribute()
    {
        $opportunity = Invoice::find($this->opportunity);

        return $this->attributes['opportunity_name'] = !empty($opportunity) ? $opportunity->name : '';
    }

    public function getAccountNameAttribute()
    {
        $account = Invoice::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }

    public function getQuoteNameAttribute()
    {
        $quote = Invoice::find($this->quote);

        return $this->attributes['account_name'] = !empty($quote) ? $quote->name : '';
    }

    public function getStatusNameAttribute()
    {
        $status = Invoice::$status[$this->status];

        return $this->attributes['status_name'] = $status;
    }

    public function getSalesorderNameAttribute()
    {
        $salesorder = Invoice::find($this->salesorder);

        return $this->attributes['account_name'] = !empty($salesorder) ? $salesorder->name : '';
    }

}
