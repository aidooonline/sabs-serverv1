@extends('layouts.admin')
@section('page-title')
    {{__('Account Industry')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Account Industry')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Account Industry')}}</li>
@endsection
@section('action-btn')
    @can('Create AccountIndustry')
        <a href="#" data-size="lg" data-url="{{ route('account_industry.create') }}" data-ajax-popup="true" data-title="{{__('Create New Account Industry')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('industry')}}</th>
                    @if(Gate::check('Edit AccountIndustry') || Gate::check('Delete AccountIndustry'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($industrys as $industry)
                    <tr>
                        <td class="sorting_1">{{$industry->name}}</td>
                        @if(Gate::check('Edit AccountIndustry') || Gate::check('Delete AccountIndustry'))
                            <td class="action text-right">
                                @can('Edit AccountIndustry')
                                    <a href="#" data-size="lg" data-url="{{ route('account_industry.edit',$industry->id) }}" data-ajax-popup="true" data-title="{{__('Edit industry')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete AccountIndustry')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$industry->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['account_industry.destroy', $industry->id],'id'=>'delete-form-'.$industry->id]) !!}
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
