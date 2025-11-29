<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $meeting-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($meeting->status == 0)
                            <span class="badge badge-success">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @elseif($meeting->status == 1)
                            <span class="badge badge-info">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @elseif($meeting->status == 2)
                            <span class="badge badge-warning">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @elseif($meeting->status == 3)
                            <span class="badge badge-danger">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @elseif($meeting->status == 4)
                            <span class="badge badge-danger">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @elseif($meeting->status == 5)
                            <span class="badge badge-warning">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($meeting->start_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('End Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($meeting->end_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Parent')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $meeting->parent }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Parent User')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $meeting->getparent($meeting->parent,$meeting->parent_id)  }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $meeting->description }}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees User')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($meeting->attendees_users->name)?$meeting->attendees_users->name:'--' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees Contact')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($meeting->attendees_contacts->name)?$meeting->attendees_contacts->name:'--' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Attendees Lead')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($meeting->attendees_leads->name)?$meeting->attendees_leads->name:'--' }}</span></dd>

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
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($meeting->assign_user)?$meeting->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($meeting->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Meeting')
            <a href="{{ route('meeting.edit',$meeting->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Edit Call')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>

