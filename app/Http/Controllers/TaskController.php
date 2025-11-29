<?php

namespace App\Http\Controllers;

use App\Account;
use App\CommonCase;
use App\Contact;
use App\Lead;
use App\Opportunities;
use App\Stream;
use App\Task;
use App\TaskStage;
use App\User;
use App\UserDefualtView;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Compound;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Task'))
        {
            $tasks = Task::where('created_by', \Auth::user()->creatorId())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'task';
            $defualtView->view   = 'list';
            User::userDefualtView($defualtView);

            return view('task.index', compact('tasks'));
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
        if(\Auth::user()->can('Create Task'))
        {
            $stage   = TaskStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            $priority = Task::$priority;
            $parent   = Task::$parent;

            $user = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            return view('task.create', compact('stage', 'parent', 'user', 'priority'));
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
        if(\Auth::user()->can('Create Task'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'stage' => 'required',
                                   'image' => 'image|mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(!empty($request->attachment))
            {
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $dir             = storage_path('upload/profile/');
                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $path = $request->file('attachment')->storeAs('upload/profile/', $fileNameToStore);
            }
            $task                = new Task();
            $task['user_id']     = $request->user;
            $task['name']        = $request->name;
            $task['stage']      = $request->stage;
            $task['priority']    = $request->priority;
            $task['start_date']  = $request->start_date;
            $task['due_date']    = $request->due_date;
            $task['parent']      = $request->parent;
            $task['parent_id']   = $request->parent_id;
            $task['description'] = $request->description;
            $task['attachment']  = !empty($request->attachment) ? $fileNameToStore : '';
            $task['created_by']  = \Auth::user()->creatorId();
            $task->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'task',
                            'stream_comment' => '',
                            'user_name' => $task->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Task Successfully Created.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        if(\Auth::user()->can('Show Task'))
        {
            return view('task.view', compact('task'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        if(\Auth::user()->can('Edit Task'))
        {
            $stage   = TaskStage::where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            $priority = Task::$priority;
            $user     = User::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $parent   = Task::$parent;

            // get previous user id
            $previous = Task::where('id', '<', $task->id)->max('id');
            // get next user id
            $next = Task::where('id', '>', $task->id)->min('id');


            $log_type = 'task comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            return view('task.edit', compact('task', 'stage', 'user', 'priority', 'streams', 'parent','previous','next'));
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
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if(\Auth::user()->can('Edit Task'))
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

            $task['user_id']    = $request->user;
            $task['name']       = $request->name;
            $task['stage']     = $request->stage;
            $task['priority']   = $request->priority;
            $task['start_date'] = $request->start_date;
            $task['due_date']   = $request->due_date;

            $task['description'] = $request->description;
            $task['created_by']  = \Auth::user()->creatorId();
            $task->update();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,'created_by' => \Auth::user()->creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'task',
                            'stream_comment' => '',
                            'user_name' => $task->name,
                        ]
                    ),
                ]
            );

            return redirect()->back()->with('success', __('Task Successfully Updated.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if(\Auth::user()->can('Delete Task'))
        {
            $task->delete();

            return redirect()->back()->with('success', 'Task ' . $task->name . ' Deleted!');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        $tasks = Task::where('created_by', \Auth::user()->creatorId())->get();

        $defualtView         = new UserDefualtView();
        $defualtView->route  = \Request::route()->getName();
        $defualtView->module = 'task';
        $defualtView->view   = 'grid';
        User::userDefualtView($defualtView);

        return view('task.grid', compact('tasks'));
    }

    public function getparent(Request $request)
    {
        if($request->parent == 'Account')
        {
            $parent = Account::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'Lead')
        {
            $parent = Lead::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'Contact')
        {
            $parent = Contact::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'Opportunities')
        {
            $parent = Opportunities::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'Case')
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
