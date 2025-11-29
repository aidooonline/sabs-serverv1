<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $call-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($call->status == 0)
                            <span class="badge badge-success">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 1)
                            <span class="badge badge-info">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 2)
                            <span class="badge badge-warning">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 3)
                            <span class="badge badge-danger">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 4)
                            <span class="badge badge-danger">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @elseif($call->status == 5)
                            <span class="badge badge-warning">{{ __(\App\Call::$status[$call->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Direction')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($call->direction == 0)
                            {{ __(\App\Call::$direction[$call->direction]) }}
                        @elseif($call->direction == 1)
                            {{ __(\App\Call::$direction[$call->direction]) }}
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($call->start_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('End Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($call->end_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Parent')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $call->parent }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Parent User')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $call->getparent($call->parent,$call->parent_id)  }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $call->description }}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees User')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($call->attendees_users->name)?$call->attendees_users->name:'-' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees Contact')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($call->attendees_contacts->name)?$call->attendees_contacts->name:'-' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees Lead')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($call->attendees_leads->name)?$call->attendees_leads->name:'-' }}</span></dd>

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
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($call->assign_user)?$call->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Created</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($call->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Call')
        <a href="{{ route('call.edit',$call->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Edit Call')}}"><i class="far fa-edit"></i>
        </a>
        @endcan
    </div>
</div>

