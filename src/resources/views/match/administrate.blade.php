@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.administrate.match', ['matchName'=> $match->name]) }}</div>

                        <div class="panel-body">
                            {!! Form::open(
                                array(
                                    'url' => route('match.administrate.save', $match->id),
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
                                        {{ Lang::get('message.field.match.closed') }}
                                    </label>
                                    <div class="col-md-6">
                                        {!! Form::checkbox('closed') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.match.invitations') }}
                                </h2>
                                
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
                            <div class="section">
                                <h2>
                                    {{ Lang::get('message.title.match.cancel') }}
                                </h2>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        {{ Lang::get('message.field.match.cancel') }}
                                        <br>
                                        <div class="warning-small">
                                            {{ Lang::get('message.field.match.cancel.warning') }}
                                        </div>
                                    </label>
                                    <div class="col-md-6 control">
                                        <a class="warn" href="{{ route("match.cancel", $match->id) }}">
                                            {{ Lang::get('message.link.match.cancel') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            {!! 
                                Form::submit(Lang::get('message.button.save'), array(
                                    'class' => 'btn btn-primary'
                                ))
                            !!}
                            {!! Form::close() !!}
                        </div>
                </div>
        </div>
</div>
@endsection
