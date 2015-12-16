@extends('app')

@section('content')

<img src="/img/world.png" class="background-img"/>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.administrate.match', ['matchName'=> $match->name]) }}</div>

                        <div class="panel-body">
                            
                                <form method="POST" action="{{ route('match.administrate.save') }}" class="form-horizontal" role="form">
                            
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.match.data') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label-display">
                                                                {{ Lang::get('input.match_name') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                {{ $match->name }}
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.match_state') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <div class="status {{ $match->state }}">
                                                                    {{ Lang::get('input.match_state.' . $match->state) }}
                                                                </div>
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label-display" for="match_public">
                                                                {{ Lang::get('input.match_public') }}
                                                                <div class="info-small">
                                                                    ({{ Lang::get('input.match_public.info') }})
                                                                </div>
                                                        </label>
                                                        <div class="col-md-9">
                                                            <input type="checkbox" id="match_public" name="match_public" {{ ( $match->public ? "checked" : "" ) }}>
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label-display">
                                                                {{ Lang::get('input.match_map_name') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                {{ Lang::get('input.match_map_name.' . $match->mapname) }}
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                              {{ Lang::get('input.match_maximum_users') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <select class="{{ invalid('match_maximum_users') }}" name="match_maximum_users" id="match_maximum_users">
                                                                        <option value="2" {{ $match->maxusers == 2 ? "selected" : "" }}>2</option>
                                                                        <option value="3" {{ $match->maxusers == 3 ? "selected" : "" }}>3</option>
                                                                        <option value="4" {{ $match->maxusers == 4 ? "selected" : "" }}>4</option>
                                                                        <option value="5" {{ $match->maxusers == 5 ? "selected" : "" }}>5</option>
                                                                        <option value="6" {{ $match->maxusers == 6 ? "selected" : "" }}>6</option>
                                                                </select>
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                        </label>
                                                        <div class="button-container col-md-9">
                                                            <input type="submit" class="btn btn-primary" value="{{ Lang::get('message.button.save') }}">
                                                        </div>
                                                </div>
                                        </div>
                                </form>
                                    
                                <form method="POST" action="{{ route('match.administrate.inviteusers') }}" class="form-horizontal" role="form">
                                        
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.match.invitations') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label-display">
                                                            {{ Lang::get('input.match_joinedusers') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            @foreach($match->joinedUsers as $joinedUser)
                                                                {!! userlabel($joinedUser) !!}
                                                            @endforeach
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label-display">
                                                            {{ Lang::get('message.label.match.invitedusers') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                            @foreach($thread->participants as $participant)
                                                                {!! userlabel($participant->user) !!}
                                                            @endforeach
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input class="{{ invalid('match_invited_users') }}" value="{{ old('match_invited_users') }}" name="match_invited_users" type="userselector" placeholder="{{ Lang::get('message.placeholder.search') }}" />
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                        </label>
                                                        <div class="button-container col-md-9">
                                                            <input type="submit" class="btn btn-primary" value="{{ Lang::get('input.match_invite') }}">
                                                        </div>
                                                </div>

                                        </div>
                                        
                                        @if($match->state == "waitingforjoins")
                                        <div class="section">
                                                <h2>
                                                    {{ Lang::get('message.title.match.start') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.match_start') }}
                                                                <div class="small">
                                                                    {{ Lang::get('input.match_start_warning') }}
                                                                </div>
                                                        </label>
                                                    
                                                        <div class="col-md-6 control">
                                                                <input
                                                                        type="button"
                                                                        class="action btn btn-primary"
                                                                        onclick="UI.confirmRedirect('{{ route("match.start") }}', '{{ Lang::get('input.match_start') }}', '{{ Lang::get('message.title.match.start') }}', '{{ Lang::get('message.button.no') }}', '{{ Lang::get('message.button.yes') }}');"
                                                                        value="{{ Lang::get('message.link.match.start') }}">
                                                        </div>
                                                </div>
                                        </div>
                                        @endif
                                        
                                        <div class="section">
                                                <h2>
                                                    {{ Lang::get('message.title.match.cancel') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.match_cancel') }}
                                                                <div class="warning-small">
                                                                    {{ Lang::get('input.match_cancel_warning_pt1') }}
                                                                    <br>
                                                                    {{ Lang::get('input.match_cancel_warning_pt2') }}
                                                                </div>
                                                        </label>
                                                    
                                                        <div class="col-md-6 control">
                                                                <input
                                                                        type="button"
                                                                        class="warn btn btn-primary"
                                                                        onclick="UI.confirmRedirect('{{ route("match.cancel") }}', '{{ Lang::get('input.match_cancel') }}', '{{ Lang::get('message.title.match.cancel') }}', '{{ Lang::get('message.button.no') }}', '{{ Lang::get('message.button.yes') }}', 'warn');"
                                                                        value="{{ Lang::get('message.link.match.cancel') }}">
                                                        </div>
                                                </div>
                                        </div>
                                </form>
                        </div>
                </div>
        </div>
</div>
@endsection
