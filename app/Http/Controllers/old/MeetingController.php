<?php

namespace App\Http\Controllers;

use App\Account;
use App\CommonCase;
use App\Contact;
use App\Lead;
use App\Meeting;
use App\Opportunities;
use App\Stream;
use App\User;
use App\UserDefualtView;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Meeting'))
        {
            $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'meeting';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);

            return view('meeting.index', compact('meetings'));

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
        if(\Auth::user()->can('Create Meeting'))
        {
            $status            = Meeting::$status;
            $parent            = Meeting::$parent;
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', '');
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_lead->prepend('--', '');

            return view('meeting.create', compact('status', 'parent', 'user', 'attendees_contact', 'attendees_lead'));

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
        if(\Auth::user()->can('Create Meeting'))
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
            $meeting                      = new Meeting();
            $meeting['user_id']           = $request->user;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['parent']            = $request->parent;
            $meeting['parent_id']         = $request->parent_id;
            $meeting['description']       = $request->description;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['created_by']        = \Auth::user()->creatorId();
            $meeting->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Meeting Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        if(\Auth::user()->can('Show Meeting'))
        {
            $status = Meeting::$status;

            return view('meeting.view', compact('meeting', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        if(\Auth::user()->can('Edit Meeting'))
        {
            $status            = Meeting::$status;
            $attendees_contact = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead    = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $attendees_lead->prepend('--', '');
            $user              = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', '');

            // get previous user id
            $previous = Meeting::where('id', '<', $meeting->id)->max('id');
            // get next user id
            $next = Meeting::where('id', '>', $meeting->id)->min('id');

            return view('meeting.edit', compact('meeting', 'attendees_contact', 'status', 'user', 'attendees_lead','previous','next'));
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
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        if(\Auth::user()->can('Edit Meeting'))
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

            $meeting['user_id']           = $request->user_id;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['description']       = $request->description;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['created_by']        = \Auth::user()->creatorId();
            $meeting->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Meeting Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Meeting $meeting
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        if(\Auth::user()->can('Delete Meeting'))
        {
            $meeting->delete();

            return redirect()->back()->with('success', 'Meeting ' . $meeting->name . ' Deleted!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'meeting';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);
        return view('meeting.grid', compact('meetings'));
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
