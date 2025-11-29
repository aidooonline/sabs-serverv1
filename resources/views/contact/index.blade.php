@extends('layouts.admin')
@section('page-title')
    {{__('Contact')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Contact')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Contact')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('contact.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create Contact')
        <a href="#" data-size="lg" data-url="{{ route('contact.create',['contact',0]) }}" data-ajax-popup="true" data-title="{{__('Create New Contact')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="budget">{{__('Email')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Phone')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('City')}}</th>
                    <th scope="col" class="sort" data-sort="Assigned User">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($contacts as $contact)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('contact.show',$contact->id) }}" data-ajax-popup="true" data-title="{{__('Contact Details')}}" class="action-item">
                                {{ ucfirst($contact->name) }}
                            </a>
                        </td>
                        <td class="budget">
                            <a href="#" class="badge badge-dot">{{ $contact->email }}</a>
                        </td>
                        <td>
                            <span class="badge badge-dot">
                                {{ $contact->phone }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{ ucfirst($contact->contact_city) }}</span>
                        </td>
                        <td>
                            <span class="col-sm-12"><span class="text-sm">{{ ucfirst(!empty($contact->assign_user)?$contact->assign_user->name:'')}}</span></span>
                        </td>
                        @if(Gate::check('Show Contact') || Gate::check('Edit Contact') || Gate::check('Delete Contact'))
                            <td class="text-right">
                                @can('Create Contact')
                                    <a href="#" data-size="lg" data-url="{{ route('contact.show',$contact->id) }}" data-toggle="tooltip" data-original-title="{{__('Details')}}" data-ajax-popup="true" data-title="{{__('Contact Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Contact')
                                    <a href="{{ route('contact.edit',$contact->id) }}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Edit')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Contact')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$contact->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $contact->id],'id'=>'delete-form-'.$contact ->id]) !!}
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
