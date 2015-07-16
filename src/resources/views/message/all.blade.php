@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-4">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.all.messages') }}</div>

                        <div class="panel-body">
                            
                                @if($threads->count() > 0)
                                    @foreach($threads as $thread)
                                        <?php $class = $thread->isUnread(Auth::user()->id) ? 'unread' : ''; ?>
                                        <a href="{{ route('thread.messages', $thread->id) }}">
                                            <div class="thread media alert {{ $class }}">
                                                <div class="subject">{{ $thread->subject }}</div>
                                                <div>
                                                    <span class="sendername">
                                                        {{ $thread->latestMessage()->user->name }}:
                                                    </span>
                                                    <span class="messagebody">
                                                        {{ $thread->latestMessage()->body }}
                                                    </span>
                                                </div>
                                                <div class="recipients">
                                                    @foreach($thread->participants as $index => $participant)
                                                        {{ $participant->user->name }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @endif
                        </div>
                </div>
        </div>
</div>
@endsection
