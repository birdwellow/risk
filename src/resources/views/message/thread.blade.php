                <div class="panel panel-default game-panel">

                        <div class="panel-heading">
                            {{ $thread->subject }}
                            <div class="recipients">
                                    @foreach($thread->participants as $participant)
                                        {!! userlabel($participant->user) !!}
                                    @endforeach
                                <a class="mini-button" toggle-for="add-users" >+</a>
                            </div>
                            
                            <div id="add-users" style="display: none;">
                                
                                <form method="POST" action="{{ route('thread.addusers', $thread->id) }}" class="form-horizontal" role="form" >

                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <table>
                                        <tr>
                                            <td>
                                                <input type="userselector" name="thread_recipients" placeholder="{{ Lang::get("message.placeholder.addusers") }}" class="{{ invalid('thread_recipients') }}">
                                            </td>
                                            <td class="slave-cell">
                                                <input type="submit" value="{{ Lang::get("message.button.add") }}" class="btn btn-primary" />
                                            <td>
                                        </tr>
                                    </table>

                                </form>
                                
                            </div>
                            
                        </div>
                    
                        <div class="panel-body">
                            
                            <div class="messages">
                                @foreach($thread->messages->reverse() as $message)
                                        <div class="message">
                                            {!! userlabel($message->user, false) !!}
                                            <span class="date-label">
                                                [{!! handyDate($message->created_at) !!}]:
                                            </span>
                                            <span class="messagebody">
                                                {!! $message->body !!}
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
                                                    <textarea id="message" name="thread_message_text" class="{{ invalid('thread_message_text') }}"></textarea>
                                            </div>
                                        </div>

                                        <input type="submit" value="{{ Lang::get('message.button.send') }}" class="btn btn-primary" >
                                </form>
                            </div>
                        </div>
                </div>