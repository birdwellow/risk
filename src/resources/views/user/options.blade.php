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
                                        
                                <form method="POST" action="{{ route('user.options') }}" class="form-horizontal" role="form" enctype="multipart/form-data" >
                                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.user.data') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('message.field.username') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="text" name="name" value="{{ Auth::user()->name }}" />
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('message.field.email') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="text" name="email" value="{{ Auth::user()->email }}" />
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
                                                                <input type="file" name="avatar" accept="image/*">
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
                                                                <select name="colorScheme" class="styled-select" id="theme-select" >
                                                                    @foreach($colorSchemeValues as $key => $value)
                                                                        <option value="{{ $key }}"
                                                                                @if($key == Auth::user()->colorscheme)
                                                                                    selected
                                                                                @endif
                                                                        >
                                                                            {{ $value }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                        </div>
                                                </div>
                                        </div>
                                        
                                        <input type="submit" value="{{ Lang::get('message.button.save') }}" class="btn btn-primary" >
                                </form>
                        </div>
                </div>
        </div>
</div>
@endsection
