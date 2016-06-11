@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.user.options') }}</div>

                        <div class="panel-body">
                                    
                                <div class="section">
                                    
                                        <form method="POST" action="{{ route('user.options') }}" class="form-horizontal" role="form" enctype="multipart/form-data" >

                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <h2>
                                                        {{ Lang::get('message.title.user.data') }}
                                                </h2>

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('input.new_user_name') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="text" name="new_user_name" value="{{ oldordefault('new_user_name', Auth::user()->name) }}" class="{{ invalid('new_user_name') }}"/>
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('input.new_user_email') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="text" name="new_user_email" value="{{ oldordefault('new_user_email', Auth::user()->email) }}" class="{{ invalid('new_user_email') }}"/>
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('input.new_user_avatarfile') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="file" name="new_user_avatarfile" accept="image/*">
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('input.new_user_theme') }}
                                                        </label>
                                                        <div class="col-md-6 data">
                                                                <select class="{{ invalid('new_user_theme') }}" name="new_user_theme" id="new_user_theme">
                                                                    @foreach($allowedThemes as $theme)
                                                                    <option value="{{ $theme }}" {{ Auth::user()->csstheme == $theme ? 'selected' : '' }}>
                                                                            {{ Lang::get('input.new_user_theme.' . $theme) }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
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
                                                            {{ Lang::get('input.user_password') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            <input type="password" class="{{ invalid('user_password') }}" name="user_password">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                            {{ Lang::get('input.new_user_password') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            <input type="password" class="{{ invalid('new_user_password') }}" name="new_user_password">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">
                                                            {{ Lang::get('input.new_user_password_confirmation') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                            <input type="password" class="{{ invalid('new_user_password_confirmation') }}" name="new_user_password_confirmation">
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
