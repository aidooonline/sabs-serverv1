@extends('layouts.admin')
@section('page-title')
    {{__('Target Lists')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Target Lists')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Target List')}}</li>
@endsection
@section('action-btn')
    @can('Create TargetList')
        <a href="#" data-size="lg" data-url="{{ route('target_list.create') }}" data-ajax-popup="true" data-title="{{__('Create New Target Lists')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('Type')}}</th>
                    @if(Gate::check('Edit TargetList') || Gate::check('Delete TargetList'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($targetlists as $targetlist)
                    <tr>
                        <td class="sorting_1">{{$targetlist->name}}</td>
                        @if(Gate::check('Edit TargetList') || Gate::check('Delete TargetList'))
                            <td class="action text-right">
                                @can('Edit TargetList')
                                    <a href="#" data-size="lg" data-url="{{ route('target_list.edit',$targetlist->id) }}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete TargetList')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$targetlist->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['target_list.destroy', $targetlist->id],'id'=>'delete-form-'.$targetlist->id]) !!}
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
