@extends('app')

@section('content')
    
<script src="/js/match.js" defer="defer"></script>
<script>
    var username = "{{ Auth::user()->name }}";
    var joinId = "{{ Auth::user()->joinid }}";
    
</script>

<div class="container right">
    
        <div class="col-md-4 col-md-offset-1 right tool-panel">
                <div class="panel panel-default">
                        <div class="panel-heading">
                                {{ Lang::get('message.title.match.chat') }}
                        </div>

                        <div class="panel-body" id="chatcontent">
                        </div>
                        
                        <input type="text" id="chatinput"/>
                </div>
        </div>
</div>
@endsection
