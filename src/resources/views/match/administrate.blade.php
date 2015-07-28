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
                            
                                <form method="POST" action="{{ route('match.administrate.save', $match->id) }}" class="form-horizontal" role="form">
                            
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.match.data') }}
                                                </h2>

                                                <div class="form-group">
                                                        <label class="col-md-4 control-label">
                                                                {{ Lang::get('message.field.match.closed') }}
                                                        </label>
                                                        <div class="col-md-6">
                                                                <input type="checkbox" name="closed" />
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
                                                               <input type="text" id="invitation_helper" placeholder="{{ Lang::get('message.placeholder.search') }}" />
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
                                                                    id="message"
                                                                    name="message"
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
                                                        </label>
                                                    
                                                        <div class="col-md-6 control">
                                                                <input
                                                                        type="button"
                                                                        class="warn btn btn-primary"
                                                                        onclick="UI.confirmRedirect('{{ route("match.cancel", $match->id) }}', '{{ Lang::get('message.field.match.cancel') }}', '{{ Lang::get('message.title.match.cancel') }}', '{{ Lang::get('message.button.no') }}', '{{ Lang::get('message.button.yes') }}');"
                                                                        value="{{ Lang::get('message.link.match.cancel') }}">
                                                                </input>
                                                                <br>
                                                                <div class="warning-small">
                                                                    {{ Lang::get('message.field.match.cancel.warning') }}
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                            
                                        <input type="submit" class="btn btn-primary" value="{{ Lang::get('message.button.save') }}">
                                </form>
                        </div>
                </div>
        </div>
</div>
@endsection
