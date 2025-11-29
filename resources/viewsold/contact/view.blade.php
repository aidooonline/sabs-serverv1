<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">


                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($contact->assign_account)?$contact->assign_account->name:'-'}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Email')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> email }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Phone')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> phone }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Billing Address')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> contact_address }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('City')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> contact_city }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('State')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> contact_state }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Country')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $contact-> contact_country }}</span></dd>
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
                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($contact->assign_user)?$contact->assign_user->name:'-'}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($contact->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit Contact')
            <a href="{{ route('contact.edit',$contact->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Contact Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>
