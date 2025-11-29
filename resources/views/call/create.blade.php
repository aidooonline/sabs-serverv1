{{Form::open(array('url'=>'call','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
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
            {{Form::label('start_date',__('Start Date')) }}
            {!!Form::date('start_date', null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('end_date',__('End Date')) }}
            {!!Form::date('end_date', null,array('class' => 'form-control','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('direction',__('Direction')) }}
            {!!Form::select('direction', $direction, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-6" data-name="parent">
        {{Form::label('parent',__('Parent'))}}
        {!!Form::select('parent', $parent, null,array('class' => 'form-control ','data-toggle'=>'select','name'=>'parent','id'=>'parent','required'=>'required')) !!}
    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{Form::label('parent_id',__('Parent User'))}}
            <select class="form-control" data-toggle='select' data-toggle='select' name="parent_id" id="parent_id">

            </select>
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
            {!! Form::select('user', $user, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
        </div>
    </div>
    <div class="col-12">
        <hr class="mt-2 mb-2">
        <h6>{{__('Attendees')}}</h6>
    </div>

    <div class="col-6">
        <div class="form-group">
            {{Form::label('attendees_user',__('Attendees User')) }}
            {!!Form::select('attendees_user', $user, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('attendees_contact',__('Attendees Contact')) }}
            {!!Form::select('attendees_contact', $attendees_contact, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('attendees_lead',__('Attendees Lead')) }}
            {!!Form::select('attendees_lead', $attendees_lead, null,array('class' => 'form-control ','data-toggle'=>'select')) !!}
            
        </div>
    </div>
    <div class="w-100 text-right">
        
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
    </div>
</div>
</div>
{{Form::close()}}

