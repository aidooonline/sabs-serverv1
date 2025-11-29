<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contact;
use App\Invoice;
use App\InvoiceItem;
use App\Opportunities;
use App\Product;
use App\ProductTax;
use App\Quote;
use App\SalesOrder;
use App\ShippingProvider;
use App\Stream;
use App\User;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Invoice'))
        {
            $invoices = Invoice::where('created_by', \Auth::user()->creatorId())->get();

            return view('invoice.index', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $id)
    {
        if(\Auth::user()->can('Create Invoice'))
        {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $tax  = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            $tax->prepend('No Tax', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $opportunities = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $opportunities->prepend('--', '');
            $salesorder = SalesOrder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $salesorder->prepend('--', '');
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $quote->prepend('--', '');
            $status  = Invoice::$status;
            $contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $contact->prepend('--', '');
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('invoice.create', compact('user', 'salesorder', 'quote', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider', 'type', 'id'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Invoice'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'shipping_postalcode' => 'required',
                                   'billing_postalcode' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }
            $invoice                        = new Invoice();
            $invoice['user_id']             = $request->user;
            $invoice['invoice_id']          = $this->invoiceNumber();
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $request->quote;
            $invoice['opportunity']         = $request->opportunity;
            $invoice['status']              = $request->status;
            $invoice['account']             = $request->account;
            $invoice['amount']              = $request->amount;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            $invoice['tax']                 = implode(',', $request->tax);
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['created_by']          = \Auth::user()->creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Invoice Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        if(\Auth::user()->can('Show Invoice'))
        {
            $settings = Utility::settings();

            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach($invoice->itemsdata as $item)
            {
                $totalQuantity += $item->quantity;
                $totalRate     += $item->price;
                $totalDiscount += $item->discount;
                $taxes         = Utility::tax($item->tax);

                $itemTaxes = [];
                foreach($taxes as $tax)
                {
                    if(!empty($tax))
                    {
                        $taxPrice            = Utility::taxRate($tax->rate, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = $tax->tax_name;
                        $itemTax['tax']      = $tax->tax . '%';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;
                        if(array_key_exists($tax->name, $taxesData))
                        {
                            $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                        }
                        else
                        {
                            $taxesData[$tax->tax_name] = $taxPrice;
                        }
                    }
                    else
                    {
                        $taxPrice            = Utility::taxRate(0, $item->price, $item->quantity);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = 'No Tax';
                        $itemTax['tax']      = '';
                        $itemTax['price']    = Utility::priceFormat($settings, $taxPrice);
                        $itemTaxes[]         = $itemTax;

                        if(array_key_exists('No Tax', $taxesData))
                        {
                            $taxesData[$itemTax['tax_name']] = $taxesData['No Tax'] + $taxPrice;
                        }
                        else
                        {
                            $taxesData['No Tax'] = $taxPrice;
                        }

                    }

                }

                $item->itemTax = $itemTaxes;
                $items[]       = $item;

            }
            $invoice->items         = $items;
            $invoice->totalTaxPrice = $totalTaxPrice;
            $invoice->totalQuantity = $totalQuantity;
            $invoice->totalRate     = $totalRate;
            $invoice->totalDiscount = $totalDiscount;
            $invoice->taxesData     = $taxesData;

            $company_setting = Utility::settings();

            return view('invoice.view', compact('invoice', 'company_setting'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        if(\Auth::user()->can('Edit Invoice'))
        {
            $opportunity = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $opportunity->prepend('--', '');
            $salesorder = SalesOrder::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $salesorder->prepend('--', '');
            $quote = Quote::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $quote->prepend('--', '');
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $status          = Invoice::$status;
            $billing_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $billing_contact->prepend('--', '');
            $tax = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('tax_name', 'id');
            $tax->prepend('No Tax', 0);
            $shipping_provider = ShippingProvider::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            // get previous user id
            $previous = Invoice::where('id', '<', $invoice->id)->max('id');
            // get next user id
            $next = Invoice::where('id', '>', $invoice->id)->min('id');


            return view('invoice.edit', compact('invoice', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'salesorder', 'quote', 'previous', 'next'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        if(\Auth::user()->can('Edit Invoice'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'shipping_postalcode' => 'required',
                                   'billing_postalcode' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }

            $invoice['user_id']             = $request->user;
            $invoice['invoice_id']          = $this->invoiceNumber();
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $request->quote;
            $invoice['opportunity']         = $request->opportunity;
            $invoice['status']              = $request->status;
            $invoice['account']             = $request->account;
            $invoice['amount']              = $request->amount;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            $invoice['tax']                 = implode(',', $request->tax);
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['created_by']          = \Auth::user()->creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Invoice Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Invoice $invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if(\Auth::user()->can('Delete Invoice'))
        {
            $invoice->delete();

            return redirect()->back()->with('success', __('Invoice Successfully delete.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function getaccount(Request $request)
    {
        $opportunitie = Opportunities::where('id', $request->opportunities_id)->first()->toArray();
        $account      = Account::find($opportunitie['account'])->toArray();

        return response()->json(
            [
                'opportunitie' => $opportunitie,
                'account' => $account,
            ]
        );
    }

    public function invoiceitem($id)
    {
        $invoice = Invoice::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('invoice.invoiceitem', compact('items', 'invoice', 'tax_rate'));
    }

    public function invoiceItemEdit($id)
    {
        $invoiceItem = InvoiceItem::find($id);

        $items = Product::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = ProductTax::where('created_by', \Auth::user()->creatorId())->get()->pluck('rate', 'id');

        return view('invoice.invoiceitemEdit', compact('items', 'invoiceItem', 'tax_rate'));
    }

    public function items(Request $request)
    {
        $items        = Product::where('id', $request->item_id)->first();
        $items->taxes = $items->tax($items->tax);

        return json_encode($items);
    }

    public function itemsDestroy($id)
    {
        $item = InvoiceItem::find($id);
        $item->delete();

        return redirect()->back()->with('success', __('Item Successfully delete.'));
    }

    public function storeitem(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoiceitem                = new InvoiceItem();
        $invoiceitem['invoice_id']  = $request->id;
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem['created_by']  = \Auth::user()->creatorId();
        $invoiceitem->save();

        return redirect()->back()->with('success', __('Invoice Item Successfully Created.'));

    }

    public function invoiceItemUpdate(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoiceitem                = InvoiceItem::find($id);
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem->save();

        return redirect()->back()->with('success', __('Invoice Item Successfully Updated.'));

    }

    public function previewInvoice($template, $color)
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();
        $invoice  = new Invoice();

        $user               = new \stdClass();
        $user->company_name = '<Company Name>';
        $user->name         = '<Name>';
        $user->email        = '<Email>';
        $user->mobile       = '<Phone>';
        $user->address      = '<Address>';
        $user->country      = '<Country>';
        $user->state        = '<State>';
        $user->city         = '<City>';


        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach($taxes as $k => $tax)
            {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if(array_key_exists('Tax ' . $k, $taxesData))
                {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                }
                else
                {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->items      = $items;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;


        $preview    = 1;
        $color      = '#' . $color;
        $font_color = Utility::getFontColor($color);

        $logo         = asset(\Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        return view('invoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function saveInvoiceTemplateSettings(Request $request)
    {
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color'])))
        {
            $post['invoice_color'] = "ffffff";
        }

        foreach($post as $key => $data)
        {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', [
                                                                                                                                             $data,
                                                                                                                                             $key,
                                                                                                                                             \Auth::user()->creatorId(),
                                                                                                                                         ]
            );
        }

        return redirect()->back()->with('success', __('Invoice Setting successfully updated.'));
    }

    public function pdf($id)
    {
        $settings = Utility::settings();

        $invoiceId = Crypt::decrypt($id);
        $invoice   = Invoice::where('id', $invoiceId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('created_by', '=', $invoice->created_by);
        $data1 = $data->get();

        foreach($data1 as $row)
        {
            $settings[$row->name] = $row->value;
        }

        $user         = new User();
        $user->name   = $invoice->name;
        $user->email  = $invoice->contacts->email;
        $user->mobile = $invoice->contacts->phone;

        $user->bill_address = $invoice->billing_address;
        $user->bill_zip     = $invoice->billing_postalcode;
        $user->bill_city    = $invoice->billing_city;
        $user->bill_country = $invoice->billing_country;
        $user->bill_state   = $invoice->billing_state;

        $user->address = $invoice->shipping_address;
        $user->zip     = $invoice->shipping_postalcode;
        $user->city    = $invoice->shipping_city;
        $user->country = $invoice->shipping_country;
        $user->state   = $invoice->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach($invoice->itemsdata as $product)
        {
            $item           = new \stdClass();
            $item->name     = $product->item;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->taxs) ? $product->taxs->rate : '';
            $item->discount = $product->discount;
            $item->price    = $product->price;


            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = \Utility::tax($product->tax);
            $itemTaxes = [];
            foreach($taxes as $tax)
            {
                $taxPrice      = \Utility::taxRate($tax->rate, $item->price, $item->quantity);
                $totalTaxPrice += $taxPrice;

                $itemTax['name']  = $tax->tax_name;
                $itemTax['rate']  = $tax->rate . '%';
                $itemTax['price'] = \App\Utility::priceFormat($settings, $taxPrice);
                $itemTaxes[]      = $itemTax;


                if(array_key_exists($tax->tax_name, $taxesData))
                {
                    $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                }
                else
                {
                    $taxesData[$tax->tax_name] = $taxPrice;
                }

            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->items         = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;

        //Set your logo
        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo.png'));

        if($invoice)
        {
            $color      = '#' . $settings['invoice_color'];
            $font_color = Utility::getFontColor($color);

            return view('invoice.templates.' . $settings['invoice_template'], compact('invoice', 'user', 'color', 'settings', 'img', 'font_color'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }
}
