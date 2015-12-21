@extends('app')

@section('content')

<img src="/img/world.png" class="background-img"/>

<div class="container">
    
        <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                        <div class="panel-heading">
                            {{ Lang::get('message.title.profile') }}
                        </div>
                    
                        <div class="panel-body">
                            
                            <div class="section">
                                
                                <table>
                                    <tr>
                                        <td class="user-avatar-profile-container">
                                            <img src="/img/avatars/{{ $user->avatarfile }}">
                                        </td>
                                        <td class="user-name-profile-container">
                                            <div>
                                                {{ $user->name }}
                                            </div>
                                            <div class="small">
                                                {{ Lang::get('message.label.membersince', ['date' => handyDate($user->created_at)]) }}
                                            </div>
                                            <div class="small">
                                                ({{ Lang::get('message.name.language.' . $user->language) }})
                                            </div>
                                            <div class="small">
                                                <a class="contact" href="{{ route('new.thread.init.to.user', $user->id) }}">
                                                    <img src="/img/message.png"> {{ Lang::get('message.label.contact') }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            
                                        </td>
                                    </tr>
                                </table>
                            
                            </div>
                            
                            <div class="section">
                                
                                <h1>
                                    {{ Lang::get('message.title.profile.statistics') }}:
                                </h1>
                                
                                <table>
                                    
                                    <tr>
                                        <td class="user-statistics">
                                            {{ Lang::get('message.label.matches.created') }}:
                                        </td>
                                        <td class="">
                                            {{ $user->matchescreated }}
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="user-statistics">
                                            {{ Lang::get('message.label.matches.played') }}:
                                        </td>
                                        <td class="">
                                            {{ $user->matchesplayed }}
                                        </td>
                                    </tr>
                                    
                                    <tr class="won">
                                        <td class="user-statistics">
                                            {{ Lang::get('message.label.matches.won') }}:
                                        </td>
                                        <td>
                                            {{ $user->matcheswon }}
                                        </td>
                                    </tr>
                                </table>
                            
                            </div>
                            
                        </div>
                </div>
        </div>
</div>
@endsection
