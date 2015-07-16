@extends('app')

@section('content')
    

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.new.message') }}
                        </div>

                        <div class="panel-body">

                                <form method="POST" action="{{ route('send.new.message') }}" class="form-horizontal" role="form" >
                                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    
                                        <div class="section">
                                            
                                                <div class="form-group">
                                                        <div class="col-md-10 col-md-offset-1">
                                                                <input type="text" name="subject" placeholder="{{ Lang::get('message.placeholder.subject') }}" />
                                                        </div>
                                                </div>
                                            
                                                <div class="form-group">
                                                        <div class="col-md-10 col-md-offset-1">
                                                                <div class="">
                                                                        <input name="usernames" type="userselector" placeholder="{{ Lang::get('message.placeholder.searchrecipient') }}" />
                                                                </div>
                                                        </div>
                                                </div>

                                                <div class="form-group">
                                                        <div class="col-md-10 col-md-offset-1">
                                                                <textarea
                                                                    id="message"
                                                                    name="message"
                                                                    placeholder="{{ Lang::get('message.placeholder.new.message') }}"
                                                                >{{ old('message') }}</textarea>
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
