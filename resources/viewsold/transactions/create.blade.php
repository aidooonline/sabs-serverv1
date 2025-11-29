{{Form::open(array('url'=>'lead','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6" style="display:none;">
        <div class="form-group">
            {{Form::label('account',__('Account')) }}
            {!! Form::select('account', $account, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('email',__('Email')) }}
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('phone',__('Phone')) }}
            {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6" style="display:none;">
        <div class="form-group">
            {{Form::label('title',__('Title')) }}
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))}}
        </div>
    </div>
   
    <div class="col-6" style="display:none;">
        <div class="form-group">
            {{Form::label('lead_address',__('Address')) }}
            {{Form::text('lead_address',null,array('class'=>'form-control','placeholder'=>__('Address')))}}
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::label('lead_country',__('Country')) }}
                       {!! Form::select('lead_country', $countries, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!} 
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            {{Form::label('lead_city',__('City')) }}
            {{Form::text('lead_city',null,array('class'=>'form-control','placeholder'=>__('City')))}}
        </div>
    </div>
    <div class="col-3" style="display:none;">
        <div class="form-group">
            {{Form::label('lead_state',__('State')) }}
            {{Form::text('lead_state',null,array('class'=>'form-control','placeholder'=>__('State')))}}
        </div>
    </div>
    <div class="col-3" style="display :none;">
        <div class="form-group">
            {{Form::label('lead_postalcode',__('Postal Code')) }}
            {{Form::number('lead_postalcode',null,array('class'=>'form-control','placeholder'=>__('Postal Code')))}}
        </div>
    </div>
    
    <div class="col-6">
        <div class="form-group">
            {{Form::label('status',__('Lead Status')) }}
            {!! Form::select('status',$status, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('stage',__('Lead Response')) }}
            {!! Form::select('lead_temperature', $leadtemperature, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
            @error('source')
            <span class="invalid-source" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('source',__('Source')) }}
            {!! Form::select('source', $leadsource, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('website',__('Referral URL (if any)')) }}
            {{Form::text('website',null,array('class'=>'form-control','placeholder'=>__('Enter Website')))}}
        </div>
    </div>

    <div class="col-6" style="display:none;"> 
        <div class="form-group">
            {{Form::label('opportunity_amount',__('Deal Amount')) }}
            {!! Form::text('opportunity_amount', null,array('class' => 'form-control')) !!}
        </div>
    </div>
    @if($type == 'campaign')
        <div class="col-6" style="display:none;">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign')) }}
                {!! Form::select('campaign', $campaign, $id,array('class' => 'form-control','data-toggle'=>'select')) !!}
            </div>
        </div>
    @else
        <div class="col-6" style="display:none;">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign')) }}
                {!! Form::select('campaign', $campaign, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
            </div>
        </div>
    @endif
    <div class="col-6" style="display:none;">
        <div class="form-group">
            {{Form::label('industry',__('Industry')) }}
            {!! Form::select('industry', $industry, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-12" style="display:none;">
        <div class="form-group">
            {{Form::label('Description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign Lead to User')) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        </div>
    </div>
</div>
<div class="w-100 text-right">
    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
</div>
</div>
{{Form::close()}}
<style>

    .col-6,.col-12,.col-3{
        margin-top:10px;margin-bottom:10px;
    }
    .form-control{
        padding: 0.35rem 1.1;
    padding-top: 0.35rem;
    padding-right: 1.25rem;
    padding-bottom: 0.35rem;
    padding-left: 1.25rem;
    }
</style>
