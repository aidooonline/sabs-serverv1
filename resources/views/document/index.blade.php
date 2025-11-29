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
    <a href="{{ route('document.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
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
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('File')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Created At')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assign User')}}</th>
                    @if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($documents as $document)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-title="{{__('Document Details')}}" class="action-item">
                                {{ ucfirst($document->name) }}
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
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($document->assign_user)?$document->assign_user->name:'-')}}</span></span>
                        </td>
                        @if(Gate::check('Show Document') || Gate::check('Edit Document') || Gate::check('Delete Document'))
                            <td class="text-right">
                                @can('Show Document')
                                    <a href="#" data-size="lg" data-url="{{ route('document.show',$document->id) }}" data-ajax-popup="true" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-title="{{__('Document Details')}}" class="action-item">
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
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
