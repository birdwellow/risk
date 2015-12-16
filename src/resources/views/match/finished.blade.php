@extends('app')

@section('content')

<img src="/img/world.png" class="background-img"/>

<script>
    
    var joinId = "{{ Auth::user()->joinid }}";
    
    $(document).ready(function(){
        $("#refresh").click(function(){
            location.reload();
        });
    });
    
</script>


<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.finished') }}</div>

                        <div class="panel-body center">
                                <table class="summary">
                                    <tr>
                                        <td>
                                            <img src="/img/finish.png" style="height: 100px; margin: 20px;">
                                        </td>
                                        <td>
                                            <h1>
                                                {{ Lang::get('message.title.finished.match', ['matchName'=> $match->name]) }}.
                                            </h1>
                                        </td>
                                    </tr>
                                </table>
                        </div>
                        
                </div>  
        </div>  
</div>

@endsection