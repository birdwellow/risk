@extends('app')

@section('content')
    
<script src="/js/match.js" defer="defer"></script>
<script>
    var user = {
        name : "{{ Auth::user()->name }}",
        color : "{{ Auth::user()->matchcolor }}"
    };
    var joinId = "{{ Auth::user()->joinid }}";
    
</script>


<!--class="navbar-left"-->
<!--class="navbar-right"-->
<!--class="navbar-fixed-bottom"-->
<div class="navbar-left panel panel-default">
    
        <div id="playmodule-togglebar">
            <button class="toggle-btn active changed" toggle-for="chat" title="{{ Lang::get("Chat") }}">
                <img src="/img/chat.png">
            </button>
            <button class="toggle-btn active" toggle-for="cards" title="{{ Lang::get("Cards") }}">
                <img src="/img/cards.png">
            </button>
            <button class="toggle-btn active" toggle-for="statistics" title="{{ Lang::get("Statistics") }}">
                <img src="/img/statistics.png">
            </button>
            <button class="toggle-btn active" toggle-for="players" title="{{ Lang::get("Players") }}">
                <img src="/img/players.png">
            </button>
            <button class="toggle-btn active" toggle-for="log" title="{{ Lang::get("Match Log") }}">
                <img src="/img/log.png">
            </button>
        </div>
    
        <div id="playmodule-container">
            <div class="playmodule" id="chat">
                <div class="header">
                    <img class="icon" src="/img/chat.png">
                    {{ Lang::get('message.title.match.chat') }}
                </div>
                
                <div class="body" class="body" id="chatcontent">
                </div>
                
                <input type="text" id="chatinput"/>
            </div>

            <div class="playmodule" id="cards">
                <div class="header">
                    <img class="icon" src="/img/cards.png">
                    {{ Lang::get( 'Cards' ) }}
                </div>
                
                <div id="cardcontainer">
                
                    <div class="card europe">
                        <div class="body">
                            <div class="symbol category-1"></div>
                            West Europe
                        </div>
                    </div>

                    <div class="card america">
                        <div class="body">
                            <div class="symbol category-2"></div>
                            Brazil
                        </div>
                    </div>

                    <div class="card africa">
                        <div class="body">
                            <div class="symbol category-3"></div>
                            Egypt
                        </div>
                    </div>

                    <div class="card europe">
                        <div class="body">
                            <div class="symbol category-2"></div>
                            England
                        </div>
                    </div>

                    <div class="card europe">
                        <div class="body">
                            <div class="symbol category-3"></div>
                            Greece
                        </div>
                    </div>
                    
                </div>
                
            </div>

            <div class="playmodule" id="statistics">
                <div class="header">
                    <img class="icon" src="/img/statistics.png">
                    {{ Lang::get( 'Statistics' ) }}
                </div>
                
                <div class="body" id="statisticscontent">
                    Some Stats
                </div>
            </div>

            <div class="playmodule" id="players">
                <div class="header">
                    <img class="icon" src="/img/players.png">
                    {{ Lang::get( 'Players' ) }}
                </div>
                
                <div class="body" id="playerscontent">
                    
                    <div class="data player active">
                        <img class="user-avatar" src="/img/avatars/Subberbazi_5527efc91bdaf.jpg">
                        <div class="state online"></div>
                        Spieler 1
                        <div></div>
                    </div>
                    <div class="data player">
                        <img class="user-avatar" src="/img/avatars/Subberbazi_5527efc91bd.jpg">
                        <div class="state offline"></div>
                        Next Player
                        <div></div>
                    </div>
                    <div class="data player">
                        <img class="user-avatar" src="/img/avatars/Subberbazi_5527efc91bda.jpg">
                        <div class="state online"></div>
                        Letzter_User
                        <div></div>
                    </div>
                    
                </div>
                
            </div>

            <div class="playmodule" id="log">
                <div class="header">
                    <img class="icon" src="/img/log.png">
                    {{ Lang::get( 'Match Log' ) }}
                </div>
                
                <div class="body" id="logcontent">
                    <div>Event 1</div>
                    <div>Event 2</div>
                    <div>Event 3</div>
                    <div>Event 4</div>
                </div>
            </div>
        </div>
    
</div>


<div id="game-map">
    <canvas id="map"></canvas>
    
</div>
@endsection
