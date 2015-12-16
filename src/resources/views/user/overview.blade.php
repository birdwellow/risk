@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-7 col-md-offset-0">
                <div class="panel panel-default">
                    @if(!Auth::user()->joinedMatch)
                        <div class="panel-heading">
                            {{ Lang::get('message.title.overview.nomatch') }}
                        </div>
                    
                        <div class="panel-body">
                            
                                @if(Auth::user()->matchnotfication)

                                <div id="alert-container">
                                        <?php
                                            $filtered = array();
                                            preg_match('/(.*?):(.*)/', Auth::user()->matchnotfication, $filtered);
                                            $class = (isset($filtered[2]) ? $filtered[2] : '');
                                        ?>
                                        <div class="alert alert-matchnotification {{ $class }}">
                                                <div class="confirm-matchnotification">
                                                    <img src="/img/confirm.png">
                                                </div>
                                                {{ Lang::get("message.text." . Auth::user()->matchnotfication) }}
                                        </div>
                                </div>

                                @endif

                                <a class="suggestion" href="{{ route('match.new') }}">
                                    <img src="/img/plus.png"/> {{ Lang::get('message.link.suggestion.create.match') }}
                                </a>
                                <br>
                                <a class="suggestion" href="{{ route('match.search') }}">
                                    <img src="/img/search.png"/> {{ Lang::get('message.link.suggestion.search.match') }}
                                </a>
                        </div>
                    @else
                        <div class="panel-heading">
                            {{ Lang::get('message.title.overview.yourmatch') }}:
                            <div class="namemarker">{{ Auth::user()->joinedMatch->name }}</div>
                        </div>
                        
                        <div class="panel-body">
    
                                @if(Auth::user()->matchnotfication)

                                <div id="alert-container">
                                        <?php
                                            $filtered = array();
                                            preg_match('/(.*?):(.*)/', Auth::user()->matchnotfication, $filtered);
                                            $class = (isset($filtered[2]) ? $filtered[2] : '');
                                        ?>
                                        <div class="alert alert-matchnotification {{ $class }}">
                                                <div class="confirm-matchnotification">
                                                    <img src="/img/confirm.png">
                                                </div>
                                                {{ Lang::get("message.text." . Auth::user()->matchnotfication) }}
                                        </div>
                                </div>

                                @endif
                            
                                <table>
                                            
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match_state') }}
                                                </td>
                                                <td>
                                                        <div class="status {{ Auth::user()->joinedMatch->state }}">
                                                            {{ Lang::get('input.match_state.' . Auth::user()->joinedMatch->state) }}
                                                        </div>
                                                </td>
                                        </tr>
                                        
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.players') }}
                                                        @if(Auth::user()->joinedMatch->state == 'waitingforjoins')
                                                            ({{ count(Auth::user()->joinedMatch->joinedUsers) }}/{{ Auth::user()->joinedMatch->maxusers }})
                                                        @endif
                                                </td>
                                                <td class="data">
                                                    @foreach(Auth::user()->joinedMatch->joinedUsers as $joinedUser)
                                                        {!! userlabel($joinedUser) !!}
                                                    @endforeach
                                                </td>
                                        </tr>
                                        
                                        @if(Auth::user()->joinedMatch->activeplayer)
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.activeplayer') }}
                                                </td>
                                                <td class="data">
                                                        {!! userlabel(Auth::user()->joinedMatch->activeplayer) !!}
                                                </td>
                                        </tr>
                                        @endif
                                        
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match_map_name') }}
                                                </td>
                                                <td class="data">
                                                        {{ Lang::get('input.match_map_name.' . Auth::user()->joinedMatch->mapname) }}
                                                </td>
                                        </tr>
                                        
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.public') }}
                                                </td>
                                                <td class="data">
                                                        @if(Auth::user()->joinedMatch->public)
                                                            {{ Lang::get('message.button.yes') }}
                                                        @else
                                                            {{ Lang::get('message.button.no') }}
                                                        @endif
                                                </td>
                                        </tr>
                                        
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.creator.name') }}
                                                </td>
                                                <td class="data">
                                                        {!! userlabel(Auth::user()->joinedMatch->createdBy) !!}
                                                        ({{ date("d M Y, H:m:s", strtotime(Auth::user()->joinedMatch->created_at)) }})
                                                </td>
                                        </tr>
                                        
                                        <tr>
                                                <td>
                                                        Thread
                                                </td>
                                                <td class="data">
                                                        <a href="{{ route('thread.allmessages', Auth::user()->joinedMatch->thread->id) }}">
                                                            &quot;{{ Auth::user()->joinedMatch->thread->subject }}&quot;
                                                        </a>
                                                </td>
                                        </tr>
                                </table>
                            
                                <br>
                            
                                <a class="action btn btn-primary right table" href="{{ route('match.goto') }}">
                                    {{ Lang::get('message.button.goto.match') }}
                                </a>
                        </div>
            
                    @endif
                </div>

            
                <div class="panel panel-default">
                        <div class="panel-heading">
                                <a href="{{ route('all.threads') }}">
                                        @if(count($unreadThreads) == 0)
                                            {{ Lang::get("message.text.no.new.messages") }}
                                        @elseif(count($unreadThreads) == 1)
                                            <img class="icon" src="/img/message.png">
                                            {{ Lang::get("message.title.new.message") }}
                                        @elseif(count($unreadThreads) >= 1)
                                            <img class="icon" src="/img/message.png">
                                            {{ Lang::get("message.title.new.messages", ["number" => count($unreadThreads)]) }}
                                        @endif
                                </a>
                        </div>
                        <div class="panel-body">
                                @if(count($unreadThreads))
                                        @foreach($unreadThreads as $thread)
                                                <a href="{{ route('thread.allmessages', $thread->id) }}">
                                                    <div class="thread unread media alert">
                                                        <div class="subject">
                                                            {{ $thread->subject }}
                                                        </div>
                                                        <div class="recipients">
                                                            <?php
                                                                $participantsString = "";
                                                                foreach($thread->participants as $index => $participant){
                                                                    if($index > 0){
                                                                        $participantsString .= ", ";
                                                                    }
                                                                    $participantsString .= $participant->user->name;
                                                                }
                                                            ?>
                                                            {{ $participantsString }}
                                                        </div>

                                                        @if($thread->latestMessage())
                                                            <div class="latestmessagesummary">
                                                                {!! userlabel($thread->latestMessage()->user, false) !!}:
                                                                <span class="messagebody">
                                                                    {{ str_limit(strip_tags($thread->latestMessage()->body, 100)) }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <div class="latestmessagesummary">
                                                                <span class="sendername"></span>
                                                                <span class="messagebody"></span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </a>
                                        @endforeach
                                @endif
                        </div>
                </div>
        </div>
    
        <div class="col-md-5 col-md-offset-0">
                <div class="panel panel-default">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.overview.matches') }}
                        </div>

                        <div class="panel-body">
                                <table>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match_name') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('input.match_joinedusers') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('input.match_startdate') }}
                                                </td>
                                        </tr>

                                    @foreach($matches as $match)
                                        <tr class="data">
                                                <td>
                                                        {{ $match->name }}
                                                </td>
                                                <td>
                                                    @foreach($match->joinedUsers as $joinedUser)
                                                        {!! userlabel($joinedUser) !!}
                                                    @endforeach
                                                </td>
                                                <td>
                                                        {{ date("d M Y, H:m:s", strtotime($match->created_at)) }}
                                                </td>
                                        </tr>
                                    @endforeach
                                </table>
                        </div>
                </div>
        </div>
</div>
@endsection
