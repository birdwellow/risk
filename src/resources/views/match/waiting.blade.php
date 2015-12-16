@extends('app')

@section('content')

<img src="/img/world.png" class="background-img"/>

<script>
    
    var joinId = "{{ Auth::user()->joinid }}";
    
    $(document).ready(function(){
        $("#refresh").click(function(){
            location.reload();
        });
    });
    
</script>


<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.waiting') }}</div>

                        <div class="panel-body center">
                                <table class="summary">
                                    <tr>
                                        <td>
                                            <img src="/img/hourglass.png" style="height: 100px; margin: 20px;">
                                        </td>
                                        <td>
                                            <h1>
                                                {{ Lang::get('message.title.waitingfor.match', ['matchName'=> $match->name]) }}.
                                            </h1>
                                            <br>
                                            <ul>
                                                <li>
                                                    {{ Lang::get('message.label.match.not.started', [
                                                        'createdBy'=> $match->createdBy->name
                                                       ])
                                                    }}
                                                </li>
                                                <li>
                                                    {{ Lang::get('message.label.match.not.enough.players.joined', [
                                                        'players'=> $match->maxusers,
                                                        'joinedUsers'=> count($match->joinedUsers)
                                                       ])
                                                    }}:
                                                    <br>
                                                    <br>
                                                    @foreach($match->joinedUsers as $joinedUser)
                                                        {!! userlabel($joinedUser) !!}
                                                    @endforeach
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            
                                <br>
                                <button id="refresh" class="btn btn-primary">
                                    {{ Lang::get('message.button.match.waiting.refresh') }}
                                </button>
                                
                                @if($match->createdBy->id == Auth::user()->id)
                                <input
                                        type="button"
                                        class="action btn btn-primary"
                                        onclick="UI.confirmRedirect('{{ route("match.start") }}', '{{ Lang::get('input.match_start') }}', '{{ Lang::get('message.title.match.start') }}', '{{ Lang::get('message.button.no') }}', '{{ Lang::get('message.button.yes') }}');"
                                        value="{{ Lang::get('message.link.match.start') }}">
                                @endif
                        </div>
                        
                </div>  
        </div>  
</div>

@endsection