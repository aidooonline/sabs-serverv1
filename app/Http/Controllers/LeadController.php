<?php

namespace App\Http\Controllers;
use App\Imports\LeadImport;
use App\Country;
use App\LeadTemperature;
use App\Account;
use App\AccountIndustry;
use App\AccountType;
use App\Campaign;
use App\Contact;
use App\Document;
use App\Lead;
use App\LeadSource;
use App\Stream;
use App\Task;
use App\User;
use App\UserDefualtView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Lead'))
        {
           $leads = "";
           if( \Auth::user()->type == 'owner'){
           
            $leads = Lead::where('created_by', \Auth::user()->creatorId())->orderBy('created_at', 'DESC')->get();
           }else{
            $leads = Lead::where('user_id', \Auth::user()->id)->orderBy('id', 'DESC')->get(); 
           
           }
           
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'lead';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);
            return view('lead.index', compact('leads'));
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
        if(\Auth::user()->can('Create Lead'))
        {
            $user       = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $leadsource = LeadSource::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $campaign   = Campaign::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $campaign->prepend('--', '');
            $industry   = AccountIndustry::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account    = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $status     = Lead::$status;

            $countries = Country::where('country_id','>',0)->get()->pluck('name','country_id');
            $countries->prepend('--', '');

            $leadtemperature = LeadTemperature::where('created_by','=',0)->get()->pluck('name','id');
            $leadtemperature->prepend('--', '');

            return view('lead.create', compact('status', 'leadsource', 'user', 'account', 'industry', 'campaign', 'type', 'id','countries','leadtemperature'));
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
        if(\Auth::user()->can('Create Lead'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users', 
                                   'status' => 'required',
                                   'source' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $lead                       = new Lead();
            $lead['user_id']            = $request->user;
            $lead['name']               = $request->name;
            $lead['account']            = $request->account;
            $lead['email']              = $request->email;
            $lead['phone']              = $request->phone;
            $lead['title']              = $request->title;
            $lead['website']            = $request->website;
            $lead['lead_address']       = $request->lead_address;
            $lead['lead_city']          = $request->lead_city;
            $lead['lead_state']         = $request->lead_state;
            $lead['lead_country']       = $request->lead_country;
            $lead['lead_temperature']   = $request->lead_temperature;
            $lead['lead_postalcode']    = $request->lead_postalcode;
            $lead['status']             = $request->status;
            $lead['source']             = $request->source;
            $lead['opportunity_amount'] = $request->opportunity_amount;
            $lead['campaign']           = $request->campaign;
            $lead['industry']           = $request->industry;
            $lead['description']        = $request->description;
            $lead['lead_temperature']        = $request->lead_temperature;
            $lead['created_by']         = \Auth::user()->creatorId();
            $lead->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'lead',
                            'stream_comment' => '',
                            'user_name' => $lead->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Lead Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        if(\Auth::user()->can('Show Lead'))
        {
            return view('lead.view', compact('lead'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        if(\Auth::user()->can('Edit Lead'))
        {
            $status   = Lead::$status;
            $source   = LeadSource::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $countries = Country::where('country_id','>',0)->get()->pluck('name','country_id');
            $countries->prepend('--', '');

            $leadtemperature = LeadTemperature::where('created_by','=',0)->get()->pluck('name','id');
            $leadtemperature->prepend('--', '');

            $user     = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $account  = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $industry = AccountIndustry::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $parent   = 'lead';
            $tasks    = Task::where('parent', $parent)->where('parent_id', $lead->id)->get();
            $log_type = 'lead comment';
            $streams  = Stream::where('log_type', $log_type)->get();
            $campaign = Campaign::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $campaign->prepend('--', '');
            // get previous user id
            $previous = Lead::where('id', '<', $lead->id)->max('id');
            // get next user id
            $next = Lead::where('id', '>', $lead->id)->min('id');


            return view('lead.edit', compact('lead', 'account', 'user', 'source', 'industry', 'status', 'tasks', 'streams', 'campaign', 'previous', 'next','countries','leadtemperature'));
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
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        if(\Auth::user()->can('Edit Lead'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users' 
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $lead['user_id']            = $request->user;
            $lead['name']               = $request->name;
            $lead['account']            = $request->account;
            $lead['email']              = $request->email;
            $lead['phone']              = $request->phone;
            $lead['title']              = $request->title;
            $lead['website']            = $request->website;
            $lead['lead_address']       = $request->lead_address;
            $lead['lead_city']          = $request->lead_city;
            $lead['lead_state']         = $request->lead_state;
            $lead['lead_country']       = $request->lead_country;
            $lead['lead_temperature']   = $request->lead_temperature;
            $lead['lead_postalcode']    = $request->lead_postalcode;
            $lead['status']             = $request->status;
            $lead['source']             = $request->source;

            $lead['opportunity_amount'] = $request->opportunity_amount;
            $lead['campaign']           = $request->source;
            $lead['description']        = $request->description;
            $lead['call_made']           = $request->has('call_made');
            $lead['mail_sent']           = $request->has('mail_sent');
            $lead['visited_site']           = $request->has('visited_site');
            $lead['offer_letter']           = $request->has('offer_letter');
            $lead['contract']           = $request->has('contract');
            $lead['payment']           = $request->has('payment');
            $lead['receipt']           = $request->has('receipt');
             

            $lead['created_by']         = \Auth::user()->creatorId();
            $lead->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'lead',
                            'stream_comment' => '',
                            'user_name' => $lead->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Lead Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        if(\Auth::user()->can('Delete Lead'))
        {
            $lead->delete();

            return redirect()->back()->with('success', __('Lead Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {

        $leads = "";
        $statuss ="";
        $leadsource ="";
        if( \Auth::user()->type == 'owner'){
        
         $leads = Lead::where('created_by', \Auth::user()->creatorId())->get();
       
         
         $statuss = Lead::$status;
        }else{
           
         $leads = Lead::where('user_id', \Auth::user()->id)->get(); 
        $statuss = Lead::$status;
         
        }

 
        
        
        $users = user::where('id', $leads[0]->user_id)->get();
        $leadsource = LeadSource::where('created_by', \Auth::user()->creatorId())->get()->pluck('name');
        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'lead';
        $defualtView->view   = 'kanban';
        User::userDefualtView($defualtView);
        return view('lead.grid', compact('statuss','users','leadsource'));
    }

    public function changeorder(Request $request)
    {
        $post   = $request->all();
        $lead   = Lead::find($post['lead_id']);
        $status = Lead::where('status', $post['status_id'])->get();


        if(!empty($status))
        {
            $lead->status = $post['status_id'];
            $lead->save();
        }

        foreach($post['order'] as $key => $item)
        {
            $order         = Lead::find($item);
            $order->status = $post['status_id'];
            $order->save();
        }
    }

    public function showConvertToAccount($id)
    {
        if(\Auth::user()->type == 'owner')
        {
            $lead        = Lead::findOrFail($id);
            $accountype  = accountType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $industry    = accountIndustry::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user        = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $document_id = Document::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('lead.convert', compact('lead', 'accountype', 'industry', 'user', 'document_id'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function convertToAccount($id, Request $request)
    {
        if(\Auth::user()->type == 'owner')
        {
            $lead = Lead::findOrFail($id);
            $usr  = \Auth::user();

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'email' => 'required|email|unique:users,email',
                                   'shipping_postalcode' => 'numeric',
                                   'lead_postalcode' => 'numeric',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $account                        = new account();
            $account['user_id']             = $request->user;
            $account['document_id']         = $request->document_id;
            $account['name']                = $request->name;
            $account['email']               = $request->email;
            $account['phone']               = $request->phone;
            $account['website']             = $request->website;
            $account['billing_address']     = $request->lead_address;
            $account['billing_city']        = $request->lead_city;
            $account['billing_state']       = $request->lead_state;
            $account['billing_country']     = $request->lead_country;
            $account['billing_postalcode']  = $request->lead_postalcode;
            $account['shipping_address']    = $request->shipping_address;
            $account['shipping_city']       = $request->shipping_city;
            $account['shipping_state']      = $request->shipping_state;
            $account['shipping_country']    = $request->shipping_country;
            $account['shipping_postalcode'] = $request->shipping_postalcode;
            $account['type']                = $request->type;
            $account['industry']            = $request->industry;
            $account['description']         = $request->description;
            $account['created_by']          = \Auth::user()->creatorId();
            $account->save();
            // end create deal

            // Update is_converted field as deal_id
            $lead->is_converted = $account->id;
            $lead->save();

            return redirect()->back()->with('success', __('Lead successfully converted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
