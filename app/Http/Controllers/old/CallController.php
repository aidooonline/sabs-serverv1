<?php

namespace App\Http\Controllers;

use App\Call;
use App\Stream;
use App\UserDefualtView;
use DemeterChain\C;
use Illuminate\Http\Request;
use App\User;
use App\Contact;
use App\Lead;
use App\Account;
use App\Opportunities;
use App\CommonCase;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Call'))
        {
            $calls = Call::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'call';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);

            return view('call.index', compact('calls'));
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
    public function create()
    {
        if(\Auth::user()->can('Create Call'))
        {
            $status            = Call::$status;
            $direction         = Call::$direction;
            $parent            = Call::$parent;
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', '');
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_lead->prepend('--', '');

            return view('call.create', compact('status', 'parent', 'user', 'attendees_contact', 'attendees_lead', 'direction'));
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
        if(\Auth::user()->can('Create Call'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $call                      = new Call();
            $call['user_id']           = $request->user;
            $call['name']              = $request->name;
            $call['status']            = $request->status;
            $call['direction']         = $request->direction;
            $call['start_date']        = $request->start_date;
            $call['end_date']          = $request->end_date;
            $call['parent']            = $request->parent;
            $call['parent_id']         = $request->parent_id;
            $call['description']       = $request->description;
            $call['attendees_user']    = $request->attendees_user;
            $call['attendees_contact'] = $request->attendees_contact;
            $call['attendees_lead']    = $request->attendees_lead;
            $call['created_by']        = \Auth::user()->creatorId();
            $call->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Call Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Call $call)
    {
        if(\Auth::user()->can('Show Call'))
        {
            $status = Call::$status;

            return view('call.view', compact('call', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Call $call)
    {
        if(\Auth::user()->can('Edit Call'))
        {
            $status            = Call::$status;
            $direction         = Call::$direction;
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_lead->prepend('--', '');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', '');

            // get previous user id
            $previous = Call::where('id', '<', $call->id)->max('id');
            // get next user id
            $next = Call::where('id', '>', $call->id)->min('id');

            return view('call.edit', compact('call', 'attendees_contact', 'status', 'user', 'attendees_lead', 'direction','previous','next'));
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
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Call $call)
    {
        if(\Auth::user()->can('Edit Call'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $call['user_id']           = $request->user_id;
            $call['name']              = $request->name;
            $call['status']            = $request->status;
            $call['direction']         = $request->direction;
            $call['start_date']        = $request->start_date;
            $call['end_date']          = $request->end_date;
            $call['description']       = $request->description;
            $call['attendees_user']    = $request->attendees_user;
            $call['attendees_contact'] = $request->attendees_contact;
            $call['attendees_lead']    = $request->attendees_lead;
            $call['created_by']        = \Auth::user()->creatorId();
            $call->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Call Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Call $call
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Call $call)
    {
        if(\Auth::user()->can('Delete Call'))
        {
            $call->delete();

            return redirect()->back()->with('success', 'Call ' . $call->name . ' Deleted!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $calls = Call::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'call';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('call.grid', compact('calls'));
    }

    public function getparent(Request $request)
    {
        if($request->parent == 'account')
        {
            $parent = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'lead')
        {
            $parent = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'contact')
        {
            $parent = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'opportunities')
        {
            $parent = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'case')
        {
            $parent = CommonCase::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        else
        {
            $parent = '';
        }

        return response()->json($parent);
    }
}
