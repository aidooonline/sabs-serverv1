@extends('layouts.admin')
@section('page-title')
    {{__('Meeting Edit')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Meeting Edit')}}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('meeting.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('meeting.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @endif
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('meeting.index')}}">{{__('Meeting')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#account_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Overview')}}</a>
                                <p class="mb-0 text-sm">{{__('Edit about your meeting information')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--account edit -->
            <div id="account_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($meeting,array('route' => array('meeting.update', $meeting->id), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name')) }}
                                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                    @error('name')
                                    <span class="invalid-name" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                {{Form::label('status',__('Status')) }}
                                {!!Form::select('status', $status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                @error('status')
                                <span class="invalid-status" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                {{Form::label('start_date',__('Start Date')) }}
                                {!!Form::date('start_date', null,array('class' => 'form-control','required'=>'required')) !!}
                                @error('start_date')
                                <span class="invalid-start_date" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('end_date',__('End Date')) }}
                                    {!!Form::date('end_date', null,array('class' => 'form-control','required'=>'required')) !!}
                                    @error('end_date')
                                    <span class="invalid-end_date" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
                                    @error('description')
                                    <span class="invalid-description" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-2">
                                <h6>{{__('Assigned')}}</h6>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('user',__('User')) }}
                                    {!! Form::select('user_id', $user, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('user')
                                    <span class="invalid-user" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-2">
                                <h5>{{__('Attendees')}}</h5>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('attendees_user',__('User')) }}
                                    {!! Form::select('attendees_user', $user, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('attendees_user')
                                    <span class="invalid-attendees_user" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('attendees_contact',__('Contact')) }}
                                    {!! Form::select('attendees_contact', $attendees_contact, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('attendees_contact')
                                    <span class="invalid-attendees_contact" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('attendees_lead',__('Lead')) }}
                                    {!! Form::select('attendees_lead', $attendees_lead, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('attendees_lead')
                                    <span class="invalid-attendees_lead" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--account edit end-->
        </div>
    </div>
@endsection
