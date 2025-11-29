@extends('layouts.admin')
@section('page-title')
    {{__('Lead Source')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Lead Source')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Lead Source')}}</li>
@endsection
@section('action-btn')
    @can('Create LeadSource')
        <a href="#" data-size="lg" data-url="{{ route('lead_source.create') }}" data-ajax-popup="true" data-title="{{__('Create New Source')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('Lead Source')}}</th>
                    @if(Gate::check('Edit LeadSource') || Gate::check('Delete LeadSource'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($lead_sources as $lead_source)
                    <tr>
                        <td class="sorting_1">{{$lead_source->name}}</td>
                        @if(Gate::check('Edit LeadSource') || Gate::check('Delete LeadSource'))
                            <td class="action text-right">
                                @can('Edit LeadSource')
                                    <a href="#" data-size="lg" data-url="{{ route('lead_source.edit',$lead_source->id) }}" data-ajax-popup="true" data-title="{{__('Edit lead_source')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete LeadSource')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$lead_source->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['lead_source.destroy', $lead_source->id],'id'=>'delete-form-'.$lead_source->id]) !!}
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
