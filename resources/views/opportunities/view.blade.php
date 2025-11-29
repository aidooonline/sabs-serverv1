<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $opportunities->name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Account name')}}</span></dt>

                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($opportunities->accounts)?$opportunities->accounts->name:'-'  }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Stage')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($opportunities->stages)?$opportunities->stages->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Amount')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->priceFormat( $opportunities->amount)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm">{{__('Probability')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $opportunities->probability }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Close Date')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{\Auth::user()->dateFormat($opportunities->close_date)}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Contacts')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($opportunities->contacts)?$opportunities->contacts->name:''}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Lead Source')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($opportunities->leadsource)?$opportunities->leadsource->name:''}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $opportunities-> description }}</span></dd>
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
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Assigned User</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($opportunities->assign_user)?$opportunities->assign_user->name:''}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">Created</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($opportunities->created_at )}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Opportunities')
            <a href="{{ route('opportunities.edit',$opportunities->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Opportunities Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>
