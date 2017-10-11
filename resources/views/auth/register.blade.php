@extends('layout.app')

@section('content')
    <div>
        <h1>Sign up</h1>
    </div>
    
    <div class = "row">
        <div class = "col-md-6 col-md-offset-3">
            {!! Form::open(['route'=>'signup.post']) !!}
                <div class="form-group">
                    {!! Form::label('name','Name') !!}
                    {!! Form::text('name', old('name'), ['class'=>'form-control']) !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('email','Email') !!}
                    {!! Form::email('email', old('email'), ['class'=>'form-control']) !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('password','Password') !!}
                    {!! Form::email('password', ['class'=>'form-control']) !!}
                </div>                
                
                <div class="form-group">
                    {!! Form::label('password_confirmation','Confirmation') !!}
                    {!! Form::email('password_confirmation', ['class'=>'form-control']) !!}
                </div>
                
                {!! Form::submit('Sign up', ['class' => 'btn btn-primary']) !!}
            
            {!! Form::close() !!}
        </div>
    </div>

@endsection