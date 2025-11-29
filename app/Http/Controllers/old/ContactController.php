<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contact;
use App\Opportunities;
use App\Plan;
use App\Stream;
use App\Task;
use App\User;
use App\UserDefualtView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Contact'))
        {
            $contacts = Contact::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'contact';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);
            return view('contact.index', compact('contacts'));
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
        if(\Auth::user()->can('Create Contact'))
        {
            $user    = Auth::user();
            $account = Account::where('created_by', $user->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);

            return view('contact.create', compact('account', 'user', 'type', 'id'));
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
        if(\Auth::user()->can('Create Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users',
                                   'contact_postalcode' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $objUser    = User::find(\Auth::user()->creatorId());
            $total_contact = $objUser->countContacts();
            $plan          = Plan::find($objUser->plan);
            if($total_contact < $plan->max_contact || $plan->max_contact == -1)
            {
                $contact                       = new Contact();
                $contact['user_id']            = $request->user;
                $contact['name']               = $request->name;
                $contact['account']            = $request->account;
                $contact['email']              = $request->email;
                $contact['phone']              = $request->phone;
                $contact['contact_address']    = $request->contact_address;
                $contact['contact_city']       = $request->contact_city;
                $contact['contact_state']      = $request->contact_state;
                $contact['contact_country']    = $request->contact_country;
                $contact['contact_postalcode'] = $request->contact_postalcode;
                $contact['description']        = $request->description;
                $contact['created_by']         = \Auth::user()->creatorId();
                $contact->save();

                Stream::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'created_by' => \Auth::user()->creatorId(),
                        'log_type' => 'created',
                        'remark' => json_encode(
                            [
                                'owner_name' => \Auth::user()->username,
                                'title' => 'contact',
                                'stream_comment' => '',
                                'user_name' => $contact->name,
                            ]
                        ),
                    ]
                );

                return redirect()->back()->with('success', __('Contact Successfully Created.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Your contact limit is over, Please upgrade plan.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        if(\Auth::user()->can('Show Contact'))
        {
            return view('contact.view', compact('contact'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        if(\Auth::user()->can('Edit Contact'))
        {
            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $account = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $opportunitiess = Opportunities::where('contact', $contact->id)->get();
            $parent         = 'contact';
            $tasks          = Task::where('parent', $parent)->where('parent_id', $contact->id)->get();
            $log_type       = 'contact comment';
            $streams        = Stream::where('log_type', $log_type)->get();

            // get previous user id
            $previous = Contact::where('id', '<', $contact->id)->max('id');
            // get next user id
            $next = Contact::where('id', '>', $contact->id)->min('id');

            return view('contact.edit', compact('contact', 'account', 'user', 'opportunitiess', 'tasks', 'streams', 'previous', 'next'));
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
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        if(\Auth::user()->can('Edit Contact'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'email' => 'required|email|unique:users',
                                   'contact_postalcode' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $contact['user_id']            = $request->user;
            $contact['name']               = $request->name;
            $contact['account']            = $request->account;
            $contact['email']              = $request->email;
            $contact['phone']              = $request->phone;
            $contact['contact_address']    = $request->contact_address;
            $contact['contact_city']       = $request->contact_city;
            $contact['contact_state']      = $request->contact_state;
            $contact['contact_country']    = $request->contact_country;
            $contact['contact_postalcode'] = $request->contact_postalcode;
            $contact['description']        = $request->description;
            $contact['created_by']         = \Auth::user()->creatorId();
            $contact->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'contact',
                            'stream_comment' => '',
                            'user_name' => $contact->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Contact Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        if(\Auth::user()->can('Delete Contact'))
        {
            $contact->delete();

            return redirect()->back()->with('success', __('Contact Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $contacts = Contact::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'contact';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('contact.grid', compact('contacts'));
    }
}
