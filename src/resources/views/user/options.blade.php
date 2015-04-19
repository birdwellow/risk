@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.user.profile') }}</div>

                        <div class="panel-body">
                                        
                            {!! Form::open(
                                array(
                                    'url' => route('user.options'),
                                    'class' => 'form-horizontal',
                                    'role' => 'form',
                                    'files' => true,
                                )
                            ) !!}
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.user.data') }}
                                </h2>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.username') }}
                                    </label>
                                    <div class="col-md-6">
                                            {!! Form::text('name', Auth::user()->name) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.email') }}
                                    </label>
                                    <div class="col-md-6">
                                            {!! Form::text('email', Auth::user()->email) !!}
                                    </div>
                                </div>
                                
                                <!--
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.password') }}
                                    </label>
                                    <div class="col-md-6">
                                            {!! Form::password('password') !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.password.confirmation') }}
                                    </label>
                                    <div class="col-md-6">
                                            {!! Form::password('passwordconfirmation') !!}
                                    </div>
                                </div>
                                -->
                                
                                
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.avatar.file') }}
                                    </label>
                                    <div class="col-md-6">
                                            {!! Form::file('avatar') !!}
                                    </div>
                                </div>
                                
                            </div>
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.game.settings') }}
                                </h2>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                            {{ Lang::get('message.field.user.theme') }}
                                    </label>
                                    <div class="col-md-6 styled-select">
                                            {!! Form::select(
                                                'colorScheme',
                                                $colorSchemeValues,
                                                Auth::user()->colorscheme,
                                                array(
                                                    "class" => "styled-select",
                                                    "id" => "theme-select"
                                                )
                                            ) !!}
                                    </div>
                                </div>
                            </div>
                            {!! 
                                Form::submit( Lang::get('message.button.save'), array(
                                    'class' => 'btn btn-primary'
                                ))
                            !!}
                            {!! Form::close() !!}
                        </div>
                </div>
        </div>
</div>
@endsection
