@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.join.match', ['matchName'=>$match->name]) }}
                        </div>

                        <div class="panel-body">
                                
                                <table>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.name') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->name }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.players') }}
                                                </td>
                                                <td class="data">
                                                    @foreach($match->joinedUsers as $joinedUser)
                                                        {!! userlabel($joinedUser) !!}
                                                    @endforeach
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.date.start') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->created_at }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('message.label.match.creator.name') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->createdBy->name }}
                                                </td>
                                        </tr>
                                </table>
                            
                                <form method="POST" action="{{ route('match.join.confirm', $match->joinid) }}" class="form-horizontal" role="form" >

                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <input type="submit" value="{{ Lang::get('message.button.match.join') }}" class="btn btn-primary" />
                                </form>
                                
                </div>
        </div>
</div>
@endsection
