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
                                                                {{ Lang::get('message.field.message.thread.subject') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input value="{{ old('subject') }}" type="text" name="subject" />
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input type="checkbox" id="reusethread" name="reusethread" />
                                                                <label for="reusethread">
                                                                    {{ Lang::get('message.field.message.thread.reuseexistingthread') }}
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
                                                                {{ Lang::get('message.field.message.thread.recipients') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <input value="{{ old('usernames') }}" name="usernames" type="userselector" />
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <label class="col-md-3 control-label">
                                                                {{ Lang::get('message.field.message.text') }}
                                                        </label>
                                                        <div class="col-md-9">
                                                                <textarea id="message" name="message">{{ old('message') }}</textarea>
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
