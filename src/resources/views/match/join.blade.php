@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.join.match', ['matchName'=>$match->name]) }}
                        </div>

                        <div class="panel-body">
                                
                                <table>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match.name') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->name }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match.joinedusers') }}
                                                </td>
                                                <td class="data">
                                                    @foreach($match->joinedUsers as $joinedUser)
                                                        {{ $joinedUser->name }}
                                                    @endforeach
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match.startdate') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->created_at }}
                                                </td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        {{ Lang::get('input.match.createdby') }}
                                                </td>
                                                <td class="data">
                                                        {{ $match->createdBy->name }}
                                                </td>
                                        </tr>
                                </table>
                            
                                <form method="POST" action="{{ route('match.join.confirm', $match->id) }}" class="form-horizontal" role="form" >

                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        
                                        <input type="submit" value="{{ Lang::get('message.button.match.join') }}" class="btn btn-primary" />
                                </form>
                                
                </div>
        </div>
</div>
@endsection
