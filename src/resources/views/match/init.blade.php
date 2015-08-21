@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.init.match') }}
                        </div>

                        <div class="panel-body">
                                <form method="POST" action="{{ route('match.new.create') }}" class="form-horizontal" role="form" >
                                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                
                                        <div class="section">
                                            <h2>
                                                    {{ Lang::get('message.title.match.data') }}
                                            </h2>
                                            
                                            <div class="form-group">
                                                    <label class="col-md-3 control-label">
                                                            {{ Lang::get('input.match_name') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                            <input class="{{ invalid('match_name') }}" type="text" name="match_name" value="{{ old('match_name') }}"/>
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
                                                        <input type="checkbox" id="match_public" value="public" name="match_public" {{ ( old('match_public') ? "checked" : "" ) }}>
                                                    </div>
                                            </div>

                                            <div class="form-group">
                                                    <label class="col-md-3 control-label">
                                                            {{ Lang::get('input.match_map_name') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                            <select class="{{ invalid('match_map_name') }}" name="match_map_name" id="match_map_name">
                                                                @foreach($mapNames as $mapName)
                                                                    <option value="{{ $mapName }}">
                                                                            {{ Lang::get('input.match_map_name.' . $mapName) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                            </div>

                                            <div class="form-group">
                                                    <label class="col-md-3 control-label">
                                                          {{ Lang::get('input.match_maximum_users') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                            <select class="{{ invalid('match_maximum_users') }}" name="match_maximum_users" id="match_maximum_users">
                                                                    <option value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4">4</option>
                                                                    <option value="5">5</option>
                                                                    <option value="6" selected="">6</option>
                                                            </select>
                                                    </div>
                                            </div>

                                        </div>
                                    
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.match.invitations') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                            {{ Lang::get('input.match_invited_users') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input class="{{ invalid('match_invited_users') }}" value="{{ old('match_invited_users') }}" name="match_invited_users" type="userselector" placeholder="{{ Lang::get('message.placeholder.search') }}" />
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                            {{ Lang::get('input.match_invitationmessage') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <textarea
                                                                    id="message"
                                                                    name="message"
                                                                >{{ old('message') }}</textarea>
                                                        </div>
                                                </div>
                                        </div>
                                    
                                        <input type="submit" value="{{ Lang::get('message.button.match.create') }}" class="btn btn-primary" />
                                </form>
                        </div>
                </div>
        </div>
</div>
@endsection
