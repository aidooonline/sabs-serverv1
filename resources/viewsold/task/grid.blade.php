@extends('layouts.admin')
@section('page-title')
    {{__('Task')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Task')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Task')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('task.index') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('List View')}}
    </a>
    @can('Create Task')
        <a href="#" data-size="lg" data-url="{{ route('task.create') }}" data-ajax-popup="true" data-title="{{__('Create New Task')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
            <i class="fa fa-plus"></i>
        </a>
    @endcan
@endsection
@section('filter')
@endsection
@section('content')
    <div class="row">
        @foreach($tasks as $task)
            <div class="col-lg-2 col-sm-6">
                <div class="card hover-shadow-lg">
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" class="rounded-circle avatar" @if(!empty($task->avatar)) src="{{(!empty($task->avatar))? asset(Storage::url("upload/profile/".$task->avatar)): asset(url("./assets/img/clients/160x160/img-1.png"))}}" @else  avatar="{{$task->name}}" @endif>
                        </div>
                        <h5 class="h6 mt-4 mb-1">
                            {{ ucfirst($task->name)}}
                        </h5>
                        <div class="mb-1">
                            {{ ucfirst($task->stages->name)}}    
                        </div>
                    </div>
                    @if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                        <div class="card-footer text-center">
                            <div class="actions d-flex justify-content-between px-4">
                                @can('Show Task')
                                    <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Create New Task')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Task')
                                    <a href="{{ route('task.edit',$task->id) }}" class="action-item" data-title="{{__('Edit Task')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Task')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$task->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id],'id'=>'delete-form-'.$task->id]) !!}
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
@push('script-page')

    <script>

        $(document).on('change', 'select[name=parent]', function () {

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
