@extends('layouts.admin')
@section('page-title')
    {{__('Deals')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Deals')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Deals')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('opportunities.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Kanban View')}}
    </a>
    @can('Create Opportunities')
        <a href="#" data-size="lg" data-url="{{ route('opportunities.create',['opportunities',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Opportunities')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Account')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Stage')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($opportunitiess as $opportunities)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('opportunities.show', $opportunities->id) }}" data-ajax-popup="true" data-title="{{__('Opportunities Details')}}" class="action-item">
                                {{ ucfirst($opportunities->name) }}
                            </a>
                        </td>
                        <td class="budget">
                            <a href="#">{{ ucfirst(!empty($opportunities->accounts)?$opportunities->accounts->name:'-')}}</a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                {{  ucfirst(!empty($opportunities->stages)?$opportunities->stages->name:'-') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->priceFormat($opportunities->amount)}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ ucfirst(!empty($opportunities->assign_user)?$opportunities->assign_user->name:'-')}}</span>
                        </td>
                        @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                            <td class="text-right">
                                @can('Show Opportunities')
                                    <a href="#" data-size="lg" data-url="{{ route('opportunities.show', $opportunities->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Opportunities Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Opportunities')
                                    <a href="{{ route('opportunities.edit',$opportunities->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Opportunities Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Opportunities')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$opportunities->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id],'id'=>'delete-form-'.$opportunities ->id]) !!}
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
