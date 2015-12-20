@extends('app')

@section('content')

<img src="/img/world.png" class="background-img"/>

<div class="container">
    
        <div class="col-md-4">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.all.threads') }}</div>

                        <div class="panel-body">
                            
                                @if($threads->count() > 0)
                                    @foreach($threads as $currentThread)
                                        <?php
                                            $selectedClass = ($currentThread->id == $thread->id) ? 'selected' : '';
                                        ?>
                                            <a class="{{ $selectedClass }}" onclick="loadThread({{ $currentThread->id }}, this)">
                                            <!--href="{{ route('thread.allmessages', $currentThread->id) }}"-->
                                            <div class="thread media alert">
                                                <div class="subject">
                                                    @if($currentThread->isUnread(Auth::user()->id))
                                                        <img class="icon" src="/img/message.png">
                                                    @else
                                                    @endif
                                                    {{ str_limit(strip_tags($currentThread->subject, 20)) }}
                                                </div>
                                                <div class="recipients">
                                                    <?php
                                                        $participantsString = "";
                                                        foreach($currentThread->participants as $index => $participant){
                                                            if($index > 0){
                                                                $participantsString .= ", ";
                                                            }
                                                            $participantsString .= $participant->user->name;
                                                        }
                                                    ?>
                                                    {{ $participantsString }}
                                                </div>
                                                
                                                @if($currentThread->latestMessage())
                                                    <div class="latestmessagesummary">
                                                        {!! userlabel($currentThread->latestMessage()->user, false) !!}:
                                                        <span class="messagebody">
                                                            {{ str_limit(strip_tags($currentThread->latestMessage()->body, 12)) }}
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
                                @else
                                    {{ Lang::get('message.text.no.messages') }}
                                @endif
                        </div>
                </div>
        </div>
    
    
        <div id="thread" class="col-md-8">
            
            @if(isset($thread))
            
                @include('message.thread')
            
            @else
                <div class="panel panel-default game-panel">
                    <div class="panel-body">
                        {{ Lang::get('message.text.no.thread.selected') }}
                    </div>
                </div>
            @endif
            
        </div>
        
        
</div>
@endsection
