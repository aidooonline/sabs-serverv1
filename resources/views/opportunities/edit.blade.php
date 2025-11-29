@extends('layouts.admin')
@section('page-title')
    {{__('Opportunities')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Opportunities Edit')}}  {{ '('. $opportunities->name .')' }}</h5>
    </div>
@endsection
@section('action-btn')
    <div class="btn-group" role="group">
        @if(!empty($previous))
            <a href="{{ route('opportunities.edit',$previous) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @else
            <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action mr-2 disabled" data-toggle="tooltip" data-original-title="{{__('Previous')}}">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif
        @if(!empty($next))
            <a href="{{ route('opportunities.edit',$next) }}" class="btn btn-sm btn-primary btn-icon-only rounded-circle btn-text btn-icon action" data-toggle="tooltip" data-original-title="{{__('Next')}}">
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
    <li class="breadcrumb-item"><a href="{{route('opportunities.index')}}">{{__('Opportunities')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Details')}}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card">
                <div class="list-group list-group-flush" id="tabs">
                    <div data-href="#Opportunities_edit" class="list-group-item custom-list-group-item text-primary">
                        <div class="media">
                            <i class="fas fa-user"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Overview')}}</a>
                                <p class="mb-0 text-sm">{{__('Edit about your opportunities information')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#account_stream" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-rss"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Stream')}}</a>
                                <p class="mb-0 text-sm">{{__('Add stream comment')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accountdocuments" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-book-open"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Documents')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned document for this opportunities')}}</p>
                            </div>
                        </div>
                    </div>
                    <div data-href="#accounttasks" class="list-group-item custom-list-group-item">
                        <div class="media">
                            <i class="fas fa-tasks"></i>
                            <div class="media-body ml-3">
                                <a href="#" class="stretched-link h6 mb-1">{{__('Tasks')}}</a>
                                <p class="mb-0 text-sm">{{__('Assigned task for this opportunities')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <!--Opportunities edit -->
            <div id="Opportunities_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($opportunities,array('route' => array('opportunities.update', $opportunities->id), 'method' => 'PUT')) }}
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
                                {!! Form::select('account', $account_name, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                @error('account')
                                <span class="invalid-account" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('contact',__('Contact')) }}
                                    {!! Form::select('contact', $contact, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                    @error('contacts')
                                    <span class="invalid-contacts" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                {{Form::label('campaign',__('Campaign')) }}
                                {!! Form::select('campaign', $campaign_id, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                @error('campaign_id')
                                <span class="invalid-campaign_id" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{Form::label('stage',__('Stage')) }}
                                {!! Form::select('stage', $stages, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                @error('stage')
                                <span class="invalid-stage" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('amount',__('Amount')) }}
                                    {{Form::number('amount',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
                                    @error('amount')
                                    <span class="invalid-amount" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('probability',__('Probability')) }}
                                    {{Form::number('probability',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
                                    @error('probability')
                                    <span class="invalid-probability" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('close_date',__('Close Date')) }}
                                    {{Form::date('close_date',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
                                    @error('close_date')
                                    <span class="invalid-close_date" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('lead_source',__('Lead Source')) }}
                                    {!! Form::select('lead_source', $lead_source, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
                                    @error('lead_source')
                                    <span class="invalid-lead_source" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('description',__('Description')) }}
                                    {!! Form::textarea('description',null,array('class' =>'form-control ','rows'=>3)) !!}
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
                                {{Form::label('user',__('User')) }}
                                {!! Form::select('user', $user,  $opportunities->user_id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
                                @error('user')
                                <span class="invalid-user" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Update'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--Opportunities edit end-->

            <!--stream edit -->
            <div id="account_stream" class="tabs-card d-none">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Stream')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::open(array('route' => array('streamstore',['opportunities',$opportunities->name,$opportunities->id]), 'method' => 'post','enctype'=>'multipart/form-data')) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {{Form::label('stream',__('Stream')) }}
                                    {{Form::text('stream_comment',null,array('class'=>'form-control','placeholder'=>__('Enter Stream Comment'),'required'=>'required'))}}
                                </div>
                            </div>
                            <input type="hidden" name="log_type" value="opportunities comment">

                            <div class="col-12 mb-3 field" data-name="attachments">
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
                                        @if($remark->data_id == $opportunities->id)
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-parent-child">
                                                        <img alt="" class="rounded-circle avatar" @if(!empty($stream->file_upload)) src="{{(!empty($stream->file_upload))? asset(Storage::url("upload/profile/".$stream->file_upload)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$remark->user_name}}" @endif>
                                                    </div>
                                                    <div class="flex-fill ml-3">
                                                        <div class="h6 text-sm mb-0">{{$remark->user_name}}<small class="float-right text-muted">{{$stream->created_at}}</small></div>
                                                        <span class="text-sm lh-140 mb-0">
                                                            {{ __('posted to') }} <a href="#">{{$remark->title}}</a> , {{$stream->log_type}} <a href="#">{{$remark->stream_comment}}</a>
                                                        </span>
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
                                    <a href="#" data-size="lg" data-url="{{ route('document.create',['opportunities',$opportunities->id]) }}" data-ajax-popup="true" data-title="{{__('Create New Documents')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                                        <th scope="col">{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody class="list">
                                    @foreach($documents as $document)
                                        <tr>
                                            <td>
                                                <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-title="{{__('Document Details')}}" class="badge badge-dot action-item">
                                                    {{ $document->name }}
                                                </a>
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
                                                <span class="badge badge-dot">{{\Auth::user()->dateFormat($document->created_at)}}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('Show Document')
                                                    <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Document Details')}}" class="action-item">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('Edit Document')
                                                    <a href="{{ route('document.edit',$document->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-title="{{__('Edit Document')}}"><i class="far fa-edit"></i></a>
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
                                    <th scope="col" class="sort" data-sort="budget">{{__('Assigned')}}</th>
                                    <th scope="col" class="sort" data-sort="status">{{__('Stage')}}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{__('Date Start')}}</th>
                                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                                    <th scope="col">{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="#" data-size="lg"  data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Task Details')}}" class="badge badge-dot action-item">
                                                {{ $task->name }}
                                            </a>
                                        </td>
                                        <td class="budget">
                                            <a href="#" class="badge badge-dot">{{ $task->parent }}</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{  !empty($task->taskstages)?$task->taskstages->name:'' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($task->start_date)}}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dot">{{  !empty($task->assign_user)?$task->assign_user->name:'' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @can('Show Task')
                                                <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Task Details')}}" class="action-item">
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
