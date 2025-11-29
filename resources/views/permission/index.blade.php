@extends('layouts.admin')
@section('page-title')
    {{__('Permission')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Permission')}}</h5>
    </div>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Permission')}}</li>
@endsection
@section('action-btn')
    <a href="#" data-size="lg" data-url="{{ route('permission.create') }}" data-ajax-popup="true" data-title="{{__('Create New Permission')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
        <i class="fa fa-plus"></i>
    </a>
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
                    <th scope="col" class="sort" data-sort="name">{{__('Permission')}}</th>
                    <th class="text-right">{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody class="list">
                @foreach($permissions as $permission)
                <tr>
                    <td class="sorting_1">{{$permission->name}}</td>
                    <td class="action text-right">
                        <a href="#" data-size="lg" data-url="{{ route('permission.edit',$permission->id) }}" data-ajax-popup="true" data-title="{{__('Edit Permission')}}" class="action-item">
                            <i class="far fa-edit"></i>
                        </a>
                        <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$permission->id}}').submit();">
                            <i class="fas fa-trash"></i>
                        </a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['permission.destroy', $permission->id],'id'=>'delete-form-'.$permission->id]) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
