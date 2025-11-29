<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignType;
use App\Lead;
use App\Opportunities;
use App\Stream;
use App\TargetList;
use App\User;
use App\UserDefualtView;
use DemeterChain\C;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Campaign'))
        {
            $campaigns = Campaign::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'campaign';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);

            return view('campaign.index', compact('campaigns'));
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
    public function create($types, $id)
    {
        if(\Auth::user()->can('Create Campaign'))
        {
            $status = Campaign::$status;
            $user   = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $type        = CampaignType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $target_list = TargetList::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('campaign.create', compact('status', 'user', 'type', 'target_list'));
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
        if(\Auth::user()->can('Create Campaign'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'budget' => 'required|numeric',
                                   'target_list' => 'required',
                                   'type' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $campaign                   = new Campaign();
            $campaign['user_id']        = $request->user;
            $campaign['name']           = $request->name;
            $campaign['type']           = $request->type;
            $campaign['status']         = $request->status;
            $campaign['start_date']     = $request->start_date;
            $campaign['end_date']       = $request->end_date;
            $campaign['budget']         = $request->budget;
            $campaign['target_list']    = $request->target_list;
            $campaign['excluding_list'] = $request->excluding_list;
            $campaign['description']    = $request->description;
            $campaign['created_by']     = \Auth::user()->creatorId();
            $campaign->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'campaign',
                            'stream_comment' => '',
                            'user_name' => $campaign->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', 'Campaign ' . $campaign->name . ' added!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Campaign $campaign
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        if(\Auth::user()->can('Show Campaign'))
        {
            return view('campaign.view', compact('campaign'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Campaign $campaign
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        if(\Auth::user()->can('Edit Campaign'))
        {
            $type        = CampaignType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $status      = Campaign::$status;
            $target_list = TargetList::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user        = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $leads          = Lead::where('campaign', $campaign->id)->get();
            $opportunitiess = Opportunities::where('campaign', $campaign->id)->get();

            // get previous user id
            $previous = Campaign::where('id', '<', $campaign->id)->max('id');
            // get next user id
            $next = Campaign::where('id', '>', $campaign->id)->min('id');

            return view('campaign.edit', compact('campaign', 'type', 'status', 'user', 'target_list', 'leads', 'opportunitiess', 'previous', 'next'));
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
     * @param \App\Campaign $campaign
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        if(\Auth::user()->can('Edit Campaign'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'budget' => 'required|numeric',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $campaign['user_id']        = $request->user;
            $campaign['name']           = $request->name;
            $campaign['type']           = $request->type;
            $campaign['status']         = $request->status;
            $campaign['start_date']     = $request->start_date;
            $campaign['end_date']       = $request->end_date;
            $campaign['budget']         = $request->budget;
            $campaign['target_list']    = $request->target_list;
            $campaign['excluding_list'] = $request->excludingtarget_list;
            $campaign['description']    = $request->description;
            $campaign['created_by']     = \Auth::user()->creatorId();
            $campaign->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'campaign',
                            'stream_comment' => '',
                            'user_name' => $campaign->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', 'Campaign ' . $campaign->name . ' Updated!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Campaign $campaign
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaign $campaign)
    {
        if(\Auth::user()->can('Delete Campaign'))
        {
            $campaign->delete();

            return redirect()->back()->with('success', __('Campaign Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $campaigns = Campaign::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'campaign';
        $defualtView->view   = 'list';
        User::userDefualtView($defualtView);

        return view('campaign.grid', compact('campaigns'));
    }
}
