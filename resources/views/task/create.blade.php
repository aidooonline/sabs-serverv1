{{Form::open(array('url'=>'task','method'=>'post','enctype'=>'multipart/form-data'))}}
<div class="row">
    <div class="col-6">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('stage',__('Task Stage')) }}
            {!!Form::select('stage', $stage, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
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
            {!!Form::date('due_date', null,array('class' => 'form-control','required'=>'required')) !!}

        </div>
    </div>

    <div class="col-6" data-name="parent">
        {{Form::label('parent',__('Assign'))}}
        {!!Form::select('parent', $parent, null,array('class' => 'form-control','data-toggle'=>'select','name'=>'parent','id'=>'parent','required'=>'required')) !!}
    </div>
    <div class="col-6" data-name="parent">
        <div class="form-group">
            {{Form::label('parent_id',__('Assign Name'))}}
            <select class="form-control" data-toggle='select' name="parent_id" id="parent_id">

            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{Form::label('priority',__('Priority')) }}
            {!!Form::select('priority', $priority, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
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
            {!! Form::select('user', $user, null,array('class' => 'form-control','data-toggle'=>'select')) !!}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
    </div>
</div>
</div>
{{Form::close()}}

