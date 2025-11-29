@extends('layouts.admin')
@section('page-title')
    {{__('Task Stage')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Task Stage')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Task Stage')}}</li>
@endsection
@section('action-btn')

   @can('Create TaskStage')
        <a href="#" data-size="lg" data-url="{{ route('task_stage.create') }}" data-ajax-popup="true" data-title="{{__('Create New Task Stage')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="name">{{__('Stage')}}</th>
                   @if(Gate::check('Edit TaskStage') || Gate::check('Delete TaskStage'))
                        <th class="text-right">{{__('Action')}}</th>
                   @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($stages as $stage)
                    <tr>
                        <td class="sorting_1">{{$stage->name}}</td>
                       @if(Gate::check('Edit TaskStage') || Gate::check('Delete TaskStage'))
                            <td class="action text-right">
                               @can('Edit TaskStage')
                                    <a href="#" data-size="lg" data-url="{{ route('task_stage.edit',$stage->id) }}" data-ajax-popup="true" data-title="{{__('Edit stage')}}" class="action-item">
                                        <i class="far fa-edit"></i>
                                    </a>
                               @endcan
                               @can('Delete TaskStage')
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$stage->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['task_stage.destroy', $stage->id],'id'=>'delete-form-'.$stage->id]) !!}
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
