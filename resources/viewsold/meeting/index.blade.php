@extends('layouts.admin')
@section('page-title')
    {{__('Meeting')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Meeting')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Meeting')}}</li>
@endsection
@section('action-btn')
    <a href="{{ route('meeting.grid') }}" class="btn btn-sm btn-primary bor-radius ml-4">
        {{__('Grid View')}}
    </a>
    @can('Create Meeting')
        <a href="#" data-size="lg" data-url="{{ route('meeting.create') }}" data-ajax-popup="true" data-title="{{__('Create New Meeting')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle">
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
                    @if(Gate::check('Show Meeting') || Gate::check('Edit Meeting') || Gate::check('Delete Meeting'))
                        <th scope="col" class="text-right">{{__('Action')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody class="list">
                @foreach($meetings as $meeting)
                    <tr>
                        <td>
                            <a href="#" data-size="lg" data-url="{{ route('meeting.show',$meeting->id) }}" data-ajax-popup="true" data-title="{{__('Meeting Details')}}" class="badge badge-dot action-item">
                                {{ ucfirst($meeting->name) }}
                            </a>
                        </td>
                        <td>
                            <a href="#" class="badge badge-dot">{{ ucfirst($meeting->parent) }}</a>
                        </td>
                        <td>
                            @if($meeting->status == 0)
                                <span class="badge badge-success">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                            @elseif($meeting->status == 1)
                                <span class="badge badge-warning">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                            @elseif($meeting->status == 2)
                                <span class="badge badge-danger">{{ __(\App\Meeting::$status[$meeting->status]) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-dot">{{\Auth::user()->dateFormat($meeting->start_date)}}</span>
                        </td>
                        <td>
                            <span class="badge badge-dot">{{  ucfirst(!empty($meeting->assign_user)?$meeting->assign_user->name:'') }}</span>
                        </td>
                        @if(Gate::check('Show Meeting') || Gate::check('Edit Meeting') || Gate::check('Delete Meeting'))
                            <td class="text-right">
                                @can('Show Meeting')
                                <a href="#" data-size="lg" data-url="{{ route('meeting.show',$meeting->id) }}" data-ajax-popup="true" data-title="{{__('Meeting Details')}}" class="action-item">
                                    <i class="far fa-eye"></i>
                                </a>
                                @endcan
                                @can('Edit Meeting')
                                    <a href="{{ route('meeting.edit',$meeting->id) }}" class="action-item" data-title="{{__('Edit Meeting')}}"><i class="far fa-edit"></i></a>
                                @endcan
                                @can('Delete Meeting')
                                    <a href="#" class="action-item " data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$meeting->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['meeting.destroy', $meeting->id],'id'=>'delete-form-'.$meeting ->id]) !!}
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

            $.ajax({
                url: '{{route('meeting.getparent')}}',
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
                }
            });
        }
    </script>
@endpush
