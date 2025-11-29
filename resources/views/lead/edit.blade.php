@extends('layouts.admin')
@section('page-title')
    {{__('Lead')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Lead Edit')}} {{ '('. $lead->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    @if($lead->is_converted != 0)
        <a href="#" data-url="{{route('account.show',$lead->is_converted)}}" data-title="{{__('Account Details')}}" data-size="lg" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Lead Already in Account')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fas fa-eye"></i>
        </a>
    @else
        <a href="#" data-url="{{ route('lead.convert.account',$lead->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Convert ['.$lead->name.'] To Account')}}" data-toggle="tooltip" data-original-title="{{__('Create Account for Lead')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fas fa-exchange-alt">
            </i>
        </a>
    @endif

    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('lead.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('lead.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
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
    <li class="breadcrumb-item"><a href="{{route('lead.index')}}">{{__('Lead')}}</a></li>
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
                                <p class="mb-0 text-sm">{{__('Edit Lead Information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#account_stream" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-rss"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Comment')}}</a>
                                <p class="mb-0 text-sm">{{__('Add comment to lead')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accounttasks" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-tasks"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Tasks')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned tasks for this lead')}}</p>
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
                        {{Form::model($lead,array('route' => array('lead.update', $lead->id), 'method' => 'PUT')) }}
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
                                {{Form::label('account',__('Account')) }}
                                {!! Form::select('account', $account, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
                                @error('account_id')
                                <span class="invalid-account_id" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('email',__('Email')) }}
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
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
                                    {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
                                    @error('phone')
                                    <span class="invalid-phone" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('title',__('Title')) }}
                                    {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))}}
                                    @error('title')
                                    <span class="invalid-phone" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('website',__('Referral Url')) }}
                                    {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter  Referral URL')))}}
                                    @error('website')
                                    <span class="invalid-website" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('lead_address',__('Address')) }}
                                    {{Form::text('lead_address',null,array('class'=>'form-control','placeholder'=>__('Ente Address')))}}
                                    @error('lead_address')
                                    <span class="invalid-lead_address" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('lead_city',__('City')) }}
                                    {{Form::text('lead_city',null,array('class'=>'form-control','placeholder'=>__('Enter City/Town')))}}
                                    @error('lead_city')
                                    <span class="invalid-lead_city" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-4" style="display:none;">
                                <div class="form-group">
                                    {{Form::label('lead_state',__('Lead State')) }}
                                    {{Form::text('lead_state',null,array('class'=>'form-control','placeholder'=>__('Enter Billing City') ))}}
                                    @error('lead_state')
                                    <span class="invalid-lead_state" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4" style="display:none; ">
                                <div class="form-group">
                                    {{Form::label('lead_postalcode',__('Lead Postal Code')) }}
                                    {{Form::text('lead_postalcode',null,array('class'=>'form-control','placeholder'=>__('Enter Billing City') ))}}
                                    @error('lead_postalcode')
                                    <span class="invalid-lead_postalcode" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    {{Form::label('lead_country',__('Country')) }}
                                    {!! Form::select('lead_country', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
                
                                    @error('lead_country')
                                    <span class="invalid-lead_country" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('status',__('Lead Status')) }}
                                    {!! Form::select('status', $status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('status')
                                    <span class="invalid-status" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('stage',__('Lead Response')) }}
                                    {!! Form::select('lead_temperature', $leadtemperature, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('source')
                                    <span class="invalid-source" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('source',__('Lead Source')) }}
                                    {!! Form::select('source', $source, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('source')
                                    <span class="invalid-source" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('opportunity_amount',__('Deal Amount')) }}
                                    {!! Form::text('opportunity_amount', null,array('class' => 'form-control')) !!}
                                    @error('source')
                                    <span class="invalid-opportunity_amount" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('campaign',__('Campaign')) }}
                                    {!! Form::select('campaign', $campaign, null,array('class' =>'form-control','data-toggle'=>'select')) !!}
                                    @error('campaign')
                                    <span class="invalid-campaign" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6" >
                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="call_made" class="form-check-input" type="checkbox"   <?php if($lead->call_made == 1){echo "checked";} ?> >
                                        <label class="form-check-label" for="flexCheckDefault">
                                          Call Made
                                        </label>
                                      </div>
                                   
                                </div>

                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="mail_sent" class="form-check-input" type="checkbox"    <?php if($lead->mail_sent == 1){echo "checked";} ?> >
                                        <label class="form-check-label">
                                         Mail Sent
                                        </label>
                                        
                                      </div>
                                </div>


                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="visited_site" class="form-check-input" type="checkbox"    <?php if($lead->visited_site == 1){echo "checked";} ?> >
                                        <label class="form-check-label" >
                                        Visited Site
                                        </label>
                                      </div>
                                </div>


                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="offer_letter" class="form-check-input" type="checkbox"   <?php if($lead->offer_letter == 1){echo "checked";} ?>>
                                        <label class="form-check-label">
                                        Offer Letter
                                        </label>
                                      </div>
                                </div>

                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="contract" class="form-check-input" type="checkbox" <?php if($lead->contract == 1){echo "checked";} ?> >
                                        <label class="form-check-label">
                                       Contract Sent
                                        </label>
                                      </div>
                                </div>


                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="payment" class="form-check-input" type="checkbox"    <?php if($lead->payment_made == 1){echo "checked";} ?> >
                                        <label class="form-check-label">
                                        Payment Made
                                        </label>
                                      </div>
                                </div>


                                <div class="form-group"> 
                                    <div class="form-check">
                                        <input name="receipt" class="form-check-input" type="checkbox"    <?php if($lead->receipt == 1){echo "checked";} ?> >
                                        <label class="form-check-label" >
                                        Receipt Sent
                                        </label>
                                      </div>
                                </div>

                                
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {!! Form::textarea('description',null,array('class' =>'form-control','rows'=>3)) !!}
                                    @error('description')
                                    <span class="invalid-description" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('user',__('Assigned User')) }}
                                    {!! Form::select('user', $user, $lead->user_id,array('class' => 'form-control','data-toggle'=>'select')) !!}
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
                        {{Form::open(array('route' => array('streamstore',['lead',$lead->name,$lead->id]), 'method' => 'post','enctype'=>'multipart/form-data')) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">

                                    {{Form::text('stream_comment',null,array('class'=>'form-control','placeholder'=>__('Enter Comment'),'required'=>'required'))}}
                                </div>
                            </div>
                            <input type="hidden" name="log_type" value="lead comment">
                            <div class="col-12 mb-3 field" data-name="attachments" style="display:none;">
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
                                        @if($remark->data_id == $lead->id)
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
                                            <span class="badge badge-dot">{{  !empty($task->assign_user)?$task->assign_user->name:'' }}</span>
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
            console.log('h');
            var parent = $(this).val();
            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{route('task.getparent')}}',
                type: 'POST',
                data: {
                    "parent": bid, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
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

 
