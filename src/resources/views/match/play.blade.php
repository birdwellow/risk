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
                <button class="toggle-btn right" toggle-function="toggleFixSideBar" title="{{ Lang::get("match.sidebar.toggle") }}">
                    <img src="/img/in.png">
                </button>
                <button class="toggle-btn active" toggle-for="chat" title="{{ Lang::get("match.sidebar.chat") }}">
                    <img src="/img/chat.png">
                </button>
                <button class="toggle-btn active" toggle-for="viewmodes" title="{{ Lang::get("match.sidebar.viewmodes") }}">
                    <img src="/img/view.png">
                </button>
                <button class="toggle-btn active" toggle-for="cards" title="{{ Lang::get("match.sidebar.cards") }}">
                    <img src="/img/cards.png">
                </button>
                <button class="toggle-btn active" toggle-for="players" title="{{ Lang::get("match.sidebar.players") }}">
                    <img src="/img/players.png">
                </button>
                <button class="toggle-btn active" toggle-for="statistics" title="{{ Lang::get("Statistics") }}">
                    <img src="/img/statistics.png">
                </button>
                <button class="toggle-btn active" toggle-for="log" title="{{ Lang::get("match.sidebar.log") }}">
                    <img src="/img/log.png">
                </button>
            </div>

            <div id="playmodule-container">
                <div class="playmodule" id="chat">
                    <div class="header">
                        <img class="icon" src="/img/chat.png">
                        {{ Lang::get('match.sidebar.chat') }}
                    </div>

                    <div class="body" class="body" id="chatcontent">
                    </div>

                    <input type="text" id="chatinput"/>
                </div>

                <div class="playmodule" id="viewmodes">
                    <div class="header">
                        <img class="icon" src="/img/view.png">
                        {{ Lang::get( 'match.sidebar.viewmodes' ) }}
                    </div>

                    <div class="body">
                        <button class="toggle-btn" mode="owner">
                            <img src="/img/view_owner.png">
                            <br>{{ Lang::get( 'match.sidebar.viewmodes.owner' ) }}
                        </button>
                        <button class="toggle-btn" mode="continent">
                            <img src="/img/view_continent.png">
                            <br>{{ Lang::get( 'match.sidebar.viewmodes.continent' ) }}
                        </button>
                    </div>

                </div>

                <div class="playmodule" id="cards">
                    <div class="header">
                        <img class="icon" src="/img/cards.png">
                        {{ Lang::get( 'match.sidebar.cards' ) }}
                    </div>

                    <div class="container"></div>
                    
                    <div class="center">
                        <button class="btn btn-primary">
                        {{ Lang::get( 'match.sidebar.cards.trade' ) }}
                        </button>
                    </div>
                    

                </div>

                <div class="playmodule" id="players">
                    <div class="header">
                        <img class="icon" src="/img/players.png">
                        {{ Lang::get( 'match.sidebar.players' ) }}
                    </div>

                    <div class="body" id="playerscontent">
                    </div>

                </div>

                <div class="playmodule" id="statistics">
                    <div class="header">
                        <img class="icon" src="/img/statistics.png">
                        {{ Lang::get( 'match.sidebar.statistics' ) }}
                    </div>

                    <div class="body" id="statisticscontent">
                        Some Stats
                    </div>
                </div>

                <div class="playmodule" id="log">
                    <div class="header">
                        <img class="icon" src="/img/log.png">
                        {{ Lang::get( 'match.sidebar.log' ) }}
                    </div>

                    <div class="body" id="logcontent">
                    </div>
                </div>
            </div>

    </div>
    
    <div id="map-controls" class="map-controls">
        <div class="title panel-heading">
            <table>
                <tr>
                    <td>
                        {{ Lang::get('match.controls') }}
                    </td>
                    <td>
                        <a class="help-button" toggle-for="help-info">?</a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="waiting">
            <img src="/img/loading_big.gif">
        </div>
        <div class="hidden">
            <div role="active-player"></div>
            <div class="current-phase">
                <div class="symbols">
                    <img src="/img/troopgain.png" class="state-symbol troopgain">
                    <img src="/img/troopdeployment.png" class="state-symbol troopdeployment">
                    <img src="/img/attack.png" class="state-symbol attack">
                    <img src="/img/troopshift.png" class="state-symbol troopshift">
                </div>
                <div class="label"></div>
                <div id="help-info" style="display: none;">
                    <table>
                        <tr>
                            <td class="help-symbol">
                                ?
                            </td>
                            <td class="help-info"></td>
                        </tr>
                    </table>
                </div>
                <div class="newtroops"></div>
                <button class="btn-primary next-phase">
                    <img src="/img/continue.png">
                    {{ Lang::get( 'match.next' ) }}
                </button>
            </div>
        </div>
    </div>
    
    <div id="game-map">
        <div class="waiting">
            <img src="/img/loading_big.gif">
        </div>
    </div>
</div>
@endsection