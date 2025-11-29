{{Form::open(array('url'=>'document','method'=>'post','enctype'=>'multipart/form-data'))}}
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
            {{Form::label('folder',__('Folder')) }}
            {!!Form::select('folder', $folder, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('type',__('Type')) }}
            {!!Form::select('type', $types, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('opportunities',__('Opportunities')) }}
            {!!Form::select('opportunities', $opportunities, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('status',__('Status')) }}
            {!!Form::select('status', $status, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('publish_date',__('Publish Date')) }}
            {!!Form::date('publish_date', null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6" style="display:none">
        <div class="form-group">
            {{Form::label('expiration_date',__('Expiration Date')) }}
            {!!Form::date('expiration_date', null,array('class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>2,'placeholder'=>__('Enter Description')))}}
        </div>
    </div>
    <div class="col-12 mb-3 field" data-name="attachments">
        <div class="attachment-upload">
            <div class="attachment-button">
                <div class="pull-left">
                    {{Form::label('attachment',__('Attachment')) }}
                    {{Form::file('attachment',array('class'=>'form-control'))}}
                </div>
            </div>
            <div class="attachments"></div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('Assign User',__('Assign User')) }}
            {!! Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
    </div>
</div>
</div>
{{Form::close()}}

