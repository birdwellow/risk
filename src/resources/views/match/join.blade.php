@extends('app')

@section('content')

<div class="container">
    
        <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.join.match') }}:
                            <div class="namemarker">{{ $match->name }}</div>
                        </div>

                        <div class="panel-body">
                            
                                <form method="POST" action="{{ route('match.join.confirm', $match->joinid) }}" class="form-horizontal" role="form" >
                                
                                        <table>
                                                <tr>
                                                        <td>
                                                                {{ Lang::get('message.label.match.players') }} ({{ count($match->joinedUsers) }}/{{ $match->maxusers }})
                                                        </td>
                                                        <td class="data">
                                                            @foreach($match->joinedUsers as $joinedUser)
                                                                {!! userlabel($joinedUser) !!}
                                                            @endforeach
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                {{ Lang::get('input.match_map_name') }}
                                                        </td>
                                                        <td class="data">
                                                                {{ Lang::get('input.match_map_name.' . $match->mapname) }}
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                {{ Lang::get('message.label.match.public') }}
                                                        </td>
                                                        <td class="data">
                                                                @if($match->public)
                                                                    {{ Lang::get('message.button.yes') }}
                                                                @else
                                                                    {{ Lang::get('message.button.no') }}
                                                                @endif
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                {{ Lang::get('message.label.match.creator.name') }}
                                                        </td>
                                                        <td class="data">
                                                                {!! userlabel($match->createdBy) !!}
                                                                ({{ date("d M Y, H:m:s", strtotime($match->created_at)) }})
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <td>
                                                                {{ Lang::get('input.match_user_colorscheme') }}
                                                        </td>
                                                        <td class="data">
                                                                <select class="{{ invalid('match_user_colorscheme') }}" name="match_user_colorscheme" id="match_user_colorscheme">
                                                                    @foreach($colorSchemes as $colorScheme)
                                                                        <option class="matchUserColorScheme {{ $colorScheme }}" value="{{ $colorScheme }}">
                                                                                {{ Lang::get('input.match_user_colorscheme.' . $colorScheme) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                        </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="2" class="placeholder"></td>
                                                </tr>

                                        </table>

                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <input type="submit" value="{{ Lang::get('message.button.match.join') }}" class="btn btn-primary" />
                                </form>

                        </div>
                </div>
        </div>
</div>
@endsection
