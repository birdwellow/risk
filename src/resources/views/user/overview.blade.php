@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-8 col-md-offset-0">
                <div class="panel panel-default">
                    @if(!Auth::user()->joinedMatch)
                        <div class="panel-heading">
                            {{ Lang::get('message.title.overview.nomatch') }}
                        </div>
                    @else
                        <div class="panel-heading">
                                {{ Lang::get('message.title.overview.yourmatch') }}
                        </div>
                        
                        <div class="panel-body">
                                <table>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.field.match.name') }}
                                                </td>
                                                <td class="data">
                                                        {{ Auth::user()->joinedMatch->name }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.field.match.joinedusers') }}
                                                </td>
                                                <td class="data">
                                                    @foreach(Auth::user()->joinedMatch->joinedUsers as $joinedUser)
                                                        {{ $joinedUser->name }}
                                                    @endforeach
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.field.match.startdate') }}
                                                </td>
                                                <td class="data">
                                                        {{ Auth::user()->joinedMatch->created_at }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.field.match.createdby') }}
                                                </td>
                                                <td class="data">
                                                        {{ Auth::user()->joinedMatch->createdBy->name }}
                                                </td>
                                        </tr>
                                </table>
                            
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
                                                                    {{ str_limit($thread->latestMessage()->body, 100) }}
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
                                                        {{ Lang::get('message.field.match.name') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('message.field.match.joinedusers') }}
                                                </td>
                                                <td>
                                                        {{ Lang::get('message.field.match.startdate') }}
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
