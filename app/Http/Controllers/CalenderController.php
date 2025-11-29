<?php

namespace App\Http\Controllers;

use App\Call;
use App\Meeting;
use App\Task;
use Illuminate\Http\Request;

class CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($calenderdata = 'all')
    {
            $calls    = Call::where('created_by', \Auth::user()->creatorId())->get();
            $meetings = Meeting::where('created_by', \Auth::user()->creatorId())->get();
            $tasks    = Task::where('created_by', \Auth::user()->creatorId())->get();

            $arrMeeting = [];
            $arrTask    = [];
            $arrCall    = [];

            if($calenderdata == 'call')
            {
                foreach($calls as $call)
                {
                    $arr['id']        = $call['id'];
                    $arr['title']     = $call['name'];
                    $arr['start']     = $call['start_date'];
                    $arr['end']       = $call['end_date'];
                    $arr['className'] = 'bg-primary';
                    $arr['url']       = route('call.show', $call['id']);
                    $arrCall[]        = $arr;
                }
            }
            elseif($calenderdata == 'task')
            {
                foreach($tasks as $task)
                {
                    $arr['id']        = $task['id'];
                    $arr['title']     = $task['name'];
                    $arr['start']     = $task['start_date'];
                    $arr['end']       = $task['due_date'];
                    $arr['className'] = 'bg-success';
                    $arr['url']       = route('task.show', $task['id']);
                    $arrTask[]        = $arr;
                }
            }
            elseif($calenderdata == 'meeting')
            {
                foreach($meetings as $meeting)
                {
                    $arr['id']        = $meeting['id'];
                    $arr['title']     = $meeting['name'];
                    $arr['start']     = $meeting['start_date'];
                    $arr['end']       = $meeting['end_date'];
                    $arr['className'] = 'bg-info';
                    $arr['url']       = route('meeting.show', $meeting['id']);
                    $arrMeeting[]     = $arr;
                }
            }
            else
            {
                foreach($calls as $call)
                {
                    $arr['id']        = $call['id'];
                    $arr['title']     = $call['name'];
                    $arr['start']     = $call['start_date'];
                    $arr['end']       = $call['end_date'];
                    $arr['className'] = 'bg-primary';
                    $arr['url']       = route('call.show', $call['id']);
                    $arrCall[]        = $arr;
                }
                foreach($tasks as $task)
                {
                    $arr['id']        = $task['id'];
                    $arr['title']     = $task['name'];
                    $arr['start']     = $task['start_date'];
                    $arr['end']       = $task['due_date'];
                    $arr['className'] = 'bg-success';
                    $arr['url']       = route('task.show', $task['id']);
                    $arrTask[]        = $arr;
                }
                foreach($meetings as $meeting)
                {
                    $arr['id']        = $meeting['id'];
                    $arr['title']     = $meeting['name'];
                    $arr['start']     = $meeting['start_date'];
                    $arr['end']       = $meeting['end_date'];
                    $arr['className'] = 'bg-info';
                    $arr['url']       = route('meeting.show', $meeting['id']);
                    $arrMeeting[]     = $arr;
                }
            }

            $calandar = array_merge($arrCall, $arrMeeting, $arrTask);
            $calandar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calandar)));

            return view('calendar.index', compact('calandar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
