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
                            <div class="form-group section">
                                <h2>
                                    {{ Lang::get('message.title.match.data') }}
                                </h2>
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.name') }}
                                </label>
                                <div class="col-md-6">
                                    {!! Form::text('name') !!}
                                </div>
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.map') }}
                                </label>
                                <div class="col-md-6">
                                    {!! Form::text('map_id') !!}
                                </div>
                            </div>
                            <div class="form-group section">
                                <h2>
                                    {{ Lang::get('message.title.match.invitations') }}
                                </h2>
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.invite') }}
                                </label>
                                <div class="col-md-6">
                                    {!! Form::text('', '', array(
                                        'id' => 'invitation_helper'
                                    )) !!}
                                    
                                    <textarea
                                        id="invited_players"
                                        name="invited_players"></textarea>                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.invitationmessage') }}
                                </label>
                                <div class="col-md-6">
                                    <textarea
                                        id="invitation_message"
                                        name="invitation_message"></textarea>                                    
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
