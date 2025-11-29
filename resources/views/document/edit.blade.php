@extends('layouts.admin')
@section('page-title')
    {{__('Document Edit')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Document Edit')}}  {{ '('. $document->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('document.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('document.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
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
    <li class="breadcrumb-item"><a href="{{route('document.index')}}">{{__('Document')}}</a></li>
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
                                <p class="mb-0 text-sm">{{__('Edit about your document information')}}</p>
                            </div>
                        </div>
                    </div>

                    <div data-href="#document_account" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-building"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Account')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned account for this document')}}</p>
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
                        {{Form::model($document,array('route' => array('document.update', $document->id), 'method' => 'PUT')) }}
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
                                    {{Form::label('folder',__('Folder')) }}
                                    {!!Form::select('folder', $folders, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('folder')
                                    <span class="invalid-folder" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('type',__('Type')) }}
                                    {!!Form::select('type', $type, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('type')
                                    <span class="invalid-type" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('account',__('Account')) }}
                                    {!!Form::select('account', $account_name, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('account')
                                    <span class="invalid-account" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('opportunities',__('Opportunities')) }}
                                    {!!Form::select('opportunities', $opportunities, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('opportunities')
                                    <span class="invalid-opportunities" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('publish_date',__('Publish Date')) }}
                                    {!!Form::date('publish_date', null,array('class' => 'form-control','required'=>'required')) !!}
                                    @error('publish_date')
                                    <span class="invalid-publish_date" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('expiration_date',__('Expiration Date')) }}
                                    {!!Form::date('expiration_date', null,array('class' => 'form-control' )) !!}
                                    @error('expiration_date')
                                    <span class="invalid-expiration_date" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('status',__('Status')) }}
                                    {!!Form::select('status', $status, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('status')
                                    <span class="invalid-status" role="alert">
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
                            <div class="col-12 mb-3 field" data-name="attachments">
                                <div class="attachment-upload">
                                    <div class="attachment-button">
                                        <div class="pull-left">
                                            {{Form::label('attachment',__('Attachment')) }}
                                            {{Form::file('attachment',array('class'=>'form-control'))}}
                                        </div>
                                    </div>
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
                                    {!! Form::select('user', $user, $document->user_id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('user')
                                    <span class="invalid-user" role="alert">
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

            <!--account -->
            <div id="document_account" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Account')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('account.create',['document',$document->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Account')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <!-- Files -->
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Website')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Type')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Country')}}</th>
                                        @if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                        <th scope="col" class="text-right">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($accounts as $account)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-ajax-popup="true" data-title="{{__('Account Details')}}" class="action-item">
                                                    {{ $account->name }}
                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#">{{ $account->website }}</a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                  {{ !empty($account->AccountType)?$account->AccountType->name:''}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{ $account->shipping_city }}</span>
                                            </td>
                                            @if(Gate::check('Show Account') || Gate::check('Edit Account') || Gate::check('Delete Account'))
                                            <td class="text-right">
                                                @can('Show Account')
                                                <a href="#" data-size="lg" data-url="{{ route('account.show',$account->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Account Details')}}" class="action-item">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('Edit Account')
                                                <a href="{{ route('account.edit',$account->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Account')}}"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('Delete Account')
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$account->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['account.destroy', $account->id],'id'=>'delete-form-'.$account ->id]) !!}
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
                    </div>
                </div>
            </div>
            <!--account Tasks end-->
        </div>
    </div>
@endsection
