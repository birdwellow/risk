@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-8 col-md-offset-0">
                <div class="panel panel-default">
                    @if(!Auth::user()->joinedMatch)
                        <div class="panel-heading">
                            {{ Lang::get('message.title.overview.nomatch') }}
                        </div>
                    
                        <div class="panel-body">
                        </div>
                    @else
                        <div class="panel-heading">
                            {{ Lang::get('message.title.overview.yourmatch') }}: <div class="namemarker">{{ Auth::user()->joinedMatch->name }}</div>
                        </div>
                        
                        <div class="panel-body">
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
                                                </td>
                                                <td class="data">
                                                    <?php
                                                        $joinedUsersString = "";
                                                        foreach(Auth::user()->joinedMatch->joinedUsers as $index => $joinedUser){
                                                            if($index > 0){
                                                                $joinedUsersString .= ", ";
                                                            }
                                                            $joinedUsersString .= $joinedUser->name;
                                                        }
                                                    ?>
                                                    {{ $joinedUsersString }} ({{ count(Auth::user()->joinedMatch->joinedUsers) }}/{{ Auth::user()->joinedMatch->maxusers }})
                                                </td>
                                        </tr>
                                        
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match_map_name') }}
                                                </td>
                                                <td class="data">
                                                        {{ Auth::user()->joinedMatch->map->name }}
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
                                                        {{ Auth::user()->joinedMatch->createdBy->name }}
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
                                    Go to match
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
                                                                    @if($thread->latestMessage()->user->avatarfile)
                                                                        <img src="/img/avatars/{{ $thread->latestMessage()->user->avatarfile }}" class="user-avatar icon">
                                                                    @else
                                                                        <img src="/img/avatars/default.png" class="user-avatar icon">
                                                                    @endif
                                                                <span class="sendername">{{ $thread->latestMessage()->user->name }}</span>:
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
    
        <div class="col-md-4 col-md-offset-0">
                <div class="panel panel-default">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.overview.matches') }}
                        </div>

                        <div class="panel-body">
                                <table>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match.name') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('input.match.joinedusers') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('input.match.startdate') }}
                                                </td>
                                        </tr>

                                    @foreach($matches as $match)
                                        <tr class="data">
                                                <td>
                                                        {{ $match->name }}
                                                </td>
                                                <td>
                                                    @foreach($match->joinedUsers as $joinedUser)
                                                        {{ $joinedUser->name }}
                                                    @endforeach
                                                </td>
                                                <td>
                                                        {{ $match->created_at }}
                                                </td>
                                        </tr>
                                    @endforeach
                                </table>
                        </div>
                </div>
        </div>
</div>
@endsection
