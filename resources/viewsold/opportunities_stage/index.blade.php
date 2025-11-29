@extends('layouts.admin')
@section('page-title')
    {{__('Deal Stage')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Deal Stage')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Deal Stage')}}</li>
@endsection
@section('action-btn')
    @can('Create OpportunitiesStage')
        <a href="#" data-size="lg" data-url="{{ route('opportunities_stage.create') }}" data-ajax-popup="true" data-title="{{__('Create New Stage')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('Deal Stage')}}</th>
                    @if(Gate::check('Edit OpportunitiesStage') || Gate::check('Delete OpportunitiesStage'))
                        <th class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($opportunities_stages as $opportunities_stage)
                    <tr>
                        <td class="sorting_1">{{$opportunities_stage->name}}</td>
                        @if(Gate::check('Edit OpportunitiesStage') || Gate::check('Delete OpportunitiesStage'))
                            <td class="action text-right">
                                @can('Edit OpportunitiesStage')
                                    <a href="#" data-size="lg" data-url="{{ route('opportunities_stage.edit',$opportunities_stage->id) }}" data-ajax-popup="true" data-title="{{__('Edit opportunities_stage')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete OpportunitiesStage')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$opportunities_stage->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities_stage.destroy', $opportunities_stage->id],'id'=>'delete-form-'.$opportunities_stage->id]) !!}
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
