@if(\Auth::user()->type == 'owner' || \Auth::user()->type == 'Admin')
    {{Form::open(array('url'=>'user','method'=>'post','enctype'=>'multipart/form-data'))}}
    <div class="row">

   
                
        <div class="col-12" >
            <div class="form-group">
                
                <input    id="created_by_user" name="created_by_user" class="form-control" type="text" value="" />

            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('User Name')) }}
                {{Form::text('username',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name')) }}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12" style="display:none;">
            <div class="form-group">
                {{Form::label('name',__('Title')) }}
                {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title')))}}
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Phone')) }}
                {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Gender')) }}
                {!! Form::select('gender', $gender, null,array('class' => 'form-control','data-toggle'=>'select','required'=>'required')) !!}
            </div>
        </div>
        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6>{{__('Login Details')}}</h6>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('email',__('Email')) }}
                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{Form::label('name',__('Password')) }}
                {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))}}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('user_roles',__('Roles')) }}
                {!! Form::select('user_roles', $roles, null,array('class' => 'form-control ','data-toggle'=>'select','required'=>'required')) !!}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Is Active')) }}
                <div>
                    <input type="checkbox" class="align-middle" name="is_active" checked>
                </div>
            </div>
        </div>
        
         
            
                 
        
        
        
        <div class="col-12 p-0">
            <hr class="m-0 mb-3">
            <h6>{{__('Avatar')}}</h6>
        </div>
        <div class="col-12 mb-3 field" data-name="avatar">
            <div class="attachment-upload">
                <div class="attachment-button">
                    <div class="pull-left">
                        {{Form::file('avatar',array('class'=>'form-control'))}}
                    </div>
                </div>
                <div class="attachment"></div>
            </div>
        </div>
        <div class="w-100 text-right">
            {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}{{Form::close()}}
        </div>
    </div>
    {{Form::close()}}
@else
    {{Form::open(array('url'=>'user','method'=>'post','enctype'=>'multipart/form-data'))}}
    <div class="form-group">
        {{Form::label('name',__('User Name')) }}
        {{Form::text('username',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
    </div>
    <div class="form-group">
        {{Form::label('name',__('Name'),array('class'=>'')) }}
        {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter User Name'),'required'=>'required'))}}
    </div>
    <div class="form-group">
        {{Form::label('email',__('Email'))}}
        {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email'),'required'=>'required'))}}
    </div>
    <div class="form-group">
        {{Form::label('password',__('Password'))}}
        {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter User Password'),'required'=>'required','minlength'=>"6"))}}
    </div>
    <div class="modal-footer">
        {{Form::submit(__('Create'),array('class'=>'btn btn-sm btn-primary rounded-pill'))}}
    </div>
    {{Form::close()}}
@endif


<script>

$(function() {
  // Handler for .ready() called.
  setunique();
});


function setunique(){
    let uniqid = uuidv4();
$("#created_by_user").val(uniqid);
}

function uuidv4() {
  return ([1e7]+1e3+4e3+8e3+1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}
    </script>