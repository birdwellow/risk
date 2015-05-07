@extends('app')

@section('content')
    
<script>
    var username = "{{ $user->name }}";
</script>

<div class="container">
    
        <div class="col-md-8 col-md-offset-0">
                <div class="panel panel-default">
                    @if(!$user->joinedMatch)
                        <div class="panel-heading">{{ Lang::get('message.title.overview.nomatch') }}</div>
                    @else
                        <div class="panel-heading">{{ Lang::get('message.title.overview.yourmatch') }}</div>
                        
                        <div class="panel-body">
                            
                        @if($user->joinedMatch)
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        {{ Lang::get('message.field.match.name') }}
                                    </td>
                                    <td>
                                        {{ Lang::get('message.field.match.joinedusers') }}
                                    </td>
                                    <td>
                                        {{ Lang::get('message.field.match.startdate') }}
                                    </td>
                                    <td>
                                        {{ Lang::get('message.field.match.createdby') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ $user->joinedMatch->name }}
                                    </td>
                                    <td>
                                        @foreach($user->joinedMatch->joinedUsers as $joinedUser)
                                            {{ $joinedUser->name }}
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $user->joinedMatch->created_at }}
                                    </td>
                                    <td>
                                        {{ $user->joinedMatch->createdBy->name }}
                                    </td>
                                </tr>
                            </table>
                        @endif
                        </div>
                    @endif
                        
                </div>
            
                <div class="panel panel-default">
                    @if(!count($invitations))
                        <div class="panel-heading">{{ Lang::get('message.title.overview.noinvitations') }}</div>
                    @else
                        <div class="panel-heading">{{ Lang::get('message.title.overview.invitations') }}</div>
                
                        <div class="panel-body">
                            <table style="width:100%;">
                            @foreach($invitations as $invitation)
                                <tr>
                                    <td style="width:20%;">
                                        {{ $invitation->invitedBy->name }}
                                    </td>
                                    <td style="width:40%;">{{ $invitation->invitation_message }}</td>
                                    <td style="width:40%; text-align: right;">
                                        <a href="{{ route('match.join', $invitation->match->id) }}">{{ Lang::get('message.link.match.join', ['matchName' => $invitation->match->name]) }}</a>
                                        &nbsp;&nbsp;&nbsp;
                                        <a class="warn" href="{{ route('invitation.reject', $invitation->id) }}">{{ Lang::get('message.link.invitation.reject') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            
                @if(count($rejectedInvitations))
                <div class="panel panel-default">
                    <div class="panel-heading">Invitations were rejected</div>

                    <div class="panel-body">
                            <table style="width:100%;">
                            @foreach($rejectedInvitations as $rejectedInvitation)
                                <tr>
                                    <td>
                                        {{
                                            Lang::get("message.text.invitation.rejectec.byfor", [
                                                "rejectorName" => $rejectedInvitation->user->name,
                                                "matchName" => $rejectedInvitation->match->name
                                            ])
                                        }}
                                    </td>
                                    <td style="text-align: right;">
                                        <a class="warn" href="{{ route('invitation.delete', $rejectedInvitation->id) }}">{{ Lang::get('message.link.invitation.delete') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                    </div>
                </div>
                @endif
        </div>
    
        <div class="col-md-4 col-md-offset-0">
                <div class="panel panel-default">
                        <div class="panel-heading">{{ Lang::get('message.title.overview.matches') }}</div>

                        <div class="panel-body">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        {{ Lang::get('message.field.match.name') }}
                                    </td>
                                    <td>
                                        {{ Lang::get('message.field.match.joinedusers') }}
                                    </td>
                                    <td>
                                        {{ Lang::get('message.field.match.startdate') }}
                                    </td>
                                </tr>
                                
                            @foreach($matches as $match)
                                <tr>
                                    <td>
                                        {{ $match->name }}
                                    </td>
                                    <td>
                                        @foreach($match->joinedUsers as $joinedUser)
                                            {{ $joinedUser->name }}
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $match->created_at }}
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                </div>
        </div>
</div>
@endsection
