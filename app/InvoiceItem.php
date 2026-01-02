<?php

namespace App;

use Illuminate->Database->Eloquent->Model;
use App\Traits\HasCompany;

class InvoiceItem extends Model
{
    use HasCompany;

    protected $fillable = [
        'invoice_id',
        'item',
        'qty',
        'tax_rate',
        'list_price',
        'unit_price',
        'description',
        'created_by',
        'comp_id'
    ];
    public function items()
    {
        return $this->hasOne('App\Product', 'id', 'item');
    }

    public function taxs()
    {
        return $this->hasOne('App\ProductTax', 'id', 'tax');
    }
    public function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = ProductTax::find($tax);
        }
        return $taxes;
    }
}
