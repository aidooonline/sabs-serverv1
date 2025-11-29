{{Form::open(array('url'=>'contact','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    @if($type == 'account')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account')) }}
                {!!Form::select('account', $account, $id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account')) }}
                {!! Form::select('account', $account, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @endif
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
    <div class="col-6">
        <div class="form-group">
            {{Form::label('contactaddress',__('Address')) }}
            {{Form::text('contact_address',null,array('class'=>'form-control','placeholder'=>__('Address'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('contactaddress',__('City')) }}
            {{Form::text('contact_city',null,array('class'=>'form-control','placeholder'=>__('City'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            {{Form::label('contactaddress',__('State')) }}
            {{Form::text('contact_state',null,array('class'=>'form-control','placeholder'=>__('State'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            {{Form::label('contact_postalcode',__('Postal Code')) }}
            {{Form::number('contact_postalcode',null,array('class'=>'form-control','placeholder'=>__('Postal Code'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            {{Form::label('contact_country',__('Country')) }}
            {{Form::text('contact_country',null,array('class'=>'form-control','placeholder'=>__('Country'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User')) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
</div>
<div class="w-100 text-right">
    {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
</div>
</div>
{{Form::close()}}
