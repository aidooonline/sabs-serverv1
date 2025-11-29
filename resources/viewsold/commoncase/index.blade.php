@extends('layouts.admin')
@section('page-title')
    {{__('Cases')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Cases')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Cases')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('commoncases.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create CommonCase')
        <a href="#" data-size="lg" data-url="{{ route('commoncases.create',['commoncases',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Case')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="completion">{{__('Account')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Priority')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                        <th scope="col">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($cases as $case)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('commoncases.show',$case->id) }}" data-ajax-popup="true" data-title="{{__('Cases Details')}}" class="badge badge-dot action-item">
                                {{ $case->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ !empty($case->accounts->name)?$case->accounts->name:'--' }}</span>
                        </td>
                        <td>
                            @if($case->status == 0)
                                <span class="badge badge-success">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @elseif($case->status == 1)
                                <span class="badge badge-info">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @elseif($case->status == 2)
                                <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @elseif($case->status == 3)
                                <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @elseif($case->status == 4)
                                <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @elseif($case->status == 5)
                                <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($case->priority == 0)
                                <span class="badge badge-primary">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                            @elseif($case->priority == 1)
                                <span class="badge badge-info">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                            @elseif($case->priority == 2)
                                <span class="badge badge-warning">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                            @elseif($case->priority == 3)
                                <span class="badge badge-danger">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-dot">{{  !empty($case->assign_user)?$case->assign_user->name:'' }}</span>
                        </td>
                        @if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                            <td>
                                <div class="d-flex">
                                    @can('Show CommonCase')
                                    <a href="#" data-size="lg" data-url="{{ route('commoncases.show',$case->id) }}" data-ajax-popup="true" data-title="{{__('Cases Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    @endcan
                                    @can('Edit CommonCase')
                                        <a href="{{ route('commoncases.edit',$case->id) }}" class="action-item" data-title="{{__('Edit Cases')}}"><i class="far fa-edit"></i></a>
                                    @endcan
                                    @can('Delete CommonCase')
                                        <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$case->id}}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id],'id'=>'delete-form-'.$case ->id]) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
