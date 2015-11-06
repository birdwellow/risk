@extends('app')

@section('content')
    
<script src="/js/thirdparty/kinetic-v5.1.0.min.js" defer="defer"></script>

<script src="/js/play/config.js"></script>
<script src="/js/play/proxy.js" defer="defer"></script>
<script src="/js/play/model.js" defer="defer"></script>
<script src="/js/play/view.js" defer="defer"></script>
<script src="/js/play/controller.js" defer="defer"></script>
<script src="/js/play/utils.js" defer="defer"></script>

<script>
    
    var joinId = "{{ Auth::user()->joinid }}";
    
</script>

<div id="game-table">
    <!--class="navbar-left"-->
    <!--class="navbar-right"-->
    <!--class="navbar-fixed-bottom"-->
    <div id="sidebar" class="navbar-left panel panel-default initial out">

            <div id="playmodule-togglebar">
                <button class="toggle-btn right" toggle-function="toggleFixSideBar" title="{{ Lang::get("Fix Sidebar") }}">
                    <img src="/img/in.png">
                </button>
                <button class="toggle-btn active" toggle-for="chat" title="{{ Lang::get("Chat") }}">
                    <img src="/img/chat.png">
                </button>
                <button class="toggle-btn active" toggle-for="cards" title="{{ Lang::get("Cards") }}">
                    <img src="/img/cards.png">
                </button>
                <button class="toggle-btn active" toggle-for="players" title="{{ Lang::get("Players") }}">
                    <img src="/img/players.png">
                </button>
                <!--
                <button class="toggle-btn active" toggle-for="statistics" title="{{ Lang::get("Statistics") }}">
                    <img src="/img/statistics.png">
                </button>
                -->
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

                <div class="playmodule" id="players">
                    <div class="header">
                        <img class="icon" src="/img/players.png">
                        {{ Lang::get( 'Players' ) }}
                    </div>

                    <div class="body" id="playerscontent">
                    </div>

                </div>

                <!--
                <div class="playmodule" id="statistics">
                    <div class="header">
                        <img class="icon" src="/img/statistics.png">
                        {{ Lang::get( 'Statistics' ) }}
                    </div>

                    <div class="body" id="statisticscontent">
                        Some Stats
                    </div>
                </div>
                -->

                <div class="playmodule" id="log">
                    <div class="header">
                        <img class="icon" src="/img/log.png">
                        {{ Lang::get( 'Match Log' ) }}
                    </div>

                    <div class="body" id="logcontent">
                    </div>
                </div>
            </div>

    </div>
    
    <div id="map-controls" class="map-controls">
        <div class="title"></div>
        <div class="active-player"></div>
        <div class="current-phase">
            <div class="label"></div>
            <div class="symbols">
                <img src="/img/troopgain.png" class="state-symbol troopgain">
                <img src="/img/troopdeployment.png" class="state-symbol troopdeployment">
                <img src="/img/attack.png" class="state-symbol attack">
                <img src="/img/troopshift.png" class="state-symbol troopshift">
            </div>
            <div class="newtroops"></div>
            <button class="btn-primary next-phase"></button>
        </div>
    </div>
    
    <div id="game-map"></div>
</div>
@endsection