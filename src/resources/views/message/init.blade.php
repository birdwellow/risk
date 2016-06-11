@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.new.thread') }}
                        </div>

                        <div class="panel-body">

                                <form method="POST" action="{{ route('new.thread.create') }}" class="form-horizontal" role="form" >
                                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    
                                        <div class="section">
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.thread_subject') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input value="{{ isset($messageTitle) ? $messageTitle : old('thread_subject') }}" type="text" name="thread_subject" class="{{ invalid('thread_subject') }}" />
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input type="checkbox" id="thread_reuseexistingthread" name="thread_reuseexistingthread"/>
                                                                <label for="thread_reuseexistingthread">
                                                                    {{ Lang::get('input.thread_reuseexistingthread') }}
                                                                </label>
                                                        </div>
                                                </div>

                                        </div>
                                    
                                        <div class="section">
                                                <h2>
                                                        {{ Lang::get('message.title.message.firstthreadmessage') }}
                                                </h2>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.thread_recipients') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input value="{{ isset($user) ? $user->name : old('thread_recipients') }}" name="thread_recipients" type="userselector" class="{{ invalid('thread_recipients') }}" />
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('input.thread_message_text') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <textarea name="thread_message_text" class="{{ invalid('thread_message_text') }}">{{ old('thread_message_text') }}</textarea>
                                                        </div>
                                                </div>
                                            
                                        </div>
                                    
                                        <input type="submit" value="{{ Lang::get('message.button.send') }}" class="btn btn-primary" />
                                </form>
                        </div>
                </div>
        </div>
</div>
@endsection
