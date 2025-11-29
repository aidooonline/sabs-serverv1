<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $campaign-> name }}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($campaign->status == 0)
                            <span class="badge badge-warning">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 1)
                            <span class="badge badge-success">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 2)
                            <span class="badge badge-danger">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                        @elseif($campaign->status == 3)
                            <span class="badge badge-info">{{ __(\App\Campaign::$status[$campaign->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Type')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $campaign->types->name}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Budget')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->priceFormat($campaign->budget)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Start Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($campaign->start_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('End Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($campaign->end_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Target Lists')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $campaign->target_lists->name}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Excluding Target Lists')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($campaign->target_lists)?$campaign->target_lists->name:'-'}}</span></dd>


                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $campaign->description }}</span></dd>

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
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($campaign->assign_user)?$campaign->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($campaign->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Campaign')
            <a href="{{ route('campaign.edit',$campaign->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Campaign Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>

