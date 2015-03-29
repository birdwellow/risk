@extends('app')

@section('content')
    
<script src="/js/app.js" defer="defer"></script>
<script>
    var username = "{{ Auth::user()->name }}";
    var userId = "{{ Auth::user()->id }}";
    var matchId = {{ $match->id }};
</script>

<div class="container right">
    
        <div class="col-md-4 col-md-offset-1 right">
                <div class="panel panel-default">
                        <div class="panel-heading">Chat</div>

                        <div class="panel-body" id="chatcontent">
                        </div>
                        <input type="text" id="chatinput">
                        </input>
                </div>
        </div>
</div>
@endsection
