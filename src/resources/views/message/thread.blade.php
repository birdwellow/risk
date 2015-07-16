@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.all.messages') }}: {{ $thread->subject }}
                            <div class="recipients">
                                @foreach($thread->participants as $index => $participant)
                                    {{ $participant->user->name }}
                                @endforeach
                            </div>
                        </div>
                    
                        <div class="panel-body">
                            
                            <div class="messages">
                                @foreach($thread->messages as $message)
                                        <div class="message">
                                            <span class="sendername">
                                                {{ $message->user->name }}:
                                            </span>
                                            <span class="messagebody">
                                                {{ $message->body }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="newmessage">
                                <form method="POST" action="{{ route('send.new.thread.message', $thread->id) }}" class="form-horizontal" role="form" enctype="multipart/form-data" >

                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="form-group">
                                            <div class="col-md-10">
                                                    {!! Form::textarea('message') !!}
                                            </div>
                                        </div>

                                        <input type="submit" value="{{ Lang::get('message.button.send') }}" class="btn btn-primary" >
                                </form>
                            </div>
                        </div>
                </div>
        </div>
</div>
@endsection
