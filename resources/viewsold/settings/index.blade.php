@extends('layouts.admin')
@php
    $logo=asset(Storage::url('uploads/logo/'));
       $company_logo=Utility::getValByName('company_logo');
       $company_small_logo=Utility::getValByName('company_small_logo');
       $company_favicon=Utility::getValByName('company_favicon');
   $lang=\App\Utility::getValByName('default_language');
@endphp
@push('css-page')
@endpush
@push('script-page')
    <script>
        $(document).ready(function () {
            $('.list-group-item').on('click', function () {
                var href = $(this).attr('data-href');
                $('.tabs-card').addClass('d-none');
                $(href).removeClass('d-none');
                $('#tabs .list-group-item').removeClass('text-primary');
                $(this).addClass('text-primary');
            });
        });
    </script>
    <script>
        $(document).on("change", "select[name='quote_template'], input[name='quote_color']", function () {
            var template = $("select[name='quote_template']").val();
            var color = $("input[name='quote_color']:checked").val();
            $('#quote_frame').attr('src', '{{url('/quote/preview')}}/' + template + '/' + color);
        });
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '{{url('/invoice/preview')}}/' + template + '/' + color);
        });
        $(document).on("change", "select[name='salesorder_template'], input[name='salesorder_color']", function () {
            var template = $("select[name='salesorder_template']").val();
            var color = $("input[name='salesorder_color']:checked").val();
            $('#salesorder_frame').attr('src', '{{url('/salesorder/preview')}}/' + template + '/' + color);
        });
    </script>
@endpush
@section('page-title')
    {{__('Settings')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">   {{__('Settings')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Settings')}}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <ul class="nav nav-tabs nav-overflow profile-tab-list" role="tablist">
                    @if(\Auth::user()->type=='super admin')
                        <li class="nav-item ml-4">
                            <a href="#business-setting" id="business-setting_tab" class="nav-link active" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">
                                <i class="fas fa-sitemap mr-2"></i>{{__('Site Setting')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <li class="nav-item ml-4">
                            <a href="#email-setting" id="email-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-mail-bulk mr-2"></i>{{__('Mailer Settings')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <li class="nav-item ml-4">
                            <a href="#pusher-setting" id="pusher-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-comment-dots mr-2"></i>{{__('Pusher Settings')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <li class="nav-item ml-4">
                            <a href="#payment-setting" id="payment-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-money-check-alt mr-2"></i>{{__('Payment Settings')}}
                            </a>
                        </li>
                    @endif



                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#company-business-setting" id="company-business-setting_tab" class="nav-link active" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-sitemap mr-2"></i>{{__('Site Setting')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#company-setting" id="company-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="far fa-building mr-2"></i>{{__('Company Settings')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#system-setting" id="system-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-cogs mr-2"></i>{{__('System Settings')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#quote-setting" id="quote-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-receipt mr-2"></i>{{__('Quote Settings')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#invoice-setting" id="invoice-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>{{__('Invoice Setting')}}
                            </a>
                        </li>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <li class="nav-item ml-4">
                            <a href="#salesorder-setting" id="salesorder-setting_tab" class="nav-link" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                <i class="fas fa-money-check-alt mr-2"></i>{{__('Sales Order Settings')}}
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(\Auth::user()->type=='super admin')
                        <div class="tab-pane fade active show" id="business-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="logo" class="form-control-label">{{ __('Logo') }}</label>
                                            <input type="file" name="logo" id="logo" class="custom-input-file">
                                            <label for="logo">
                                                <i class="fa fa-upload"></i>
                                                <span>{{__('Choose a file')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="logo-div">
                                            <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}" width="170px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="favicon" class="form-control-label">{{ __('Favicon') }}</label>
                                            <input type="file" name="favicon" id="favicon" class="custom-input-file">
                                            <label for="favicon">
                                                <i class="fa fa-upload"></i>
                                                <span>{{__('Choose a file')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        <div class="logo-div">
                                            <img src="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" width="50px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @error('logo')
                                        <div class="row">
                                    <span class="invalid-logo" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                     </span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('title_text',__('Title Text')) }}
                                        {{Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))}}
                                        @error('title_text')
                                        <span class="invalid-title_text" role="alert">
                                     <strong class="text-danger">{{ $message }}</strong>
                                 </span>
                                        @enderror
                                    </div>
                                    @if(\Auth::user()->type=='super admin')
                                        <div class="form-group col-md-6">
                                            {{Form::label('footer_text',__('Footer Text')) }}
                                            {{Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))}}
                                            @error('footer_text')
                                            <span class="invalid-footer_text" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                     </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('default_language',__('Default Language')) }}
                                            <div class="changeLanguage">
                                                <select name="default_language" id="default_language" class="form-control custom-select" data-toggle="select">
                                                    @foreach(\App\Utility::languages() as $language)
                                                        <option @if($lang == $language) selected @endif value="{{$language }}">{{Str::upper($language)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('display_landing_page_',__('Landing Page Display')) }}
                                            <div class="col-12 mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="display_landing_page" id="display_landing_page" {{ $settings['display_landing_page'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label form-control-label" for="display_landing_page"></label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_1',__('Footer Link Title 1')) }}
                                        {{Form::text('footer_link_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 1')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_1',__('Footer Link href 1')) }}
                                        {{Form::text('footer_value_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 1')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_2',__('Footer Link Title 2')) }}
                                        {{Form::text('footer_link_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 2')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_2',__('Footer Link href 2')) }}
                                        {{Form::text('footer_value_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 2')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_3',__('Footer Link Title 3')) }}
                                        {{Form::text('footer_link_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 3')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_3',__('Footer Link href 3')) }}
                                        {{Form::text('footer_value_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 3')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <div class="tab-pane fade" id="email-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::open(array('route'=>'email.setting','method'=>'post'))}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_driver',__('Mail Driver')) }}
                                        {{Form::text('mail_driver',env('MAIL_DRIVER'),array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))}}
                                        @error('mail_driver')
                                        <span class="invalid-mail_driver" role="alert">
                                     <strong class="text-danger">{{ $message }}</strong>
                                     </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_host',__('Mail Host')) }}
                                        {{Form::text('mail_host',env('MAIL_HOST'),array('class'=>'form-control ','placeholder'=>__('Enter Mail Driver')))}}
                                        @error('mail_host')
                                        <span class="invalid-mail_driver" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_port',__('Mail Port')) }}
                                        {{Form::text('mail_port',env('MAIL_PORT'),array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))}}
                                        @error('mail_port')
                                        <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_username',__('Mail Username')) }}
                                        {{Form::text('mail_username',env('MAIL_USERNAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))}}
                                        @error('mail_username')
                                        <span class="invalid-mail_username" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_password',__('Mail Password')) }}
                                        {{Form::text('mail_password',env('MAIL_PASSWORD'),array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))}}
                                        @error('mail_password')
                                        <span class="invalid-mail_password" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_encryption',__('Mail Encryption')) }}
                                        {{Form::text('mail_encryption',env('MAIL_ENCRYPTION'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                        @error('mail_encryption')
                                        <span class="invalid-mail_encryption" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_from_address',__('Mail From Address')) }}
                                        {{Form::text('mail_from_address',env('MAIL_FROM_ADDRESS'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))}}
                                        @error('mail_from_address')
                                        <span class="invalid-mail_from_address" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('mail_from_name',__('Mail From Name')) }}
                                        {{Form::text('mail_from_name',env('MAIL_FROM_NAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                        @error('mail_from_name')
                                        <span class="invalid-mail_from_name" role="alert">
                                                 <strong class="text-danger">{{ $message }}</strong>
                                                 </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <a href="#" data-url="{{route('test.mail' )}}" data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" class="btn btn-sm btn-info rounded-pill">
                                            {{__('Send Test Mail')}}
                                        </a>
                                    </div>
                                    <div class="form-group col-md-6 text-right">
                                        {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                                    </div>
                                </div>
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <div class="tab-pane fade" id="pusher-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::model($settings,array('route'=>'pusher.setting','method'=>'post'))}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('pusher_app_id *',__('Pusher App Id *')) }}
                                        {{Form::text('pusher_app_id',env('PUSHER_APP_ID'),array('class'=>'form-control font-style'))}}
                                        @error('pusher_app_id')
                                        <span class="invalid-pusher_app_id" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('pusher_app_key',__('Pusher App Key')) }}
                                        {{Form::text('pusher_app_key',env('PUSHER_APP_KEY'),array('class'=>'form-control font-style'))}}
                                        @error('pusher_app_key')
                                        <span class="invalid-pusher_app_key" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('pusher_app_secret',__('Pusher App Secret')) }}
                                        {{Form::text('pusher_app_secret',env('PUSHER_APP_SECRET'),array('class'=>'form-control font-style'))}}
                                        @error('pusher_app_secret')
                                        <span class="invalid-pusher_app_secret" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('pusher_app_cluster',__('Pusher App Cluster')) }}
                                        {{Form::text('pusher_app_cluster',env('PUSHER_APP_CLUSTER'),array('class'=>'form-control font-style'))}}
                                        @error('pusher_app_cluster')
                                        <span class="invalid-pusher_app_cluster" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='super admin')
                        <div class="tab-pane fade" id="payment-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <div class="card-body">
                                {{Form::open(array('route'=>'payment.setting','method'=>'post'))}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('currency_symbol',__('Currency Symbol *')) }}
                                            {{Form::text('currency_symbol',env('CURRENCY_SYMBOL'),array('class'=>'form-control','required'))}}
                                            @error('currency_symbol')
                                            <span class="invalid-currency_symbol" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('currency',__('Currency *')) }}
                                            {{Form::text('currency',env('CURRENCY'),array('class'=>'form-control font-style','required'))}}
                                            <small> {{__('Note: Add currency code as per three-letter ISO code.')}}<br> <a href="https://stripe.com/docs/currencies" target="_blank">{{__('you can find out here..')}}</a></small> <br>
                                            @error('currency')
                                            <span class="invalid-currency" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <div class="col-6 py-2">
                                        <h5 class="h5">{{__('Stripe')}}</h5>
                                    </div>
                                    <div class="col-6 py-2 text-right">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="enable_stripe" id="enable_stripe" {{ env('ENABLE_STRIPE') == 'on' ? 'checked="checked"' : '' }}>
                                            <label class="custom-control-label form-control-label" for="enable_stripe">{{__('Enable Stripe')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('stripe_key',__('Stripe Key')) }}
                                            {{Form::text('stripe_key',env('STRIPE_KEY'),['class'=>'form-control','placeholder'=>__('Enter Stripe Key')])}}
                                            @error('stripe_key')
                                            <span class="invalid-stripe_key" role="alert">
                                             <strong class="text-danger">{{ $message }}</strong>
                                         </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('stripe_secret',__('Stripe Secret')) }}
                                            {{Form::text('stripe_secret',env('STRIPE_SECRET'),['class'=>'form-control ','placeholder'=>__('Enter Stripe Secret')])}}
                                            @error('stripe_secret')
                                            <span class="invalid-stripe_secret" role="alert">
                                             <strong class="text-danger">{{ $message }}</strong>
                                         </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <div class="col-6 py-2">
                                        <h5 class="h5">{{__('PayPal')}}</h5>
                                    </div>
                                    <div class="col-6 py-2 text-right">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" name="enable_paypal" id="enable_paypal" {{ env('ENABLE_PAYPAL') == 'on' ? 'checked="checked"' : '' }}>
                                            <label class="custom-control-label form-control-label" for="enable_paypal">{{__('Enable Paypal')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pb-4">
                                        <label class="paypal-label form-control-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-primary btn-sm active">
                                                <input type="radio" name="paypal_mode" value="sandbox" {{ env('PAYPAL_MODE') == '' || env('PAYPAL_MODE') == 'sandbox' ? 'checked="checked"' : '' }}>{{__('Sandbox')}}
                                            </label>
                                            <label class="btn btn-primary btn-sm ">
                                                <input type="radio" name="paypal_mode" value="live" {{ env('PAYPAL_MODE') == 'live' ? 'checked="checked"' : '' }}>{{__('Live')}}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paypal_client_id">{{ __('Client ID') }}</label>
                                            <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{env('PAYPAL_CLIENT_ID')}}" placeholder="{{ __('Client ID') }}"/>
                                            @if ($errors->has('paypal_client_id'))
                                                <span class="invalid-feedback d-block">
                                            {{ $errors->first('paypal_client_id') }}
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="paypal_secret_key">{{ __('Secret Key') }}</label>
                                            <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{env('PAYPAL_SECRET_KEY')}}" placeholder="{{ __('Secret Key') }}"/>
                                            @if ($errors->has('paypal_secret_key'))
                                                <span class="invalid-feedback d-block">
                                            {{ $errors->first('paypal_secret_key') }}
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                                </div>
                                {{Form::close()}}
                            </div>
                        </div>
                    @endif


                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade active show" id="company-business-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="full_logo" class="form-control-label">{{ __('Logo') }}</label>
                                            <input type="file" name="full_logo" id="full_logo" class="custom-input-file">
                                            <label for="full_logo">
                                                <i class="fa fa-upload"></i>
                                                <span>{{__('Choose a file')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                        <div class="logo-div">
                                            <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}" width="170px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="favicon" class="form-control-label">{{ __('Favicon') }}</label>
                                            <input type="file" name="favicon" id="favicon" class="custom-input-file">
                                            <label for="favicon">
                                                <i class="fa fa-upload"></i>
                                                <span>{{__('Choose a file')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6 d-flex align-items-center justify-content-center mt-3">
                                        <div class="logo-div">
                                            <img src="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')}}" width="50px" class="img_setting">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @error('logo')
                                        <div class="row">
                                    <span class="invalid-logo" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                     </span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('title_text',__('Title Text')) }}
                                        {{Form::text('title_text',null,array('class'=>'form-control','placeholder'=>__('Title Text')))}}
                                        @error('title_text')
                                        <span class="invalid-title_text" role="alert">
                                     <strong class="text-danger">{{ $message }}</strong>
                                 </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_text',__('Footer Text')) }}
                                        {{Form::text('footer_text',null,array('class'=>'form-control','placeholder'=>__('Footer Text')))}}
                                        @error('footer_text')
                                        <span class="invalid-footer_text" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                     </span>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_1',__('Footer Link Title 1')) }}
                                        {{Form::text('footer_link_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 1')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_1',__('Footer Link href 1')) }}
                                        {{Form::text('footer_value_1',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 1')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_2',__('Footer Link Title 2')) }}
                                        {{Form::text('footer_link_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 2')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_2',__('Footer Link href 2')) }}
                                        {{Form::text('footer_value_2',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 2')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_link_3',__('Footer Link Title 3')) }}
                                        {{Form::text('footer_link_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link Title 3')))}}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_value_3',__('Footer Link href 3')) }}
                                        {{Form::text('footer_value_3',null,array('class'=>'form-control','placeholder'=>__('Enter Footer Link 3')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::model($settings,array('route'=>'company.setting','method'=>'post'))}}
                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_name *',__('Company Name *')) }}
                                        {{Form::text('company_name',null,array('class'=>'form-control font-style'))}}
                                        @error('company_name')
                                        <span class="invalid-company_name" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_address',__('Address')) }}
                                        {{Form::text('company_address',null,array('class'=>'form-control font-style'))}}
                                        @error('company_address')
                                        <span class="invalid-company_address" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_city',__('City')) }}
                                        {{Form::text('company_city',null,array('class'=>'form-control font-style'))}}
                                        @error('company_city')
                                        <span class="invalid-company_city" role="alert">
                                                                    <strong class="text-danger">{{ $message }}</strong>
                                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_state',__('State')) }}
                                        {{Form::text('company_state',null,array('class'=>'form-control font-style'))}}
                                        @error('company_state')
                                        <span class="invalid-company_state" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_zipcode',__('Zip/Post Code')) }}
                                        {{Form::text('company_zipcode',null,array('class'=>'form-control'))}}
                                        @error('company_zipcode')
                                        <span class="invalid-company_zipcode" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group  col-md-6">
                                        {{Form::label('company_country',__('Country')) }}
                                        {{Form::text('company_country',null,array('class'=>'form-control font-style'))}}
                                        @error('company_country')
                                        <span class="invalid-company_country" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_telephone',__('Telephone')) }}
                                        {{Form::text('company_telephone',null,array('class'=>'form-control'))}}
                                        @error('company_telephone')
                                        <span class="invalid-company_telephone" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_email',__('System Email *')) }}
                                        {{Form::text('company_email',null,array('class'=>'form-control'))}}
                                        @error('company_email')
                                        <span class="invalid-company_email" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('company_email_from_name',__('Email (From Name) *')) }}
                                        {{Form::text('company_email_from_name',null,array('class'=>'form-control font-style'))}}
                                        @error('company_email_from_name')
                                        <span class="invalid-company_email_from_name" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="orders-tab">
                            {{Form::model($settings,array('route'=>'system.setting','method'=>'post'))}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{Form::label('site_currency',__('Currency *')) }}
                                        {{Form::text('site_currency',null,array('class'=>'form-control font-style'))}}
                                        @error('site_currency')
                                        <span class="invalid-site_currency" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('site_currency_symbol',__('Currency Symbol *')) }}
                                        {{Form::text('site_currency_symbol',null,array('class'=>'form-control'))}}
                                        @error('site_currency_symbol')
                                        <span class="invalid-site_currency_symbol" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="example3cols3Input">{{__('Currency Symbol Position')}}</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio mb-3">

                                                        <input type="radio" id="customRadio5" name="site_currency_symbol_position" value="pre" class="custom-control-input" @if(@$settings['site_currency_symbol_position'] == 'pre') checked @endif>
                                                        <label class="custom-control-label" for="customRadio5">{{__('Pre')}}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-radio mb-3">
                                                        <input type="radio" id="customRadio6" name="site_currency_symbol_position" value="post" class="custom-control-input" @if(@$settings['site_currency_symbol_position'] == 'post') checked @endif>
                                                        <label class="custom-control-label" for="customRadio6">{{__('Post')}}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_date_format" class="form-control-label">{{__('Date Format')}}</label>
                                        <select type="text" name="site_date_format" class="form-control selectric" id="site_date_format">
                                            <option value="M j, Y" @if(@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015</option>
                                            <option value="d-m-Y" @if(@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>d-m-y</option>
                                            <option value="m-d-Y" @if(@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>m-d-y</option>
                                            <option value="Y-m-d" @if(@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>y-m-d</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="site_time_format" class="form-control-label">{{__('Time Format')}}</label>
                                        <select type="text" name="site_time_format" class="form-control selectric" id="site_time_format">
                                            <option value="g:i A" @if(@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM</option>
                                            <option value="g:i a" @if(@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm</option>
                                            <option value="H:i" @if(@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{Form::label('quote_prefix',__('Quote Prefix')) }}
                                        {{Form::text('quote_prefix',null,array('class'=>'form-control'))}}
                                        @error('quote_prefix')
                                        <span class="invalid-quote_prefix" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('salesorder_prefix',__('Sales Order Prefix')) }}
                                        {{Form::text('salesorder_prefix',null,array('class'=>'form-control'))}}
                                        @error('salesorder_prefix')
                                        <span class="invalid-salesorder_prefix" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('invoice_prefix',__('Invoice Prefix')) }}
                                        {{Form::text('invoice_prefix',null,array('class'=>'form-control'))}}
                                        @error('invoice_prefix')
                                        <span class="invalid-invoice_prefix" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('footer_title',__('Quote/SalesOrder/Invoice Footer Title')) }}
                                        {{Form::text('footer_title',null,array('class'=>'form-control'))}}
                                        @error('footer_title')
                                        <span class="invalid-footer_title" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('shipping_display',__('Quote / Invoice / Sales-Order Shipping Display')) }}
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="shipping_display" class="custom-control-input" id="shipping_display" {{ $settings['shipping_display']=='on' ? 'checked="checked"' : '' }}>
                                            <label name="shipping_display" class="custom-control-label form-control-label" for="shipping_display"></label>
                                        </div>
                                        @error('shipping_display')
                                        <span class="invalid-shipping_display" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-12">
                                        {{Form::label('footer_notes',__('Quote/SalesOrder/Invoice Footer Notes')) }}
                                        {{Form::textarea('footer_notes', null, ['class'=>'form-control','rows'=>'3'])}}
                                        @error('footer_notes')
                                        <span class="invalid-footer_notes" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                {{Form::submit(__('Save Change'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
                            </div>
                            {{Form::close()}}
                        </div>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade" id="quote-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="{{route('quote.template.setting')}}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="{{route('quote.template.setting')}}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="address">{{__('Quote Template')}}</label>
                                                    <select class="form-control" name="quote_template" data-toggle="select">
                                                        @foreach(Utility::templateData()['templates'] as $key => $template)
                                                            <option value="{{$key}}" {{(isset($settings['quote_template']) && $settings['quote_template'] == $key) ? 'selected' : ''}}> {{$template}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">{{__('Color Input')}}</label>
                                                    <div class="row gutters-xs">
                                                        @foreach(Utility::templateData()['colors'] as $key => $color)
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="quote_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['quote_color']) && $settings['quote_color'] == $color) ? 'checked' : ''}}>
                                                                    <span class="colorinput-color" style="background:#{{$color}}"></span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    {{__('Save')}}
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            @if(isset($settings['quote_template']) && isset($settings['quote_color']))
                                                <iframe id="quote_frame" class="w-100 h-1450" frameborder="0" src="{{route('quote.preview',[$settings['quote_template'],$settings['quote_color']])}}"></iframe>
                                            @else
                                                <iframe id="quote_frame" class="w-100 h-1450" frameborder="0" src="{{route('quote.preview',['template1','fffff'])}}"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade" id="invoice-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="{{route('invoice.template.setting')}}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="{{route('invoice.template.setting')}}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="address">{{__('Invoice Template')}}</label>
                                                    <select class="form-control" name="invoice_template" data-toggle="select">
                                                        @foreach(Utility::templateData()['templates'] as $key => $template)
                                                            <option value="{{$key}}" {{(isset($settings['invoice_template']) && $settings['invoice_template'] == $key) ? 'selected' : ''}}> {{$template}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">{{__('Color Input')}}</label>
                                                    <div class="row gutters-xs">
                                                        @foreach(Utility::templateData()['colors'] as $key => $color)
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="invoice_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['invoice_color']) && $settings['invoice_color'] == $color) ? 'checked' : ''}}>
                                                                    <span class="colorinput-color" style="background:#{{$color}}"></span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    {{__('Save')}}
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            @if(isset($settings['invoice_template']) && isset($settings['invoice_color']))
                                                <iframe id="invoice_frame" class="w-100 h-1450" frameborder="0" src="{{route('invoice.preview',[$settings['invoice_template'],$settings['invoice_color']])}}"></iframe>
                                            @else
                                                <iframe id="invoice_frame" class="w-100 h-1450" frameborder="0" src="{{route('invoice.preview',['template1','fffff'])}}"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                    @if(\Auth::user()->type=='owner')
                        <div class="tab-pane fade" id="salesorder-setting" role="tabpanel" aria-labelledby="orders-tab">
                            <form id="setting-form" method="post" action="{{route('salesorder.template.setting')}}">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <form id="setting-form" method="post" action="{{route('salesorder.template.setting')}}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="address">{{__('Sales Order Template')}}</label>
                                                    <select class="form-control" name="salesorder_template" data-toggle="select">
                                                        @foreach(Utility::templateData()['templates'] as $key => $template)
                                                            <option value="{{$key}}" {{(isset($settings['salesorder_template']) && $settings['salesorder_template'] == $key) ? 'selected' : ''}}> {{$template}} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">{{__('Color Input')}}</label>
                                                    <div class="row gutters-xs">
                                                        @foreach(Utility::templateData()['colors'] as $key => $color)
                                                            <div class="col-auto">
                                                                <label class="colorinput">
                                                                    <input name="salesorder_color" type="radio" value="{{$color}}" class="colorinput-input" {{(isset($settings['salesorder_color']) && $settings['salesorder_color'] == $color) ? 'checked' : ''}}>
                                                                    <span class="colorinput-color" style="background: #{{$color}}"></span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-primary rounded-pill">
                                                    {{__('Save')}}
                                                </button>
                                            </form>
                                        </div>
                                        <div class="col-md-10">
                                            @if(isset($settings['salesorder_template']) && isset($settings['salesorder_color']))
                                                <iframe id="salesorder_frame" class="w-100 h-1450" frameborder="0" src="{{route('salesorder.preview',[$settings['salesorder_template'],$settings['salesorder_color']])}}"></iframe>
                                            @else
                                                <iframe id="salesorder_frame" class="w-100 h-1450" frameborder="0" src="{{route('salesorder.preview',['template1','fffff'])}}"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

