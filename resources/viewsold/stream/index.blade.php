@extends('layouts.admin')
@section('page-title')
    {{__('Stream')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Stream')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Stream')}}</li>
@endsection
@section('action-btn')
@endsection
@section('filter')
@endsection
@section('content')
    <div class="card">
        <div class="row justify-content-between align-items-center">
            <div class="col-sm-12">
                <div class="card card-fluid">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Latest comments')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($streams as $stream)
                            @php
                                $remark = json_decode($stream->remark);
                            @endphp

                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-parent-child">
                                        <img alt="" class="rounded-circle avatar" @if(!empty($stream->file_upload)) src="{{(!empty($stream->file_upload))? asset(Storage::url("upload/profile/".$stream->file_upload)): asset(url("public/assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$remark->user_name}}" @endif>
                                    </div>
                                    <div class="flex-fill ml-3">
                                        <div class="h6 text-sm mb-0">{{$remark->user_name}}<small class="float-right text-muted">{{$stream->created_at}}</small></div>
                                        <span class="text-sm lh-140 mb-0">
                                            posted to <a href="#">{{$remark->title}}</a> , {{$stream->log_type}}  <a href="#">{{$remark->stream_comment}}</a>
                                        </span>
                                        <a href="#" class="action-item float-right" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$stream->id}}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['stream.destroy', $stream->id],'id'=>'delete-form-'.$stream->id]) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
