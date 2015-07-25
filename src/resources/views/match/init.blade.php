@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.init.match') }}
                        </div>

                        <div class="panel-body">
                                <form method="POST" action="{{ route('match.create') }}" class="form-horizontal" role="form" >
                                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                
                                        <div class="section">
                                            <h2>
                                                    {{ Lang::get('message.title.match.data') }}
                                            </h2>
                                            
                                            <div class="form-group">
                                                    <label class="col-md-3 control-label">
                                                            {{ Lang::get('message.field.match.name') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                            <input type="text" name="name" value="{{ old('name') }}"/>
                                                    </div>
                                            </div>

                                            <div class="form-group">
                                                    <label class="col-md-3 control-label">
                                                            {{ Lang::get('message.field.match.map') }}
                                                    </label>
                                                    <div class="col-md-9">
                                                            <select name="mapName" id="mapname-select">
                                                                @foreach($mapNames as $mapName)
                                                                    <option value="{{ $mapName }}">
                                                                            {{ $mapName }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">
                                                        {{ Lang::get('message.field.match.closed') }}
                                                </label>
                                                <div class="col-md-9">
                                                        <input type="checkbox" name="closed"/>
                                                </div>
                                            </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                              {{ Lang::get('message.field.match.maxusers') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <select name="maxusers" id="maxusers">
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
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input value="{{ old('invited_players') }}" name="invited_players" type="userselector" placeholder="{{ Lang::get('message.placeholder.search') }}" />
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                            {{ Lang::get('message.placeholder.invitation.message') }}
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
