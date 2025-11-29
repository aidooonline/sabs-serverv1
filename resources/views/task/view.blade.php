<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $task-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Stage')}}</span></dt>
                    <dd class="col-sm-8">
                        {{ !empty($task->stages->name)?$task->stages->name:'-' }}
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Priority')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($task->priority == 0)
                            <span class="badge badge-success">{{ __(\App\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 1)
                            <span class="badge badge-info">{{ __(\App\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 2)
                            <span class="badge badge-warning">{{ __(\App\Task::$priority[$task->priority]) }}</span>
                        @elseif($task->priority == 3)
                            <span class="badge badge-danger">{{ __(\App\Task::$priority[$task->priority]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($task->start_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Due Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($task->due_date  )}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Assigned')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $task->parent }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Assigned Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{  $task->getparent($task->parent,$task->parent_id)  }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $task->description }}</span></dd>

                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card">
            <div class="card-footer py-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <div class="row align-items-center">
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Assigned User')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($task->assign_user)?$task->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($task->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Task')
        <a href="{{ route('task.edit',$task->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Edit Call')}}"><i class="far fa-edit"></i>
        </a>
        @endcan
    </div>
</div>
