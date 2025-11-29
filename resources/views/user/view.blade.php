<div class="col-lg-12 order-lg-1">
    <div class="card">
        <div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('User Name')}} </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->username }}</span>
                        </div>
                        <div class="col-sm-3 text-sm-right">
                            <img src="{{(!empty($user->avatar))? asset(Storage::url("upload/profile/".$user->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" width="50px;">
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Name')}} </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->name }}</span>
                        </div>

                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Title')}}</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->title }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Email')}}</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Phone')}}</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->phone }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Gender')}}</small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{ $user->gender }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Created At :')}} </small>
                        </div>
                        <div class="col-sm-5">
                            <span class="text-sm">{{\Auth::user()->dateFormat($user->created_at )}}</span>
                        </div>

                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="h6 text-sm mb-3 mb-sm-0">{{__('Teams and Access Control')}}</small>
                        </div>
                        <div class="col-sm-12">
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">{{__('Type')}}</small>
                                </div>
                                <div class="col-sm-5">
                                    <span class="text-sm">{{ $user->type }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">{{__('Is Active')}}</small>
                                </div>
                                <div class="col-sm-5">
                                    <input type="checkbox" class="align-middle" disabled name="is_active" {{($user->is_active == 1)? 'checked': ''}}>
                                </div>
                                <div class="col-sm-4">
                                    <small class="h6 text-sm mb-3 mb-sm-0">{{__('Roles')}}</small>
                                </div>
                                <div class="col-sm-5">
                                        <span class="text-sm">{{!empty($roles[0]->name)?$roles[0]->name:'-' }}</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="w-100 text-right pr-2">
        @can('Edit User')
            <a href="{{ route('user.edit',$user->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="btn btn-sm btn-secondary btn-icon-only rounded-circle pl-1"><i class="far fa-edit"></i>
            </a>
        @endcan
    </div>
</div>
