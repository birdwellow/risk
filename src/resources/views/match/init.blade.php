@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.init.match') }}</div>

                        <div class="panel-body">
                            {!! Form::open(
                                array(
                                    'url' => route('match.create'),
                                    'class' => 'form-horizontal',
                                    'role' => 'form',
                                )
                            ) !!}
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.match.data') }}
                                </h2>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        {{ Lang::get('message.field.match.name') }}
                                    </label>
                                    <div class="col-md-6">
                                        {!! Form::text('name') !!}
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        {{ Lang::get('message.field.match.map') }}
                                    </label>
                                    <div class="col-md-6 styled-select">
                                        <select name="mapName" id="mapname-select" class="styled-select">
                                            @foreach($mapNames as $mapName)
                                            <option value="{{ $mapName }}">
                                                {{ $mapName }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        {{ Lang::get('message.field.match.closed') }}
                                    </label>
                                    <div class="col-md-6">
                                        {!! Form::checkbox('closed') !!}
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        {{ Lang::get('message.field.match.maxusers') }}
                                    </label>
                                    <div class="col-md-6 styled-select">
                                        <select name="maxusers" id="maxusers" class="styled-select">
                                            <option value="2">2</option>
                                            <option value="2">3</option>
                                            <option value="2">4</option>
                                            <option value="2">5</option>
                                            <option value="2" selected="">6</option>
                                        </select>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.match.invitations') }}
                                </h2>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                    </label>
                                    <div class="col-md-6">
                                        {!! Form::text('', '', array(
                                            'id' => 'invitation_helper',
                                            'placeholder' => Lang::get('message.placeholder.search')
                                        )) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                    </label>
                                    <div class="col-md-6">
                                        <textarea
                                            id="invited_players"
                                            name="invited_players"
                                            placeholder="{{ Lang::get('message.placeholder.invitation.playernames') }}"></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                    </label>
                                    <div class="col-md-6">
                                    <textarea
                                        id="invitation_message"
                                        name="invitation_message"
                                        placeholder="{{ Lang::get('message.placeholder.invitation.message') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            {!! 
                                Form::submit(Lang::get('message.button.match.create'), array(
                                    'class' => 'btn btn-primary'
                                ))
                            !!}
                            {!! Form::close() !!}
                        </div>
                </div>
        </div>
</div>
@endsection
