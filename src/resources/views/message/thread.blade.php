                <div class="panel panel-default game-panel">

                        <div class="panel-heading">
                            {{ $thread->subject }}
                            <div class="recipients">
                                <?php
                                    $participantsString = "";
                                    foreach($thread->participants as $index => $participant){
                                        if($index > 0){
                                            $participantsString .= ", ";
                                        }
                                        $participantsString .= $participant->user->name;
                                    }
                                ?>
                                {{ $participantsString }}
                                <a class="mini-button" href="javascript:UI.toggle('#addUsersToThread')">+</a>
                            </div>
                            
                            <div id="addUsersToThread">
                                
                                <form method="POST" action="{{ route('thread.addusers', $thread->id) }}" class="form-horizontal" role="form" >

                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <table>
                                        <tr>
                                            <td>
                                                <input type="userselector" name="usernames" placeholder="{{ Lang::get("message.placeholder.addusers") }}">
                                            </td>
                                            <td class="slave-cell">
                                                <input type="submit" value="+" class="btn btn-primary" />
                                            <td>
                                        </tr>
                                    </table>

                                </form>
                                
                            </div>
                            
                        </div>
                    
                        <div class="panel-body">
                            
                            <div class="messages">
                                @foreach($thread->messages as $message)
                                        <div class="message">
                                                @if($message->user->avatarfile)
                                                    <img src="/img/avatars/{{ $message->user->avatarfile }}" class="user-avatar icon-s">
                                                @else
                                                    <img src="/img/avatars/default.png" class="user-avatar icon-s">
                                                @endif
                                            <span class="sendername">{{ $message->user->name }}</span>:
                                            <span class="messagebody">
                                                {{ $message->body }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="newmessage">
                                <form method="POST" action="{{ route('thread.newmessage', $thread->id) }}" class="form-horizontal" role="form" enctype="multipart/form-data" >

                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="form-group">
                                            <div class="col-md-12">
                                                    <textarea id="message" name="message"></textarea>
                                            </div>
                                        </div>

                                        <input type="submit" value="{{ Lang::get('message.button.send') }}" class="btn btn-primary" >
                                </form>
                            </div>
                        </div>
                </div>