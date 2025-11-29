@extends('layouts.admin')
@section('page-title')
    {{__('Accounts')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Account')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Account')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('account.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create Account')
        <a href="#" data-size="lg" data-url="{{ route('account.create',['account',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Account')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection

@section('content')
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="Email">{{__('Email')}}</th>
                    <th scope="col" class="sort" data-sort="Phone">{{__('Phone')}}</th>
                    <th scope="col" class="sort" data-sort="Website">{{__('Website')}}</th>
                    <th scope="col" class="sort" data-sort="Assigned User">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($accounts as $account)
                    <tr>
                        <td>

                            <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-ajax-popup="true" data-title="{{__('Account Details')}}" class="action-item">
                                {{ ucfirst($account->name) }}
                            </a>
                        </td>
                        <td class="budget">
                            {{ $account->email }}

                        </td>
                        <td>
                            <span class="badge badge-dot">
                              {{ $account->phone}}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ $account->website }}</span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($account->assign_user)?$account->assign_user->name:'-')}}</span></span>
                        </td>

                        @if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))

                            <td class="text-right">
                                @can('Show Account')
                                    <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Account Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Account')
                                    <a href="{{ route('account.edit',$account->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Account')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$account->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account ->id]) !!}
                                {!! Form::close() !!}
                                @endcan
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function () {
            console.log('hi');
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
@endpush

