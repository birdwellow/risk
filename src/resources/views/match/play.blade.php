@extends('app')

@section('content')
    
<script src="/js/match.js" defer="defer"></script>
<script>
    var username = "{{ Auth::user()->name }}";
    var joinId = "{{ Auth::user()->joinid }}";
    
</script>

<div class="container right">
    
</div>

        <div class="col-md-4 col-md-offset-0 right tool-panel">
                <div class="panel panel-default">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.match.chat') }}
                        </div>

                        <div class="panel-body" id="chatcontent">
                        </div>
                        
                        <input type="text" id="chatinput"/>
                </div>
        </div>

        <div id="footer">
                <div class="col-md-8 tool-panel" id="tabs-left">
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="footer-tab" id="cards-tab-head">
                                        {{ Lang::get( 'Cards' ) }}
                                    </div>
                                    <div class="footer-tab" id="stats-tab-head">
                                        {{ Lang::get( 'Statistics' ) }}
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="footer-tab-body" id="cards-tab-body">
                                        
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
                                        
                                    </div>
                                    <div class="footer-tab-body" id="stats-tab-body">
                                        //Statistics
                                    </div>
                                </div>
                        </div>
                </div>
                <div class="col-md-4 tool-panel" id="tabs-right">
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="footer-tab" id="players-tab-head">
                                        {{ Lang::get( 'Players' ) }}
                                    </div>
                                    <div class="footer-tab" id="events-tab-head">
                                        {{ Lang::get( 'Match Log' ) }}
                                    </div>
                                </div>

                                <div class="panel-body">
                                    <div class="footer-tab-body" id="players-tab-body">
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
                                    <div class="footer-tab-body" id="events-tab-body">
                                        <div>Event 1</div>
                                        <div>Event 2</div>
                                        <div>Event 3</div>
                                        <div>Event 4</div>
                                    </div>
                                </div>
                        </div>
                </div>
        </div>
@endsection
