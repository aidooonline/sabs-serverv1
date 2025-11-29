@extends('layouts.admin')
@section('page-title')
    {{__('Document')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Document')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Document')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('document.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Document')
        <a href="#" data-size="lg" data-url="{{ route('document.create',['document',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Document')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($documents as $document)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($document->avatar)) src="{{(!empty($document->avatar))? asset(Storage::url("upload/profile/".$document->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$document->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">{{ ucfirst($document->name)}}</h5>
                        <div class="mb-1"><a href="#" class="text-sm small text-muted" data-toggle="tooltip" data-placement="right" title="Status">
                                @if($document->status == 0)
                                    <span class="badge badge-success">{{ __(\App\Document::$status[$document->status]) }}</span>
                                @elseif($document->status == 1)
                                    <span class="badge badge-warning">{{ __(\App\Document::$status[$document->status]) }}</span>
                                @elseif($document->status == 2)
                                    <span class="badge badge-danger">{{ __(\App\Document::$status[$document->status]) }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    @if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Document')
                                    <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-title="{{__('Create New Document')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Document')
                                    <a href="{{ route('document.edit',$document->id) }}" class="action-item" data-title="{{__('Edit Document')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Document')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$document->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id],'id'=>'delete-form-'.$document->id]) !!}
                                {!! Form::close() !!}
                                @endcan

                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @endforeach
    </div>
@endsection

