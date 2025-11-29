@section('action-btn')

<!-- literally user can create accounts -->
@can('Create Product')
<a href="#" data-size="lg" data-url="{{ route('accounts.create') }}" data-ajax-popup="true"
    data-title="{{__('Create New Account')}}" class="btn btn-sm btn-purple btn-icon-only rounded-circle">
    <i class="fa fa-plus"></i>
</a>
@endcan
@endsection



@extends('layouts.admin')

@section('title')

@endsection

@section('action-btn')

@endsection
@section('content')

@include('layouts.inlinecss')

<div class="row dashboardtext" style="padding-bottom:80px;padding-top:10px;">
    <h4 class="card-title">
       {{$pagetitle}}
      </h4>

 

        <div  style="margin-left:15px;margin-right:15px;padding-top:20px;margin-top:0 !important;padding-left:0 !important;padding-right:0 !important;border-radius:10px 10px;">
            {{Form::model($account,array('route' => array('accounts.update', $account->id), 'method' => 'PUT')) }}
            
            <div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Account Number</label>
                        
                        {{Form::text('account_number',null,array('class'=>'form-control','required'=>'readonly','readonly'))}}
                         {{Form::hidden('id',null,array('class'=>'form-control'))}}
                        
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>First Name</label>
                        {{Form::text('first_name',null,array('class'=>'form-control','required'=>'required','id'=>'first_name'))}}
                    </div>
                </div>

<input type="text" style="display:none" value="{{\Auth::user()->created_by_user}}" name="user" />

                <div class="col-12">
                    <div class="form-group">
                        <label>Middle Name</label>
                        {{Form::text('middle_name',null,array('class'=>'form-control','id'=>'middle_name'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Surname</label>
                        {{Form::text('surname',null,array('class'=>'form-control','required'=>'required','id'=>'surname'))}}
                    </div>
                </div>
              

                
 

 


              
               
                <div class="col-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        {{Form::text('phone_number',null,array('class'=>'form-control','required'=>'required','id'=>'phone_number'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Other Phone Number</label>
                        {{Form::text('sec_phone_number',null,array('class'=>'form-control'))}}
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label>Occupation</label>
                        {{Form::text('occupation',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Residential Address</label>
                        {{Form::text('residential_address',null,array('class'=>'form-control','placeholder'=>__('')))}}
                    </div>
                </div>

                 

                <div class="col-12">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        
                        {!!Form::text('date_of_birth2',null,array('class' => 'form-control','required'=>'required')) !!}
                       
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Postal Address</label>
                        {{Form::text('postal_address',null,array('class'=>'form-control'))}}
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Email</label>
                        {{Form::text('email',null,array('class'=>'form-control'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Select Nationality</label>

                        {!! Form::select('nationality', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>ID Number</label>
                        {{Form::text('id_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin</label>
                        {{Form::text('next_of_kin',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>
                

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Id Number</label>
                        {{Form::text('next_of_kin_id_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label>Next of Kin Phone Number</label>
                        {{Form::text('next_of_kin_phone_number',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>

               
                 
                <div class="col-12">
                    <div class="form-group">
                        <label>Legal Consent:</label>
                        <p style="padding:5px 10px;">I certify that the information provided above is true and I am aware that detection of any false declaration renders my application void.</p>
                        {{Form::checkbox('legalconsent',null,array('class'=>'form-control','required'=>'required'))}}
      
                    </div>
                </div>


                  
                <div class="col-12" style="margin-bottom:20px;padding-bottom:20px;">

                    

                    <div class="form-group">
                    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-purple rounded-pill mr-auto'))}}{{Form::close()}}
                    </div>
                </div>
 

            </div>


        </div>
        {{Form::close()}}

    
<script type="text/ja"


</div>
</div>


 

@include('layouts.listdivstyles')
@include('layouts.genrandnumbers') 
@include("layouts.modalview1") 
@include("layouts.modalscripts")



@endsection

@push('script-page')

@endpush