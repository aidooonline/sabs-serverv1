<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Name')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $commonCase-> name }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Number')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $commonCase->number}}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Status')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($commonCase->status == 0)
                            <span class="badge badge-success">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 1)
                            <span class="badge badge-info">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 2)
                            <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 3)
                            <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 4)
                            <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @elseif($commonCase->status == 5)
                            <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$commonCase->status]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Account')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($commonCase->accounts)?$commonCase->accounts->name:'-'  }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm">{{__('Priority')}}</span></dt>
                    <dd class="col-sm-8">
                        @if($commonCase->priority == 0)
                            <span class="badge badge-primary">{{ __(\App\CommonCase::$priority[$commonCase->status]) }}</span>
                        @elseif($commonCase->priority == 1)
                            <span class="badge badge-info">{{ __(\App\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 2)
                            <span class="badge badge-warning">{{ __(\App\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @elseif($commonCase->priority == 3)
                            <span class="badge badge-danger">{{ __(\App\CommonCase::$priority[$commonCase->priority]) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Contacts')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($commonCase->contacts->name)?$commonCase->contacts->name:'-' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Type')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ !empty($commonCase->types)?$commonCase->types->name:'-' }}</span></dd>

                    <dt class="col-sm-4"><span class="h6 text-sm mb-0">{{__('Description')}}</span></dt>
                    <dd class="col-sm-8"><span class="text-sm">{{ $commonCase->description }}</span></dd>
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
                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{ __('Assigned User') }}</span></dt>

                            <dd class="col-sm-12"><span class="text-sm">{{ !empty($commonCase->assign_user)?$commonCase->assign_user->name:'-'}}</span></dd>

                            <dt class="col-sm-12"><span class="h6 text-sm mb-0">{{__('Created')}}</span></dt>
                            <dd class="col-sm-12"><span class="text-sm">{{\Auth::user()->dateFormat($commonCase->created_at)}}</span></dd>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit CommonCase')
            <a href="{{ route('commoncases.edit',$commonCase->id) }}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1" data-title="{{__('Case Edit')}}"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>


