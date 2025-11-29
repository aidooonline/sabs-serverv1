{{Form::open(array('url'=>'opportunities','method'=>'post','enctype'=>'multipart/form-data'))}}
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
                {{Form::label('account',__('Account Name')) }}
                {!!Form::select('account', $account_name, $id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('account',__('Account')) }}
                {!!Form::select('account', $account_name, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @endif
    @if($type == 'contact')
        <div class="col-6" style="display:none; ">
            <div class="form-group">
                {{Form::label('contact',__('Contact')) }}
                {!!Form::select('contact', $contact, $id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @else
        <div class="col-6" style="display:none;">
            <div class="form-group">
                {{Form::label('contact',__('Contact')) }}
                {!!Form::select('contact', $contact, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @endif
    @if($type == 'campaign')
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign_id',__('Campaign')) }}
                {!!Form::select('campaign_id', $campaign_id,$id,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @else
        <div class="col-6">
            <div class="form-group">
                {{Form::label('campaign',__('Campaign')) }}
                {!!Form::select('campaign', $campaign_id, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            </div>
        </div>
    @endif
    <div class="col-6">
        <div class="form-group">
            {{Form::label('stage',__('Deal Stage')) }}
            {!!Form::select('stage', $opportunities_stage, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('amount',__('Amount')) }}
            {!! Form::number('amount', null,array('class' => 'form-control ','placeholder'=>__('Enter Amount'),'required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('probability',__('Probability')) }}
            {{Form::number('probability',null,array('class'=>'form-control','placeholder'=>__('Enter Probability'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('close_date',__('Close Date')) }}
            {{Form::date('close_date',null,array('class'=>'form-control','placeholder'=>__('Enter Title'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('lead_source',__('Lead Source')) }}
            {!! Form::select('lead_source', $leadsource, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Description',__('Description')) }}
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
