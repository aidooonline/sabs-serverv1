@extends('layouts.admin')
@section('page-title')
    {{__('Call')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Call')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Call')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('call.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create Call')
        <a href="#" data-size="lg" data-url="{{ route('call.create') }}" data-ajax-popup="true" data-title="{{__('Create New Call')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    <th scope="col" class="sort" data-sort="status">{{__('Status')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Date Start')}}</th>
                    <th scope="col" class="sort" data-sort="completion">{{__('Assigned User')}}</th>
                    @if(Gate::check('Show Call') || Gate::check('Edit Call') || Gate::check('Delete Call'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($calls as $call)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('call.show',$call->id) }}" data-ajax-popup="true" data-title="{{__('show Call')}}" class="action-item">
                                 {{ ucfirst($call->name) }}
                            </a>
                        </td>
                        <td class="budget">
                            {{ ucfirst($call->parent) }}
                        </td>
                        <td>
                            @if($call->status == 0)
                                <span class="badge badge-success">{{ __(\App\Call::$status[$call->status]) }}</span>
                            @elseif($call->status == 1)
                                <span class="badge badge-warning">{{ __(\App\Call::$status[$call->status]) }}</span>
                            @elseif($call->status == 2)
                                <span class="badge badge-danger">{{ __(\App\Call::$status[$call->status]) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($call->start_date)}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{  ucfirst(!empty($call->assign_user)?$call->assign_user->name:'') }}</span>
                        </td>
                        @if(Gate::check('Show Call') || Gate::check('Edit Call') || Gate::check('Delete Call'))
                            <td class="text-right">
                                @can('Show Call')
                                    <a href="#" data-size="lg" data-url="{{ route('call.show',$call->id) }}" data-ajax-popup="true" data-title="{{__('show Call')}}" class="action-item">
                                        <i class="far fa-eye"></i>
                                    </a>
                                @endcan
                                @can('Edit Call')
                                    <a href="{{ route('call.edit',$call->id) }}" class="action-item" data-title="{{__('Edit Call')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Call')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$call->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['call.destroy', $call->id],'id'=>'delete-form-'.$call ->id]) !!}
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
            var parent = $(this).val();

            getparent(parent);
        });

        function getparent(bid) {
            console.log(bid);
            $.ajax({
                url: '{{route('call.getparent')}}',
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
