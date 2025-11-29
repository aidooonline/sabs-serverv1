@extends('layouts.admin')
@section('page-title')
    {{__('Account Edit')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Account Edit')}}  {{ '('. $account->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('account.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('account.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action disabled" data-toggle="tooltip" data-original-title="{{__('Next')}}">
                <i class="fas fa-chevron-right"></i>
            </a>
        @endif
i    </div>
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('account.index')}}">{{__('Account')}}</a></li>
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
                                <p class="mb-0 text-sm">{{__('Edit about your account information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#account_stream" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-rss"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Comment')}}</a>
                                <p class="mb-0 text-sm">{{__('Add comment')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountcontact" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-users"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Contacts')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned contacts for this account')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountopportunities" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-handshake"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Deals')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned deals for this account')}}</p>
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" data-href="#accountcases" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-file-alt"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Cases')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountdocuments" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-book-open"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Documents')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned documents')}}</p>
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" data-href="#accounttasks" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-tasks"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Tasks')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned tasks for this account')}}</p>
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
                        {{Form::model($account,array('route' => array('account.update', $account->id), 'method' => 'PUT')) }}
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
                                    {{Form::label('email',__('Email')) }}
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter email'),'required'=>'required'))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('phone',__('Phone')) }}
                                    {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter phone'),'required'=>'required'))}}
                                    @error('phone')
                                    <span class="invalid-phone" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('website',__('Website')) }}
                                    {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website')))}}
                                    @error('website')
                                    <span class="invalid-website" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('billing_address',__('Address')) }}
                                    <a style="display:none;" class="btn btn-xs small btn-primary rounded-pill mr-auto float-right p-1 px-4" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address"><i class="fas fa-copy"></i></a>
                                    <span class="clearfix"></span>
                                    {{Form::text('billing_address',null,array('class'=>'form-control','placeholder'=>__('Enter Address')))}}
                                    @error('billing_address')
                                    <span class="invalid-billing_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('shipping_address',__('Shipping Address')) }}
                                    {{Form::text('shipping_address',null,array('class'=>'form-control','placeholder'=>__('Enter Shipping Address')))}}
                                    @error('shipping_address')
                                    <span class="invalid-shipping_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::label('city',__('City/Town')) }}
                                    {{Form::text('billing_city',null,array('class'=>'form-control','placeholder'=>__('City/Town'),))}}
                                    @error('billing_city')
                                    <span class="invalid-billing_city" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('state',__('State')) }}
                                    {{Form::text('billing_state',null,array('class'=>'form-control','placeholder'=>__('Enter Billing State')))}}
                                    @error('billing_state')
                                    <span class="invalid-billing_state" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-3">
                                <div class="form-group">
                                    {{Form::label('billing_country',__('Country')) }}
                                    {{Form::text('billing_country',null,array('class'=>'form-control','placeholder'=>__('Enter country')))}}
                                    @error('billing_country')
                                    <span class="invalid-billing_country" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('billing_country',__('Postal Code')) }}
                                    {{Form::number('billing_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Billing Postal Code') ))}}
                                    @error('billing_postalcode')

                                    <span class="invalid-billing_postalcode" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            
                            <div class="col-12">
                                <hr class="mt-1 mb-2">
                                <h6>{{__('Detail')}}</h6>
                            </div>
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('type',__('Type')) }}
                                    {!! Form::select('type', $accountype, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('type')
                                    <span class="invalid-name" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('industry',__('Industry')) }}
                                    {!! Form::select('industry', $industry, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('industry')
                                    <span class="invalid-industry" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('document_id',__('Document')) }}
                                    {!! Form::select('document_id', $document_id, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                    @error('industry')
                                    <span class="invalid-industry" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Name')))}}
                                </div>
                            </div>

                            
                            <div class="col-6" style="display:none;">
                                {{Form::label('user',__('User')) }}
                                {!! Form::select('user', $user, $account->user_id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                @error('user')
                                <span class="invalid-user" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--account edit end-->

            <!--stream edit -->
            <div id="account_stream" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Comments')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::open(array('route' => array('streamstore',['account',$account->name,$account->id]), 'method' => 'post','enctype'=>'multipart/form-data')) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('stream',__('Comment')) }}
                                    {{Form::text('stream_comment',null,array('class'=>'form-control','placeholder'=>__('Enter Comment on Account'),'required'=>'required'))}}
                                </div>
                            </div>
                            <input type="hidden" name="log_type" value="account comment">
                            <div style="display:none;" class="col-12 mb-3 field" data-name="attachments">
                                <div class="attachment-upload">
                                    <div class="attachment-button">
                                        <div class="pull-left">
                                            {{Form::label('attachment',__('Attachment')) }}
                                            {{Form::file('attachment',array('class'=>'form-control'))}}
                                        </div>
                                    </div>
                                    <div class="attachments"></div>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <div class="w-100 mt-3 text-right">
                                    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
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
                                        @if($remark->data_id == $account->id)
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-parent-child">
                                                </div>
                                                <div class="flex-fill ml-3">
                                                    <div class="h6 text-sm mb-0">
                                                     
                                                    <small class="float-right text-muted">{{$stream->created_at->diffForHumans()}} 
                                                            
                                        <span style="color:#61c7f6 !important;"> ({{ $stream->created_at->isoFormat('DD-MMM-YYYY')}} - {{$stream->created_at->format('g:i A')}})</span>
                                                          
                                                    </small>
                                                    
                                                    </div>
                                                    <span class="text-sm lh-140 mb-0">
                                                     <h6 class="commentuser">
                                                        {{$remark->owner_name}}  
                                                     </h6> 
                                                </span>
                                                <p class="commentdiv">
                                                        {{$remark->stream_comment}} 
                                                </p>    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--stream edit end-->

            <!--account contact -->
            <div id="accountcontact" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Contacts')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('contact.create',['account',$account->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Contact')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Email')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Phone')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('City')}}</th>
                                        @if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                                            <th scope="col">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($contacts as $contact)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('contact.show',$contact->id) }}" data-ajax-popup="true" data-title="{{__('Contact Details')}}" class="action-item">
                                                    {{ $contact->name }}
                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#">{{ $contact->email }}</a>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                    {{ $contact->phone }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{ $contact->contact_city }}</span>
                                            </td>
                                            @if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                                            <td>
                                                <div class="d-flex">
                                                    @can('Show Contact')
                                                    <a href="#" data-size="lg" data-url="{{ route('contact.show',$contact->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Contact Details')}}" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('Edit Contact')
                                                    <a href="{{ route('contact.edit',$contact->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Contact Edit')}}"><i class="far fa-edit"></i></a>
                                                    @endcan
                                                    @can('Delete Contact')
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$contact->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $contact->id],'id'=>'delete-form-'.$contact ->id]) !!}
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </div>
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
            <!--account contact end-->

            <!--account opportunities -->
            <div id="accountopportunities" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Deals')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('opportunities.create',['account',$account->id]) }}" data-ajax-popup="true" data-title="{{__('Add Deal to Account')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col" class="sort" data-sort="status">{{__('Deal Stage')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Amount')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                                        @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                            <th scope="col">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($opportunitiess as $opportunities)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('opportunities.show', $opportunities->id) }}" data-ajax-popup="true" data-title="{{__('Opportunities Details')}}" class="action-item">
                                                    {{ $opportunities->name }}
                                                </a>
                                            </td>

                                            <td>
                                                <span class="badge badge-dot">
                                                    {{  !empty($opportunities->stages)?$opportunities->stages->name:'-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->priceFormat($opportunities->amount)}}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{  !empty($opportunities->assign_user)?$opportunities->assign_user->name:'-' }}</span>
                                            </td>
                                            @if(Gate::check('Show Opportunities') || Gate::check('Edit Opportunities') || Gate::check('Delete Opportunities'))
                                            <td>
                                                <div class="d-flex">
                                                    @can('Show Opportunities')
                                                    <a href="#" data-size="lg" data-url="{{ route('opportunities.show', $opportunities->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Opportunities Details')}}" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('Edit Opportunities')
                                                    <a href="{{ route('opportunities.edit',$opportunities->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" class="action-item" data-title="{{__('Opportunities Edit')}}"><i class="far fa-edit"></i></a>
                                                    @endcan
                                                    @can('Delete Opportunities')
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$opportunities->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['opportunities.destroy', $opportunities->id],'id'=>'delete-form-'.$opportunities ->id]) !!}
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </div>
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
            <!--account opportunities end-->

            <!--account cases -->
            <div id="accountcases" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Cases')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('commoncases.create',['account',$account->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Common Case')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table align-items-center dataTable">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                        <th scope="col" class="sort" data-sort="budget">{{__('Number')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Priority')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                                        @if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                                            <th scope="col">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($cases as $case)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('commoncases.show',$case->id) }}" data-ajax-popup="true" data-title="{{__('Cases Details')}}" class="action-item">
                                                    {{ $case->name }}
                                                </a>
                                            </td>
                                            <td class="budget">
                                                <a href="#">{{ $case->number }}</a>
                                            </td>
                                            <td>
                                                @if($case->status == 0)
                                                    <span class="badge badge-success">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @elseif($case->status == 1)
                                                    <span class="badge badge-info">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @elseif($case->status == 2)
                                                    <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @elseif($case->status == 3)
                                                    <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @elseif($case->status == 4)
                                                    <span class="badge badge-danger">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @elseif($case->status == 5)
                                                    <span class="badge badge-warning">{{ __(\App\CommonCase::$status[$case->status]) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($case->priority == 0)
                                                    <span class="badge badge-primary">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                                                @elseif($case->priority == 1)
                                                    <span class="badge badge-info">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                                                @elseif($case->priority == 2)
                                                    <span class="badge badge-warning">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                                                @elseif($case->priority == 3)
                                                    <span class="badge badge-danger">{{ __(\App\CommonCase::$priority[$case->priority]) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->dateFormat($case->created_at->diffForHumans())}}</span>
                                            </td>
                                            @if(Gate::check('Show CommonCase') || Gate::check('Edit CommonCase') || Gate::check('Delete CommonCase'))
                                            <td>
                                                <div class="d-flex">
                                                    @can('Show CommonCase')
                                                    <a href="#" data-size="lg" data-url="{{ route('commoncases.show',$case->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Cases Details')}}" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('Edit CommonCase')
                                                    <a href="{{ route('commoncases.edit',$case->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Cases Edit')}}"><i class="far fa-edit"></i></a>
                                                    @endcan
                                                    @can('Delete CommonCase')
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$case->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['commoncases.destroy', $case->id],'id'=>'delete-form-'.$case ->id]) !!}
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </div>
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
            <!--account cases end-->

            <!--account Documents -->
            <div id="accountdocuments" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Documents')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('document.create',['account',$account->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Documents')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col" class="sort" data-sort="budget">{{__('File')}}</th>
                                        <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                                        <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                                        @if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                            <th scope="col">{{__('Action')}}</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-title="{{__('Document Details')}}" class="action-item">
                                                    {{ $document->name }}</a>
                                            </td>
                                            <td class="budget">
                                                @if(!empty($document->attachment))
                                                    <a href="{{ asset(Storage::url('upload/profile')).'/'.$document->attachment }}" download=""><i class="fas fa-download"></i></a>
                                                @else
                                                    <span>
                                                        {{ __('No File') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->status == 0)
                                                    <span class="badge badge-success">{{ __(\App\Document::$status[$document->status]) }}</span>
                                                @elseif($document->status == 1)
                                                    <span class="badge badge-warning">{{ __(\App\Document::$status[$document->status]) }}</span>
                                                @elseif($document->status == 2)
                                                    <span class="badge badge-danger">{{ __(\App\Document::$status[$document->status]) }}</span>
                                                @elseif($document->status == 3)
                                                    <span class="badge badge-danger">{{ __(\App\Document::$status[$document->status]) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">{{\Auth::user()->dateFormat($document->created_at->diffForHumans())}}</span>
                                            </td>
                                            @if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                                            <td>
                                                <div class="d-flex">
                                                    @can('Show Document')
                                                    <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Document Details')}}" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('Edit Document')
                                                    <a href="{{ route('document.edit',$document->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Document Edit')}}"><i class="far fa-edit"></i></a>
                                                    @endcan
                                                    @can('Delete Document')
                                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$document->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['document.destroy', $document->id],'id'=>'delete-form-'.$document ->id]) !!}
                                                    {!! Form::close() !!}
                                                    @endcan
                                                </div>
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
            <!--account Documents end-->

            <!--account Tasks -->
            <div id="accounttasks" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{__('Tasks')}}</h6>
                            </div>
                            <div class="text-right">
                                <div class="actions">
                                    <a href="#" data-size="lg" data-url="{{ route('task.create') }}" data-ajax-popup="true" data-title="{{__('Create New Task')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-wrapper p-3">
                        <!-- Files -->
                        <div class="mb-3">

                            <table class="table align-items-center dataTable">
                                <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                                    <th scope="col" class="sort" data-sort="budget">{{__('Parent')}}</th>
                                    <th scope="col" class="sort" data-sort="status">{{__('Stage')}}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{__('Date Start')}}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                                    @if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                        <th scope="col">{{__('Action')}}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody class="list">
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Task Details')}}" class="action-item">
                                                {{ $task->name }}
                                            </a>
                                        </td>
                                        <td class="budget">
                                            <a href="#">{{ $task->parent }}</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{  !empty($task->stages)?$task->stages->name:'' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($task->start_date)}}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{  !empty($task->assign_user)?$task->assign_user->name:'-' }}</span>
                                        </td>
                                        @if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                                        <td>
                                            <div class="d-flex">
                                                @can('Show Task')
                                                <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Task Details')}}" class="action-item">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('Edit Task')
                                                <a href="{{ route('task.edit',$task->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Task')}}"><i class="far fa-edit"></i></a>
                                                @endcan
                                                @can('Delete Task')
                                                <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$task->id}}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id],'id'=>'delete-form-'.$task ->id]) !!}
                                                {!! Form::close() !!}
                                                @endcan
                                            </div>
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
            <!--account Tasks end-->

        </div>
    </div>
@endsection
@push('script-page')

    <script>

        $(document).on('change', 'select[name=parent]', function () {
            console.log('click');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log('getparent', bid);
            $.ajax({
                url: '{{route('task.getparent')}}',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    console.log('get data');
                    console.log(data);
                    $('#parent_id').empty();
                    {{--$('#parent_id').append('<option value="">{{__('Select Parent')}}</option>');--}}

                    $.each(data, function (key, value) {
                        $('#parent_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                    if (data == '') {
                        $('#parent_id').empty();
                    }
                }
            });
        }
    </script>
@endpush
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function () {
            console.log('hi');
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })
    </script>
@endpush
