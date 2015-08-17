@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-12">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.search.match') }}
                            <div class="info-small">
                                {{ Lang::get('message.title.search.match.help') }}
                            </div>
                        </div>

                        <div class="panel-body">
                            <table>
                                
                                    <tr>
                                        <td>
                                            {{ Lang::get('input.match_name') }}
                                        </td>
                                        <td colspan="2">
                                            {{ Lang::get('input.match_joinedusers') }}
                                        </td>
                                        <td>
                                            {{ Lang::get('message.label.match.creator.name') }}
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="placeholder delimiter">
                                    </tr>

                                    @foreach($matches as $match)
                                        <tr>

                                            <td>
                                                {{ $match->name }}
                                            </td>
                                            <td>
                                                {{ count($match->joinedusers) }}
                                                /
                                                {{ $match->maxusers }}
                                            </td>
                                            <td>
                                                @foreach($match->joinedusers as $joinedUser)
                                                    {!! userlabel($joinedUser) !!}
                                                @endforeach
                                            </td>
                                            <td>
                                                {!! userlabel($match->createdby) !!}
                                                ({{ date("d M Y, H:m:s", strtotime($match->created_at)) }})
                                            </td>
                                            <td>
                                                <a href="{{ route('match.join.init', $match->joinid) }}">
                                                    Join
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                            </table>
                        </div>
                </div>
        </div>
</div>
@endsection
