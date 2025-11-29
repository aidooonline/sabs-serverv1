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
    <a href="{{ route('task.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
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
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Parent')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Stage')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Date Start')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif

                </tr>
                </thead>
                <tbody class="list">
                @foreach($tasks as $task)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Task Details')}}" class="action-item">
                                 {{ ucfirst($task->name) }}</a>
                        </td>
                        <td class="budget">
                            {{ ucfirst($task->parent) }}
                        </td>
                        <td>
                            <span class="badge badge-dot">{{  ucfirst(!empty($task->stages)?$task->stages->name:'') }}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($task->start_date)}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{  ucfirst(!empty($task->assign_user)?$task->assign_user->name:'') }}</span>
                        </td>
                        @if(Gate::check('Show Task') || Gate::check('Edit Task') || Gate::check('Delete Task'))
                            <td class="text-right">
                                @can('Show Task')
                                    <a href="#" data-size="lg" data-url="{{ route('task.show',$task->id) }}" data-ajax-popup="true" data-title="{{__('Task Details')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Task')
                                    <a href="{{ route('task.edit',$task->id) }}" class="action-item" data-title="{{__('Task Edit ')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Task')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$task->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['task.destroy', $task->id],'id'=>'delete-form-'.$task ->id]) !!}
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
