@extends('layouts.admin')
@section('page-title')
    {{__('Role')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Role')}}</h5>
    </div>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Role')}}</li>
@endsection
@section('action-btn')
    @can('Create Role')
        <a href="#" data-url="{{ route('role.create') }}" data-size="xl" data-ajax-popup="true" data-title="{{__('Create New Role')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th width="150">{{__('Role')}} </th>
                    <th>{{__('Permissions')}} </th>
                    @if(Gate::check('Edit Role') || Gate::check('Delete Role'))
                        <th width="150" class="text-right">{{__('Action')}} </th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach ($roles as $role)
                    <tr class="font-style">
                        <td width="150">{{ $role->name }}</td>
                        <td class="Permission">
                            <div class="badges">
                                @for($j=0;$j<count($role->permissions()->pluck('name'));$j++)
                                    <span class="badge badge-primary">{{$role->permissions()->pluck('name')[$j]}}</span>
                                @endfor
                            </div>
                        </td>
                        @if(Gate::check('Edit Role') || Gate::check('Delete Role'))
                            <td class="action text-right">
                                @can('Edit Role')
                                    <a href="#" class="action-item" data-url="{{ route('role.edit',$role->id) }}" data-size="xl" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Role')}}">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete Role')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$role->id}}').submit();"><i class="fas fa-trash"></i></a>

                                    {!! Form::open(['method' => 'DELETE', 'route' => ['role.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
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
