@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).ready(function () {
            $('.cp_link').on('click', function () {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('Success', '{{__('Link Copy on Clipboard')}}', 'success')
            });
        });
    </script>
@endpush
@section('page-title')
    {{__('Form Builder')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Form Builder')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Form Builder')}}</li>
@endsection
@section('action-btn')
    @if(\Auth::user()->can('Create Form Builder'))
        <a href="#" data-url="{{ route('form_builder.create') }}" data-size="md" data-ajax-popup="true" data-title="{{__('Create New Form')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle" data-toggle="tooltip">
            <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
        </a>
    @endif
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th>{{__('Name')}}</th>
                    <th>{{__('Response')}}</th>
                    @if(\Auth::user()->can('Edit Form Builder') || \Auth::user()->can('Delete Form Builder'))
                        <th class="text-right" width="200px">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach ($forms as $form)
                    <tr>
                        <td>{{ $form->name }}</td>
                        <td>
                            {{ $form->response->count() }}
                        </td>
                        @if(\Auth::user()->can('Edit Form Builder') || \Auth::user()->can('Delete Form Builder'))
                            <td class="text-right">
                                <a href="#" data-size="md" data-url="{{ route('form.field.bind',$form->id) }}" data-ajax-popup="true" data-title="{{__('Convert Setting')}}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Convert Setting')}}"><i class="fas fa-exchange-alt"></i></a>
                                <a href="#" class="action-item cp_link" data-link="{{url('/form/'.$form->code)}}" data-toggle="tooltip" data-original-title="{{__('Click to copy link')}}"><i class="fas fa-file"></i></a>
                                @can('Show Form Builder')
                                    <a href="{{route('form_builder.show',$form->id)}}" class="action-item" data-toggle="tooltip" data-original-title="{{__('Form field')}}"><i class="fas fa-table"></i></a>
                                @endcan
                                <a href="{{route('form.response',$form->id)}}" class="action-item" data-toggle="tooltip" data-original-title="{{__('View Response')}}"><i class="fas fa-eye"></i></a>
                                @can('Edit Form Builder')
                                    <a href="#" class="action-item" data-url="{{ route('form_builder.edit',$form->id) }}" data-ajax-popup="true" data-title="{{__('Edit Form')}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endcan
                                @can('Delete Form Builder')
                                    <a href="#!" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-{{$form->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['form_builder.destroy', $form->id],'id'=>'delete-form-'.$form->id]) !!}
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

