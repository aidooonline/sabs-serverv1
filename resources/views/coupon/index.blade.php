@extends('layouts.admin')
@push('script-page')
    <script>
        $(document).on('click', '.code', function () {
            var type = $(this).find('.icon-input').val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush
@section('page-title')
    {{__('Coupon')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0">{{__('Coupon')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Coupon')}}</li>
@endsection
@section('action-btn')
    <a href="#" data-url="{{ route('coupon.create') }}" data-size="lg" data-ajax-popup="true" data-title="{{__('Create New Coupon')}}" class="btn btn-sm btn-primary btn-icon-only rounded-circle" data-toggle="tooltip">
        <i class="fas fa-plus"></i>
    </a>
@endsection
@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table align-items-center dataTable">
                <thead>
                <tr>
                    <th scope="col" class="sort" data-sort="name"> {{__('Name')}}</th>
                    <th scope="col" class="sort" data-sort="budget">{{__('Code')}}</th>
                    <th scope="col" class="sort" data-sort="status">{{__('Discount (%)')}}</th>
                    <th scope="col">{{__('Limit')}}</th>
                    <th scope="col" class="sort" data-sort="completion"> {{__('Used')}}</th>
                    <th scope="col" class="action text-right">{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($coupons as $coupon)
                    <tr>

                        <td class="budget">{{ $coupon->name }} </td>
                        <td>{{ $coupon->code }}</td>
                        <td>
                            {{ $coupon->discount }}
                        </td>
                        <td>{{ $coupon->limit }}</td>
                        <td>{{ $coupon->used_coupon() }}</td>
                        <td class="text-right">
                            <div class="actions ml-3">
                                <a href="{{ route('coupon.show',$coupon->id) }}" class="action-item" data-toggle="tooltip" title="{{__('View')}}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#!" class="action-item" data-size="lg" data-url="{{ route('coupon.edit',$coupon->id) }}" data-ajax-popup="true" data-title="{{__('Edit Coupon')}}" data-toggle="tooltip" data-original-title="{{__('Edit')}}">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                <a href="#!" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$coupon->id}}').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                {!! Form::open(['method' => 'DELETE', 'route' => ['coupon.destroy', $coupon->id],'id'=>'delete-form-'.$coupon->id]) !!}
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

