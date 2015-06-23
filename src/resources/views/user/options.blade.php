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
                                    
                                <div class="section">
                                    
                                        <form method="POST" action="{{ route('user.options') }}" class="form-horizontal" role="form" enctype="multipart/form-data" >

                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('message.field.avatar.file') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="file" name="avatar" accept="image/*">
                                                        </div>
                                                </div>

                                                <input type="submit" value="{{ Lang::get('message.button.save') }}" class="btn btn-primary" >
                                        </form>

                                </div>
                            
                                    
                                <div class="section">
                                    
                                        <form method="POST" action="{{ route('user.password.save') }}" class="form-horizontal" role="form" enctype="multipart/form-data" >

                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <h2>
                                                        {{ Lang::get('message.title.user.password') }}
                                                </h2>

                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                            {{ Lang::get('message.field.old.password') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            {!! Form::password('oldpassword') !!}
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                            {{ Lang::get('message.field.new.password') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            {!! Form::password('newpassword') !!}
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                            {{ Lang::get('message.field.new.password.confirmation') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            {!! Form::password('newpasswordconfirm') !!}
                                                    </div>
                                                </div>

                                                <input type="submit" value="{{ Lang::get('message.button.save') }}" class="btn btn-primary" >
                                        </form>

                                </div>
                        </div>
                </div>
        </div>
</div>
@endsection
